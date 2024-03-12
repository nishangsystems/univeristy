<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class ExamCourseCode extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['course_id', 'course_code', 'exam_code', 'year_id'];

    public function year()
    {
        # code...
        return $this->belongsTo(Batch::class, 'year_id');
    }

    public function course()
    {
        # code...
        return $this->belongsTo(Subjects::class, 'course_id');
    }

    public function results()
    {
        # code...
        $year = \App\Helpers\Helpers::instance()->getCurrentAccademicYear();
        return $this->hasMany(Result::class, 'subject_id', 'course_id');
    }

}
