<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Departments extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'school_id'
    ];

    public function teachers()
    {
        return $this->hasManyThrough(User::class, DepartmentTeacher::class, 'user_id');
    }
    public function HOD()
    {
        return $this->hasOne(HeadOfDepartment::class);
    }
}
