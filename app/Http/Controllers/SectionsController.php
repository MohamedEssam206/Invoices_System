<?php

namespace App\Http\Controllers;

use App\Models\Sections;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SectionsController extends Controller
{

    function __construct()
    {
        $this->middleware(['permission:الأقسام'], ['only' => ['index'] ]);
    }


    public function index()
    {
        $sections = Sections::all();
        return view('sections.section' , compact('sections'));
    }


    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'section_name' => 'required|unique:sections|max:255',
            'description' => 'required',
        ],
        [
            'section_name.required' => 'برجاء أدخال أسم القسم',
            'section_name.unique' => 'أسم القسم مسجل مسبقا',
            'description.required' => 'برجاء أدخال الوصف',

        ]
        );

            sections::create([
                'section_name' =>$request->section_name,
                'description' =>$request->description,
                "created_by" =>(Auth::user()->name),
            ]);
            session()->flash('Add' , 'تم اضافه القسم بنجاح');
            return redirect('/sections');

        }


    public function show(Sections $sections)
    {
        //
    }


    public function edit(Sections $sections)
    {
        //
    }


    public function update(Request $request)
    {
        $id = $request->id;

        $this->validate($request , [
            'section_name' => 'required|max:255|unique:sections,section_name,'.$id,
            'description' => 'required',
        ],
        [

            'section_name.required' => 'برجاء أدخال أسم القسم',
            'section_name.unique' => 'أسم القسم مسجل مسبقا',
            'description.required' => 'برجاء أدخال الوصف',
        ]);

        $section = Sections::find($id);

        $section->update([
            'section_name' => $request-> section_name,
            'description' => $request-> description

        ]);
        session()->flash('edit' , 'تم تعديل القسم بنجاح');
        return redirect('/sections');

    }

    public function destroy(Request $request)
    {
        $id = $request->id;
        Sections::find($id)->delete();
        session()->flash('delete' , 'تم حذف القسم بنجاح');
        return redirect('/sections');
    }



}
