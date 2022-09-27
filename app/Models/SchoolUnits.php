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

    ];

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
        return $this->hasMany(BoardingFee::class, 'parent_id');
    }

    public function semesters()
    {
        return $this->hasMany(Semester::class, 'background_id');
    }

    public function programLevels()
    {
        return $this->hasManyThrough(Level::class, ProgramLevel::class);
    }
}
