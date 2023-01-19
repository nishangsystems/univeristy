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

    public function total()
    {
        return $this->ca_score ?? 0 + $this->exam_score ?? 0;
    }

    public function passed()
    {
        $prog = ProgramLevel::find($this->class_id)->program;
        return ($this->ca_score ?? 0 + $this->exam_score ?? 0) >= ($prog->ca_total + $prog->exam_total)*0.5;
    }

}
