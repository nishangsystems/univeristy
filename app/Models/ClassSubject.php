<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassSubject extends Model
{
    use HasFactory;

    protected $fillable = ['class_id','coef', 'subject_id'];

    public function subject(){
        return $this->belongsTo(Subjects::class, 'subject_id');
    }

    public function class(){
        return $this->belongsTo(SchoolUnits::class, 'class_id');
    }

}
