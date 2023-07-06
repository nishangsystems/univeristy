<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseLog extends Model
{
    use HasFactory;

    protected $table = 'course_log';
    protected $connection = 'mysql';
    protected $fillable = ['topic_id', 'attendance_id', 'campus_id', 'details', 'year_id'];

    public function attendance(){
        return $this->belongsTo(Attendance::class, 'attendance_id');
    }

    public function topic(){
        return $this->belongsTo(Topic::class, 'topic_id');
    }
}
