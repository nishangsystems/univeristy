<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campus extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'telephone',
        'school_id'
    ];

    public function programs()
    {
        return $this->hasManyThrough(Program::class, CampusProgram::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'campus_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'campus_id');
    }
}
