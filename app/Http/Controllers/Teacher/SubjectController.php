<?php

namespace App\Http\Controllers\Teacher;

use App\Models\Result;
use App\Models\SchoolUnits;
use App\Models\ClassSubject;
use App\Models\TeachersSubject;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use \Session;


class SubjectController extends Controller
{

    public function index(Request $request)
    {
        if ($request->class) {
           $unit = SchoolUnits::find($request->class);
           $data['subjects'] = $unit->subject;
        } else {
            $data['subjects'] = Auth::user()->subjectR(\App\Helpers\Helpers::instance()->getCurrentAccademicYear());
        }
        $data['classes'] = \App\Http\Controllers\Admin\StudentController::baseClasses();
        return view('teacher.subjects')->with($data);
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
