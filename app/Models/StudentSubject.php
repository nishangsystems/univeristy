<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentSubject extends Model
{
    use HasFactory;

    protected $fillable = ['student_id', 'semester_id', 'course_id', 'year_id', 'resit_id', 'paid'];

    protected $table = 'student_courses';
    protected $connection = 'mysql';
    
    public function student()
    {
        # code...
        return $this->belongsTo(Students::class, 'student_id');
    }

    public function subject()
    {
        # code...
        return $this->belongsTo(Subjects::class, 'course_id');
    }

}
