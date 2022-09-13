<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'batch_id',
        'course_id',
        'type'

    ];
}
