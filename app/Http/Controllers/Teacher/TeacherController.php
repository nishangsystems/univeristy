<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use \Session;
use \PDF;
use Illuminate\Http\Request;
class TeacherController extends Controller
{
    public function assigncourse(){
        $data['levels'] = (\Session::get('graduate')==200)?\App\Level::where('name','<=',500)->get():\App\Level::where('name','>',500)->get();
        return view('teacher.course.assigncourse')->with($data);
     }

     public function updatecourses(){
        return view('teacher.course.updatecourse');
    }

     public function contoassign(Request $request){
         $lid=$request->input('lid');
         $did=$request->input('dip');
         $sid=$request->input('sid');
        return view('teacher.course.contoassign')->withLid($lid)->withSid($sid)->withDid($did);
     }

     public function assigningsubj($pid,$lid,$sid){
         $data['level']=\App\Level::find($lid); 
         $data['program']=\App\Option::find($pid); 
         $data['semester']=\App\Semester::find($sid);
         $data['infos'] = \App\TeachersCourses::where(['level_id'=>$lid,'prog_id'=>$pid,'semester_id'=>$sid, 'year_id'=>\App\Helpers\Helpers::instance()->getCurrentAccademicYear(Session::get('graduate'))])->get();
         $data['courses']=\App\Course::where(['level_id'=>$lid,'programe_id'=>$pid,'sem'=>$sid])->get();
         return view('teacher.course.assigntolecturer')->with($data);
     }

     public function saveassign(Request $request){
        $this->validate($request, [
            'tid' => 'required',
            'cid' => 'required',
        ]);
         $ayear=\App\Year::find(\App\Helpers\Helpers::instance()->getCurrentAccademicYear(\Session::get('graduate')))->id ;
         if(\App\TeachersCourses::WHERE('year_id',$ayear)->WHERE('semester_id',$request->input('sid'))   
         ->WHERE('course_id',$request->input('cid'))->WHERE('teacher_id',$request->input('tid'))
         ->count()>0
     ){
     Session::flash('error','Records already Exists');
        return redirect()->back()->with('e','Records Already Exists');
     }
     else{
        $teach = new \App\TeachersCourses();
        $teach->teacher_id=$request->input('tid');
        $teach->course_id=$request->input('cid');
        $teach->semester_id=$request->input('sid');
        $teach->year_id=$ayear;
        $teach->prog_id=$request->input('pid');    
        $teach->level_id=$request->input('lid');  
        $teach->save();
        
        Session::flash('s','Course successfully assigned to lecturer');
        return redirect()->back();
     }
     }


     public function deletecourse(Request $request){
        $this->validate($request, [
            'id' => 'required',
        ]);
        $teach =\App\TeachersCourses::find($request->id); 
        $teach->delete();
        Session::flash('s','Course successfully assigned to lecturer');
        return redirect()->back();
     }
}
