<?php
namespace App\Helpers;


use App\Models\Result;
use Illuminate\Contracts\Session\Session;

class Helpers
{
    public function getYear()
    {
        return session()->get('mode', $this->getCurrentAccademicYear());
    }

    public function getCurrentAccademicYear(){
        $config = \App\Models\Config::all()->last();
        return $config->year_id;
    }

    public function getCurrentSemester(){
        $config = \App\Models\Config::all()->last();
        return $config->semester_id;
    }

    public static function instance()
    {
        return new Helpers();
    }

    public function getScore($seq_id, $subject_id, $class_id, $year, $student_id){
        $result = Result::where([
            'student_id' => $student_id,
            'class_id' => $class_id,
            'sequence' =>$seq_id,
            'subject_id'=>$subject_id,
            'batch_id'=>$year
        ])->first();

        if($result){
            return $result->score;
        }
    }


}

