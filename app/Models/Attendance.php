<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'attendance';
    protected $fillable = ['year_id', 'campus_id', 'teacher_id', 'subject_id', 'check_in', 'check_out'];
    

    public function campus()
    {
        # code...
        return $this->belongsTo(Campus::class, 'campus_id');
    }

    public function teacher()
    {
        # code...
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function subject()
    {
        # code...
        return $this->belongsTo(Subjects::class, 'subject_id');
    }
}
