<?php

namespace App\Http\Controllers;

use App\Models\invoice_attachments;
use App\Models\Invoices;
use App\Models\Invoices_details;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InvoicesDetailsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoices_details $invoices_details)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $invoices = Invoices::where('id' , $id)->first();
        $details = Invoices_details::where('id_invoice',$id)->get();
        $attachments = invoice_attachments::where('invoice_id' , $id)->get();
        return view('invoices.details_invoices' , compact('invoices' , 'details' , 'attachments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Invoices_details $invoices_details)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoices_details $invoices_details)
    {
        //
    }


    public function open_file($invoice_number , $file_name)
    {
         // ده كود العرض بيعرض الفاتوره وبياخد اتنين برامتر (invoice_number , file_name) الأسم الفايل بتاع الفاتوره وبياخد اسم الفايل الي انت بترفعه
        $st ="Attachments";
        $pathToFile = public_path($st.'/'.$invoice_number.'/'.$file_name);
        return response()->file($pathToFile);

    }

    public function download_file($invoice_number , $file_name)
    {
         // ده كود التنزيل الي بينزل الفاتوره وبياخد اتنين برامتر (invoice_number , file_name) الأسم الفايل بتاع الفاتوره وبياخد اسم الفايل الي انت بترفعه
        $st ="Attachments";
        $download = public_path($st.'/'.$invoice_number.'/'.$file_name);
        return response()->file($download);

    }


    public function delete_file(Request $request)
    {
        $invoices = invoice_attachments::findOrFail($request->id_file);
        $invoices->delete();
        Storage::disk('public_uploads')->delete('Attachments/'. $request->invoice_number.'/'.$request->file_name);
        session()->flash('delete', 'تم حذف المرفق بنجاح');
        return back();
    }

}




