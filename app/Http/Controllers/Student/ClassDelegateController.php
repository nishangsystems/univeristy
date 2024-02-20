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

    public function check_in(Request $request, $campus_id, $subject_id, $teacher_id)
    {
        # code...
        $data['subject'] = Subjects::find($subject_id);
        $data['teacher'] = \App\Models\User::find($teacher_id);
        $data['campus'] = \App\Models\Campus::find($campus_id);
        $data['title'] = "Take Attendance for {$data['teacher']->name} [{$data['subject']->name}]";
        return view('student.delegate.check_in', $data);
    }

    public function check_in_save(Request $request)
    {
        # code...
        try {
            $this->classDelegateService->check_in($request->all());
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
            $teachers = \App\Models\TeachersSubject::where('class_id', $class->id)->where('subject_id', $course_id)->where('batch_id', $this->current_accademic_year)->where('campus_id', $student->campus_id)->get();
            $data['course'] = $course;
            $data['class_course'] = $class_course;
            $data['teachers'] = $teachers;
            return view('student.delegate.lecturers', $data);
        }
    }
    
}
