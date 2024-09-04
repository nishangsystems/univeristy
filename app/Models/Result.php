<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $fillable = [
        'batch_id', 'student_id', 'class_id', 'semester_id',
        'subject_id', 'ca_score', 'exam_score', 'exam_code', 'coef', 'remark',
        'class_subject_id', 'reference', 'user_id', 'campus_id', 'published'
    ];

    public function year()
    {
        # code...
        return $this->belongsTo(Batch::class, 'batch_id');
    }

    public function semester()
    {
        # code...
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    public function student()
    {
        # code...
        return $this->belongsTo(Students::class, 'student_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subjects::class, 'subject_id');
    }

    public function class_subject()
    {
        return $this->belongsTo(ClassSubject::class, 'class_subject_id');
    }

    public function total()
    {
        return $this->ca_score ?? 0 + $this->exam_score ?? 0;
    }

    public function passed()
    {
        $prog = ProgramLevel::find($this->class_id)->program;
        return ($this->ca_score ?? 0 + $this->exam_score ?? 0) >= ($prog->ca_total + $prog->exam_total)*0.5;
    }

    public function grade()
    {
        # code...
        $grades = \App\Models\ProgramLevel::find($this->class_id)->program->gradingType->grading->sortBy('grade') ?? [];

        if(count($grades) == 0){return '-';}

        $score = $this->ca_score ?? 0 + $this->exam_score ?? 0;
        if (!$score == 0) {
            # code...
            foreach ($grades as $key => $grade) {
                if ($score >= $grade->lower && $score <= $grade->upper) {return $grade;}
            }
        }
        return '';
    } 

    public function _class(){
        return $this->belongsTo(ProgramLevel::class, 'class_id');
    }
}
