<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subjects extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'coef', 'level_id', 'semester_id', 'status'];

    public function units()
    {
        return  $this->belongsToMany(SchoolUnits::class, 'class_subjects', 'subject_id', 'class_id');
    }
}
