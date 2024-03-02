<?php

namespace App\Http\Controllers;

use App\Exports\InvoicesExport;
use App\Models\invoice_attachments;
use App\Models\Invoices;
use App\Models\Invoices_details;
use App\Models\Sections;
use App\Models\User;
use App\Notifications\Add_invoice_new;
use App\Notifications\AddInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;


class InvoicesController extends Controller
{
    function __construct()
    {
        # ده الكود ال بيتيح للمستخدم انه يقدر يدخل علي اي وميقدرش يدخل علي ايه
        $this->middleware(['permission:قائمة الفواتير'], ['only' => ['index'] ]);
        $this->middleware(['permission:أضافة فاتورة'], ['only' => ['create', 'store'] ]);
        $this->middleware(['permission:الفواتير المدفوعة'], ['only' => ['invoices_paid'] ]);
        $this->middleware(['permission:الفواتير الغير مدفوعة'], ['only' => ['invoices_Unpaid'] ]);
        $this->middleware(['permission:الفواتير المدفوعة جزئيا'], ['only' => ['invoices_Partial'] ]);
        $this->middleware(['permission:تعديل الفاتورة'], ['only' => ['edit'] ]);
        $this->middleware(['permission:تغير حالة الدفع'], ['only' => ['show'] ]);
        $this->middleware(['permission:طباعة الفاتورة'], ['only' => ['Print_invoice'] ]);
    }


    public function index()
    {
        $invoices = Invoices::all();
        return view('invoices.invoices' , compact('invoices'));
    }



    public function create()
    {
        $sections = Sections::all();
        return view('invoices.add_invoice' , compact('sections'));
    }



    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_number' => 'required|unique:invoices|max:255',
            'product' => 'required',
            'Section' => 'required',
        ],
            [
                'invoice_number.required' => ' برجاء أدخال رقم الفاتورة',
                'invoice_number.unique' => 'رقم الفاتورة مسجل مسبقا',
                'product.required' => 'برجاء أدخال المنتج',
                'Section.required' => 'برجاء أدخال القسم',

            ]
        );

        // ده الي بيحفظ في جدول ال invoices الرئيسي
        Invoices::create([
            'invoice_number' => $request-> invoice_number,
            'invoice_Date' => $request-> invoice_Date ,
            'Due_date' => $request-> Due_date,
            'product' => $request->product,
            'section_id'  => $request-> Section ,
            'Amount_collection' => $request-> Amount_collection,
            'Amount_Commission' => $request-> Amount_Commission,
            'Discount' => $request-> Discount,
            'Value_VAT' => $request-> Value_VAT,
            'Rate_VAT' => $request-> Rate_VAT,
            'Total' => $request-> Total,
            'Status' =>  'غير مدفوعة ',
            'Value_Status' => 2,
            'note' => $request-> note,
        ]);

        //الكود ده الي بيضيف بيانات الفاتوره في حدول invoices_details
            // هنا انت بتجيب ال id الي في جدول ال invoices
        $invoice_id = Invoices::latest()->first()->id;
        Invoices_details::create([
            'id_Invoice' => $invoice_id ,
            'invoice_number' =>$request->invoice_number,
            'product' =>$request-> product,
            'Section' =>$request-> Section,
            'Status' =>'غير مدفوعه',
            'Value_Status' =>'2',
            'note' =>$request-> note,
            'user' =>(Auth::User()->name),
        ]);

        // ده الكود الي بيعمل store قي جدول ال invoice_attachments
            // لو جالك ريكويست اسمه pic اعملي الحاجات دي
        if ($request->hasFile('pic')) {
            //هنا بيجيب ال id من جدول ال invoices
            $invoice_id =Invoices::latest()->first()->id;
            $image =$request->file('pic');
            // هنا بيجيب اسم الفايل
            $file_name = $image->getClientOriginalName();
            // ال  invoice_number ده رقم الفاتوره كام
            $invoice_number = $request->invoice_number;

            // هنا بيعمل new للحاجات دي في الداتا بيز في جدول ال invoice_attachments
            $attachments = new invoice_attachments();
            $attachments->file_name = $file_name;
            $attachments->invoice_number = $invoice_number;
            $attachments->Created_by = Auth::User()->name;
            $attachments->invoice_id =$invoice_id;
            $attachments->save();


            // Move Pic in folder Public
            $imageName = $request->pic->getClientOriginalName();
            // الأمر ده بيعمل فولدر في ال public  وبيحد جواه فولدر Attachments وجواه بيحط ال invoice_number وبيحط ال image_name
            $request->pic->move(public_path('Attachments/' . $invoice_number) , $imageName);

        }

                #mail to mailtraip
//            $user = User::first();
//            $user->notify(new AddInvoice($invoice_id));
//            Notification::send($user , new AddInvoice($invoice_id));


        $user = User::get();
        $invoices = invoices::latest()->first();
        Notification::send($user, new \App\Notifications\Add_invoice_new($invoices));



        session()->flash('Add_invoice');
            return redirect('/invoices');
    }


    public function show($id)
    {
        $invoices = invoices::where('id', $id)->first();
        return view('invoices.status_update', compact('invoices'));
    }


    public function edit($id)
    {
        $invoices = Invoices::where('id', $id)->first();
        $sections = Sections::all();
        return view('invoices.edit_invoice' , compact('sections' , 'invoices'));
    }


    public function update(Request $request)
    {
        $invoices = Invoices::findOrFail($request->invoice_id);
        $invoices->update([
            'invoice_number' => $request->invoice_number,
            'invoice_Date' => $request->invoice_Date,
            'Due_date' => $request->Due_date,
            'product' => $request->product,
            'section_id' => $request->Section,
            'Amount_collection' => $request->Amount_collection,
            'Amount_Commission' => $request->Amount_Commission,
            'Discount' => $request->Discount,
            'Rate_VAT' => $request->Rate_VAT,
            'Value_VAT' => $request->Value_VAT,
            'Total' => $request->Total,
            'note' => $request->note,
        ]);

        session()->flash('edit_invoice');
        return redirect('/invoices');
    }


    public function destroy(Request $request)
    {
        $id = $request->invoice_id;
        $invoices = Invoices::where('id' , $id)->first();
        $Details = invoice_attachments::where('invoice_id', $id)->first();

        $id_page =$request->id_page;


        if (!$id_page==2) {

            if (!empty($Details->invoice_number)) {

                Storage::disk('public_uploads')->deleteDirectory($Details->invoice_number);
            }

            $invoices->forceDelete();
            session()->flash('delete_invoice');
            return redirect('/invoices');

        }

        else {

            $invoices->delete();
            session()->flash('archive_invoice');
            return redirect('/Archive');
        }


    }


    public function get_products($id)
    {
        // ده الكود الي بيجيب الداتا من جدول ال products ويعرضه لما اختار ال section_id
        $products = DB::table("products")->where("section_id", $id)->pluck("product_name", "id");
        return json_encode($products);
    }

    public function Status_Update($id, Request $request)
    {
        $invoices = invoices::findOrFail($id);

        if ($request->Status === 'مدفوعة') {

            $invoices->update([
                'Value_Status' => 1,
                'Status' => $request->Status,
                'Payment_Date' => $request->Payment_Date,
            ]);

            invoices_Details::create([
                'id_Invoice' => $request->invoice_id,
                'invoice_number' => $request->invoice_number,
                'product' => $request->product,
                'Section' => $request->Section,
                'Status' => $request->Status,
                'Value_Status' => 1,
                'note' => $request->note,
                'Payment_Date' => $request->Payment_Date,
                'user' => (Auth::user()->name),
            ]);
        }
        elseif ($request->Status === 'غير مدفوعة'){
            $invoices->update([
                'Value_Status' => 2,
                'Status' => $request->Status,
                'Payment_Date' => $request->Payment_Date,
            ]);
            invoices_Details::create([
                'id_Invoice' => $request->invoice_id,
                'invoice_number' => $request->invoice_number,
                'product' => $request->product,
                'Section' => $request->Section,
                'Status' => $request->Status,
                'Value_Status' => 2,
                'note' => $request->note,
                'Payment_Date' => $request->Payment_Date,
                'user' => (Auth::user()->name),
            ]);
        }
        else {
            $invoices->update([
                'Value_Status' => 3,
                'Status' => $request->Status,
                'Payment_Date' => $request->Payment_Date,
            ]);
            invoices_Details::create([
                'id_Invoice' => $request->invoice_id,
                'invoice_number' => $request->invoice_number,
                'product' => $request->product,
                'Section' => $request->Section,
                'Status' => $request->Status,
                'Value_Status' => 3,
                'note' => $request->note,
                'Payment_Date' => $request->Payment_Date,
                'user' => (Auth::user()->name),
            ]);
        }
        session()->flash('Status_Update');
        return redirect('/invoices');

    }
    // الفواتير المدفوعه

    public function invoices_paid()
    {
        $invoices = Invoices::where('Value_Status' , 1)->get();
        return view('invoices.invoices_paid' , compact('invoices'));
    }

    // الفواتير الغير مدفوعه
    public function invoices_Unpaid()
    {
        $invoices = Invoices::where('Value_Status' , 2)->get();
        return view('invoices.invoices_unpaid' , compact('invoices'));
    }
    // الفواتير المدفوعه جزئيا

    public function invoices_Partial()
    {
        $invoices = Invoices::where('Value_Status' , 3)->get();
        return view('invoices.invoices_partial' , compact('invoices'));
    }


    // ده الكود الي بيطبع الفواتير
    public function Print_invoice($id)
    {
        $invoices = Invoices::where('id' , $id)->first();
        return view('invoices.print_invoice' , compact('invoices'));
    }



    // ده الكود الي بيعملي export للداتا
    public function export()
    {
        return Excel::download(new InvoicesExport, 'invoices.xlsx');
    }


    # دي ال function  الي بتقرأ منها كل ال notfy ال جايه من الي بيضيف الفاتوره

    public function MarkAsRead_all(Request $request)
    {
        $unreadNotifications = auth()->user()->unreadNotifications ;

        if ($unreadNotifications){
            $unreadNotifications->markAsRead();
            return back();
        }
    }


}
