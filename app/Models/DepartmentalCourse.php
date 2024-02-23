<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepartmentalCourse extends Model
{
    use HasFactory;

    protected $fillable = ['school_unit_id', 'subject_id'];

    public function department(Type $var = null)
    {
        # code...\
       return $this->belongsTo(SchoolUnits::class, 'school_unit_id'); 
    }

    public function course(Type $var = null)
    {
        # code...\
       return $this->belongsTo(Subject::class, 'subject_id'); 
    }
}
