<?php

namespace App\Services;

use App\Helpers\Helpers as Helpers;
use App\Models\ProgramLevel;
use App\Models\School;
use App\Models\SchoolUnits;
use App\Models\Students;
use App\Models\User;

class HeadOfSchoolService{
    
    protected $current_accademic_year;
    public function __construct($current_accademic_year)
    {
        # code...
        $this->current_accademic_year = $current_accademic_year;
    }

    public function getSchoolUnitById($id)
    {
        # code...
        return SchoolUnits::find($id);
    }

    public function getClassById($id)
    {
        # code...
        return ProgramLevel::find($id);
    }
    
    public function schools($user_id)
    {
        # code...
        $user = User::find($user_id);
        $schools = $user->headOfSchoolFor(1)->get();
        return $schools;
    }
    
    public function school_students($school_id, $active = 1, $year = null, $level_id = null)
    {
        $year = $year == null ? $this->current_accademic_year : $year;
        # code...
        $school = SchoolUnits::find($school_id);
        $departments = $school->children ?? collect();
        $programs = SchoolUnits::whereIn('parent_id', $departments->pluck('id')->toArray())->distinct()->get();
        
        if($level_id == null){
            $students = Students::join('student_classes', 'student_classes.student_id', '=', 'students.id')
                ->where('students.active', $active)
                ->where('student_classes.year_id', $year)
                ->join('program_levels', 'program_levels.id', '=', 'student_classes.class_id')
                ->whereIn('program_levels.program_id', $programs->pluck('id')->toArray())
                ->select('students.*')->orderBy('name')->get();
    
            return $students;
        }else{
            $students = Students::join('student_classes', 'student_classes.student_id', '=', 'students.id')
                ->where('students.active', $active)
                ->where('student_classes.year_id', $year)
                ->join('program_levels', 'program_levels.id', '=', 'student_classes.class_id')
                ->whereIn('program_levels.program_id', $programs->pluck('id')->toArray())
                ->where('program_levels.level_id', $level_id)
                ->select('students.*')->orderBy('name')->get();
    
            return $students;
        }

    }
    
    public function departments($school_id)
    {
        # code...
        $school = SchoolUnits::find($school_id);
        if($school != null){
            return $school->children;
        }
        return collect();
    }
    
    public function department_students($department_id, $active = 1, $year = null, $level_id = null)
    {
        # code...
        $year = $year == null ? $this->current_accademic_year : $year;
        $department = SchoolUnits::find($department_id);
        if(($programs = $department->children)->count() > 0){
            if($level_id == null){
                $students = Students::join('student_classes', 'student_classes.student_id', '=', 'students.id')
                    ->where('students.active', $active)
                    ->where('student_classes.year_id', $year)
                    ->join('program_levels', 'program_levels.id', '=', 'student_classes.class_id')
                    ->whereIn('program_levels.program_id', $programs->pluck('id')->toArray())
                    ->select('students.*')->orderBy('name')->get();
    
                return $students;
            }else{
                $students = Students::join('student_classes', 'student_classes.student_id', '=', 'students.id')
                    ->where('students.active', $active)
                    ->where('student_classes.year_id', $year)
                    ->join('program_levels', 'program_levels.id', '=', 'student_classes.class_id')
                    ->whereIn('program_levels.program_id', $programs->pluck('id')->toArray())
                    ->where('program_levels.level_id', $level_id)
                    ->select('students.*')->orderBy('name')->get();
    
                return $students;
            }
        }
        return collect();
    }
    
    public function programs($school_id, $department_id = null)
    {
        # code...
        if($department_id != null){
            if(($department = SchoolUnits::find($department_id)) != null){
                return $department->children;
            }
            return collect();
        }
        if($school_id != null){
            $department_ids = SchoolUnits::where('parent_id', $school_id)->pluck('id')->toArray();
            if(count($department_ids) > 0){
                return SchoolUnits::whereIn('parent_id', $department_ids)->distinct()->get();
            }
            return collect();
        }
        return collect();
        
    }
    
    
    public function program_students($program_id, $active = 1, $year = null, $level_id = null)
    {
        # code...
        $year = $year == null ? $this->current_accademic_year : $year;
        if($level_id == null){
            $students = Students::join('student_classes', 'student_classes.student_id', '=', 'students.id')
                ->where('student_classes.year_id', $year)
                ->where('students.active', $active)
                ->join('program_levels', 'program_levels.id', '=', 'student_classes.class_id')
                ->where('program_levels.program_id', $program_id)
                ->select('students.*')->orderBy('name')->distinct()->get();
            return $students;
        }else{
            $students = Students::join('student_classes', 'student_classes.student_id', '=', 'students.id')
                ->where('student_classes.year_id', $year)
                ->where('students.active', $active)
                ->join('program_levels', 'program_levels.id', '=', 'student_classes.class_id')
                ->where('program_levels.program_id', $program_id)
                ->where('program_levels.level_id', $level_id)
                ->select('students.*')->orderBy('name')->distinct()->get();
            return $students;
        }
    }
    
    public function classes($school_id, $department_id = null, $program_id = null)
    {
        # code...
        $schools = collect();
        if($program_id != null){
            $schoolUnit = SchoolUnits::find($program_id);
            return $schoolUnit->classes ?? collect();
        }
        if($department_id != null){
            $schoolUnit = SchoolUnits::find($department_id);
            $program_ids = $schoolUnit->children()->pluck('school_units.id')->toArray();
            $classes = ProgramLevel::whereIn('program_id', $program_ids)->distinct()->get();
            return $classes;
        }
        $school = SchoolUnits::find($school_id);
        $department_ids = $school->children->pluck('id')->toArray();
        $program_ids = SchoolUnits::whereIn('parent_id', $department_ids)->pluck('id')->toArray();
        $classes = ProgramLevel::whereIn('program_id', $program_ids)->distinct()->get();
        return $classes ?? collect();

    }
    
    public function class_students($class_id, $active=1, $year = null)
    {
        # code...
        $year = $year == null ? $this->current_accademic_year : $year;
        $class = ProgramLevel::find($class_id);
        if($class != null){
            return $class->_students($year)->where('students.active', $active)->orderBy('name')->get();
        }
        return collect();
    }
}