<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DepartmentalCourseController extends Controller
{
    //

    public function index()
    {
        # code...
        $data['title'] = "Departments";
        $data['departments'] = \App\Models\SchoolUnits::where('unit_id', 3)->orderBy('name')->get();
        return view('admin.subject.dep_courses.index', $data);
    }

    public function courses(Request $request, $dep_id)
    {
        # code...
        $department = \App\Models\SchoolUnits::find($dep_id);
        $data['title'] = "Departmental Courses For {$department->name}";
        $data['department'] = $department;
        $data['courses'] = $department->departmentalCourses;
        return view('admin.subject.dep_courses.courses', $data);
    }

    public function create(Request $request, $dep_id)
    {
        # code...
        $dep = \App\Models\SchoolUnits::find($dep_id);
        $data['title'] = "Create Departmental Course For {$dep->name}";
        $data['department'] = $dep;
        return view('admin.subject.dep_courses.create', $data);
    }

    public function store(Request $request, $dep_id)
    {
        # code...
        try {
            //code...
            $validity = Validator::make($request->all(), ['school_unit_id'=>'required', 'subject_id'=>'required']);
            if($validity->fails()){
                session()->flash('error', $validity->errors()->first());
                return back()->withInput();
            }
            $data = $request->all();
            $instance = new \App\Models\DepartmentalCourse($data);
            $instance->save();
            return back()->with('success', 'Done');
        } catch (\Throwable $th) {
            //throw $th;
            session()->flash('error', $th->getMessage());
            return back()->withInput();
        }
    }

    public function drop($dep_course_id)
    {
        # code...
        $dep_course = \App\Models\DepartmentalCourse::find($dep_course_id);
        if($dep_course != null){
            $dep_course->delete();
            return back()->with('success', 'Done');
        }
        return back()->with('error', "Operation failed. Could not found");
    }
}
