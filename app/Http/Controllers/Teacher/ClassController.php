<?php

namespace App\Http\Controllers\Teacher;

use App\Helpers\Helpers;
use App\Models\Batch;
use App\Models\ClassMaster;
use App\Models\SchoolUnits;
use App\Models\Students;
use App\Models\Term;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\DailyAttendance;
use App\Models\ProgramLevel;
use App\Models\StudentAttendance;
use App\Models\StudentClass;
use App\Models\TeachersSubject;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use \Session;

class ClassController extends Controller
{

    public function program_courses()
    {
        # code...
        $data['title'] = 'Courses For '.SchoolUnits::find(ClassMaster::where('user_id', '=', auth()->id)->first()->department_id)->name;
        // $data['courses'] = 
    }

    public function program_levels_list(Request $request, $department_id)
    {
        # code...
        if(!$request->has('id')){
            $data['title'] = "Classes ".($request->campus_id != null ? ' Under '.\App\Models\Campus::find($request->campus_id)->first()->name : '').' - '.SchoolUnits::find($department_id)->name;
        }else{
            $data['title'] = "Class List ".($request->campus_id != null ? ' For '.\App\Models\Campus::find($request->campus_id)->first()->name : '').' - '.ProgramLevel::find($request->id)->name();
        }
        return view('teacher.class_list', $data);
    }

    public function index()
    {
        $data['options'] = \App\Http\Controllers\Admin\StudentController::baseClasses();
        if (\request('type') == 'master') {
            $data['classes'] = ClassMaster::where('batch_id', \App\Helpers\Helpers::instance()->getCurrentAccademicYear())->where('user_id', Auth::user()->id)->get();
            // dd($data);
            $data['title'] = "Your Departments";
            return view('teacher.class_master')->with($data);
        } else {
            $data['units']  = \App\Models\ProgramLevel::join('teachers_subjects', ['teachers_subjects.class_id'=>'program_levels.id'])
                                ->where(['teachers_subjects.teacher_id'=>auth()->id()])
                                ->distinct()
                                ->select(['program_levels.*', 'teachers_subjects.campus_id'])
                                ->get();
            return view('teacher.class')->with($data);
        }
    }

    public function reportCard($class, $t,  $student_id)
    {
        $class = SchoolUnits::find($class);
        $data['class'] = $class;
        $data['term'] = Term::find($t);
        $year = Batch::find(\App\Helpers\Helpers::instance()->getYear());
        $data['year'] = $year;
        $data['subjects'] = \App\Models\Students::find($student_id)->class(\App\Helpers\Helpers::instance()->getYear())->subjects;
        $data['user'] = \App\Models\Students::find($student_id);
        return view('teacher.report_card')->with($data);
    }

    public function students($class_id)
    {
        $class = ProgramLevel::find($class_id);
        $data['class'] = $class;

        // $data['students'] = $class->students(\Session::get('mode', \App\Helpers\Helpers::instance()->getCurrentAccademicYear()))->paginate(15);
        $data['students'] = StudentClass::where('class_id', '=', $class_id)
                ->where('year_id', '=', \App\Helpers\Helpers::instance()->getCurrentAccademicYear())
                ->join('students', ['students.id'=>'student_classes.student_id'])
                ->where(function($q){
                    request()->has('campus') ? $q->where(['students.campus_id'=>request('campus')]) : null;
                })
                ->orderBy('students.name', 'ASC')->get('students.*');
        return view('teacher.student')->with($data);
    }

    public function master_sheet(Request $request)
    {
        # code...
        return view('teacher.master_sheet');
    }

    public function student($student_id)
    {
        $data['user'] = \App\Models\Students::find($student_id);
        return view('teacher.student_detail')->with($data);
    }

    public function classes()
    {
        $data['year'] = \App\Helpers\Helpers::instance()->getCurrentAccademicYear();
        $data['classes'] = ClassMaster::where('batch_id', \App\Helpers\Helpers::instance()->getCurrentAccademicYear())->where('user_id', Auth::user()->id)->get();
        return view('teacher.classmaster')->with($data);
    }

    public function rank($class_id)
    {
        $class = ClassMaster::find($class_id)->class;
        $data['class'] = $class;
        $data['year'] = \App\Helpers\Helpers::instance()->getCurrentAccademicYear();
        $data['students'] = $class->students(\Session::get('mode', \App\Helpers\Helpers::instance()->getCurrentAccademicYear()))->paginate(15);
        return view('teacher.rank')->with($data);
    }

    public function attendannce_index(Request $request)
    {
        $data['title'] = __('text.take_course_attendance');
        $data['courses'] = \App\Models\TeachersSubject::where([
            'teacher_id' => auth()->id(),
            'batch_id' => \App\Helpers\Helpers::instance()->getCurrentAccademicYear(),
        ])->join('subjects', ['subjects.id'=>'teachers_subjects.subject_id'])
        ->distinct()->select('subjects.*', 'teachers_subjects.class_id as class', 'teachers_subjects.campus_id', 'teachers_subjects.id as teacher_subject_id')->get();
        return view('teacher.course.attendance_index', $data);
    }

    public function setup_attendance_course(Request $request, $course_id) //$course_id refers to teacher courses
    {
        $data['title'] = __('text.confirm_attendance');
        $data['teacher_subject'] = TeachersSubject::find($course_id);
        return view('teacher.course.attendance_setup', $data);
    }

    public function record_attendance(Request $request, $teacher_subject_id)
    {
        $teacher_subject = TeachersSubject::find($teacher_subject_id);
        $data['course'] = $teacher_subject->subject;
        $data['title'] = __('text.record_attendance_for', ['title'=>$data['course']->name, 'code'=>$data['course']->code]).' - '.now()->format(DATE_RFC850);
        if($teacher_subject != null){
            $da = DailyAttendance::where(['teacher_id'=>$teacher_subject->teacher_id, 'course_id'=>$teacher_subject->subject_id, 'year'=>Helpers::instance()->getCurrentAccademicYear()])->whereTime('created_at', ' > ', now()->addHours(-3)->format(DATE_ATOM))->first();
            if($da == null){
                $dA = new DailyAttendance();
                $dA->year = Helpers::instance()->getCurrentAccademicYear();
                $dA->course_id = $teacher_subject->subject_id;
                $dA->teacher_id = $teacher_subject->teacher_id;
                $dA->save();
                $data['attendance_id'] = $dA->id;
                $data['students'] = $dA->attedance()->join('students', 'students.id', '=', 'student_attendance.student_id')->orderBy('id', 'DESC')->distinct()->get(['student_attendance.id', 'students.name', 'students.matric']);
            }else{
                $data['attendance_id'] = $da->id;
                $data['students'] = $da->attedance()->join('students', 'students.id', '=', 'student_attendance.student_id')->orderBy('id', 'DESC')->distinct()->get(['student_attendance.id', 'students.name', 'students.matric']);
            }
            return view('teacher.course.record_attendance', $data);
        }
    }

    public function record_attendance_save(Request $request, $attendance_id)
    {
        try {
            //code...
            // return $attendance_id;
            $matric = $request->matric;
            $course_id = $request->course_id;
            $attendance = DailyAttendance::find($attendance_id);
            // return $attendance;
            if($matric != null){
                $student = Students::where('matric', $matric)->first();
                if(StudentAttendance::where(['student_id'=> $student->id, 'attendance'=>$attendance_id, 'year'=>Helpers::instance()->getCurrentAccademicYear()])->count() > 0){ goto _RETURN;}
                $att = new StudentAttendance();
                
                $att->year = Helpers::instance()->getCurrentAccademicYear();
                $att->student_id = $student->id??null;
                $att->course_id = $course_id;
                $att->attendance = $attendance_id;
                $att->teacher_id = auth()->id();
                $att->save();
                // return 1;
                
                _RETURN:
                $students = $attendance->attedance()->join('students', 'students.id', '=', 'student_attendance.student_id')->orderBy('id', 'DESC')->distinct()->get(['student_attendance.id', 'students.name', 'students.matric']);
                return response()->json(['students'=>$students]);
            }
            return response()->setStatusCode(500);
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public function drop_student_attendance(Request $request, $sa_id)
    {
        # code...
        try{
            $student_attendance = StudentAttendance::find($sa_id);
            if($student_attendance  != null){
                $da = DailyAttendance::find($student_attendance->attendance);
                $student_attendance->delete();
                $students = $da->attedance()->join('students', 'students.id', '=', 'student_attendance.student_id')->orderBy('id', 'DESC')->distinct()->get(['student_attendance.id', 'students.name', 'students.matric']);
                return response()->json(['students'=>$students]);
            }
        }catch(\Throwable $th){
            return $th->getMessage;
        }
    }

}
