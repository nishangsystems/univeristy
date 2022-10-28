<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassSubject extends Model
{
    use HasFactory;

    protected $fillable = ['class_id', 'coef', 'status', 'subject_id'];

    public function subject()
    {
        return $this->belongsTo(Subjects::class, 'subject_id');
    }

    public function class()
    {
        return $this->belongsTo(ProgramLevel::class, 'class_id');
    }

    /**
     * relationship between subject and notes
     */
    public function subjectNotes()
    {
        return $this->hasMany(SubjectNotes::class);
    }
}
