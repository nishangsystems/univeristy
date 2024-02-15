<?php

namespace App\Services;

use App\Helpers\Helpers as Helpers;
use App\Models\ProgramLevel;
use App\Models\HeadOfSchool;
use App\Models\SchoolUnits;
use App\Models\Students;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class HeadOfSchoolService{
    
    protected $current_accademic_year;
    public function __construct($current_accademic_year)
    {
        # code...
        $this->current_accademic_year = $current_accademic_year;
    }

    public function users()
    {
        # code...
        $data = User::join('head_of_schools', 'head_of_schools.user_id', '=', 'users.id')
            ->join('school_units', 'school_units.id', '=', 'head_of_schools.school_unit_id')
            ->select(['users.*', 'school_units.id as school_id', 'school_units.name as school_name', 'head_of_schools.status as status', 'head_of_schools.id as hos_id'])
            ->distinct()->get();
            // dd($data);
        return $data;
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
                ->orderBy('program_levels.id')->orderBy('students.name')
                ->select('students.*')->get();
    
            return $students;
        }else{
            $students = Students::join('student_classes', 'student_classes.student_id', '=', 'students.id')
                ->where('students.active', $active)
                ->where('student_classes.year_id', $year)
                ->join('program_levels', 'program_levels.id', '=', 'student_classes.class_id')
                ->whereIn('program_levels.program_id', $programs->pluck('id')->toArray())
                ->where('program_levels.level_id', $level_id)
                ->orderBy('program_levels.id')->orderBy('students.name')
                ->select('students.*')->get();
    
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
                    ->orderBy('program_levels.id')->orderBy('students.name')
                    ->select('students.*')->get();
    
                return $students;
            }else{
                $students = Students::join('student_classes', 'student_classes.student_id', '=', 'students.id')
                    ->where('students.active', $active)
                    ->where('student_classes.year_id', $year)
                    ->join('program_levels', 'program_levels.id', '=', 'student_classes.class_id')
                    ->whereIn('program_levels.program_id', $programs->pluck('id')->toArray())
                    ->where('program_levels.level_id', $level_id)
                    ->orderBy('program_levels.id')->orderBy('students.name')
                    ->select('students.*')->get();
    
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
                ->orderBy('program_levels.id')->orderBy('students.name')
                ->select('students.*')->distinct()->get();
            return $students;
        }else{
            $students = Students::join('student_classes', 'student_classes.student_id', '=', 'students.id')
                ->where('student_classes.year_id', $year)
                ->where('students.active', $active)
                ->join('program_levels', 'program_levels.id', '=', 'student_classes.class_id')
                ->where('program_levels.program_id', $program_id)
                ->where('program_levels.level_id', $level_id)
                ->orderBy('program_levels.id')->orderBy('students.name')
                ->select('students.*')->distinct()->get();
            return $students;
        }
    }
    
    public function classes($school_id, $department_id = null, $program_id = null)
    {
        # code...
        $schools = collect();
        if($program_id != null){
            $schoolUnit = SchoolUnits::find($program_id);
            return ($schoolUnit->classes()->orderBy('level_id')->get()) ?? collect();
        }
        if($department_id != null){
            $schoolUnit = SchoolUnits::find($department_id);
            $program_ids = $schoolUnit->children()->pluck('school_units.id')->toArray();
            $classes = ProgramLevel::whereIn('program_id', $program_ids)->orderBy('program_id')->orderBy('level_id')->distinct()->get();
            return $classes;
        }
        $school = SchoolUnits::find($school_id);
        $department_ids = $school->children->pluck('id')->toArray();
        $program_ids = SchoolUnits::whereIn('parent_id', $department_ids)->pluck('id')->toArray();
        $classes = ProgramLevel::whereIn('program_id', $program_ids)->orderBy('program_id')->orderBy('level_id')->distinct()->get();
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

    public function update($hos_id, $update){
        $hos = HeadOfSchool::find($hos_id);
        $hos->update($update);
        return $hos;
    }

    public function delete($hos_id){
        $hos = HeadOfSchool::find($hos_id);
        $hos->delete();
        return true;
    }

    public function allSchools(){
        $schools = SchoolUnits::where('unit_id', 1)->orderBy('name')->get();
        return $schools;
    }

    public function save($data){
        $validity = Validator::make($data, ['user_id'=>'required', 'school_unit_id'=>'required', 'status'=>'boolean|nullable']);
        if($validity->fails()){
            throw new \Exception("Validation Error. ".$validity->errors()->first());
        }
        $instance = new HeadOfSchool($data);
        $instance->save();
        return $instance;
    }
}