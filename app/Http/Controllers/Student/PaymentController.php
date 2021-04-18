<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Session;
class PaymentController extends Controller
{

    public function index($course_id, $year_id, $semester_id){
        $data['year_id'] = $year_id;
        $data['semester_id'] = $semester_id;
        $data['assignments'] = \App\Course::find($course_id)->assignment()->where(['materials.year_id'=>$year_id,'materials.semester_id'=>$semester_id])->get();
        $data['title'] = "Assignments for ".\App\Year::find($year_id)->name."  ".\App\Semesters::find($semester_id)->byLocale()->name;
        return view('student.assignment.index')->with($data);
    }


    public function view($id){
        $data['assignment'] = \App\Materials::find($id);
        return view('student.assignment.view')->with($data);
    }


}
