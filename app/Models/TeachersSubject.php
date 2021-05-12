<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeachersSubject extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'subject_id',
        'batch_id',
        'class_id',
    ];

    public function subject(){
        return $this->belongsTo(ClassSubject::class, 'subject_id');
    }

    public function class(){
        return  $this->belongsTo(SchoolUnits::class, 'class_id');
    }

    public function user(){
        return  $this->belongsTo(User::class, 'teacher_id');
    }
}
