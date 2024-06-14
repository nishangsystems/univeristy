<?php


namespace App\Http\Controllers\API\Student;


use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Resources\StudentResource3;
use App\Models\Batch;
use App\Models\Charge;
use App\Models\NonGPACourse;
use App\Models\PlatformCharge;
use App\Models\Semester;
use App\Models\Students;
use App\Models\Subjects;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    public function ca(Request $request)
    {
        $current_year = \App\Helpers\Helpers::instance()->getYear();
        
        $student = Auth('student_api')->user();
        
        $year = Batch::find($request->year ?? Helpers::instance()->getCurrentAccademicYear());
        $class = $student->_class($year->id);
        $semester = $request->semester ? Semester::find($request->semester) : Helpers::instance()->getSemester($class->id);
        // dd($request->all());
        if($class == null){
            return view('api.error')->with('error', "No result found. Make sure you were admitted to this institution by or before the selected academic year");
        }

        $registered_courses = $student->registered_courses($year->id)->where('semester_id', $semester->id)->pluck('course_id')->toArray();

        $data['title'] = "My CA Result";
        $data['user'] = $student;
        $data['year'] = $year;
        $data['class'] = $class;
        $data['ca_total'] = $class->program()->first()->ca_total;
        $data['semester'] = $semester;
        $data['grading'] = $class->program()->first()->gradingType->grading()->get() ?? [];
        $res = $student->result()->where('results.batch_id', '=', $year->id)->where('results.semester_id', '=', $semester->id)->pluck('subject_id')->toArray();
        // $data['subjects'] = $class->subjects()->whereIn('subjects.id', $res)->get();
        $results = array_map(function($subject_id)use($data, $year, $semester, $student){
            return [
                'id'=>$subject_id,
                'code'=>Subjects::find($subject_id)->code ?? '',
                'name'=>Subjects::find($subject_id)->name ?? '',
                'status'=>Subjects::find($subject_id)->status ?? '',
                'coef'=>Subjects::find($subject_id)->coef ?? '',
                'ca_mark'=>$student->result()->where('results.batch_id', '=', $year->id)->where('results.subject_id', '=', $subject_id)->where('results.semester_id', '=', $semester->id)->first()->ca_score ?? '',
            ];
        // }, $res);
        }, $registered_courses);
        // dd($data['results']);
        $data['results'] = collect($results)->filter(function($el){return $el != null;});
        return view('api.ca_result')->with($data);

    }

    public function exam(Request $request)
    {
        $current_year = \App\Helpers\Helpers::instance()->getYear();
        
        $student = Auth('student_api')->user();
        

        // return $request->all();
        $year = Batch::find($request->year ?? $this->current_accademic_year);
        $class = $student->_class($year->id);
        $semester = $request->semester ? Semester::find($request->semester) : Helpers::instance()->getSemester($class->id);

        // return ['year'=>$year, 'semester'=>$semester];
        // check if results are published
        if(!$semester->result_is_published($year->id, $student->id)){
            return response()->json(['message'=>'Results Not Yet Published For This Semester.', 'error_type'=>'err6'], 400);
        }

        // check if semester result fee is set && that student has payed
        $plcharge = PlatformCharge::where(['year_id'=>$year->id])->first();
        $amount = $plcharge->result_amount ?? null;
        if($amount != null && $amount > 0){
            $charge = Charge::where(['year_id'=>$year, 'semester_id'=>$semester->id, 'student_id'=>auth('student')->id(), 'type'=>'RESULTS'])->first();
            if($charge == null){
                return response()->json(['message'=>'Pay Semester Result Charges to continue', 'error_type'=>'err5'], 400);
            }
        }

        if($class == null){
            return response()->json(['message'=>"No result found. Make sure you were admitted to this institution by or before the selected academic year", 'error_type'=>'err6'], 400);
        }

        // CODE TO CHECK FOR PAYMENT OF REQUIRED PLATFORM PAYMENTS; WILL BE COMMENTED OUT TILL IT SHOULD TAKE EFFECT
        // if(){
        //     return back()->with('error', "You have not paid plaftorm or semester result charges for the selected semester");
        // }

        // END OF CHECK FOR PAYMENT OF REQUIRED PLATFORM PAYMENTS

        $registered_courses = $student->registered_courses($year->id)->where('semester_id', $semester->id)->pluck('course_id')->toArray();

        $data['title'] = "My Exam Result";
        $data['user'] = $student;
        $data['semester'] = $semester;
        $data['class'] = $class;
        $data['year'] = $year;
        $data['ca_total'] = $class->program()->first()->ca_total;
        $data['exam_total'] = $class->program()->first()->exam_total;
        $data['grading'] = $class->program()->first()->gradingType->grading()->get() ?? [];
        $res = $student->result()->where('results.batch_id', '=', $year->id)->where('results.semester_id', $semester->id)->distinct()->pluck('subject_id')->toArray();
        // $data['subjects'] = $class->subjects()->whereIn('subjects.id', $res)->get();
        $non_gpa_courses = Subjects::whereIn('code', NonGPACourse::pluck('course_code')->toArray())->pluck('id')->toArray();
        // $non_gpa_courses = [];
        // return $non_gpa_courses;
        $results = array_map(function($subject_id)use($data, $year, $semester, $student){
            $ca_mark = $student->result()->where('results.batch_id', '=', $year->id)->where('results.subject_id', '=', $subject_id)->where('results.semester_id', '=', $semester->id)->first()->ca_score ?? 0;
            $exam_mark = $student->result()->where('results.batch_id', '=', $year->id)->where('results.subject_id', '=', $subject_id)->where('results.semester_id', '=', $semester->id)->first()->exam_score ?? 0;
            $total = $ca_mark + $exam_mark;
            $rol = [
                'id'=>$subject_id,
                'code'=>Subjects::find($subject_id)->code ?? '',
                'name'=>Subjects::find($subject_id)->name ?? '',
                'status'=>Subjects::find($subject_id)->status ?? '',
                'coef'=>Subjects::find($subject_id)->coef ?? '',
                'ca_mark'=>$ca_mark,
                'exam_mark'=>$exam_mark,
                'total'=>$total
            ];
            foreach ($data['grading'] as $key => $value) {
                # code...
                if ($total >= $value->lower && $total <= $value->upper) {
                    # code...
                    $grade = $value;
                    $rol['grade'] = $grade->grade;
                    $rol['remark'] = $grade->remark;
                    $rol['weight'] = $grade->weight;
                }
            }
            if(!array_key_exists('grade', $rol)){
                $rol['grade'] = null;
                $rol['remark'] = null;
                $rol['weight'] = null;
            }
            return $rol;

            // dd($grade);
        // }, $res);
        }, $registered_courses);
        $data['results'] = collect($results)->filter(function($el){return $el != null;});
        //

        $fee = [
            'total_debt'=>$student->total_debts($year->id),
            'total_paid'=>$student->total_paid($year->id),
            'total' => $student->total($year->id),
            'balance' => $student->bal(null, $year->id),
            'total_balance' => $student->total_balance(null, $year->id),
            'fraction' => $semester->semester_min_fee
        ];
        
        // $data['fee_data'] = $fee;
        
        $min_fee = $fee['total']*$fee['fraction'];
        // $data['total_balance'] = $student->total_balance($student->id, $year->id);
        $data['access'] = ($fee['total'] - $fee['total_balance']) >= $min_fee || Students::find($student)->classes()->where(['year_id'=>$year->id, 'result_bypass_semester'=>$semester->id, 'bypass_result'=>1])->count() > 0;
        // dd($data);
        // return $data;
        if(!$data['access']){
            return response()->json(['message'=>"You have not paid upto the minimum fee of {$fee['min_fee']} required to access results. Pay your fee at ".url('/')." to continue.", "error_type"=>'err1'], 400);
        }
        return response()->json(['data'=>$data]);
    }

    
    public function download_exam(Request $request)
    {
        // return $request->all();

        
        $student = Auth('student_api')->user();
        
        $year = $request->year ?? $this->current_accademic_year;
        $semester = $request->semester ? Semester::find($request->semester) : Helpers::instance()->getSemester($student->_class($year)->id);

        $registered_courses = $student->registered_courses($year)->where('semester_id', $semester->id)->pluck('course_id')->toArray();

        $data['title'] = "";
        $data['user'] = $student;
        $data['class'] = $data['user']->_class($year);
        $data['year'] = $year;
        $data['semester'] = $semester;
        $data['ca_total'] = $student->_class($year)->program()->first()->ca_total;
        $data['exam_total'] = $student->_class($year)->program()->first()->exam_total;
        $data['grading'] = $student->_class($year)->program()->first()->gradingType->grading()->get() ?? [];
        $non_gpa_courses = NonGPACourse::pluck('id')->toArray();
        $res = $student->result()->where('results.batch_id', '=', $year)->where('results.semester_id', $semester->id)->distinct()->pluck('subject_id')->toArray();
        $data['subjects'] = $student->_class($year)->subjects()->whereIn('subjects.id', $res)->get();
        $results = array_map(function($subject_id)use($data, $year, $semester, $student){
            $ca_mark = $student->result()->where('results.batch_id', '=', $year)->where('results.subject_id', '=', $subject_id)->where('results.semester_id', '=', $semester->id)->first()->ca_score ?? 0;
            $exam_mark = $student->result()->where('results.batch_id', '=', $year)->where('results.subject_id', '=', $subject_id)->where('results.semester_id', '=', $semester->id)->first()->exam_score ?? 0;
            $total = $ca_mark + $exam_mark;
            $rol = [
                'id'=>$subject_id,
                'code'=>Subjects::find($subject_id)->code ?? '',
                'name'=>Subjects::find($subject_id)->name ?? '',
                'status'=>Subjects::find($subject_id)->status ?? '',
                'coef'=>Subjects::find($subject_id)->coef ?? '',
                'ca_mark'=>$ca_mark,
                'exam_mark'=>$exam_mark,
                'total'=>$total
            ];
            foreach ($data['grading'] as $key => $value) {
                # code...
                if ($total >= $value->lower && $total <= $value->upper) {
                    # code...
                    $grade = $value;
                    $rol['grade'] = $grade->grade;
                    $rol['remark'] = $grade->remark;
                    $rol['weight'] = $grade->weight;
                }
            }
            if(!array_key_exists('grade', $rol)){
                $rol['grade'] = null;
                $rol['remark'] = null;
                $rol['weight'] = null;

            }
            return $rol;
            
            // dd($grade);
        // }, $res);
        }, $registered_courses);

        $data['results'] = collect($results)->filter(function($el){return $el != null;});
        

        $data['results'] = collect($results)->filter(function($el){return $el != null;});

        // dd($data);
        if ($data['class']->program->background->background_name == "PUBLIC HEALTH") {
            # code...
            $pdf = Pdf::loadView('student.templates.public_health_exam_result',$data);
        }else{
            $pdf = Pdf::loadView('student.templates.exam-result-template',$data);
        }
        $fname = 'files/'.str_replace('/', '_', $data['user']->matric).'_'.time().'_'.$semester->name.'_EXAM_RESULTS.pdf';
        $fpath = public_path($fname);
        $pdf->save($fpath);
        return response()->json(['status'=>'success', 'url'=>asset($fname)]);
        // return $pdf->download($student->matric.'_'.$semester->name.'_EXAM_RESULTS.pdf');
        // return view('student.templates.exam-result-template')->with($data);
    }

}