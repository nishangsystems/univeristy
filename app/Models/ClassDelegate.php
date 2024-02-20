<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassDelegate extends Model
{
    use HasFactory;

    protected $fillable = ['campus_id', 'class_id', 'student_id', 'status', 'year_id'];

    public function campus(){
        return $this->belongsTo(Campus::class, 'campus_id');
    }

    public function class(){
        return $this->belongsTo(ProgramLevel::class, 'class_id');
    }

    public function student(){
        return $this->belongsTo(Students::class, 'student_id');
    }
    public function year(){
        return $this->belongsTo(Batch::class, 'year_id');
    }
}
