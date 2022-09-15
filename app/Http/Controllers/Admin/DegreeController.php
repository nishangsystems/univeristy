<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DegreeController extends Controller
{
    //
    public function index()
    {
        # code...
        // get the degree types for the given school
        if (request()->has('school_id')) {
            $degrees = \App\Models\School::find(request('school_id'))->degrees();
        }
        else {
            $degrees = \App\Models\School::find(auth()->user()->school_id)->degrees();
        }
        // return view();
    }

    // show form to create a degree/background
    public function create()
    {
        # code...
        // return view();
    }

    public function save()
    {
        request()->validate([
            'name'=>'required',
            'school_id'=>'required'
        ]);
        try {
            //code...
            if (count(\App\Models\School::find(request('school_id'))->degrees()->where('name', request('name')))>0) {
                # code...
                return back()->with('error', 'Degree already exists');
            }
            $degree = new \App\Models\Degree(request()->all());
            $degree->save();
            return redirect(route('degrees.index').'?school_id='.$degree->school_id);
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('error', $th->getMessage());
        }
    }
    
    public function edit()
    {
        # code...
        // return view();
    }
    public function update()
    {
        request()->validate([
            'degree_id'=>'required',
            'name'=>'required',
            'school_id'=>'required'
        ]);
        try {

            //Make sure a degree name does not already exist
            if (count(\App\Models\School::find(request('school_id'))->degrees()->where('name', request('name')))>0 && \App\Models\Degree::find(request('degree_id'))->name != request('name')) {
                # code...
                return back()->with('error', 'Degree already exists');
            }
            $degree = \App\Models\Degree::find(request('degree_id'));
            $degree->name = request('name');
            $degree->save();
            return redirect(route('degrees.index').'?school_id='.$degree->school_id);
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('error', $th->getMessage());
        }
    }

    public function delete()
    {
        // if degree has programs, it can't be deleted.
        if (request()->has('degree_id')) {
            # code...
            if (count(\App\Models\Degree::find(request('degree_id'))->programs()) > 0) {
                # code...
                return back()->with('error', 'Can not deleted. Degree has programs');
            }
            \App\Models\Degree::find(request('degree_id'))->delete();
            return redirect(route('degrees.index'));
        }
    }
}
