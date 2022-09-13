<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramCourse extends Model
{
    use HasFactory;

    protected $fillable = ['program_id', 'course_id', 'school_level_id', 'degree_semester_id'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function semester()
    {
        return $this->belongsTo(DegreeSemester::class);
    }

    public function level()
    {
        return $this->belongsTo(SchoolLevel::class);
    }
}
