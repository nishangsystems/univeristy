<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Nette\Utils\Strings;

class SchoolsController extends Controller
{
    //
    public function index()
    {
        # code...
        $data['title'] = "Manage Schools";
        return view('admin.schools.index');
    }

    public function create()
    {
        $data['title'] = "New School";
        return view('admin.schools.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'=>'required',
            'contact'=>'required',
            'address'=>'required',
        ]);
        if (!$validator->fails()) {
            # code...
            try {
                $school = new \App\Models\School($request->all());
                if ($request->has('logo_path')) {
                    # code...
                    $filename = time().str_shuffle("lorem98346dsfde43ocf9840021bvd").'.'. $request->file('logo_path')->getClientOriginalExtension();
                    $request->file('logo_path')->storeAs('public/images/logos', $filename);
                    $filename = public_path('storage/public/images/logos/').$filename;
                    $school->logo_path = $filename;
                }
                $school->save();
                return back()->with('success', 'School created');
            } catch (\Throwable $th) {
                //throw $th;
                return back()->with('error', $th->getMessage());
            }
        }else {
            return back()->with('error', $validator->errors()->first());
        }
    }

    public function edit($id)
    {
        $data['title'] = "Edit School";
        $data['school'] = \App\Models\School::find($id);
        return view('admin.schools.edit', $data);
    }

    public function update($id, Request $request)
    {
        # code...
    }

    public function preview($id)
    {
        # code...
        $data['title'] = "School Preview";
        $data['school'] = \App\Models\School::find($id);
        return view('admin.schools.preview', $data);
    }


}
