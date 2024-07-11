<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;

    protected $fillable = ['name'];
    protected $table = "status";

    public function campuses($program_id = null){
        return $program_id == null ? 
            $this->belongsToMany(Campus::class, CampusProgramStatus::class, 'campus_id', 'status', 'id', 'name') :
            $this->belongsToMany(Campus::class, CampusProgramStatus::class, 'campus_id', 'status', 'id', 'name')->where('campus_program_status.program_id', $program_id) ;
    }

    public function programs($campus_id = null){
        return $campus_id == null ? 
            $this->belongsToMany(SchoolUnits::class, CampusProgramStatus::class, 'program_id', 'status', 'id', 'name') :
            $this->belongsToMany(SchoolUnits::class, CampusProgramStatus::class, 'program_id', 'status', 'id', 'name')->where('campus_program_status.campus_id', $campus_id) ;
    }
}
