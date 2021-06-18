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
        'dob',
        'pob',
        'admission_batch_id',
        'password',
    ];

    public function class($year)
    {
        return $this->belongsToMany(SchoolUnits::class, 'student_classes', 'student_id', 'class_id')->where('year_id', $year)->first();
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

    public function total()
    {
        return $this->class(Helpers::instance()->getYear())->fee();
    }

    public function paid()
    {
        $items = $this->payments()->selectRaw('COALESCE(sum(amount),0) total')->where('batch_id', Helpers::instance()->getYear())->get();
        return $items->first()->total;
    }

    public function bal()
    {
        return $this->total() - $this->paid();
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

    /**
     * relationship between users(students) and scholarship
     */
    public function scholarships()
    {
        return $this->hasMany(Scholarship::class);
    }
}
