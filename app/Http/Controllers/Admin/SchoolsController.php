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
        $data['title'] = __('text.manage_schools');
        return view('admin.schools.index');
    }

    public function create()
    {
        $data['title'] = __('text.new_school');
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
                return back()->with('success', __('text.word_done'));
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
        $data['title'] = __('text.edit_school');
        $data['school'] = \App\Models\School::find($id);
        return view('admin.schools.edit', $data);
    }

    public function update($id, Request $request)
    {
        # code...
        $validator = Validator::make($request->all(), [
            'name'=>'required',
            'contact'=>'required',
            'address'=>'required',
        ]);
        if (!$validator->fails()) {
            # code...
            try {
                $school = \App\Models\School::find($id);
                if (\App\Models\School::where('name', '=', $request->name)->count()>0 && $school->name != $request->name) {
                    # code...
                    return back()->with('error', __('text.record_already_exist', ['item'=>$request->name]));
                }
                $school->name = $request->name;
                $school->contact = $request->contact;
                $school->address = $request->address;

                if ($request->has('logo_path')) {
                    # code...
                    $filename = time().str_shuffle("lorem98346dsfde43ocf9840021bvd").'.'. $request->file('logo_path')->getClientOriginalExtension();
                    $request->file('logo_path')->storeAs('public/images/logos', $filename);
                    $filename = public_path('storage/public/images/logos/').$filename;
                    $school->logo_path = $filename;
                }
                $school->save();
                return back()->with('success', __('text.word_done'));
            } catch (\Throwable $th) {
                //throw $th;
                return back()->with('error', $th->getMessage());
            }
        }else {
            return back()->with('error', $validator->errors()->first());
        }
    }

    public function preview($id)
    {
        # code...
        $data['title'] = __('text.school_preview');
        $data['school'] = \App\Models\School::find($id);
        return view('admin.schools.preview', $data);
    }


}
