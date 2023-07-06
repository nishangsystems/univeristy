<?php

namespace App\Models;

use App\StudentsClass;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory;
    
    protected $fillable = ['from_year', 'to_year', 'from_class', 'to_class', 'type', 'user_id'];
    protected $table = 'promotions';
    protected $connection = 'mysql';

    function class()
    {
        # code...
        return $this->belongsTo(ProgramLevel::class, 'from_class');
    }

    function nextClass(){
        return $this->belongsTo(ProgramLevel::class, 'to_class');
    }

    function year(){
        return $this->belongsTo(Batch::class, 'from_year');
    }

    function nextYear(){
        return $this->belongsTo(Batch::class, 'to_year');
    }

    function students()
    {
        # code...
        return $this->hasMany(StudentPromotions::class);
    }
}
