<?php

namespace App\Models;

use App\Helpers\Helpers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\HasApiTokens;

class Students extends Authenticatable
{
    use HasFactory, HasApiTokens, SoftDeletes;

    protected $fillable = [
        'name', 'email', 'phone', 'address', 'gender', 'username', 'matric', 
        'dob', 'pob', 'campus_id', 'admission_batch_id', 'password', 'parent_name', 
        'program_id', 'parent_phone_number', 'imported', 'active', 'password', 
        'region', 'division', 'nationality', 'deleted_at', 'program_status'
    ];

    protected $dates = ['deleted_at', 'created_at', 'updated_at', 'dob'];

    protected $connection = 'mysql';

    public function extraFee($year_id)
    {
        $builder = $this->hasMany(ExtraFee::class, 'student_id')->where('year_id', '=', $year_id);
        return $builder->count() == 0 ? null : $builder->first();
    }

    public function _classes($year_id = null)
    {
        # code...
        return $this->belongsToMany(ProgramLevel::class, 'student_classes', 'student_id', 'class_id')
            ->where(function($builder)use($year_id){
                if($year_id != null)
                    $builder->where('student_classes.year_id', $year_id);
            })
            ->orderByDesc('student_classes.year_id');
    }
    
    public function _class($year=null)
    {
        $cls = $this->belongsToMany(ProgramLevel::class, StudentClass::class, 'student_id', 'class_id')
        ->where(function($builder)use($year){
            if($year != null)
                $builder->where('student_classes.year_id', $year);
        })->orderBy('student_classes.year_id', 'DESC')->first();

        return $cls;
    }

    public function class($year)
    {
        $class = $this->_class($year);
        $class = $class ==  null ? $this->_class() : $class;
        return $class->campus_programs($this->campus_id)->first() ?? null;
    }

    public function a_class($year)
    {
        $class = $this->_class($year);
        return $class ==  null ? $this->_class() : $class;
    }

    public function classes()
    {
        return $this->hasMany(StudentClass::class, 'student_id');
    }

    public function result()
    {
        return $this->hasMany(Result::class, 'student_id');
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class, 'admission_batch_id');
    }


    public function payments()
    {
        return $this->hasMany(Payments::class, 'student_id');
    }

    public function payIncomes($year)
    {
        # code...
        return $this->hasMany(PayIncome::class, 'student_id')->where('batch_id', '=', $year);
    }

    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    public function total($year_id = null)
    {
        $year = $year_id == null ? Helpers::instance()->getCurrentAccademicYear() : $year_id;
        if ($this->classes()->where('year_id', $year)->first() != null) {
            switch($this->program_status){
                case "ON-CAMPUS":
                    return $this->_class($year)->campus_programs($this->campus_id)->first()->payment_items()->where(['year_id'=>$year])->sum('amount');
                case "HYBRID":
                    $builder = $this->_class($year)->campus_programs($this->campus_id)->first()->payment_items()->where(['year_id'=>$year]);
                    return $builder->where('name', 'TUTION')->sum('hybrid_amount') + $builder->where('name', 'REGISTRATION')->sum('amount');
                case "INTERNATIONAL":
                    $builder = $this->_class($year)->campus_programs($this->campus_id)->first()->payment_items()->where(['year_id'=>$year]);
                    return $builder->where('name', 'TUTION')->sum('international_amount') + $builder->where('name', 'REGISTRATION')->sum('amount');
            }
        }
        dd(0);
        return 0;
    }

    public function registration_total($year_id = null)
    {
        $year = $year_id == null ? Helpers::instance()->getCurrentAccademicYear() : $year_id;
        if ($this->classes()->where('year_id', $year)->first() != null) {
            # code...
            // return $this->campus()->first()->campus_programs()->where(['program_level_id' => $this->_class(Helpers::instance()->getCurrentAccademicYear())->id ?? 0, 'campus_id'=>$this->campus_id])->first()->payment_items()->first()->amount ?? -1;
            $rec = $this->_class($year)->campus_programs($this->campus_id)->first()->payment_items()->where(['year_id'=>$year, 'name'=>'REGISTRATION'])->first();
            return $rec ? $rec->amount : 0;
        }
        
        return 0;
    }

    public function payment_items($year_id = null)
    {
        $year = $year_id == null ? Helpers::instance()->getCurrentAccademicYear() : $year_id;
        if ($this->classes()->where('year_id', $year)->first() != null) {
            # code...
            return $this->_class($year)->campus_programs($this->campus_id)->first()->payment_items;
        }
        return collect();
    }

    public function paid($year = null) // fee paid for current academic year
    {
        $year = $year == null ? Helpers::instance()->getYear() : $year;
        $items = $this->payments()->where('payment_year_id', $year)->get();
        return $items->sum('amount');
    }

    public function registration_paid($year = null) // fee paid for current academic year
    {
        $year = $year == null ? Helpers::instance()->getYear() : $year;
        $item_id = $this->payment_items()->where('name', 'REGISTRATION')->first()->id??0;
        $items = $this->payments()->where('batch_id', $year)->where('payment_id', $item_id)->selectRaw('COALESCE(sum(amount),0) total')->get();
        return $items->first()->total??0;
    }

    // current year's unpaid fee
    public function bal($student_id = null, $year = null)
    {
        $year = $year == null ? Helpers::instance()->getCurrentAccademicYear() : $year;
        $scholarship = Helpers::instance()->getStudentScholarshipAmount($this->id, $year);
        // dd($scholarship);
        $ret = $this->total($year) + ($this->extraFee($year) == null ? 0 : $this->extraFee($year)->amount) - $this->paid($year) - ($scholarship);
        // dd($ret);
        return $ret;
    }

    // current year's unpaid fee
    public function registration_bal($student_id = null, $year = null)
    {
        $year = $year == null ? Helpers::instance()->getCurrentAccademicYear() : $year;
        $scholarship = Helpers::instance()->getStudentScholarshipAmount($this->id, $year);
        // dd($scholarship);
        return $this->registration_total()  - $this->registration_paid();
    }

    // current year's unpaid fee
    public function total_balance($student_id = null, $year = null)
    {
        $year = $year == null ? Helpers::instance()->getCurrentAccademicYear() : $year;
        return $this->total_debts($year);
        $scholarship = Helpers::instance()->getStudentScholarshipAmount($this->id);
        $ret = $this->total($year) + $this->total_debts($year-1) + ($this->extraFee($year) == null ? 0 : $this->extraFee($year)->amount) - $this->paid($year) - ($scholarship);
    }

    public function debt($year)
    {
        # code...
        $paymentBuilder = Payments::where(['student_id'=>$this->id, 'batch_id'=>$year]);
        if($paymentBuilder->count() == 0){return 0;}
        return $paymentBuilder->sum('debt');
    }
    
    public function totalScore($sequence, $year)
    {
        $class = $this->class($year);
        $subjects = $class->subjects;
        $total = 0;
        foreach ($subjects as $subject) {
            $total += Helpers::instance()->getScore($sequence, $subject->id, $class->id, $year, $this->id) * $subject->coef;
        }

        return $total;
    }

    public function averageScore($sequence, $year)
    {
        $total = $this->totalScore($sequence, $year);
        $gtotal = 0;
        $class = $this->class($year);
        $subjects = $class->subjects;
        foreach ($subjects as $subject) {
            $gtotal += 20 * $subject->coef;
        }
        if ($gtotal == 0 || $total == 0) {
            return 0;
        } else {
            return number_format((float)($total / $gtotal) * 20, 2);
        }
    }

    public function collectBoardingFees()
    {
        return $this->hasMany(CollectBoardingFee::class, 'student_id');
    }

    public function rank($sequence, $year)
    {

        $rank = $this->hasMany(Rank::class, 'student_id')->where([
            'sequence_id' => $sequence,
            'year_id' => $year
        ])->first();

        return $rank ? $rank->position : "NOT SET";
    }

    public function results($class_id, $year_id, $semester_id = null){
        return \Cache::remember("users_results_score_$this->id, $class_id, $year_id, $semester_id", 60, function () use ($class_id, $year_id, $semester_id) {
                 $semester = $semester_id == null ? Helpers::instance()->getSemester($class_id)->id : $semester_id;
                    return collect(Result::where(['student_id' => $this->id,  'class_id' => $class_id, 'batch_id' => $year_id, 'semester_id'=>$semester])->get() ?? [])->groupBy("subject_id");
        });
    }


     public function hasResult( $class_id, $year_id, $semester_id = null){
         $semester = $semester_id == null ? Helpers::instance()->getSemester($class_id)->id : $semester_id;
         return Result::where(['student_id' => $this->id,  'batch_id' => $year_id, 'semester_id'=>$semester])->count() > 0;
    }



    
    public function ca_score($course_id, $class_id, $year_id, $semester_id = null)
    {
        return \Cache::remember("users_ca_score_$this->id $course_id, $class_id, $year_id, $semester_id", 60, function () use ($course_id, $class_id, $year_id, $semester_id) {
            $result = $this->results($class_id, $year_id, $semester_id );
             if (isset($result[$course_id][0]['ca_score'])) {
                return $result[$course_id][0]['ca_score'];
             }
             return '';
        });
    }
    
    public function exam_score($course_id, $class_id, $year_id, $semester_id = null)
    {
       

        return \Cache::remember("users_exam_score_$this->id $course_id, $class_id, $year_id, $semester_id", 60, function () use ($course_id, $class_id, $year_id, $semester_id) {
           
             $result = $this->results($class_id, $year_id, $semester_id );
        
             if (isset($result[$course_id][0]['exam_score'])) {
                return $result[$course_id][0]['exam_score'];
             }

             return '';
        });
    }

    public function total_score($course_id, $class_id, $year_id, $semester_id = null)
    {
        return \Cache::remember("users_total_score_$this->id $course_id, $class_id, $year_id, $semester_id", 60, function () use ($course_id, $class_id, $year_id, $semester_id) {
            $result = $this->results($class_id, $year_id, $semester_id );
            if (isset($result[$course_id][0])) {
                 return ($result[$course_id][0]['ca_score'] ?? 0) + ($result[$course_id][0]['exam_score'] ?? 0);
            }
             return '';
        });
    }

    public function grade($course_id, $class_id, $year_id, $semester_id = null)
    {

        return \Cache::remember("users_grade_score_$this->id _ $course_id, $class_id, $year_id, $semester_id", 60, function () use ($course_id, $class_id, $year_id, $semester_id) {
        # code...
        $grades = \Cache::remember('grading_scale', 60, function () use ($class_id) {
            return  \App\Models\ProgramLevel::find($class_id)->program->gradingType->grading->sortBy('grade') ?? [];
        });

        if(count($grades) == 0){return '-';}

        $score = $this->total_score($course_id, $class_id, $year_id, $semester_id);
        if ($score != '') {
            # code...
            foreach ($grades as $key => $grade) {
                if ($score >= $grade->lower && $score <= $grade->upper) {return $grade->grade;}
            }
        }
        return '';
         });
       
    } 

    public function allScholarships($year = null)
    {
        # code...
        $year = $year == null ? Helpers::instance()->getCurrentAccademicYear() : $year;
        return $this->hasMany(StudentScholarship::class, 'student_id')->where('student_scholarships.batch_id', '<', $year+1)->get();
    }

    // cumulative depts upto the current academic year
    public function total_debts($year)
    {
        # code...


        $campus_program_levels = $this->classes()
            ->where('year_id', '>', $this->admission_batch_id-1)
            ->join('campus_programs', ['campus_programs.program_level_id' => 'student_classes.class_id'])
            ->where('campus_programs.campus_id', $this->campus_id)
            ->get(['campus_programs.id', 'student_classes.year_id']);
        // dd($campus_program_levels);
        // fee amounts
        $fees = 0;
        foreach (Batch::where('id', '<', $year+1)->get() as $key => $batch) {
            # code...
            $class = $this->_class($batch->id);
            if($class== null or $class->campus_programs($this->campus_id)->first() == null )continue;
            if($this->program_status == "ON-CAMPUS"){
                $fees += $this->_class($batch->id)->campus_programs($this->campus_id)->first()->payment_items()->where('payment_year_id', $batch->id)->sum('amount');
            }elseif($this->program_status == "HYBRID"){
                $builder = $this->_class($batch->id)->campus_programs($this->campus_id)->first()->payment_items()->where('payment_year_id', $batch->id);
                $fees += $builder->where('name', 'TUTION')->sum('bybrid_amount') + $builder->where('name', 'REGISTRATION')->sum('amount');
            }elseif($this->program_status == "INTERNATIONAL"){
                $builder = $this->_class($batch->id)->campus_programs($this->campus_id)->first()->payment_items()->where('payment_year_id', $batch->id);
                $fees += $builder->where('name', 'TUTION')->sum('international_amount') + $builder->where('name', 'REGISTRATION')->sum('amount');
            }
        }
        
        $scholarships = $this->allScholarships($year);
        $extra_fees = ExtraFee::where('student_id', $this->id)->where('year_id', '<', $year+1)->sum('amount');
        $total_paid = Payments::where('student_id', $this->id)->sum('amount');
        
        $cumDebt = $fees + $extra_fees - $scholarships-> sum('amount') - $total_paid;
        return $cumDebt;



    }

    public function total_paid($year)
    {
        # code...

        $campus_program_levels = StudentClass::where('student_id', '=', $this->id)->where('year_id', '<=', $year)->distinct()
            ->join('campus_programs', ['campus_programs.program_level_id' => 'student_classes.class_id'])->pluck('campus_programs.id')->toArray();
        // fee amounts
        $fee_items = PaymentItem::whereIn('campus_program_id', $campus_program_levels)->where('name', 'TUTION')->pluck('id')->toArray();
        
        $payments = Payments::whereIn('payment_id', $fee_items)->where(['student_id' => $this->id])->where('batch_id', '<=', $year)->get();
        $fee_payments_sum = $payments->sum('amount');
        $debt_payments_sum = $payments->sum('debt');
        
        return $fee_payments_sum - $debt_payments_sum;
    }

    public function registered_courses($year_id = null)
    {
        # code...
        $year = $year_id == null ? Helpers::instance()->getCurrentAccademicYear() : $year_id;
        return $this->hasMany(StudentSubject::class, 'student_id')->WHERE('year_id', '=', $year);
    }
    
    public function course_pivot(){
        return $this->hasMany(StudentSubject::class, 'student_id');
    }

    public function transactions()
    {
        # code...
        return $this->hasMany(Transaction::class, 'student_id');
    }

    public function classDelegateOf()
    {
        # code...
        return $this->belongsToMany(ProgramLevel::class, ClassDelegate::class,'student_id', 'class_id');
    }

    public function feeClearance()
    {
        # code...
        return $this->hasOne(FeeClearance::class, 'student_id');
    }

    public function track(){
        return $this->hasOne(StudentTrack::class, 'student_id');
    }
}
