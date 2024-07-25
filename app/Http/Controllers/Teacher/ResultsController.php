<?php

namespace App\Http\Controllers\Teacher;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Grading;
use App\Models\ProgramLevel;
use App\Models\Result;
use App\Models\Semester;
use App\Models\Students;
use App\Models\Subjects;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
    

    
    public function ca_upload_report(Request $request, $year=null, $semester=null, $pl=null){
        $data['title'] = "CA Upload Record";
        $data['year_id'] = $year; $data['semester_id'] = $semester; $data['class_id'] = $pl;
        $data['years'] = Batch::all();
        $data['semesters'] = Semester::all();
        $data['classes'] = \App\Http\Controllers\Controller::sorted_program_levels();
        if($pl != null){
            $data['year'] = Batch::find($year);
            $data['semester'] = Semester::find($semester);
            $data['class'] = ProgramLevel::find($pl);
            if($data['semester'] != null && $data['class'] != null){
                $data['title'] = "CA Upload Record For ".($data['class'] == null ? "" :$data['class']->name()).", ".($data['semester']->name??'')." ".($data['year']->name??'');
                $uploaded = Result::where(['batch_id'=>$year, 'semester_id'=>$semester, 'class_id'=>$pl])->whereNotNull('ca_score')->orderBy('subject_id')->distinct()->pluck('subject_id')->toArray();
                $data['record'] = ProgramLevel::find($pl)->subjects()->where('semester_id', $semester)->get()->map(function($rec)use($uploaded){
                    $rec->_status = in_array($rec->id, $uploaded) ? 1: 0;
                    return $rec;
                });
            }
        }
        return view('teacher.result.ca_upload_record', $data);
    }

    public function exam_upload_report(Request $request, $year=null, $semester=null, $pl=null){
        $data['title'] = "Exam Upload Record";
        $data['year_id'] = $year; $data['semester_id'] = $semester; $data['class_id'] = $pl;
        $data['years'] = Batch::all();
        $data['semesters'] = Semester::all();
        $data['classes'] = \App\Http\Controllers\Controller::sorted_program_levels();
        if($pl != null){
            $data['year'] = Batch::find($year);
            $data['semester'] = Semester::find($semester);
            $data['class'] = ProgramLevel::find($pl);
            if($data['semester'] != null && $data['class'] != null){
                $data['title'] = "Exam Upload Record For ".($data['class'] == null ? "" :$data['class']->name()).", ".($data['semester']->name??'')." ".$data['year']->name??'';
                $uploaded = Result::where(['batch_id'=>$year, 'semester_id'=>$semester, 'class_id'=>$pl])->whereNotNull('exam_score')->orderBy('subject_id')->distinct()->pluck('subject_id')->toArray();
                $data['record'] = ProgramLevel::find($pl)->subjects()->where('semester_id', $semester)->get()->map(function($rec)use($uploaded){
                    $rec->_status = in_array($rec->id, $uploaded) ? 1: 0;
                    return $rec;
                });
            }
        }
        return view('teacher.result.exam_upload_record', $data);
    }

    public function upload_statistics($class_id){
        $year = Batch::find(Helpers::instance()->getCurrentAccademicYear());
        $semester = Helpers::instance()->getSemester($class_id);
        $program_level = ProgramLevel::find($class_id);
        $data = [
            'year'=>$year, 'semester'=>$semester, 'class'=>$program_level,
            'title'=>"Course Result Upload Records For {$semester->name} {$year->name} | ".($program_level == null ? '' : $program_level->name())
        ];
        return view('teacher.result.upload_record_index', $data);
    }
}
