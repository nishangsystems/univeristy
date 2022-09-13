<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillabe = [
        'name',
        'code',
        'credit_value',
        'type',
        'status' ,//[could be available or unavailable ],
        'program_id',
        'school_level_id'
    ];

    public function notes()
    {
        return $this->hasMany(CourseDocument::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function teacher()
    {
        return $this->hasManyThrough(User::class, TeacherCourse::class);
    }

    public function results($year)
    {
        return $this->hasMany(Result::class)->where('batch_id', $year);
    }

    public function student_results($year, $student_id)
    {
        return $this->hasMany(Result::class)->where('batch_id', $year)->where('student_id', $student_id);
    }

    public function students()
    {
        # code...
    }
}
