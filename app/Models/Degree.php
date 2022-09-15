<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Degree extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
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

    public function information()
    {
        return $this->hasMany(SchoolDocument::class);
    }

    public function programs()
    {
        return $this->hasMany(Program::class);
    }
}
