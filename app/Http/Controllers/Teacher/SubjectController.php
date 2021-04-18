<?php

namespace App\Http\Controllers\Teacher;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use \Session;
class SubjectController extends Controller
{

    public function courses(){
        $year_id =  \App\Helpers\Helpers::instance()->getCurrentAccademicYear(Session::get('graduate'));
        $semester_id =  \App\Helpers\Helpers::instance()->getCurrentSemester(Session::get('graduate'));
        $data['year_id'] = $year_id;
        $data['semester_id'] = $semester_id;
        $data['courses'] = \Auth::user()->teacher->courses($year_id,$semester_id);
        $data['title'] = "Courses for ".\App\Year::find($year_id)->name."  ".\App\Semesters::find($semester_id)->byLocale()->name;
        return view('user.courses')->with($data);
    }

    public function student($course_id, $year_id, $semester_id){
        $data['type'] = 0;
        $data['year'] = $year_id;
        $data['course_id'] = $course_id;
        $data['semester'] = $semester_id;
        $data['students'] = \App\Course::find($course_id)->student($year_id,$semester_id)->get();
        if($data['students']->count() > 0){
            $data['title'] = "Student offering ".\App\Course::find($course_id)->byLocale()->title." in ".\App\Year::find($year_id)->name." | ".\App\Semesters::find($semester_id)->name;
        }else{
            $data['title'] = "No Student offered ".\App\Course::find($course_id)->byLocale()->title." in ".\App\Year::find($year_id)->name." | ".\App\Semesters::find($semester_id)->name;
        }
        return view('user.student')->with($data);
    }

    public function coursesall(Request $request){
        $year_id = $request->year;
        $semester_id = $request->semester;
        $data['year_id'] = $year_id;
        $data['semester_id'] = $semester_id;
        $data['courses'] = \Auth::user()->teacher->courses($year_id,$semester_id);
        $data['title'] = "Courses for ".\App\Year::find($year_id)->name."  ".\App\Semesters::find($semester_id)->byLocale()->name;

   //  echo ( $data['courses']->count());
      return view('user.courses')->with($data);
    }

    public function classList(Request $request){
        $data['students'] = null;
        $data['program_id'] =0;
        $data['level_id'] = 0;
        $data['title'] = "Select Department, Program and level to get Options List";
        return view('user.class_list')->with($data);
    }


    public function getClassList(Request $request){
        $data['program_id'] = $request->program;
        $data['level_id'] = $request->level;
        $data['students'] = \App\Students::select('*')
                            ->join('student_infos',['student_infos.id'=>'students.student_id'])
                            ->where(['student_infos.level_id'=>$request->level,'student_infos.program_id'=>$request->program])->orderBy('student_infos.firstname','ASC')->get();
        $data['title'] = "Student Under ".\App\Department::find($request->department)->name.",  ".\App\Options::find($request->program)->byLocale()->name." Level ".\App\Level::find($request->level)->name;
        return view('user.class_list')->with($data);
    }


}
