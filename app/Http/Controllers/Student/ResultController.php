<?php

namespace App\Http\Controllers\Student;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;
class ResultController extends Controller
{

    public function index(){
        $year_id =  \App\Helpers\Helpers::instance()->getCurrentAccademicYear(\Auth::guard('student')->user()->studentInfo->level->name);
        $semester_id =  \App\Helpers\Helpers::instance()->getCurrentSemester(\Auth::guard('student')->user()->studentInfo->level->name);
        $data['year_id'] = $year_id;
        $data['semester_id'] = $semester_id;
        if(\Auth::guard('student')->user()->studentInfo->options->department_id == 9){
            $data['results'] = \Auth::guard('student')->user()->p_result($year_id,$semester_id);
            $data['title'] = "results for ".\App\Year::find($year_id)->name."  ".\App\Semesters::find($semester_id)->byLocale()->name;
            return view('student.result.p_index')->with($data);
        }else{
            $data['results'] = \Auth::guard('student')->user()->result($year_id,$semester_id);
            $data['earned'] = 200;
            $data['maximum'] = \Auth::guard('student')->user()->studentInfo->options->max_credit;
            $data['gpa'] = 3.5;
            $data['title'] = "results for ".\App\Year::find($year_id)->name."  ".\App\Semesters::find($semester_id)->byLocale()->name;
            return view('student.result.index')->with($data);
        }
    }

    public function resultsall(Request $request){
        $year_id = $request->year;
        $semester_id = $request->semester;
        $data['year_id'] = $year_id;
        $data['semester_id'] = $semester_id;
        if(\Auth::guard('student')->user()->studentInfo->options->department_id == 9){
            $data['results'] = \Auth::guard('student')->user()->p_result($year_id,$semester_id);
            $data['title'] = "results for ".\App\Year::find($year_id)->name."  ".\App\Semesters::find($semester_id)->byLocale()->name;
            return view('student.result.p_index')->with($data);
        }else{
            $data['results'] = \Auth::guard('student')->user()->result($year_id,$semester_id);
            $data['title'] = "results for ".\App\Year::find($year_id)->name."  ".\App\Semesters::find($semester_id)->byLocale()->name;
            return view('student.result.index')->with($data);
        }
    }

    public function cas(){
        $year_id =  \App\Helpers\Helpers::instance()->getCurrentAccademicYear(\Auth::guard('student')->user()->studentInfo->level->name);
        $semester_id =  \App\Helpers\Helpers::instance()->getCurrentSemester(\Auth::guard('student')->user()->studentInfo->level->name);
        $data['year_id'] = $year_id;
        $data['semester_id'] = $semester_id;
        $data['results'] = \Auth::guard('student')->user()->result($year_id,$semester_id);
        $data['title'] = "results for ".\App\Year::find($year_id)->name."  ".\App\Semesters::find($semester_id)->byLocale()->name;
       return view('student.result.ca')->with($data);
        //echo json_encode($data['results']);
    }

    public function casall(Request $request){
        $year_id = $request->year;
        $semester_id = $request->semester;
        $data['year_id'] = $year_id;
        $data['semester_id'] = $semester_id;
         $data['results'] = \Auth::guard('student')->user()->result($year_id,$semester_id);
        $data['title'] = "results for ".\App\Year::find($year_id)->name."  ".\App\Semesters::find($semester_id)->byLocale()->name;
        return view('student.result.ca')->with($data);
    }

    public function __construct(){
        $this->middleware('auth:student');
    }
}
