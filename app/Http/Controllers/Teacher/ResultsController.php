<?php

namespace App\Http\Controllers\teacher;

use App\Http\Controllers\Controller;
use App\Models\Grading;
use App\Models\Result;
use App\Models\Students;
use Illuminate\Http\Request;

class ResultsController extends Controller
{
    //

    public function __construct()
    {
        # code...
    }
    
    //

    public function index(Request $request)
    {
        # code...
        $data['courses'] = \App\Models\TeachersSubject::where([
            'teacher_id' => auth()->id(),
            'batch_id' => \App\Helpers\Helpers::instance()->getCurrentAccademicYear(),
        ])->join('subjects', ['subjects.id'=>'teachers_subjects.subject_id'])
        ->distinct()->select('subjects.*', 'teachers_subjects.class_id as class', 'teachers_subjects.campus_id')->get();
        $data['title'] = "My Courses";
        return view('teacher.course.results.index', $data);
    }
    
    //

    public function fill_ca(Request $request)
    {
        # code...
    }
    
    //

    public function store_result(Request $request)
    {
        # code...
        $validity = Validator::make($request->all(), [
            'student'=>'required', 'semester_id'=>'required', 'subject'=>'required', 'year'=>'required',
            'class_id'=>'required', 'coef'=>'required', 'ca_score'=>'required'
        ]);

        if($validity->fails()){
            return response(['message'=>'Validation error. '.$validity->errors()->first()]);
        }

        try{
            
            $totalMark = ($request->ca_score??0) + ($request->exam_score??0);
            $grading = Grading::where('lower', '<=', $totalMark)->where('upper', '>=', $totalMark)->first();
            $student = Students::find($request->student);
            $data = [
                'batch_id'=>$request->year, 'student_id'=>$request->student, 'class_id'=>$request->class_id, 'semester_id'=>$request->semester_id, 
                'subject_id'=>$request->subject, 'ca_score'=>$request->ca_score, 'exam_score'=>$request->exam_score, 'coef'=>$request->coef, 'remark'=>$grading->remark??'FAIL',
                'class_subject_id'=>$request->class_subject_id, 'reference'=>'REF'.$request->year.$request->student.$request->class_id.$request->semester_id.$request->subject_id.$request->coef, 
                'user_id'=>auth()->id(), 'campus_id'=>$student->campus_id, 'published'=>0
            ];
            $base = ['batch_id'=>$request->year, 'student_id'=>$request->student, 'class_id'=>$request->class_id, 'semester_id'=>$request->semester_id, 
            'subject_id'=>$request->subject];
    
            Result::updateOrInsert($base, $data);
            return response(['message'=>'saved successfully']);
        }catch(\Throwable $th){
            return response(['message'=>$th->getMessage()], 500);
        }
    }
    
}
