<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ArchiveInvoicesController;
use App\Http\Controllers\CustomersReportController;
use App\Http\Controllers\InvoiceAttachmentsController;
use App\Http\Controllers\InvoicesController;
use App\Http\Controllers\InvoicesDetailsController;
use App\Http\Controllers\InvoicesReportController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SectionsController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});


Auth::routes();
//Auth::routes(['register'=> false]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::resource('invoices' , InvoicesController::class);

// ده ال Route الي برفع منه ملف جديد بيروح علي function ال store
Route::resource('InvoiceAttachments' , InvoiceAttachmentsController::class);

Route::get('/InvoicesDetails/{id}' , [InvoicesDetailsController::class , 'edit']);

Route::get('View_file/{invoice_number}/{file_name}', [InvoicesDetailsController::class, 'open_file'] );
Route::get('download/{invoice_number}/{file_name}', [InvoicesDetailsController::class, 'download_file'] );
Route::post('delete_file', [InvoicesDetailsController::class, 'delete_file'])->name('delete_file');

Route::get('edit_invoice/{id}' , [InvoicesController::class, 'edit']);
Route::get('Status_show/{id}' , [InvoicesController::class , 'show'])->name('Status_show');
Route::post('/Status_Update/{id}', [InvoicesController::class , 'Status_Update'])->name('Status_Update');

// ال Route ده بتاع ال Archive
Route::resource('Archive', ArchiveInvoicesController::class);

// ال Route ده بتاع ال print الي بيطبعلي الفاتوره
Route::get('print_invoice/{id}' , [InvoicesController::class , 'Print_invoice']);

Route::get('/section/{id}' , [InvoicesController::class , 'get_products']);

// ال Routes دي بتاعت الفواتير المدفوعه والغير مدفوعه والمدفوعه جزئيا
Route::get('invoices_paid' , [InvoicesController::class , 'invoices_paid']);
Route::get('invoices_unpaid' , [InvoicesController::class , 'invoices_Unpaid']);
Route::get('invoices_partial/{id}' , [InvoicesController::class , 'invoices_Partial']);

// ال Route ده بتاع ال export
Route::get('export_invoices', [InvoicesController::class, 'export']);


Route::resource('sections' , SectionsController::class);
Route::resource('products' , ProductsController::class);


Route::middleware('auth')->group(function () {

    // Our resource routes
    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);
});
 # routes تقارير الفاتورة
Route::get('invoices_report' , [InvoicesReportController::class , 'index']);
Route::post('Search_invoices' , [InvoicesReportController::class , 'Search_invoices']);

 # routes تقارير المستخدمين
Route::get('customers_report' , [CustomersReportController::class , 'index'])->name('customers_report');
Route::post('Search_customers' , [CustomersReportController::class , 'Search_customers']);

# دي الروت الي بتقرأ منها كل ال notfy ال جايه من الي بيضيف الفاتوره
Route::get('MarkAsRead_all' , [InvoicesController::class , 'MarkAsRead_all'])->name('MarkAsRead_all');

Route::get('/{page}' , [AdminController::class , 'index'] );

