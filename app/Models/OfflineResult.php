<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfflineResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_id', 'student_id', 'class_id', 'semester_id', 
        'subject_id', 'ca_score', 'exam_score', 'coef', 'remark',
        'class_subject_id', 'reference', 'user_id'
    ];
    protected $connection = 'mysql';

    protected $table = 'offline_results';
    
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
                if ($grade->lower <= $score && $grade->upper >= $score) {return $grade;}
            }
        }
        return '';
    } 

}
