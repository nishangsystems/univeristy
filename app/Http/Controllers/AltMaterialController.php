<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\ClassMaster;
use App\Models\SchoolUnits;
use App\Models\Term;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Campus;
use App\Models\Material;
use App\Models\ProgramLevel;
use App\Models\StudentClass;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use \Session;

class AltMaterialController extends Controller
{

    public function index(Request $request)
    {
        // base64_encode() && base64_decode() will be used to handle query strings
        $data['title'] = ($request->has('type') ? "Departmental Material" : '')
                        .($request->has('program_level_id') ? "Material For ".ProgramLevel::find(request('program_level_id'))->program()->first()->name.' : Level '.ProgramLevel::find(request('program_level_id'))->level()->first()->level : '')
                        .($request->has('campus_id') ? ' : '.Campus::find(request('campus_id'))->name.' Campus' :'');
        return view('teacher.material.index', $data);
    }

    public function create()
    {
        # code...
        $data['title'] = 'Create Material For '.(request('type') != null ? auth()->user()->classes()->first()->name : '')
                        .(request('program_level_id') != null ? ProgramLevel::find(request('program_level_id'))->program()->first()->name.' : Level '.ProgramLevel::find(request('program_level_id'))->level()->first()->level : '')
                        .(request('campus_id') != null ? Campus::find(request('campus_id'))->name : '');;
        return view('teacher.material.create', $data);
    }

    public function save(Request $request)
    {
        // return $request->file('file')->getClientOriginalExtension();
        $validate = Validator::make($request->all(), [
            'title'=>'required',
            'visibility'=>'required',
            'file'=>'required|mimes:pdf,docx,odt,xls,xlsx,txt,ppt,jpg,jpeg,gif,png|max:2048',
        ]);

        if ($validate->fails()) {
            # code...
            return back()->with('error', $validate->errors()->first());
        }
        try {
            //code...
            // $path = '';
            // $extensions = ['pdf', 'docx', 'doc', 'txt', 'xls', 'xlsx', 'jpeg', 'jpg', 'png', 'gif', 'odt', 'ppt'];
            // if ($request->hasFile('file')) {
                # code...
                
                $extension = $request->file("file")->getClientOriginalExtension();
                $name = $request->file('file')->getClientOriginalName();
                $fname = time() . '.' . $extension;
                $request->file('file')->move('storage/material/', $fname);
                // $path = storage_path('material').'/'.$fname;
                // $request->file('file')->move('storage/SubjectNotes/', $path);

                    Material::create([
                        'title'=>$request->title,
                        'school_unit_id'=>$request->school_unit_id ?? null,
                        'file'=>$fname,
                        'visibility'=>$request->visibility,
                        'user_id'=>auth()->id(),
                        'level_id'=>$request->level_id ?? null,
                        'message'=>$request->message ?? ''
                    ]);
                    return redirect(route('material.index').'?'.(request('type') ? 'type='.request('type') : '').(request('program_level_id') ? 'program_level_id='.request('program_level_id') : '').(request('campus_id') ? 'campus_id='.request('campus_id') : ''))->with('success', 'Done');
                // }
                // else{
                //     return back()->with('error', 'Operation failed. Wrong file type.');
                // }
            // }
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('error', 'Operation failed. '.$th->getMessage());
        }


        # code...
    }

    public function edit($id)
    {
        # code...
        $data['item'] = Material::find($id);
        $data['title'] = 'Edit '.$data['item']->title;
        return view('teacher.material.edit', $data);
    }
    
    public function update(Request $request, $id)
    {
        # code...
        $request->validate([
            'title'=>'required',
            'date'=>'required',
            'visibility'=>'required|in:general,students,teachers,admins',
        ]);
        try {
            //code...
            $path = '';
            $extensions = ['pdf', 'docx', 'doc', 'txt', 'xls', 'xlsx', 'jpeg', 'jpg', 'png', 'gif'];
            if ($request->hasFile('file')) {
                # code...
                $file = $request->file('file');
                $ext = $file->getClientOriginalExtension();
                if (!in_array($ext, $extensions)) {
                    # code...
                    $file_name = '_'.rand(1000000000, 9999999999).'_'.date('Y_D_M_H_i_s', time()).'.'.$ext;
                    $file_path = public_path('files/material');
                    $file->move($file_path, $file_name);
                    $path .= $file_path.'/'.$file_name;


                    Material::create([
                        'title'=>$request->title,
                        'school_unit_id'=>$request->school_unit_id ?? null,
                        'file'=>$path,
                        'visibility'=>$request->visibility,
                        'user_id'=>auth()->id(),
                        'level_id'=>$request->level_id ?? null,
                        'message'=>$request->message ?? ''
                    ]);
                    return redirect(route('material.index').'?'.(request('type') ? 'type='.request('type') : '').(request('program_level_id') ? 'program_level_id='.request('program_level_id') : '').(request('campus_id') ? 'campus_id='.request('campus_id') : ''))->with('success', 'Done');
                }
                else{
                    return back()->with('error', 'Operation failed. Wrong file type.');
                }
            }
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('error', 'Operation failed. '.$th->getMessage());
        }
        try {
            //code...
            $not = Material::find($id);
            $not->fill($request->all());
            $not->save();
            return redirect(route('material.index').'?'.(request('type') ? 'type='.request('type') : '').(request('program_level_id') ? 'program_level_id='.request('program_level_id') : '').(request('campus_id') ? 'campus_id='.request('campus_id') : ''))->with('success', 'Done');
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('error', 'Operation failed '.$th->getMessage());
        }
    }
    
    public function show($id)
    {
        # code...
        $data['item'] = Material::find($id);
        $data['title'] = $data['item']->title;
        return view('teacher.material.edit', $data);
    }


    public function drop(Request $request, $id)
    {
        # code...
        $material = Material::find($id);
        unlink(storage_path('material'.'/'.$material->file));
        $material->delete();
        return back()->with('success', 'Done');
    }
}
