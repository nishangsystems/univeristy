<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamResultDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'mark',
        'exam_result_id'
    ];
}
