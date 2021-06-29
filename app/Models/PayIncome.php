<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayIncome extends Model
{
    use HasFactory;

    protected $fillable = [
        'income_id',
        'batch_id',
        'class_id',
        'student_id'

    ];

    /**
     * relationship between payments made for an income and the income type
     */
    public function income()
    {
        return $this->belongsTo(Income::class, 'income_id');
    }

    /**
     * relationship between a class the payments mad e for an income(payincome)
     */
    public function studentClass()
    {
        return $this->belongsTo(StudentClass::class, 'student_class_id');
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class, 'batch_id');
    }
}
