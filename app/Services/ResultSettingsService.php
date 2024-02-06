<?php

namespace App\Services;

use App\Models\Semester;
use Illuminate\Support\Facades\Validator;

class ResultSettingsService{

    public function setCaUploadDateline($semester_id, $date)
    {
        # code...
        $validity = Validator::make(compact('date'), ['date'=>'date']);
        if($validity->fails()){
            throw new \Exception("Validation Error. ".$validity->errors()->first());
        }
        $update = ['ca_upload_latest_date'=>$date];
        $semester = Semester::find($semester_id);
        if($semester == null)
            throw new \Exception("Target semester not found");

        $semester->update($update);
        return $semester;
    }

    public function setExamUploadDateline($semester_id, $date)
    {
        # code...
        // dd($date);
         $validity = Validator::make(compact('date'), ['date'=>'date']);
         if($validity->fails()){
             throw new \Exception("Validation Error. ".$validity->errors()->first());
         }
         $update = ['exam_upload_latest_date'=>$date];
         $semester = Semester::find($semester_id);
         if($semester == null)
             throw new \Exception("Target semester not found");
 
         $semester->update($update);
         return $semester;
    }
}