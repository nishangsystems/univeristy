<?php

namespace App\Models;

use Carbon\Traits\Units;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolUnits extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'unit_id',
        'parent_id',
    ];

    public function unit(){
        return  $this->hasMany(SchoolUnits::class, 'parent_id');
    }

    public function unitType(){
        return $this->belongsTo(Units::class, 'unit_id');
    }

    public function subjects(){
        return  $this->belongsToMany(Subjects::class,'class_subjects','class_id','subject_id');
    }
}
