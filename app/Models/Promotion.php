<?php

namespace App\Models;

use App\StudentsClass;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory;
    
    protected $fillable = ['from_year', 'to_year', 'from_class', 'to_class', 'type'];

    protected $table = 'promotions';

    function class()
    {
        # code...
        return $this->hasOne(StudentsClass::class, 'from_class');
    }

    function nextClass(){
        return $this->hasOne(StudentClass::class, 'to_class');
    }

    function year(){
        return $this->hasOne(Batch::class, 'from_class');
    }

    function nextYear(){
        return $this->hasOne(Batch::class. 'to_year');
    }

    function students()
    {
        # code...
        return $this->hasMany(StudentPromotions::class);
    }
}
