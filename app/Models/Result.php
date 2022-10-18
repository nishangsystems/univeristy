<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_id', 'student_id', 'class_id', 'sequence', 
        'subject_id', 'score', 'coef', 'remark',
        'class_subject_id', 'reference'
    ];
}
