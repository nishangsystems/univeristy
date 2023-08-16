<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProgramLevel extends Model
{
    use HasFactory;
    
    protected $fillable = ['program_id', 'level_id', 'resit_cost'];
    protected $connection = 'mysql';
    
    public function students()
    {
        return $this->hasMany(Students::class, 'program_id');
    }
    public function _students($year)
    {
        return $this->belongsToMany(Students::class, 'student_classes', 'class_id', 'student_id')->where('year_id', '=', $year);
    }
    public function program()
    {
        return $this->belongsTo(SchoolUnits::class, 'program_id');
    }

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function campuses()
    {
        return $this->belongsToMany(Campus::class, CampusProgram::class, 'program_level_id', 'campus_id');
    }

    public function class_subjects()
    {
        return $this->hasMany(ClassSubject::class, 'class_id');
    }
    

    public function class_subjects_by_semester($semester_id)
    {
        return $this->hasMany(ClassSubject::class, 'class_id')->join('subjects', ['subjects.id'=>'class_subjects.subject_id'])->where('subjects.semester_id', '=', $semester_id)->get(['class_subjects.*']);
    }

    public function subjects()
    {
        return $this->belongsToMany( Subjects::class, ClassSubject::class, 'class_id', 'subject_id');
    }

    public function name()
    {
        # code...
        // dd ($this->program->name.' : Level '.$this->level->level);

        return (SchoolUnits::find($this->program_id)->name??'').' : Level '.Level::find($this->level_id)->level;
    }


    public function campus_programs($campus_id = null)
    {
        # code...
        return $this->hasMany(CampusProgram::class, 'program_level_id')
            ->where(function ($qr) use ($campus_id) {
                $campus_id == null ? null : $qr->where(['campus_id' => $campus_id]);
                });
    }


    public function _campus_programs()
    {
        # code...
        return $this->hasMany(CampusProgram::class, 'program_level_id');
    }

    public function resit_cost_isset()
    {
        return $this->resit_cost == null;
    }
}
