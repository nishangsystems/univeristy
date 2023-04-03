<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Campus;
use App\Models\ProgramLevel;
use App\Models\Subjects;
use App\Models\TeachersSubject;
use App\Models\User;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    //

    public function init_teacher_attendance(Request $request)
    {
        # code...
        $data['title'] = "Teachers Attendance";
        return view('admin.attendance.init', $data);
    }

    public function teacher_subjects(Request $request){
        $matric = $request->matric;
        $c_year = Helpers::instance()->getCurrentAccademicYear();
        $subjects = ProgramLevel::join('teachers_subjects', ['teachers_subjects.class_id'=>'program_levels.id'])->where(['batch_id'=>$c_year])
                    ->join('users', ['users.id'=>'teachers_subjects.teacher_id'])->where(['users.matric'=>$matric])
                    // ->join('class_subjects', ['class_subjects.id'=>'teachers_subjects.subject_id'])
                    ->join('subjects', ['subjects.id'=>'teachers_subjects.subject_id'])
                    ->select(['subjects.*'])->distinct()->get();

        return response()->json(['subjects'=>$subjects]);
    }

    public function take_teacher_attendance(Request $request)
    {
        # code...
        $matric = $request->matric;
        $data['teacher'] = User::where(['matric'=>$matric])->first();
        $data['subject'] = Subjects::find($request->subject_id);
        $data['title'] = 'Take Attendance For <span class="text-primary">'.$data['teacher']->name.'</span> In <span class="text-primary">'.$data['subject']->name. ' [ '.$data['subject']->code.' ]</span>';
        $data['year'] = Helpers::instance()->getCurrentAccademicYear();
        $data['time'] = now()->format('Y-m-d H:m');
        $data['campus'] = Campus::find(auth()->user()->campus_id??0);
        $teacher_subject = TeachersSubject::where(['teacher_id'=>$data['teacher']->id, 'subject_id'=>$data['subject']->id, 'batch_id'=>$data['year'], 'campus_id'=>auth()->user()->campus_id??0])->first();
        $data['class'] = $teacher_subject->class??null;
        $data['record'] = Attendance::where(['year_id'=>$data['year'], 'campus_id'=>auth()->user()->campus_id??0, 'teacher_id'=>$data['teacher']->id, 'subject_id'=>$data['subject']->id])->orderBy('id', 'DESC')->get();
        return view('admin.attendance.record', $data);
    }

    public function save_teacher_attendance(Request $request)
    {
        # code...
        // return $request->all();
        $request->validate(['year_id'=>'required', 'campus_id'=>'required', 'teacher_id'=>'required', 'subject_id'=>'required', 'check_in'=>'required']);
        $data = ['year_id'=>$request->year_id, 'campus_id'=>$request->campus_id, 'teacher_id'=>$request->teacher_id, 'subject_id'=>$request->subject_id, 'check_in'=>$request->check_in];
        $instance = new Attendance($data);
        $instance->save();
        return back()->with('success', __('text.word_done'));
    }


    public function checkout_teacher(Request $request)
    {
        # code...
        $attendance = Attendance::find($request->attendance_id);
        $data['attendance'] = $attendance;
        $data['title'] = 'Check Out <span class="text-primary">'.$attendance->teacher->name.'</span> In <span class="text-primary">'.$attendance->subject->name.' [ '.$attendance->subject->code.' ] </span>';
        $data['time'] = now()->format('Y-m-d H:i');
        $data['record'] = Attendance::where(['year_id'=>$attendance->year_id, 'campus_id'=>$attendance->campus_id, 'teacher_id'=>$attendance->teacher_id, 'subject_id'=>$attendance->subject_id])->orderBy('id', 'DESC')->get();
        return view('admin.attendance.checkout', $data);
    }
    public function save_checkout_teacher(Request $request)
    {
        # code...
        // return $request->all();
        $request->validate(['check_out'=>'required']);
        $data = ['year_id'=>$request->year_id, 'campus_id'=>$request->campus_id, 'teacher_id'=>$request->teacher_id, 'subject_id'=>$request->subject_id, 'check_in'=>$request->check_in];
        $instance = Attendance::find($request->attendance_id);
        $instance->check_out = $request->check_out;
        $instance->save();
        return back()->with('success', __('text.word_done'));
    }
}
