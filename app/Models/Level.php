<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    use HasFactory;

    //protected $fillable = ['level'];
    
    //public $table = 'level';

    public function courses()
    {
        return $this->hasMany('\App\Course','level_id');
    }

    public function course($program)
    {
        return $this->hasMany('\App\Course','level_id')->where('programe_id',$program)->get();
    }

    public function lcourses($program,$semester)
    {
       // $major  = $this->hasMany('\App\Course','level_id')->where('programe_id',$program)->where('sem',$semester)->get();
        $electives =  $this->hasMany('\App\ElectiveCourses','level_id')->where('programe_id',$program)->where('semester_id',$semester)->get();
        $courses = [];
        foreach($electives as $elective){
            $course = $elective->course;
            if($elective->status != null){
                $course->status = $elective->status;
            }
            if($elective->credit_value != null){
                $course->credit_value = $elective->credit_value;
            }
            array_push($courses, $elective->course);
        }
        return $courses;
    }

    public function department(){
    return $this->belongsToMany('\App\Options' ,'programe_id');
 }
 public function student(){
    return $this->belongsTo('\App\StudentInfo' ,'level_id');
 }
}
