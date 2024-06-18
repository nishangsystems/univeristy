<?php

namespace App\Http\Controllers\api\parent;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Charge;
use App\Models\PlatformCharge;
use App\Models\SchoolContact;
use App\Models\Semester;
use App\Models\Students;
use App\Models\Subjects;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    //
    public function students()
    {
        $parent = auth('parent_api')->user();

        $children = Students::where('parent_phone_number', $parent->phone)->get();
        return response()->json(['data'=>$children]);
    }

    //
    public function semesters(Request $request)
    {
        $parent = auth('parent_api')->user();
        if($request->has('student')){
            $student = Students::find($request->student);
            if(!in_array($student, $parent->children()->all())){
                return response()->json(['message'=>'The specified student is not your child', 'error_type'=>'general-error'], 400);
            }
            if($student == null){
                return response()->json(['status'=>400, 'message'=>'student not found', 'error_type'=>'general-error'], 400);
            }
            $semesters = Helpers::instance()->getSemesters($student->_class()->id);
            return response()->json(['data'=>$semesters]);
        }
        return response()->json(['message'=>'Student must be specified', 'error_type'=>'general-error'], 400);
    }

    // expects year and student as request params
    public function fee(Request $request)
    {
        $parent = auth('parent_api')->user();
        if(!$request->has('student')){
            return response()->json(['message'=>'student not specified', 'error_type'=>'general-error'], 400);
        }
        $student = Students::find($request->student);
        if(!in_array($student, $parent->children()->all())){
            return response()->json(['message'=>'The specified student is not your child', 'error_type'=>'general-error'], 400);
        }

        $year = $request->get('year', Helpers::instance()->getCurrentAccademicYear());
        $data['student'] = $student;
        $data['total_paid'] = number_format($student->total_paid( $year));
        $data['total_debt'] = number_format($student->bal($student->id, $year));
        $data['payments'] = $student->payments()->where(['batch_id'=>($year)])->get()
        ->each(function($item){
            $item->item = $item->item??null;
        });
        if($data['payments']->count() == 0){
            return response()->json(['message'=>'No Fee payments found for '.(Batch::find($year)->name??''), 'error_type'=>'general-error'], 400);
        }
        return response()->json(['data'=>$data]);
    }

    // expects semester, year and student as request params
    public function results(Request $request)
    {
        $parent = auth('parent_api')->user();
        if(!$request->has('student')){
            return response()->json(['message'=>'student not specified', 'error_type'=>'general-error'], 400);
        }
        $student = Students::find($request->student);
        if(!in_array($student, $parent->children()->all())){
            return response()->json(['message'=>'The specified student is not your child', 'error_type'=>'general-error'], 400);
        }

        $year = Batch::find($request->year ?? $this->current_accademic_year);
        $class = $student->_class($year->id);
        $semester = $request->semester ? Semester::find($request->semester) : Helpers::instance()->getSemester($class->id);

        
        if(!$semester->result_is_published($year->id, $student->id)){
            return response()->json(['message'=>'Results Not Yet Published For This Semester.', 'error_type'=>'no-result-error'], 400);
        }

        // check if semester result fee is set && that student has payed
        $plcharge = PlatformCharge::where(['year_id'=>$year->id])->first();
        $amount = $plcharge->result_amount ?? null;
        if($amount != null && $amount > 0){
            $charge = Charge::where(['year_id'=>$year, 'semester_id'=>$semester->id, 'student_id'=>$student->id, 'type'=>'RESULTS'])->first();
            if($charge == null){
                return response()->json(['message'=>'Pay Semester Result Charges to continue', 'error_type'=>'err5', 'error_type'=>'result-charge-error'], 400);
            }
        }

        if($class == null){
            return response()->json(['message'=>"No result found. Make sure you were admitted to this institution by or before the selected academic year", 'error_type'=>'no-result-error'], 400);
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
        $data['access'] = ($fee['total'] - $fee['total_balance']) >= $min_fee || $student->classes()->where(['year_id'=>$year->id, 'result_bypass_semester'=>$semester->id, 'bypass_result'=>1])->count() > 0;
        // dd($data);
        // return $data;
        if(!$data['access']){
            return response()->json(['message'=>"You have not paid upto the minimum fee of {$fee['min_fee']} required to access results. Pay your fee at ".url('/')." to continue.", "error_type"=>'min-fee-error'], 400);
        }
        return response()->json(['data'=>$data]);
    }
    
    //
    public function contacts()
    {
        $parent = auth('parent_api')->user();
        $contacts = SchoolContact::all();
        return response()->json(['data'=>$contacts]);
    }
}
