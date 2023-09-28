<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAttendance extends Model
{
    protected $table = 'student_attendance';
    
    use HasFactory;

    public function dailyAttendance()
    {
        # code...
        return $this->belongsTo(DailyAttendance::class, 'attendance');
    }
}
