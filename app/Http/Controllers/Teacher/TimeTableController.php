<?php

namespace App\Http\Controllers\Teacher;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;

class TimeTableController extends Controller
{
  
    public function exam($option){
        $data['option_id'] = $option;
        $data['student'] = $request->user();
        $data['year'] = \App\Helpers\Helpers::instance()->getCurrentAccademicYear(Session::get('graduate'));
        $data['semester'] =  \App\Helpers\Helpers::instance()->getCurrentSemester(Session::get('graduate'));
        $data['program'] =\App\TeachersCourses::select('options.id','options.name')->where('teachers_courses.teacher_id',\Auth::user()->teacher->id)
                                ->where(['teachers_courses.year_id'=>$data['year'],'teachers_courses.semester_id'=>$data['semester']])
                                ->join('courses',['courses.id'=>'teachers_courses.course_id'])
                                ->join('options',['options.id'=>'courses.programe_id'])->distinct('options.id')->get();

        $data['periods'] = \App\Period::select('periods.id','periods.starts','periods.ends')->join('timetbls',['periods.id'=>'timetbls.periods_id'])
                                    ->where(['timetbls.ayear'=>$data['year'],'timetbls.sem'=>$data['semester']])
                                    ->where('timetbls.timetbl_type_id',2)
                                    ->where('timetbls.options_id',$option)
                                    ->distinct('periods.id')->get();               
        return view('teacher.timetable.exam')->with($data);
    }

    public function class($option){
        $data['option_id'] = $option;
        $data['student'] = $request->user();
        $data['year'] = \App\Helpers\Helpers::instance()->getCurrentAccademicYear(Session::get('graduate'));
        $data['semester'] =  \App\Helpers\Helpers::instance()->getCurrentSemester(Session::get('graduate'));
        $data['periods'] = \App\Period::select('periods.id','periods.starts','periods.ends')->join('timetbls',['periods.id'=>'timetbls.periods_id'])
                                ->where(['timetbls.ayear'=>$data['year'],'timetbls.sem'=>$data['semester']])
                                ->where('timetbls.timetbl_type_id',1)
                                ->where('timetbls.options_id',$option)
                                ->distinct('periods.id')->get();
        
        $data['program'] =\App\TeachersCourses::select('options.id','options.name')->where('teachers_courses.teacher_id',\Auth::user()->teacher->id)
                                ->where(['teachers_courses.year_id'=>$data['year'],'teachers_courses.semester_id'=>$data['semester']])
                                ->join('courses',['courses.id'=>'teachers_courses.course_id'])
                                ->join('options',['options.id'=>'courses.programe_id'])->distinct('options.id')->get();
                        
        //cho json_encode($data);                        
        return view('teacher.timetable.class')->with($data);
    }

    public function postexam(Request $request){
        $option = $request->option;
        $data['student'] = $request->user();
        $data['option_id'] = $option;
        $data['year'] = \App\Helpers\Helpers::instance()->getCurrentAccademicYear(Session::get('graduate'));
        $data['semester'] =  \App\Helpers\Helpers::instance()->getCurrentSemester(Session::get('graduate'));
        $data['program'] =\App\TeachersCourses::select('options.id','options.name')->where('teachers_courses.teacher_id',\Auth::user()->teacher->id)
                                ->where(['teachers_courses.year_id'=>$data['year'],'teachers_courses.semester_id'=>$data['semester']])
                                ->join('courses',['courses.id'=>'teachers_courses.course_id'])
                                ->join('options',['options.id'=>'courses.programe_id'])->distinct('options.id')->get();

         $data['periods'] = \App\Period::select('periods.id','periods.starts','periods.ends')->join('timetbls',['periods.id'=>'timetbls.periods_id'])
                                ->where(['timetbls.ayear'=>$data['year'],'timetbls.sem'=>$data['semester']])
                                ->where('timetbls.timetbl_type_id',2)
                                ->where('timetbls.options_id',$option)
                                ->distinct('periods.id')->get();               
        return view('teacher.timetable.exam')->with($data);
    }

    public function postclass(Request $request){
        $option = $request->option;
        $data['student'] = $request->user();
        $data['option_id'] = $option;
        $data['year'] = \App\Helpers\Helpers::instance()->getCurrentAccademicYear(Session::get('graduate'));
        $data['semester'] =  \App\Helpers\Helpers::instance()->getCurrentSemester(Session::get('graduate'));
        $data['periods'] = \App\Period::select('periods.id','periods.starts','periods.ends')->join('timetbls',['periods.id'=>'timetbls.periods_id'])
                                ->where(['timetbls.ayear'=>$data['year'],'timetbls.sem'=>$data['semester']])
                                ->where('timetbls.timetbl_type_id',1)
                                ->where('timetbls.options_id',$option)
                                ->distinct('periods.id')->get();
        
        $data['program'] =\App\TeachersCourses::select('options.id','options.name')->where('teachers_courses.teacher_id',\Auth::user()->teacher->id)
                                ->where(['teachers_courses.year_id'=>$data['year'],'teachers_courses.semester_id'=>$data['semester']])
                                ->join('courses',['courses.id'=>'teachers_courses.course_id'])
                                ->join('options',['options.id'=>'courses.programe_id'])->distinct('options.id')->get();
                        
                                
        return view('teacher.timetable.class')->with($data);
    }
}