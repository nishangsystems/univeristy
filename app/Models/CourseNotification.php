<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseNotification extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'title', 'message', 'date', 'status', 'course_id', 'campus_id'];
    protected $table = 'course_notifications';

    public function campus()
    {
        # code...
        return $this->belongsTo(Campus::class, 'campus_id');
    }


    public function created_by()
    {
        # code...
        return $this->belongsTo(User::class, 'user_id');
    }

    public function created_at()
    {
        # code...
        return $this->belongsTo(Subjects::class, 'course_id');
    }
}
