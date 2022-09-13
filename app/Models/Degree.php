<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Degree extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'duration',
        'school_id'
    ];

    public function semesters()
    {
        return $this->hasMany(SchoolSemester::class);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

}
