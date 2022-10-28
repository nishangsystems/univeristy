<?php

namespace App\Models;


use App\Helpers\Helpers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class StudentInfo extends Model
{
   
     protected $guard = 'studentinfo';
     
    public function options()
    {
        return $this->belongsTo('\App\Options','program_id');
    }

    public function year()
    {
        return $this->belongsTo('\App\Year','year_id');
    }

    public function level()
    {
         echo "asdd";die;
        return $this->belongsTo('\App\Models\Level','level_id');
    }

    public function student()
    {
        return $this->hasOne('\App\Models\Students','student_id');
    }

    public function medicalCompleted($year_id)
    {
      //  return ($this->hasMany('App\StudentMedical','student_id')->where('year_id', $year_id)->count() != 0);
      return 1;
    }

    public function myFeesPayment($year){
        return $this->hasMany('App\FeePaymt','student_id')->where('yearid', $year)->get();
    }

    public function department(){
        return $this->belongsTo('\App\Options','program_id');
    }

    public function result($year_id, $semester_id){
        $results = [];
       if($this->level->name >= 600){ //masters
        $results =  $this->hasMany('App\Result','student_id')->where('year_id', $year_id)->where('semester_id',$semester_id)->get();
       }else{ //undergraduate
            if($year_id < 3){
                $results = \App\OldResult::where('matric', $this->matricule)->where('year_id', $year_id)->where('semester_id',$semester_id)->get();
                //$results =  $this->hasMany('App\OldResult','student_id')->where('year_id', $year_id)->where('semester_id',$semester_id)->get();
            }else{
                $results = \App\Result::where('matric', $this->matricule)->where('year_id', $year_id)->where('semester_id',$semester_id)->get();
                //$results =  $this->hasMany('App\Result','student_id')->where('year_id', $year_id)->where('semester_id',$semester_id)->get();
            }
       }
       $newGrade = [];
       foreach($results as $result){
            $total = $result->ca + $result->exam;
            $result->grade = \App\Helpers\Helpers::instance()->getGrade($total,$this->options->id);
            array_push($newGrade, $result);
       }

       return $newGrade;
    }

    public function resultModel($year_id, $semester_id){
      $results =  $this->hasMany('App\Result','student_id')->where('year_id', $year_id)->where('semester_id',$semester_id);

    return $results;
    }

    public function resultPublicHealth($year_id, $semester_id){
        $results = \App\Result::where('matric', $this->matricule)->where('year_id', $year_id)->where('semester_id',$semester_id)->get();
        $newGrade = [];
        foreach($results as $result){
            $total = $result->exam * 5;
            $result->grade = \App\Helpers\Helpers::instance()->getGrade($total,$this->options->id);
            array_push($newGrade, $result);
        }
        return $newGrade;
    }

    public function myFees($level){
        $fee = 0;
        $myFee = $this->options->fees($level);
        $fee = ($myFee  == null)?0
                :($myFee->fee_amt +$myFee->reg_fee);
        return $fee;
    }

    public function feeAdded($year){
        $fees =  $this->hasMany('App\FeesAdded','student_id')->where('year_id', $year)->get();
       $discount = 0;
       foreach($fees as $fee){
            $discount = $discount + ($fee->amt_added == null)?0:$fee->amt_added;
       }
       return $discount;
    }

    public function totalfeePaid($year){
        $fees =  $this->hasMany('App\FeePaymt','student_id')->where('yearid', $year)->get();
        $discount = 0;
        foreach($fees as $fee){
             $discount = $discount + (is_numeric($fee->fee_amt)?$fee->fee_amt:0);
        }
        return $discount;
    }

    public function myBalance($year, $level){
        return ($this->myFees($level) - $this->discount($year) - $this->totalfeePaid($year) + $this->feeAdded($year));
    }

    public function discount($year){
       $fees =  $this->hasMany('App\FeePaymt','student_id')->where('yearid', $year)->get();
       $discount = 0;
       foreach($fees as $fee){
            $discount = $discount + ($fee->disc == null)?0:$fee->disc;
       }
       return $discount;
    }

    public function hasPaidHalf($year){
       if($this->country == null){
           return ($this->totalfeePaid($year) >= ($this->options->fees($this->level_id)->first_install));
       }else{
           return true;
       }
    }


}

