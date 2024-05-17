<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BulkMarkChange extends Model
{
    use HasFactory;

    protected $connection = "tracking_db";
    protected $table = "bulk_mark_changes";
    protected $fillable = ['year_id', 'semester_id', 'course_id', 'class_id', 'action', 'additional_mark', 'actor', 'interval'];

    public function year(){
        return $this->belongsTo(Batch::class, 'year_id');
    }

    public function semester(){
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    public function course(){
        return $this->belongsTo(Subjects::class, 'course_id');
    }

    public function class(){
        return $this->belongsTo(ProgramLevel::class, 'class_id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'actor');
    }
}
