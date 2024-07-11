<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampusProgramStatus extends Model
{
    use HasFactory;

    protected $fillable = ['campus_id', 'program_id', 'status'];
    protected $table = "campus_program_status";

    public function campus(){
        return $this->belongsTo(Campus::class, 'campus_id');
    }

    public function program(){
        return $this->belongsTo(SchoolUnits::class, 'program_id');
    }

    public function status(){
        return $this->belongsTo(Status::class, 'status', 'name');
    }
}
