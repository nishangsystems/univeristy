<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramExamGrading extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_id',
        'exam_grading_id'
    ];
}
