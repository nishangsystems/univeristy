<?php

namespace App\Http\Controllers\Student;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CourseController extends Controller
{
  
    public function index(){
        $year_id =  \App\Helpers\Helpers::instance()->getCurrentAccademicYear(\Auth::guard('student')->user()->studentInfo->level->name);
        $semester_id =  \App\Helpers\Helpers::instance()->getCurrentSemester(\Auth::guard('student')->user()->studentInfo->level->name);
        $data['year_id'] = $year_id;
        $data['semester_id'] = $semester_id;
        $data['credit'] = \Auth::guard('student')->user()->credit($year_id,$semester_id);
        $data['courses'] = \Auth::guard('student')->user()->courses($year_id,$semester_id);
        $data['title'] = "Courses for ".\App\Year::find($year_id)->name."  ".\App\Semesters::find($semester_id)->byLocale()->name;
        return view('student.course.index')->with($data);
    }


    public function coursesall(Request $request){
        $year_id = $request->year;
        $semester_id = $request->semester;
        $data['year_id'] = $year_id;
        $data['semester_id'] = $semester_id;
        $data['credit'] = \Auth::guard('student')->user()->credit($year_id,$semester_id);
        $data['courses'] = \Auth::guard('student')->user()->courses($year_id,$semester_id);
        $data['title'] = "Courses for ".\App\Year::find($year_id)->name."  ".\App\Semesters::find($semester_id)->byLocale()->name;
     
   //  echo ( $data['courses']->count());
      return view('student.course.index')->with($data);
    }

    public function edit(){
        $year_id =  \App\Helpers\Helpers::instance()->getCurrentAccademicYear(\Auth::guard('student')->user()->studentInfo->level->name);
        $semester_id =  \App\Helpers\Helpers::instance()->getCurrentSemester(\Auth::guard('student')->user()->studentInfo->level->name);
        $data['medicals'] =  \Auth::guard('student')->user()->medicalCompleted($year_id);
        $data['year_id'] = $year_id;
        $data['semester_id'] = $semester_id;
        $data['credit'] = \Auth::guard('student')->user()->credit($year_id,$semester_id);
        $data['courses'] = \Auth::guard('student')->user()->courses($year_id,$semester_id);
        $data['title'] = "Register Courses for ".\App\Year::find($year_id)->name."  ".\App\Semesters::find($semester_id)->byLocale()->name;
     
   //  echo ( $data['courses']->count());
      return view('student.course.add')->with($data);
    }


    public function __construct(){
        $this->middleware('auth:student');
    }
}
