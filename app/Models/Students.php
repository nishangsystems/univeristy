<?php

namespace App\Models;

use App\Helpers\Helpers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\HasApiTokens;

class Students extends Authenticatable
{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'gender',
        'username',
        'matric',
        'dob',
        'pob',
        'campus_id',
        'admission_batch_id',
        'password',
        'parent_name',
        'program_id',
        'parent_phone_number',
        'imported',
        'active'
    ];

    protected $connection = 'mysql';

    public function extraFee($year_id)
    {
        $builder = $this->hasMany(ExtraFee::class, 'student_id')->where('year_id', '=', $year_id);
        return $builder->count() == 0 ? null : $builder->first();
    }
    
    public function _class($year=null)
    {
        
        $data =  $this->belongsToMany(ProgramLevel::class, 'student_classes', 'student_id', 'class_id');
        if($year == null)
            return $this->belongsToMany(ProgramLevel::class, 'student_classes', 'student_id', 'class_id')->where('student_classes.year_id', '<=', Helpers::instance()->getCurrentAccademicYear())->orderByDesc('student_classes.year_id')->first();
        else
            return  $this->belongsToMany(ProgramLevel::class, 'student_classes', 'student_id', 'class_id')->where('student_classes.year_id', '=', $year)->orderByDesc('student_classes.year_id')->first();
    }

    public function class($year)
    {
        // return CampusProgram::where('campus_id', $this->campus_id)->where('program_level_id', $this->_class($year)->id)->first();
        return $this->_class($year)->campus_programs($this->campus_id)->first() ?? null;
    }

    public function classes()
    {
        return $this->hasMany(StudentClass::class, 'student_id');
    }

    public function result()
    {
        return $this->hasMany(Result::class, 'student_id');
    }

    public function offline_result()
    {
        return $this->hasMany(OfflineResult::class, 'student_id');
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
            # code...
            // return $this->campus()->first()->campus_programs()->where(['program_level_id' => $this->_class(Helpers::instance()->getCurrentAccademicYear())->id ?? 0, 'campus_id'=>$this->campus_id])->first()->payment_items()->first()->amount ?? -1;
            $rec = $this->_class($year)->campus_programs($this->campus_id)->first()->payment_items()->where(['year_id'=>$year, 'name'=>'TUTION'])->first();
            return $rec ? $rec->amount : 0;
        }
        
        return 0;
    }

    public function paid($year = null) // fee paid for current academic year
    {
        $year = $year == null ? Helpers::instance()->getYear() : $year;
        $items = $this->payments()->where('batch_id', $year)->selectRaw('COALESCE(sum(amount),0) total')->get();
        return $items->first()->total;
    }

    // current year's unpaid fee
    public function bal($student_id = null, $year = null)
    {
        $year = $year == null ? Helpers::instance()->getCurrentAccademicYear() : $year;
        $scholarship = Helpers::instance()->getStudentScholarshipAmount($this->id, $year);
        // dd($scholarship);
        return $this->total() + ($this->extraFee($year) == null ? 0 : $this->extraFee($year)->amount) - $this->paid() - ($scholarship);

    }

    // current year's unpaid fee
    public function total_balance($student_id = null, $year = null)
    {
        $year = $year == null ? Helpers::instance()->getCurrentAccademicYear() : $year;
        $scholarship = Helpers::instance()->getStudentScholarshipAmount($this->id);
        return $this->total($year) + $this->total_debts($year-1) + ($this->extraFee($year) == null ? 0 : $this->extraFee($year)->amount) - $this->paid($year) - ($scholarship);
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
    
    public function ca_score($course_id, $class_id, $year_id, $semester_id = null)
    {
        # code...
        $semester = $semester_id == null ? Helpers::instance()->getSemester($class_id)->id : $semester_id;
        $record = Result::where(['student_id' => $this->id, 'subject_id' => $course_id, 'class_id' => $class_id, 'batch_id' => $year_id, 'semester_id'=>$semester])->first() ?? null;
        if ($record != null) {
            # code...
            return $record->ca_score ?? '';
        }
        return '';
    }
    
    public function offline_ca_score($course_id, $class_id, $year_id, $semester_id = null)
    {
        # code...
        $semester = $semester_id == null ? Helpers::instance()->getSemester($class_id)->id : $semester_id;
        $record = OfflineResult::where(['student_id' => $this->id, 'subject_id' => $course_id, 'class_id' => $class_id, 'batch_id' => $year_id, 'semester_id'=>$semester])->first() ?? null;
        if ($record != null) {
            # code...
            return $record->ca_score ?? '';
        }
        return '';
    }
    
    public function exam_score($course_id, $class_id, $year_id, $semester_id = null)
    {
        # code...
        $semester = $semester_id == null ? Helpers::instance()->getSemester($class_id)->id : $semester_id;
        $record = Result::where(['student_id' => $this->id, 'subject_id' => $course_id, 'class_id' => $class_id, 'batch_id' => $year_id, 'semester_id'=>$semester])->first() ?? null;
        if ($record != null) {
            # code...
            return $record->exam_score ?? '';
        }
        return '';
    }
    
    public function offline_exam_score($course_id, $class_id, $year_id, $semester_id = null)
    {
        # code...
        $semester = $semester_id == null ? Helpers::instance()->getSemester($class_id)->id : $semester_id;
        $record = OfflineResult::where(['student_id' => $this->id, 'subject_id' => $course_id, 'class_id' => $class_id, 'batch_id' => $year_id, 'semester_id'=>$semester])->first() ?? null;
        if ($record != null) {
            # code...
            return $record->exam_score ?? '';
        }
        return '';
    }

    public function total_score($course_id, $class_id, $year_id, $semester_id = null)
    {
        # code...
        $semester = $semester_id == null ? Helpers::instance()->getSemester($class_id)->id : $semester_id;
        $record = Result::where(['student_id' => $this->id, 'subject_id' => $course_id, 'class_id' => $class_id, 'batch_id' => $year_id, 'semester_id'=>$semester])->first() ?? null;
        if ($record != null) {
            # code...
            return ($record->ca_score ?? 0) + ($record->exam_score ?? 0);
        }
        return '';
    }

    public function offline_total_score($course_id, $class_id, $year_id, $semester_id = null)
    {
        # code...
        $semester = $semester_id == null ? Helpers::instance()->getSemester($class_id)->id : $semester_id;
        $record = OfflineResult::where(['student_id' => $this->id, 'subject_id' => $course_id, 'class_id' => $class_id, 'batch_id' => $year_id, 'semester_id'=>$semester])->first() ?? null;
        if ($record != null) {
            # code...
            return ($record->ca_score ?? 0) + ($record->exam_score ?? 0);
        }
        return '';
    }

    public function offline_grade($course_id, $class_id, $year_id, $semester_id = null)
    {
        # code...
        $grades = \App\Models\ProgramLevel::find($class_id)->program->gradingType->grading->sortBy('grade') ?? [];

        if(count($grades) == 0){return '-';}

        $score = $this->offline_total_score($course_id, $class_id, $year_id, $semester_id);
        if ($score != '') {
            # code...
            foreach ($grades as $key => $grade) {
                if ($score >= $grade->lower && $score <= $grade->upper) {return $grade->grade;}
            }
        }
        return '';
    } 

    public function grade($course_id, $class_id, $year_id, $semester_id = null)
    {
        # code...
        $grades = \App\Models\ProgramLevel::find($class_id)->program->gradingType->grading->sortBy('grade') ?? [];

        if(count($grades) == 0){return '-';}

        $score = $this->total_score($course_id, $class_id, $year_id, $semester_id);
        if ($score != '') {
            # code...
            foreach ($grades as $key => $grade) {
                if ($score >= $grade->lower && $score <= $grade->upper) {return $grade->grade;}
            }
        }
        return '';
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
        $fee_items = [];
         foreach (Batch::where('id', '<', $year+1)->get() as $key => $batch) {
            # code...
            if(($class = $this->_class($batch->id))== null)continue;
            $fee_items[] = $this->_class($batch->id)->campus_programs($this->campus_id)->first()->payment_items()->where('year_id', $batch->id)->first();
        }
        $fee_items = collect($fee_items);

        $extra_fees = ExtraFee::where('year_id', '<', $year+1)->where('student_id', $this->id)->sum('amount');
        $payments = Payments::where('student_id', $this->id)->where('payment_year_id', '<', $year+1)->whereIn('payment_id', $fee_items->pluck('id')->toArray())->sum(DB::raw('amount - debt'));
        $scholarships = StudentScholarship::where('student_id', $this->id)->where('batch_id', '<', $year+1)->sum('amount');
        $fees = $fee_items->sum('amount');

        $debt = $fees + $extra_fees - ($payments + $scholarships);
        return $debt;

    }

    public function total_paid($year)
    {
        # code...

        $campus_program_levels = StudentClass::where('student_id', '=', $this->id)->where('year_id', '<=', $year)->distinct()
            ->join('campus_programs', ['campus_programs.program_level_id' => 'student_classes.class_id'])->pluck('campus_programs.id')->toArray();
        // fee amounts
        $fee_items = PaymentItem::whereIn('campus_program_id', $campus_program_levels)->pluck('id')->toArray();
        
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

    
}
