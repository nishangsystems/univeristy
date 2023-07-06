<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NonGPACourse extends Model
{
    use HasFactory;
     protected $table = "non_gpa_courses";
     protected $fillable = ['background_id', 'course_code'];
     protected $connection = 'mysql';

    public function background()
    {
        # code...
        return $this->belongsTo(Background::class, 'background_id');
    }

    public function course()
    {
        # code...
        return $this->belongsTo(Subject::class, 'course_code');
    }
}
