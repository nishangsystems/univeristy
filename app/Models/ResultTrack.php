<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResultTrack extends Model
{
    use HasFactory;

    protected $connection = "tracking_db";
    protected $table = "result_tracks";
    protected $fillable = ['student_id', 'batch_id', 'semester_id', 'course_id', 'action', 'actor', 'data'];

    public function student(){
        return $this->belongsTo(Students::class, 'student_id');
    }

    public function year(){
        return $this->belongsTo(Batch::class, 'batch_id');
    }

    public function semester(){
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    public function course(){
        return $this->belongsTo(Subjects::class, 'course_id');
    }

    public function change(){
        return json_decode($this->data);
    }

    public function user(){
        return $this->belongsTo(User::class, 'actor');
    }
}
