<?php

namespace App\Http\Controllers;

use App\Models\Products;
use App\Models\Sections;
use Illuminate\Http\Request;

class ProductsController extends Controller
{

    function __construct()
    {
        $this->middleware(['permission:المنتجات'], ['only' => ['index'] ]);
    }


    public function index()
    {
        $sections = Sections::all();
        $products = Products::all();
        return view('products.product' , compact('sections' ,'products'));
    }


    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_name' => 'required|unique:products|max:255',
            'section_id' => 'required',
            'description' => 'required'
        ],
        [
            'product_name.required' => 'برجاء أدخال أسم المنتج',
            'product_name.unique' => 'أسم المنتج مسجل مسبقا',
            'section_id.required' => 'برجاء أدخال القسم',
            'description.required' => 'برجاء أدخال الوصف',
        ]);

        Products::create([
            'product_name' => $request->product_name,
            'section_id' => $request->section_id,
            'description' => $request->description,

        ]);
        session()->flash('Add', 'تم اضافة المنتج بنجاح ');
        return redirect('/products');
    }


    public function show(Products $products)
    {
        //
    }


    public function edit(Products $products)
    {
        //
    }


    public function update(Request $request)
    {

        $id = sections::where('section_name', $request->section_name)->first()->id;

        $products = Products::findOrFail($request->pro_id);

        $this->validate($request, [
            'product_name' =>'required|max:255|:products,product_name,'.$id,
            'description' => 'required',
        ],
            [

                'product_name.required' => 'برجاء أدخال أسم المنتج',
                'description.required' => 'برجاء أدخال الوصف',
            ]);

        $products->update([
            'product_name' => $request->product_name,
            'description' => $request->description,
            'section_id' => $id,
        ]);

        session()->flash('Edit', 'تم تعديل المنتج بنجاح');
        return back();

    }


    public function destroy(Request $request)
    {
        $Product = Products::findorfail($request->pro_id);
        $Product->delete();
        session()->flash('delete' , 'تم حذف القسم بنجاح');
        return redirect('/products');
    }
}
