<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    use HasFactory;
    protected $fillable = ['teacher_subject_id', 'subject_id', 'title', 'duration', 'level', 'parent_id', 'week', 'teacher_id', 'campus_id'];

    protected $connection = 'mysql';
    public function teacherSubject()
    {
        return $this->belongsTo(TeachersSubject::class, 'teacher_subject_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subjects::class, 'subject_id');
    }

    public function parent()
    {
        return $this->belongsTo(Topic::class, 'parent_id');
    }
    
    public function teacher()
    {
        # code...
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
