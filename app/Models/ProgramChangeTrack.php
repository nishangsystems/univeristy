<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramChangeTrack extends Model
{
    use HasFactory;

    protected $connection = "tracking_db";
    protected $table = "program_change_tracks";
    protected $fillable = ['former_class', 'current_class', 'student_id', 'user_id'];

    public function former_class(){
        return $this->belongsTo(ProgramLevel::class, 'former_class');
    }

    public function current_class(){
        return $this->belongsTo(ProgramLevel::class, 'current_class');
    }

    public function student(){
        return $this->belongsTo(Students::class, 'student_id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }
}
