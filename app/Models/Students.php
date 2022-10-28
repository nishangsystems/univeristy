<?php

namespace App\Models;

use App\Helpers\Helpers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
//use App\Models\studentInfo;
class Students extends Authenticatable
{
    use HasFactory;
    
    protected $guard = 'student';
    
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
        'parent_phone_number'
    ];

    public function class($year)
    {
        
        return CampusProgram::where('campus_id', $this->campus_id)->where('program_level_id', $this->program_id)->first();
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

    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }
    
    public function studentInfo()
    {
      $batch = \Auth::guard('student')->user()->admission_batch_id;
      
      $data_year =  Batch::where('id', $batch)->first();
      
        return $data_year;
    }

    public function total()
    {
        return $this->campus()->first()->campus_programs()->where('program_level_id', $this->program_id)->first()->payment_items()->first()->amount ?? -1;
    }

    public function paid()
    {
        $items = $this->payments()->selectRaw('COALESCE(sum(amount),0) total')->where('batch_id', Helpers::instance()->getYear())->get();
        return $items->first()->total;
    }

    public function bal($student_id)
    {
        $scholarship = Helpers::instance()->getStudentScholarshipAmount($student_id);
        return $this->total() - $this->paid() - ($scholarship);
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


}
