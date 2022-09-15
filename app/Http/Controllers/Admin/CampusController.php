<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CampusController extends Controller
{
    //list the campuses of a given school
    public function index()
    {
        if (request()->has('school_id')) {
            $data['campuses'] = \App\Models\School::find(request('school_id'))->campuses();
        }
        else {
            $data['campuses'] = [];
        }
        return view();
    }

    // home page of a given campus
    public function home()
    {
        
    }

    public function create()
    {
        # code...
        // return view();
    }

    public function store()
    {
        # code...
        request()->validate([
            'school_id'=>'required',
            'name'=>'required',
            'address'=>'required',
            'telephone'=>'required|tel'
        ]);

        try {
            if(request('school_id') != null){
                if (count(\App\Models\School::find(request('school_id'))->campuses()->where('name', request('name'))) == 0) {
                    # code...
                    $campus = new \App\Models\Campus(request()->all());
                    $campus->save();
                    return redirect(route('campus.index'));
                }
                else {
                    return back()->with('error', 'Campus with name '.request('name').' already exists in this school');
                }
            }
            else {
                return back()->with('error', 'No school specified');
            }
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('error', $th->getMessage());
        }
    }

    public function edit()
    {
        if (request()->has('campus_id')) {
            # code...
            $data['data'] = \App\Models\Campus::find(request('campus_id'));
            // return view('view', $data);
        }
        else {
            return back()->with('error', 'No campus specified');
        }
    }

    public function update()
    {
        request()->validate([
            'campus_id'=>'required',
            'school_id'=>'required',
            'name'=>'required',
            'address'=>'required',
            'telephone'=>'required|tel'
        ]);

        try {
            if(request('campus_id') != null){
                $campus = \App\Models\Campus::find(request('campus_id'));
                $campus->fill(request()->all());
                $campus->save();
                return back()->with('success', 'Campus updated.');
            }
            else {
                return back()->with('error', 'No campus specified');
            }
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('error', $th->getMessage());
        }
    }

    public function delete()
    {
        if (request()->has('campus_id')) {
            # code...
            \App\Models\Campus::find(request('campus_id'))->delete();
            return redirect(route('campus.index')); 
        }
        return back()->with('error', 'No campus specified');
    }

}
