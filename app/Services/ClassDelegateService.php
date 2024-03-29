<?php

namespace App\Services;

use App\Helpers\Helpers;
use App\Models\Attendance;
use App\Models\ClassDelegate;
use App\Models\Semester;
use App\Models\Students;
use Illuminate\Support\Facades\Validator;

class ClassDelegateService{

    protected $current_accademic_year;
    public function __construct(Helpers $helpers){
        $this->current_accademic_year = $helpers->getCurrentAccademicYear();
    }


    public function getById($delegate_id)
    {
        # code...
        return ClassDelegate::find($delegate_id);
    }


    public function getAll()
    {
        # code...
        $delegates = ClassDelegate::orderBy('class_id')->get();
        return $delegates;
    }
    public function getMyCourses($student_id)
    {
        # code...
        $student =   Students::find($student_id);
        $class = $student->_class($this->current_accademic_year);
        $courses = $class->subjects;
        return $courses;
    }

    public function check_in($data)
    {
        # code...
        $validity = Validator::make($data, ['year_id'=>'required', 'campus_id'=>'required', 'teacher_id'=>'required', 'subject_id'=>'required', 'check_in'=>'required']);
        if($validity->fails()){
            throw new \Exception($validity->errors()->first());
        }
        $instance = new Attendance($data);
        $instance->save();
        return $instance;
    }

    public function check_out($attendance_id, $time)
    {
        # code...
        $attendance = Attendance::find($attendance_id);
        $attendance->update(['check_out'=>$time]);
        return $attendance;
    }

    public function store($data)
    {
        # code...
        $validity = Validator::make($data, ['year_id'=>'required', 'campus_id'=>'required', 'class_id'=>'required']);
        if($validity->fails()){
            throw new \Exception($validity->errors()->first());
        }

        if(ClassDelegate::where(['year_id'=>$data['year_id'], 'campus_id'=>$data['campus_id'], 'class_id'=>$data['class_id']])->count() < 2){
            $instance = new ClassDelegate(['year_id'=>$data['year_id'], 'campus_id'=>$data['campus_id'], 'class_id'=>$data['class_id'], 'status'=>0]);
            $instance->save();
            return $instance;
        }
        throw new \Exception('Not more than two class delegates are allowed');
    }

    public function update($delegate_id, $update)
    {
        # code...
        $delegate = ClassDelegate::find($delegate_id);
        if($delegate != null){
            $delegate->update($update);
            return $delegate;
        }
        throw new \Exception("Class delegate record not found");
    }

    public function log_course($data)
    {
        # code...
        $validity = Validator::make($data, ['topic_id'=>'required', 'attendance_id'=>'required', 'campus_id'=>'required', 'details'=>'required']);
        if($validity->fails()){
            throw new \Exception($validity->errors->first());
        }
        $log = new \App\Models\CourseLog($data);
        $log->save();
        return $log;
    }

}