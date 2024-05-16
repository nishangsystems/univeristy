<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentTrack extends Model
{
    use HasFactory;

    protected $connection = "tracking_db";
    protected $table = "student_tracks";
    protected $fillable = ['student_id', 'class_id', 'action', 'actor', 'year_id'];
    
    public function student(){
        return $this->belongsTo(Students::class, 'student_id');
    }
    
    public function class(){
        return $this->belongsTo(ProgramLevel::class, 'class_id');
    }
    
    public function user(){
        return $this->belongsTo(User::class, 'actor');
    }
    
    public function year(){
        return $this->belongsTo(Batch::class, 'year_id');
    }
}
