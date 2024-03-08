<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CodedResultsController extends Controller
{
    //

    public function index(Request $request)
    {
        # code...
        $data['title'] = "Manage Coded Results";
        if($request->action == 'all'){

        }
        return view('admin.result.coded.index', $data);
    }

    public function import_course_codes(Request $request)
    {
        # code...
        $data['title'] = "Import Course Exam Codes";
        return view('admin.result.coded.import_course_codes', $data);
    }

    public function save_course_codes(Request $request)
    {
        # code...
    }

    public function import_student_codes(Request $request)
    {
        # code...
        $data['title'] = "Import Student Exam Codes";
        return view('admin.result.coded.import_student_codes', $data);
    }

    public function save_student_codes(Request $request)
    {
        # code...
    }

    public function course(Request $request, $course_id)
    {
        # code...
        $course = \App\Models\Subjects::find($course_id);
        $year = \App\Models\Batch::find($this->current_accademic_year);
        $data['title'] = "Coding for {$course->name} [$course->code] | {$year->name}";
        $data['course'] = $course;
        $data['year'] = $year;
        return view('admin.result.coded.course', $data);
    }

    public function save_course_code(Request $request, $course_id)
    {
        # code...
    }

    public function import_results(Request $request)
    {
        # code...
        $data['title'] = "Import Exam [Coded Results]";
        return view('admin.result.coded.import_coded_exam', $data);
    }

    public function save_results(Request $request)
    {
        # code...
    }

    public function import_results_per_course(Request $request, $course_id)
    {
        # code...
        $course = \App\Models\Subjects::find($course_id);
        $year = \App\Models\Batch::find($this->current_accademic_year);
        $data['title'] = "Import Coded exam for {$course->name} [$course->code] | {$year->name}";
        $data['course'] = $course;
        $data['year'] = $year;
        return view('admin.result.coded.import_coded_exam', $data);
    }

    public function save_results_per_course(Request $request, $course_id)
    {
        # code...
    }
}
