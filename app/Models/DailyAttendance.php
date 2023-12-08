<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyAttendance extends Model
{
    use HasFactory;
    protected $fillable = ['course_id', 'teacher_id', 'period_id', 'year'];

    public function attedance(){
        return $this->hasMany(StudentAttendance::class, "attendance");
    }
}
