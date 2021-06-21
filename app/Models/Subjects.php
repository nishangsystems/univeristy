<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subjects extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'coef'];

    public function units()
    {
        return  $this->belongsToMany(SchoolUnits::class, 'class_subjects', 'subject_id', 'class_id');
    }

    /**
     * relationship between subject and notes
     */
    public function subjectNotes()
    {
        return $this->hasMany(SubjectNotes::class);
    }
}
