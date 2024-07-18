<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentStatusChangeTrack extends Model
{
    use HasFactory;

    protected $connection = "tracking_db";
    protected $table = "student_status_tracks";
    protected $fillable = ['student_id', 'state', 'user_id', 'reason'];

    public function student()
    {
        return $this->belongsTo(Students::class, 'student_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
