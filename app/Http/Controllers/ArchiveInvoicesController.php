<?php

namespace App\Http\Controllers;

use App\Models\Invoices;
use Illuminate\Http\Request;

class ArchiveInvoicesController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    function __construct()
    {
        $this->middleware(['permission:أرشيف الفواتير'], ['only' => ['index']]);
    }


    public function index()
    {
        // معني الكود انه بيظهر ليا الداتا الي تم ارشفها بس
        $invoices = Invoices::onlyTrashed()->get();
        return view('invoices.Archive_Invoices' , compact('invoices'));
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
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        // معني الكود أنه بيرجع اي فاتوره موجوده في ال Archive
        $id = $request->invoice_id;
        $invoices = Invoices::withTrashed()->where('id' , $id)->restore();
        session()->flash('restore_invoice');
        return redirect('/invoices');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        // معني الكود أنه بيروح يشوف اي الحاجه الي موجوده جوا ال Archive ويعملها delete
        $id = $request->invoice_id;
        $invoices = Invoices::withTrashed()->where('id' , $id)->first();
        $invoices->forceDelete();
        session()->flash('delete_invoice');
        return redirect('/Archive');
    }
}
