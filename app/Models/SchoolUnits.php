<?php

namespace App\Models;

use App\Helpers\Helpers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Staudenmeir\LaravelAdjacencyList\Eloquent\HasRecursiveRelationships;

class SchoolUnits extends Model
{
    use HasFactory;
    use HasRecursiveRelationships;

    protected $fillable = [
        'name',
        'unit_id',
        'parent_id',
        'grading_type_id',
        'resit_cost',
    ];
    protected $connection = 'mysql';

    public function gradingType()
    {
        # code...
        return $this->belongsTo(GradingType::class, 'grading_type_id');
    }

    public function getParentKeyName()
    {
        return 'parent_id';
    }

    public function unit()
    {
        return  $this->hasMany(SchoolUnits::class, 'parent_id');
    }

    public function type()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function students($year)
    {
        return $this->belongsToMany(Students::class, 'student_classes', 'class_id', 'student_id')->where('year_id', $year);
    }
    // public function students($year)
    // {
    //     return $this->hasManyThrough(Students::class, StudentClass::class, 'class_id', 'student_id')->where('year_id', $year);
    // }


    public function subjects()
    {
        return  $this->belongsToMany(Subjects::class, 'class_subjects', 'class_id', 'subject_id');
    }

    public function subject()
    {
        return  $this->hasMany(ClassSubject::class, 'class_id');
    }

    public function items()
    {
        return  $this->hasMany(PaymentItem::class, 'unit');
    }

    public function fee()
    {
        $items = $this->items()->selectRaw('COALESCE(sum(amount),0) total')->where('year_id', Helpers::instance()->getYear())->get();
        return  $items->first()->total;
    }

    public function setChildrenFee($input)
    {
        foreach ($this->unit as $unit) {
            $input['unit'] = $unit->id;
            PaymentItem::create($input);
            if ($unit->has('unit')) {
                $unit->setChildrenFee($input);
            }
        }
    }



    public function collectBoardingFees()
    {
        return $this->hasMany(CollectBoardingFee::class, 'class_id');
    }

    public function boardingTypes()
    {
        return $this->hasMany(BoardingFee::class);
    }

    public function parent(){
        return $this->belongsTo(SchoolUnits::class, 'parent_id');
    }

    public function has_unit(SchoolUnits $school_unit)
    {

        $parent = $school_unit->parent;
        if($parent == null){return false;}
        elseif($parent->id == $this->id || $school_unit->id == $this->id){
            return true;
        }else{return $this->is_parent_to($parent);}
    }

    public function semesters()
    {
        return $this->hasMany(Semester::class, 'background_id');
    }

    public function programLevels()
    {
        return $this->belongsToMany(Level::class, ProgramLevel::class, 'level_id', 'program_id');
    }

    public function classes()
    {
        return $this->hasMany(ProgramLevel::class, 'program_id');
    }

    public function background()
    {
        return $this->belongsTo(Background::class);
    }

    public function resit_cost_isset()
    {
        return $this->resit_cost == null;
    }

    public function schoolHeadedBy()
    {
        # code...
        return $this->belongsToMany(User::class, HeadOfSchool::class, 'school_unit_id', 'user_id')->where('active', 1);
    }

    public function departmentalCourses()
    {
        # code...
        return $this->belongsToMany(Subjects::class, DepartmentalCourse::class, 'school_unit_id', 'subject_id');
    }

    public function degree()
    {
        # code...
        return $this->belongsTo(Degree::class, 'degree_id');
    }

    public function banks($campus_id = null)
    {
        # code...
        return $campus_id == null ?
            $this->belongsToMany(Bank::class, ProgramBank::class) :
            $this->belongsToMany(Bank::class, ProgramBank::class)->where('program_banks.campus_id', $campus_id);
    }

    public function provision_status($campus = null){
        return $campus == null ?
            $this->belongsToMany(Status::class, CampusProgramStatus::class, 'status', 'program_id', 'name', 'id') :
            $this->belongsToMany(Status::class, CampusProgramStatus::class, 'status', 'program_id', 'name', 'id')->where('campus_program_status.campus_id', $campus) ;
    }
}
