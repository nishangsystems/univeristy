<?php

namespace App\Models;

use App\Helpers\Helpers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Students extends Authenticatable
{
    use HasFactory;

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
        'imported'
    ];

    public function extraFee($year_id)
    {
        $builder = $this->hasMany(ExtraFee::class, 'student_id')->where('year_id', '=', $year_id);
        return $builder->count() == 0 ? null : $builder->first();
    }

    public function class($year)
    {
        return CampusProgram::where('campus_id', $this->campus_id)->where('program_level_id', $this->classes(Helpers::instance()->getCurrentAccademicYear())->first()->class_id)->first();
    }

    public function _class($year)
    {
        return $this->belongsToMany(ProgramLevel::class, 'student_classes', 'student_id', 'class_id')->where('year_id', '=', $year)->first();
    }

    public function classes()
    {
        return $this->hasMany(StudentClass::class, 'student_id');
    }

    public function result()
    {
        return $this->hasMany(Result::class, 'student_id');
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

    public function total()
    {
        if ($this->classes()->where('year_id', Helpers::instance()->getCurrentAccademicYear())->first() != null) {
            # code...
            return $this->campus()->first()->campus_programs()->where('program_level_id', $this->classes()->where('year_id', Helpers::instance()->getCurrentAccademicYear())->first()->class_id ?? 0)->first()->payment_items()->first()->amount ?? -1;
        }
        return 0;
    }

    public function paid()
    {
        $items = $this->payments()->selectRaw('COALESCE(sum(amount),0) total')->where('batch_id', Helpers::instance()->getYear())->get();
        return $items->first()->total;
    }

    public function bal($student_id = null, $year = null)
    {
        $year = $year == null ? Helpers::instance()->getCurrentAccademicYear() : $year;
        $scholarship = Helpers::instance()->getStudentScholarshipAmount($this->id);
        return $this->total() + $this->total_debts($year) + ($this->extraFee($year) == null ? 0 : $this->extraFee($year)->amount) - $this->paid() - ($scholarship);
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

    public function debt($year)
    {
        # code...
        $paymentBuilder = Payments::where(['student_id'=>$this->id, 'batch_id'=>$year]);
        if($paymentBuilder->count() == 0){return 0;}
        return $paymentBuilder->orderBy('id', 'DESC')->first()->debt;
    }

    public function total_debts($year)
    {
        # code...
        $student = Students::find($this->id);

        $student_class_instances = StudentClass::where('student_id', '=', $this->id)->where('year_id', '<', $year)->get();
        $campus_program_levels = StudentClass::where('student_id', '=', $this->id)->where('year_id', '<', $year)->distinct()
            ->join('campus_programs', ['campus_programs.program_level_id' => 'student_classes.class_id'])->get();
        // fee amounts
        $fee_items = PaymentItem::whereIn('campus_program_id', $campus_program_levels->pluck('id'))->get();
        $fee_items_sum = $fee_items->sum('amount');
        
        $fee_payments_sum = Payments::whereIn('payment_id', $fee_items->pluck('id'))->where(['student_id' => $this->id])->where('batch_id', '<', $year)->sum('amount');
        $fee_debts_sum = Payments::whereIn('payment_id', $fee_items->pluck('id'))->where(['student_id' => $this->id])->where('batch_id', '<', $year)->sum('debt');
        $next_debt = $fee_items_sum + $fee_debts_sum - $fee_payments_sum;

        return $next_debt;
    }


    public function total_paid($year)
    {
        # code...

        $campus_program_levels = StudentClass::where('student_id', '=', $this->id)->where('year_id', '<=', $year)->distinct()
            ->join('campus_programs', ['campus_programs.program_level_id' => 'student_classes.class_id'])->get();
        // fee amounts
        $fee_items = PaymentItem::whereIn('campus_program_id', $campus_program_levels->pluck('id'))->get();
        
        $fee_payments_sum = Payments::whereIn('payment_id', $fee_items->pluck('id'))->where(['student_id' => $this->id])->where('batch_id', '<=', $year)->sum('amount');
        $debt_payments_sum = Payments::whereIn('payment_id', $fee_items->pluck('id'))->where(['student_id' => $this->id])->where('batch_id', '<=', $year)->sum('debt');
        
        return $fee_payments_sum - $debt_payments_sum;
    }

}
