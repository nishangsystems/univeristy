<?php

namespace App\Models;

use App\Helpers\Helpers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subjects extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'coef', 'level_id', 'semester_id', 'status', 'objective'];

    protected $connection = 'mysql';
    public function units()
    {
        return  $this->belongsToMany(SchoolUnits::class, 'class_subjects', 'subject_id', 'class_id')->whereNull('class_subjects.deleted_at');
    }

    
    public function classes()
    {
        return  $this->belongsToMany(ProgramLevel::class, ClassSubject::class, 'subject_id', 'class_id');
    }

    
    public function class_subject()
    {
        return  $this->hasMany(ClassSubject::class, 'subject_id');
    }

    public function _class_subject($class_id)
    {
        return  $this->hasMany(ClassSubject::class, 'subject_id')->where(['class_id'=>$class_id])->first();
    }

    public function student_subjects()
    {
        return $this->hasMany(StudentSubject::class, 'course_id');
    }

    public function students($year_id = null)
    {
        # code...
        $year = $year_id ?? Helpers::instance()->getCurrentAccademicYear();
        return $this->hasManyThrough(Students::class, StudentSubject::class, 'student_id', 'id');
    }

    public function semester()
    {
        # code...
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    public function level()
    {
        # code...
        return $this->belongsTo(Level::class, 'level_id');
    }

    public function teachers()
    {
        # code...
        return $this->belongsToMany(User::class, TeachersSubject::class, 'subject_id', 'teacher_id');
    }

}
