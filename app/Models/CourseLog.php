<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseLog extends Model
{
    use HasFactory;

    protected $table = 'course_log';
    protected $fillable = ['topic_id', 'attendance_id', 'campus_id', 'details'];

    public function attendance(){
        return $this->belongsTo(Attendance::class, 'attendance_id');
    }
}
