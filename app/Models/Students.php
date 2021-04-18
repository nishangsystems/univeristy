<?php

namespace App\Models;

use App\Helpers\Helpers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Students extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'gender',
        'username',
        'dob',
        'pob',
        'admission_batch_id',
        'password',
    ];

    public function class($year){
        return $this->belongsToMany(SchoolUnits::class,'student_classes', 'student_id','class_id')->where('year_id', $year)->first();
    }

    public function classes(){
        return $this->hasMany(StudentClass::class,'student_id');
    }

    public function result(){
        return $this->hasMany(Result::class,'student_id');
    }

    public function payments(){
        return $this->hasMany(Payments::class,'student_id');
    }

    public function total(){
        return $this->class(Helpers::instance()->getYear())->fee();
    }

    public function paid(){
        $items = $this->payments()->selectRaw('COALESCE(sum(amount),0) total')->where('batch_id', Helpers::instance()->getYear())->get();
        return  $items->first()->total;
    }

    public function bal(){
       return $this->total() - $this->paid();
    }
}
