<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepartmentTeacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'department_id'
    ];

    public function department()
    {
        return $this->belongsTo(Departments::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
