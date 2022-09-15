<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SchoolController extends Controller
{
    //school home page
    public function show()
    {
        // return view()
    }

    // show form to create school
    public function create()
    {
        // return view()
    }

    public function store()
    {
        request()->validate([
            'name'=>'required',
            'description'=>'required',
            'logo'=>'required|image'
        ]);

        try {
            if (count(\App\Models\School::where('name', request('name'))) == 0) {
                # code...
                $logo_filename = 'logo_'.random_int(10000000, 99999999).'_'.time().'_'.request()->getClientIp().'.'. request()->file('logo')->getClientOriginalExtension();
                request()->file('logo')->storeAs('public/images/logo', $logo_filename);
    
                $school = new \App\Models\School(request()->all());
                $school->logo_path = $logo_filename;
                $school->save();
                return redirect(route('school.home', $school->id));
            }
            else{
                return back()->with('error', 'A school with name: '.request('name').' already exists');
            }
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('error', $th->getMessage());
        }
    }

    public function edit()
    {
        # code...
    }
    public function update()
    {
        request()->validate([
            'school_id'=>'required',
            'name'=>'required',
            'description'=>'required',
        ]);

        try {
            if (count(\App\Models\School::where('name', request('name'))) > 0) {
                # code...
                $school = \App\Models\School::find(request('school_id'));
                if (!$school->name == request('name')) {
                    return back()->with('error', 'A school already exists with name '.request('name'));
                }
                $school->fill(request()->all);
                if(request()->has('logo')){
                    $logo_filename = 'logo_'.random_int(10000000, 99999999).'_'.time().'_'.request()->getClientIp().'.'. request()->file('logo')->getClientOriginalExtension();
                    request()->file('logo')->storeAs('public/images/logo', $logo_filename);
                    $school->logo_path = $logo_filename;
                }
                $school->save();
                return redirect(route('school.home', $school->id));
            }
            else{
                $school = \App\Models\School::find(request('school_id'));
                $school->fill(request()->all);
                if(request()->has('logo')){
                    $logo_filename = 'logo_'.random_int(10000000, 99999999).'_'.time().'_'.request()->getClientIp().'.'. request()->file('logo')->getClientOriginalExtension();
                    request()->file('logo')->storeAs('public/images/logo', $logo_filename);
                    $school->logo_path = $logo_filename;
                }
                $school->save();
                return redirect(route('school.home', $school->id));
            }
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('error', $th->getMessage());
        }
    }

    public function delete()
    {
        # code...
        unlink(storage_path().'/public/images/logo/'.\App\Models\School::find(request('school_id'))->name);
        \App\Models\School::find(request('school_id'))->delete();
        return redirect(route('school.home'));
    }
}
