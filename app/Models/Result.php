<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_id', 'student_id', 'class_id', 'semester_id', 
        'subject_id', 'ca_score', 'exam_score', 'coef', 'remark',
        'class_subject_id', 'reference', 'user_id'
    ];
}
