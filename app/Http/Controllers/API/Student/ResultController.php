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
            return view('api.error')->with('error', 'Results Not Yet Published For This Semester.');
        }

        // check if semester result fee is set && that student has payed
        $plcharge = PlatformCharge::where(['year_id'=>$year->id])->first();
        $amount = $plcharge->result_amount ?? null;
        if($amount != null && $amount > 0){
            $charge = Charge::where(['year_id'=>$year, 'semester_id'=>$semester->id, 'student_id'=>auth('student')->id(), 'type'=>'RESULTS'])->first();
            if($charge == null){
                return view('api.error')->with('error', 'Pay Semester Result Charges to continue');
            }
        }

        if($class == null){
            return view('api.error')->with('error', "No result found. Make sure you were admitted to this institution by or before the selected academic year");
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
        // return ($registered_courses);
        $sum_cv = $data['results']->sum('coef');
        $sum_earned_cv = collect($results)->filter(function($el){return ($el != null) && ($el['ca_mark']+$el['exam_mark'] >= 50);})->sum('coef');
        $gpa_cv = $data['results']->whereNotIn('id', $non_gpa_courses)->sum('coef');
        $gpa_cv_earned = $data['results']->whereNotIn('id', $non_gpa_courses)->filter(function($el){return ($el != null) && ($el['ca_mark']+$el['exam_mark'] >= 50);})->sum('coef');
        $sum_gpts = $data['results']->whereNotIn('id', $non_gpa_courses)->sum(function($item){
            return $item['coef'] * $item['weight'];
        });
        $gpa = $sum_gpts/($gpa_cv==0?1:$gpa_cv);
        // dd($sum_gpts);
        $gpa_data['sum_cv'] = $sum_cv;
        $gpa_data['gpa_cv'] = $gpa_cv;
        $gpa_data['sum_cv_earned'] = $sum_earned_cv;
        $gpa_data['gpa_cv_earned'] = $gpa_cv_earned;
        $gpa_data['gpa'] = $gpa;

        $data['gpa_data'] = $gpa_data;

        $fee = [
            'total_debt'=>$student->total_debts($year->id),
            'total_paid'=>$student->total_paid($year->id),
            'total' => $student->total($year->id),
            'balance' => $student->bal(null, $year->id),
            'total_balance' => $student->total_balance(null, $year->id),
            'fraction' => $semester->semester_min_fee
        ];
        $data['fee_data'] = $fee;
        // TOTAL PAID - TOTAL DEBTS FOR THIS YEAR = AMOUNT PAID FOR THIS YEAR
        // $data['min_fee'] = $fee['total']*$fee['fraction'];
        // $data['access'] = ($fee['total'] - $fee['balance']) >= $data['min_fee'] || $student->classes()->where(['year_id'=>$year->id, 'result_bypass_semester'=>$semester->id, 'bypass_result'=>1])->count() > 0;

        $data['min_fee'] = $fee['total']*$fee['fraction'];
        $data['total_balance'] = $student->total_balance($student->id, $year->id);
        $data['access'] = ($fee['total'] - $fee['total_balance']) >= $data['min_fee'] || Students::find($student)->classes()->where(['year_id'=>$year->id, 'result_bypass_semester'=>$semester->id, 'bypass_result'=>1])->count() > 0;
        // dd($data);
        // return $data;
        if ($class->program->background->background_name == "PUBLIC HEALTH") {
            # code...
            return view('api.public_health_exam_result')->with($data);
        }
        return view('api.exam_result')->with($data);

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
        // $sum_cv = $data['results']->sum('coef');
        // $sum_earned_cv = collect($results)->filter(function($el){return ($el != null) && ($el['ca_mark']+$el['exam_mark'] >= 50);})->sum('coef');
        // $gpa_cv = $data['results']->whereNotIn('id', $non_gpa_courses)->sum('coef');
        // $gpa_cv_earned = $data['results']->whereNotIn('id', $non_gpa_courses)->filter(function($el){return ($el != null) && ($el['ca_mark']+$el['exam_mark'] >= 50);})->sum('coef');
        // $sum_gpts = $data['results']->whereNotIn('id', $non_gpa_courses)->sum(function($item){
        //     return $item['coef'] * $item['weight'];
        // });
        // $gpa = $sum_gpts/($gpa_cv==0?1:$gpa_cv);
        // // dd($gpa);
        // $gpa_data['sum_cv'] = $sum_cv;
        // $gpa_data['gpa_cv'] = $gpa_cv;
        // $gpa_data['sum_cv_earned'] = $sum_earned_cv;
        // $gpa_data['gpa_cv_earned'] = $gpa_cv_earned;
        // $gpa_data['gpa'] = $gpa;

        // $data['gpa_data'] = $gpa_data;


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