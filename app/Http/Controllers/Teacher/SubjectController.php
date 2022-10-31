<?php

namespace App\Http\Controllers\Teacher;

use App\Helpers\Helpers;
use App\Models\Result;
use App\Models\SchoolUnits;
use App\Models\ClassSubject;
use App\Models\TeachersSubject;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ProgramLevel;
use App\Models\StudentClass;
use App\Models\Subjects;
use Illuminate\Support\Facades\Auth;
use \Session;


class SubjectController extends Controller
{

    public function index(Request $request)
    {
        if ($request->class) {
           $unit = ProgramLevel::find($request->class);
           $data['title'] = 'My '.$unit->program()->first()->name.' : LEVEL '.$unit->level()->first()->level;
           $data['subjects'] = \App\Models\Subjects::join('teachers_subjects', ['teachers_subjects.subject_id'=>'subjects.id'])
                        ->where(['teachers_subjects.class_id'=>$request->class])
                        ->where(function($q)use ($request){
                            $request->has('campus') ? $q->where(['teachers_subjects.campus_id'=>$request->campus]):null;
                        })->distinct()->select(['subjects.*','teachers_subjects.class_id as class', 'teachers_subjects.campus_id'])->get();
        } else {
            $data['courses'] = \App\Models\TeachersSubject::where([
                'teacher_id' => auth()->id(),
                'batch_id' => \App\Helpers\Helpers::instance()->getCurrentAccademicYear(),
            ])->join('subjects', ['subjects.id'=>'teachers_subjects.subject_id'])
            ->distinct()->select('subjects.*', 'teachers_subjects.class_id as class', 'teachers_subjects.campus_id')->get();
        }
        // dd($data);
        return view('teacher.subjects')->with($data);
    }


    public function course_list(Request $request, $class_id, $course_id)
    {
        # code...
        
        $data['students'] = StudentClass::where(['student_classes.class_id'=>$class_id])
                    ->where(['student_classes.year_id'=>Helpers::instance()->getYear()])
                    ->join('students', ['students.id'=>'student_classes.student_id'])
                    ->where(['students.campus_id'=>$request->campus_id])
                    ->join('student_courses', ['student_courses.student_id'=>'students.id'])
                    ->where(['student_courses.course_id'=>$course_id])
                    ->select(['students.*'])->get();

        $class = ProgramLevel::find($class_id);
        $data['title'] = "Class List For ".$class->program()->first()->name.': LEVEL '.$class->level()->first()->level.' ('.Subjects::find($course_id)->name.') : '.\App\Models\Campus::find(request('campus_id'))->name;
        
        return view('teacher.students', $data);
    }

    public function result($subject)
    {
       if(request('class')){
            $data['subject'] = ClassSubject::find($subject);
       }else{
            $data['subject'] = TeachersSubject::find($subject)->subject;
       }
        return view('teacher.result')->with($data);
    }

    public function store(Request $request)
    {
        $result = Result::where([
            'student_id' => $request->student,
            'class_id' => $request->class_id,
            'sequence' => $request->sequence,
            'subject_id' => $request->subject,
            'batch_id' => $request->year
        ])->first();

        if ($result == null) {
            $result = new Result();
        }

        $result->batch_id = $request->year;
        $result->student_id =  $request->student;
        $result->class_id =  $request->class_id;
        $result->sequence =  $request->sequence;
        $result->subject_id =  $request->subject;
        $result->score =  $request->score;
        $result->coef =  $request->coef;
        $result->remark = "";
        $result->class_subject_id =  $request->class_subject_id;
        $result->save();
    }
}
