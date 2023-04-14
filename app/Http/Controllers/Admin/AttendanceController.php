<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Campus;
use App\Models\ClassSubject;
use App\Models\ProgramLevel;
use App\Models\Subjects;
use App\Models\TeachersSubject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;

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
        $data['time'] = now()->format('d-m-Y H:i');
        $data['record'] = Attendance::where(['year_id'=>$attendance->year_id, 'campus_id'=>$attendance->campus_id, 'teacher_id'=>$attendance->teacher_id, 'subject_id'=>$attendance->subject_id])->orderBy('id', 'DESC')->get();
        return view('admin.attendance.checkout', $data);
    }
    public function save_checkout_teacher(Request $request)
    {
        # code...
        // return $request->all();
        $request->validate(['check_out'=>'required']);
        $instance = Attendance::where('id', $request->attendance_id)->update(['check_out'=>$request->check_out]);
        return back()->with('success', __('text.word_done'));
    }

    public function delete_teacher_attendance(Request $request)
    {
        # code...
        $instance = Attendance::find($request->attendance_id);
        // dd($instance);
        $route = route('admin.attendance.teacher.record', ['matric'=>$instance->teacher->matric, 'subject_id'=>$instance->subject_id]);
        $instance->delete();
        return redirect($route)->with('success', __('text.word_done'));
    }

    public function attendance_report(Request $request)
    {
        # code...
        $campus = $request->campus_id;
        $data['title'] = "Attendance Report For ".Campus::find(auth()->user()->campus_id)->name;
        if($request->month != null){
            $date = Date::parse($request->month);
            $year = Helpers::instance()->getCurrentAccademicYear();
            $data['title'] = "Attendance Report For ".date('F Y', strtotime($request->month)).' - '.Campus::find(auth()->user()->campus_id)->name;
            // Get all lecturers alongside the subjects they handle and their corresponding attendance records
            // Attendance uses subject_id from subjects table
            // 

            $data['report'] = ClassSubject::join('teachers_subjects', ['teachers_subjects.subject_id'=>'class_subjects.id'])->where(['teachers_subjects.batch_id'=>$year, 'teachers_subjects.campus_id'=>$campus])
                    ->join('users', ['users.id'=>'teachers_subjects.teacher_id'])->where('users.type', '=', 'teacher')
                    ->join('attendance', ['attendance.teacher_id'=>'teachers_subjects.teacher_id'])->where(['attendance.year_id'=>$year, 'attendance.campus_id'=>$campus])->whereNotNull('attendance.check_out')
                    ->whereMonth('check_in', $date)->whereYear('check_in', $date)
                    ->select(['users.id as teacher_id', 'users.name', 'attendance.*'])->distinct()->get()->groupBy(['teacher_id']);

            // $data['report'] = User::where(['users.type'=>'teacher'])
            //         ->join('teachers_subjects', ['teachers_subjects.teacher_id'=>'users.id'])->where(['teachers_subjects.batch_id'=>$year, 'teachers_subjects.campus_id'=>$campus])
            //         ->join('class_subjects', ['class_subjects.id'=>'teachers_subjects.subject_id'])
            //         ->join('attendance', ['attendance.teacher_id'=>'users.id'])->where(['attendance.year_id'=>$year, 'attendance.campus_id'=>$campus])->whereNotNull('attendance.check_out')
            //         ->whereMonth('check_in', $date)->whereYear('check_in', $date)
            //         ->select(['users.id as teacher_id', 'users.name', 'attendance.*'])->distinct()->get()->groupBy(['teacher_id']);

            return view('admin.attendance.report.general', $data);
            // dd($data['report']->toArray());
        }
        return view('admin.attendance.report.index', $data);
    }
}
