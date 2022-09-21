<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CampusesController extends Controller
{
    //

    public function index()
    {
        # code...
        $data['title'] = "Manage Campuses";
        if (request()->has('school_id')) {
            $data['campuses'] = \App\Models\School::find(request('school_id'))->campuses();
        }
        else {
            $data['campuses'] = \App\Models\Campus::all();
        }
        return view('admin.campuses.index', $data);
    }

    public function create()
    {
        # code...
        $data['title'] = "Add New Campus";
        if (request()->has('school_id')) {
            $data['campuses'] = \App\Models\School::find(request('school_id'))->campuses();
        }
        else {
            $data['campuses'] = \App\Models\Campus::all();
        }
        return view('admin.campuses.create', $data);
    }
    
    public function store(Request $request)
    {
        # code...
        $validator = Validator::make($request->all(), [
            'school_id'=>'required',
            'name'=>'required',
            'address'=>'required',
            'telephone'=>'required'
        ]);

        if ($validator->fails()) {
            # code...
            return back()->with('error', $validator->errors()->first());
        }

        try {
            //code...
            if (\App\Models\School::find($request->school_id)->campuses()->where('name', $request->name)->count() > 0) {
                return back()->with('error', 'A campus with name '.$request->name.' already exist');
            }
    
            (new \App\Models\Campus($request->all()))
                ->save();
            return back()->with('success', 'Campus created');
        } catch (\Throwable $th) {
            //throw $th;
            throw $th;
            return back()->with('error', $th->getMessage());
        }

    }

    public function edit($id)
    {
        $data['title'] = "edit campus";
        $data['campus'] = \App\Models\Campus::find($id);
        if (request()->has('school_id')) {
            $data['campuses'] = \App\Models\School::find(request('school_id'))->campuses();
        }
        else {
            $data['campuses'] = \App\Models\Campus::all();
        }
        return view('admin.campuses.edit', $data);
    }

    public function update($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'school_id'=>'required',
            'name'=>'required',
            'address'=>'required',
            'relephone'=>'required'
        ]);

        try {
            // if name and/or contact already exist, reject update
            $campus = \App\Models\Campus::find($id);
            if ($campus->name != $request->name && \App\Models\Campus::where('name', $request->name)->count() > 0) {
                # code...
                return back()->with('error', 'Update rejected. The campus name '.$request->name.' already exist');
            }
            if (isset($request->telephone) && $campus->telephone != $request->telephone && \App\Models\Campus::where('telephone', $request->telephone)->count() > 0) {
                # code...
                return back()->with('error', 'Update rejected. The campus tel '.$request->telephone.' already exist');
            }
            $campus->fill($request->all());
            $campus->save();
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('error', $th->getMessage());
        }
    }

    public function delete($id)
    {
        # code...
        if (\App\Models\Campus::find($id)->students()->count() > 0) {
            # code...
            return back()->with('error', 'Campus can not be deleted. Campus has students');
        }
        \App\Models\Campus::find($id)->delete();
    }
}
