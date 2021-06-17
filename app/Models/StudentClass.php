<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentClass extends Model
{
    use HasFactory;

    use \Staudenmeir\LaravelCte\Eloquent\QueriesExpressions;

    protected $fillable = ['student_id', 'class_id', 'year_id'];

    public function class()
    {
        return $this->belongsTo(SchoolUnits::class, 'class_id');
    }

    public function student()
    {
        return $this->belongsTo(Students::class, 'student_id');
    }

    /**
     * relationship between a class the payments mad e for an income(payincome)
     */
    public function payIncomes()
    {
        return $this->hasMany(PayIncome::class, 'student_class_id');
    }
}
