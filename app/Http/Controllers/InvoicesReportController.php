<?php

namespace App\Http\Controllers;

use App\Models\Invoices;
use Illuminate\Http\Request;

class InvoicesReportController extends Controller
{
    public function index()
    {
        return view('reports.invoices_report');
    }


    public function Search_invoices(Request $request)
    {

        $radio_box = $request->radio;

        # في حالة البحث بنوع الفاتورة
        if ($radio_box == 1 ) {

# في حالة عدم تحديد تاريخ
            if ($request->type_invoice && $request->start_at =='' && $request->end_at == ''){
                $invoices = Invoices::select('*')->where('Status' , '=' ,$request->type_invoice)->get();
                $type_invoices = $request->type_invoice;
                return view('reports.invoices_report' , compact('type_invoices'))->withDetails($invoices);
            }
            # في حالة تحديد تاريخ استحقاق
            else{
                $start_at = date($request->start_at);
                $end_at =date($request->end_at);
                $type_invoices = $request->type_invoice;
                $invoices = invoices::whereBetween('invoice_Date',[$start_at,$end_at])->where('Status','=',$request->type_invoice)->get();
                return view('reports.invoices_report' , compact('type_invoices','start_at','end_at'))->withDetails($invoices);

            }
        }
#  البحث برقم الفاتورة
        else {
            $invoices = Invoices::select('*')->where('invoice_number' , '=' , $request->invoice_number)->get();
            return view('reports.invoices_report')->withDetails($invoices);
        }


    }

}
