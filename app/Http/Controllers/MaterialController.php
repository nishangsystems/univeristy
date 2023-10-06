<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\ClassMaster;
use App\Models\SchoolUnits;
use App\Models\Term;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Campus;
use App\Models\Level;
use App\Models\Material;
use App\Models\ProgramLevel;
use App\Models\StudentClass;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use \Session;

class MaterialController extends Controller
{

    public function index(Request $request, $layer, $layer_id, $campus_id=0 )
    {
        // get the layer type and id from the request
        // layer types: ['S=>SCHOOL', 'F=>FACULTY', 'D=>DEPARTMENT', 'P=>PROGRAM', 'L=>LEVEL', 'C=>CLASS']
        
        $material = Material::where(function($q) use($campus_id){
            $campus_id == 0 ? null : $q->where('campus_id', $campus_id);
        })->get();
        $campus = $campus_id == 0 ? null : Campus::find($campus_id)->name??null;
        $data = [];
        switch ($layer) {
            case 'S': case 'SCHOOL':
                # code...
                $data['material'] = $material->where('unit_id',1);
                $data['title'] = $data['title'] = "General Material ".$campus??'';
                break;
            
            case 'F': case 'FACULTY':
                # code...
                break;
            
            case 'D': case 'DEPARTMENT':
                # code...
                // if the user is a class master
                $data['title'] = "Departmental Material For ".(SchoolUnits::find($layer_id)->name??null).' '.$campus??null;
                $department_ids = ClassMaster::where(['user_id'=>auth()->id()])->pluck('department_id')->toArray();
                
                if (in_array($layer_id, $department_ids)) {
                    # code...
                    $data['material'] = $material->where('unit_id', 3)->where('school_unit_id', request('layer_id'));

                }elseif(User::where('id', auth()->id())->where('type', '!=', 'teacher')->count() > 0) {
                    $data['notifications'] = $material->where('user_id', auth()->id())->where('school_unit_id', $layer_id);
                }else {
                    $data['material'] = $material->empty();
                }
                
                break;
            
            case 'P': case 'PROGRAM':
                # code...
                // if the user is a class master
                $data['title'] = "Program Material For ".(SchoolUnits::find($layer_id)->name??'').' '.$campus??null;
                $department_ids = ClassMaster::where(['user_id'=>auth()->id()])->pluck('department_id')->toArray();
                $program_ids = SchoolUnits::where(['unit_id'=>4])->whereIn('parent_id', $department_ids)->pluck('id');
                if (in_array($layer_id, $program_ids)) {
                    # code...
                    $data['material'] = $material->where('unit_id',4)->where('school_unit_it',$layer_id);
                }elseif(User::where('id', auth()->id())->where('type', '!=', 'teacher')->count() > 0) {
                    $data['notifications'] = $material->where('user_id', auth()->id())->where('school_unit_id', $layer_id);
                } else {
                    # code...
                    $data['material'] = $material->empty();
                }
                
                break;
            
            case 'L': case 'LEVEL':
                # code...
                // if user is class master
                $data['title'] = "Level ".Level::find($layer_id)->level.' Material '.$campus??null;
                $department_ids = ClassMaster::where(['user_id'=>auth()->id()])->pluck('department_id')->toArray();
                $program_ids = SchoolUnits::where(['unit_id'=>4])->whereIn('parent_id', $department_ids)->pluck('id');
                $level_ids = Level::join('program_levels', ['program_levels.level_id'=>'levels.id'])
                            ->join('school_units', ['school_units.id'=>'program_levels.program_id'])
                            ->where(['school_units.uint_id'=>4])
                            ->whereIn('school_units.id', $program_ids)
                            ->distinct()->pluck('levels.id')->toArray();

                if (in_array($layer_id, $level_ids)) {
                    # code...
                    $data['material'] = $material->where('level_id', $layer_id);
                }elseif(User::where('id', auth()->id())->where('type', '!=', 'teacher')->count() > 0) {
                    $data['notifications'] = $material->where('user_id', auth()->id())->where('level_id', $layer_id);
                }else {
                    # code...
                    $data['material'] = $material->empty();
                }
                break;
            
            case 'C': case 'CLASS':
                # code...
                // for a class master
                $class = ProgramLevel::find(request('layer_id'));
                $data['title'] = "Class Material For ".$class->name().' '.$campus??null;
                if(ClassMaster::where(['user_id'=>auth()->id()])->count() > 0){
                    // return 777;
                    $department_ids = ClassMaster::where(['user_id'=>auth()->id()])->pluck('department_id')->toArray();
                    $class_ids = SchoolUnits::where(['unit_id'=>4])->whereIn('parent_id', $department_ids)
                                ->join('program_levels', ['program_levels.program_id'=>'school_units.id'])
                                ->pluck('program_levels.id')->toArray();
    
                    if(in_array($layer_id, $class_ids)){
                        $data['material'] = $material->where('school_unit_id',$class->program_id)->where('level_id',$class->level_id);
                    }else {
                        $data['material'] = $material->empty();
                    }
                }elseif(User::where('id', auth()->id())->where('type', '!=', 'teacher')->count() > 0) {
                    $data['notifications'] = $material->where('user_id', auth()->id())->where('school_unit_id', $class->program_id)->where('level_id', $class->level_id);
                }
                else {
                    // For a normal teacher to view class material
                    $classes  = \App\Models\TeachersSubject::where(['teacher_id'=>auth()->id()])
                                ->pluck('class_id')->toArray();
                    if (in_array($layer_id, $classes)) {
                        # code...
                        $data['material'] = $material->where('school_unit_id',$class->program_id)->where('level_id', $class->level_id);
                    } else {
                        # code...
                        $data['material'] = $material->empty();
                    }
                    
                }
                break;
            
            default:
                # code...
                break;
        }
        // $data['material'] = $data['material']->where(function($c){
        //     $c->where('user_id', auth()->id())
        //         ->where('visibility', 'teacher'||'general');
        // });
        // $data['title'] = ($request->has('type') ? "Departmental Material For ".SchoolUnits::find($request->_d)->name ?? '' : '')
        //                 .($request->has('program_level_id') ? "Material For ".ProgramLevel::find(request('program_level_id'))->program()->first()->name.' : Level '.ProgramLevel::find(request('program_level_id'))->level()->first()->level : '')
        //                 .($request->has('campus_id') ? ' : '.Campus::find(request('campus_id'))->name.' Campus' :'');
        return view('teacher.material.index', $data);
    }

    public function create(Request $request, $layer, $layer_id, $campus_id=0 )
    {
        # code...
        $campus = $campus_id == 0 ? null : Campus::find($campus_id)->name??null;
        
        switch ($layer) {
            case 'S':
                # code...
                $data['title'] = "Create General Material ".$campus??'';
                break;
            
            case 'F':
                # code...
                break;
            
            case 'D':
                # code...
                // if the user is a class master
                $data['title'] = "Create Departmental Material For ".(SchoolUnits::find($layer_id)->name??null).' '.$campus??null;
                break;
            
            case 'P':
                # code...
                // if the user is a class master
                $data['title'] = "Create Program Material For ".(SchoolUnits::find($layer_id)->name??'').' '.$campus??null;
                break;
            
            case 'L':
                # code...
                // if user is class master
                $data['title'] = "Create Level ".Level::find($layer_id)->level.' Material '.$campus??null;
                break;
            
            case 'C':
                # code...
                // for a class master
                $class = ProgramLevel::find(request('layer_id'));
                $data['title'] = "Create Class Material For ".$class->name().' '.$campus??null;
                break;
            
            default:
                # code...
                break;
        }
        // $data['title'] = (request('type') != null ? auth()->user()->classes()->first()->name : '')
        //                 .(request('program_level_id') != null ? ProgramLevel::find(request('program_level_id'))->program()->first()->name.' : Level '.ProgramLevel::find(request('program_level_id'))->level()->first()->level : '')
        //                 .(request('campus_id') != null ? Campus::find(request('campus_id'))->name : '');
        return view('teacher.material.create', $data);
    }

    public function save(Request $request, $layer, $layer_id, $campus_id = null)
    {
        # code...
        $request->validate([
            'title'=>'required',
            'visibility'=>'required|in:general,students,teachers,admins',
            'file'=>'required|max:2048|mimes:pdf,docx,odt,xls,xlsx,txt,ppt,jpg,jpeg,gif,png'
        ]);

        try {

            // store file and get name
            $extension = $request->file("file")->getClientOriginalExtension();
            $name = $request->file('file')->getClientOriginalName();
            $fname = time() . '.' . $extension;
            $request->file('file')->move('storage/material/', $fname);
            // basic request data
            $data = [
                'title'=>$request->title,
                'file'=>$fname,
                'visibility'=>$request->visibility,
                'user_id'=>auth()->id(),
                'level_id'=>$request->level_id ?? null,
                'message'=>$request->message ?? ''
            ];
            

            // additional request data
            $data['campus_id'] = $campus_id;
            $data['user_id'] = auth()->id();
            switch ($layer) {
                case 'S':
                    # code...
                    $data['unit_id'] = 1;
                    $data['school_unit_id'] = $layer_id;
                    break;
                case 'F':
                    # code...
                    $data['unit_id'] = 2;
                    $data['school_unit_id'] = $layer_id;
                    break;
                case 'D':
                    # code...
                    $data['unit_id'] = 3;
                    $data['school_unit_id'] = $layer_id;
                    break;
                case 'P':
                    # code...
                    $data['unit_id'] = 4;
                    $data['school_unit_id'] = $layer_id;
                    break;
                case 'C':
                    # code...
                    $class = ProgramLevel::find($layer_id);
                    $data['unit_id'] = 4;
                    $data['school_unit_id'] = $class->program_id;
                    $data['level_id'] = $class->level_id;
                    break;
                case 'L':
                    # code...
                    $data['unit_id'] = 4;
                    $data['level_id'] = $layer_id;
                    break;
                
                default:
                    # code...
                    return back()->with('error', 'Unknown material type.');
                    break;
            }
            Material::create($data);
            return redirect(route('material.index', [$layer, $layer_id, $campus_id]))->with('success', 'Done');
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('error', 'Operation failed '.$th->getMessage());
        }
    }
    
    public function edit($layer, $layer_id, $campus_id, $id)
    {
        # code...
        $data['item'] = Material::find($id);
        $data['title'] = 'Edit '.$data['item']->title;
        return view('teacher.material.edit', $data);
    }
    
    public function update(Request $request, $layer, $layer_id, $campus_id, $id)
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
                    return redirect(route('material.index', [$layer, $layer_id, $campus_id]))->with('success', 'Done');
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
    
    public function show($layer, $layer_id, $campus_id, $id)
    {
        # code...
        $data['item'] = Material::find($id);
        $data['title'] = $data['item']->title;
        return view('teacher.material.edit', $data);
    }

    public function drop(Request $request, $layer, $layer_id, $campus_id, $id)
    {
        # code...
        $material = Material::find($id);
        unlink(storage_path('material'.'/'.$material->file));
        $material->delete();
        return back()->with('success', 'Done');
    }
}
