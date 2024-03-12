<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassUpdate extends Model
{
    use HasFactory;

    protected $fillable = ['former_class', 'current_class', 'user_id', 'student_id'];

    public function formerClass()
    {
        # code...
        return $this->belongsTo(ProgramLevel::class, 'former_class');
    }

    public function currentClass()
    {
        # code...
        return $this->belongsTo(ProgramLevel::class, 'current_class');
    }

    public function user()
    {
        # code...
        return $this->belongsTo(User::class, 'user_id');
    }

    public function student()
    {
        # code...
        return $this->belongsTo(Student::class, 'user_id');
    }
}
