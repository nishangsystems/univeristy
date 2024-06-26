<?php

namespace App\Models;

use App\Helpers\Helpers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class ClassSubject extends Model
{
    use HasFactory, SoftDeletes;
    protected $connection = 'mysql';
    
    protected $fillable = ['class_id', 'coef', 'status', 'subject_id', 'hours', 'deleted_at'];

    public function subject()
    {
        return $this->belongsTo(Subjects::class, 'subject_id');
    }

    public function teachers()
    {
        # code...
        return $this->belongsToMany(User::class, TeachersSubject::class, 'subject_id', 'teacher_id');
    }

    public function course_master()
    {
        # code...
        return $this->belongsToMany(User::class, TeachersSubject::class, 'subject_id', 'teacher_id')->where('teachers_subjects.is_master', 1)->first();
        return User::join('teachers_subjects', ['teachers_subjects.teacher_id'=>'users.id'])
            ->where(['teachers_subjects.is_master'=> 1, 'teachers_subjects.class_id'=>$this->class_id, 'teachers_subjects.subject_id'=])->first();
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

    public function notifications()
    {
        return $this->hasMany(CourseNotification::class, 'course_id');
    }

    public function offline_results()
    {
        return $this->results();
        // return $this->hasMany(OfflineResult::class, 'class_subject_id');
    }


    public function results()
    {
        return $this->hasMany(Result::class, 'class_subject_id');
    }

    public function offline_passed_with_grade($grade, $year_id, $semester_id = null)
    {
        return $this->passed_with_grade($grade, $year_id, $semester_id);
    }

    public function passed_with_grade($grade, $year_id, $semester_id = null)
    {
        $grade = \App\Models\ProgramLevel::find($this->class_id)->program->gradingType->grading->where('grade', $grade)->first() ?? null;
        $semester = $semester_id == null ? Helpers::instance()->getSemester($this->class_id)->id : $semester_id;
        $results = $this->hasMany(Result::class, 'class_subject_id')->where(['semester_id'=>$semester, 'batch_id'=>$year_id])->get();
        $count = 0;
        foreach ($results as $key => $result) {
            $score = ($result->ca_score??0 ) + ($result->exam_score ?? 0);
            if( $score >= $grade->lower && $score <= $grade->upper){++$count;}
        }
        return $count;
    }

    public function offline_passed($year_id, $semester_id = null)
    {
        
        return $this->passed($year_id, $semester_id);
    }

    public function passed($year_id, $semester_id = null)
    {
        $semester = $semester_id == null ? Helpers::instance()->getSemester($this->class_id)->id : $semester_id;
        $results = $this->hasMany(Result::class, 'class_subject_id')->where(['semester_id'=>$semester, 'batch_id'=>$year_id])->get();
        $count = 0;
        foreach ($results as $key => $result) {
            $score = $result->ca_score + $result->exam_score ?? 0;
            if($score >= 50){++$count;}
        }
        return $count;
    }

}
