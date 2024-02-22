<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Subjects;
use App\Services\ClassDelegateService;
use Illuminate\Http\Request;

class ClassDelegateController extends Controller
{

    protected $classDelegateService;
    //
    public function __construct(ClassDelegateService $classDelegateService)
    {
        # code...
        $this->classDelegateService = $classDelegateService;
    }

    public function index()
    {
        # code...
        $student = auth('student')->user();
        if($student != null){
            $data['courses'] = $this->classDelegateService->getMyCourses($student->id);
            $data['title'] = "Record Lecturer Attendance";
            return view('student.delegate.index', $data);
        }
    }

    public function check_in(Request $request, $teacher_subject_id)
    {
        # code...
        $teacher_subject = \App\Models\TeachersSubject::find($teacher_subject_id);
        if($teacher_subject != null){
            $data['subject'] = $teacher_subject->subject;
            $data['teacher'] = $teacher_subject->user;
            $data['year'] = $teacher_subject->batch_id;
            $data['campus'] = $teacher_subject->campus;
            $data['teacher_subject'] = $teacher_subject;
            $data['record'] = \App\Models\Attendance::where(['year_id'=>$teacher_subject->batch_id, 'campus_id'=>$teacher_subject->campus_id, 'teacher_id'=>$teacher_subject->teacher_id, 'subject_id'=>$teacher_subject->subject_id])->orderBy('id', 'DESC')->get();
            $data['title'] = "Checkin {$data['teacher']->name} For {$data['subject']->name}";
            // dd($data);
            return view('student.delegate.check_in', $data);
        }
        return back()->with('error', 'Error occured reading data');
    }

    public function check_in_save(Request $request)
    {
        # code...
        try {
            // dd($request->all());
            $this->classDelegateService->check_in($request->all());
            return back()->with('success', 'Done');
        } catch (\Throwable $th) {
            //throw $th;
            session()->flash('error', $th->getMessage());
            return back()->withInput();
        }
    }

    public function check_out(Request $request, $attendance_id)
    {
        # code...
        $attendance = \App\Models\Attendance::find($attendance_id);
        if($attendance != null){
            $data['attendance'] = $attendance;
            $data['title'] = "Checkout {$attendance->teacher->name} For {$attendance->subject->name}|{$attendance->campus->name}";
            $data['record'] = \App\Models\Attendance::where(['year_id'=>$attendance->year_id, 'campus_id'=>$attendance->campus_id, 'teacher_id'=>$attendance->teacher_id, 'subject_id'=>$attendance->subject_id])->orderBy('id', 'DESC')->get();
            return view('student.delegate.check_out', $data);
        }
    }

    public function check_out_save(Request $request, $attendance_id)
    {
        # code...
        try {
            $this->classDelegateService->check_out($attendance_id, $request->check_out);
            return back()->with('success', 'Done');
        } catch (\Throwable $th) {
            //throw $th;
            session()->flash('error', $th->getMessage());
            return back()->withInput();
        }
    }

    public function record_attendance(Request $request, $course_id)
    {
        # code...
        $student = auth('student')->user();
        $class = $student->_class($this->current_accademic_year);
        if($class != null){
            $course = \App\Models\Subjects::find($course_id);
            $class_course = \App\Models\ClassSubject::where('class_id', $class->id)->where('subject_id', $course_id)->first();
            $teachers = \App\Models\TeachersSubject::where(
                [
                    'class_id'=>$class->id, 
                    'subject_id'=> $course_id, 
                    'batch_id'=> \App\Helpers\Helpers::instance()->getCurrentAccademicYear(), 
                    'campus_id'=> $student->campus_id
                    ]
                )->get();
            $data['course'] = $course;
            $data['class_course'] = $class_course;
            $data['teachers'] = $teachers;
            // dd($data);
            return view('student.delegate.lecturers', $data);
        }
    }
    

    public function drop_attendance(Request $request, $attendance_id)
    {
        # code...
        $attendance = \App\Models\Attendance::find($attendance_id);
        if($attendance != null and $attendance->check_out != null){
            session()->flash('error', 'Record can not be deleted after checking out');
            return back()->withInput();
        }
        if($attendance->courseLog() > 0){
            session()->flash('error', "Record already has acourse log and can't be delelted");
            return back()->withInput();
        }
        $attendance->delete();
        return redirect(route('student.delegate.index'))->with('success', 'Done');        
    }

    public function course_log_init(Request $request, $attendance_id)
    {
        # code...
        $attendance = \App\Models\Attendance::find($attendance_id);

        $teacher = $attendance->teacher;
        $subject = $attendance->subject;
        $data['title'] = "Sign Course Log For {$subject->name} [ {$subject->code} ] | {$attendance->teacher->name} ";
        $data['attendance'] = $attendance;
        $data['content'] = \App\Models\Topic::where(['subject_id'=>$subject->id, 'level'=>2, 'teacher_id'=>$teacher->id])->where('campus_id', $attendance->campus_id)->get();

        // dd($data);
        return view('student.delegate.log.init', $data);

    }
    
    public function course_log(Request $request, $attendance_id, $topic_id)
    {
        
        # code...
        $topic = \App\Models\Topic::find($request->topic_id);
        $attendance = \App\Models\Attendance::find($request->attendance_id);
        $subject = $attendance->subject;
        $campus = $attendance->campus;
        $year = $attendance->year;
        $sub_topics = \App\Models\Topic::where(['subject_id'=> $subject->id, 'level'=>2, 'campus_id'=>$campus->id, 'teacher_id'=>$attendance->teacher_id])->pluck('id')->toArray();
        $data['title'] = $topic ?
            "Sign Course Log For { $topic->title } | {$subject->name} [ {$subject->code} ] | {$attendance->teacher->name}" :
            "Course Log For {$subject->name} [ {$subject->code} ] | {$attendance->teacher->name}";
        $data['subject'] = $subject;
        $data['campus'] = $campus;
        $data['topic'] = $topic;
        $data['period'] = "From {$attendance->check_in->format('Y-m-d H:i')} To {$attendance->check_out->format('Y-m-d H:i')}";
        $data['attendance_record'] = $attendance;
        $data['log_history'] = \App\Models\CourseLog::whereIn('topic_id', $sub_topics)->get();
        
        return view('student.delegate.log.course_log', $data);
    }

    public function course_log_save(Request $request, $attendance_id, $topic_id)
    {
        # code...
        try {
            //code...
            $this->classDelegateService->log_course($request->all());
            return back()->with('success', 'Done');
        } catch (\Throwable $th) {
            //throw $th;
            session()->flash('error', "M____{$th->getMessage()} ____F____{$th->getFile()}____L____{$th->getLine()}");
            return back()->withInput();
        }
    }
    
}
