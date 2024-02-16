<?php

namespace App\Services;

use App\Helpers\Helpers;
use App\Models\Attendance;
use App\Models\Semester;
use App\Models\Students;
use Illuminate\Support\Facades\Validator;

class ClassDelegateService{

    protected $current_accademic_year;
    public function __construct(Helpers $helpers){
        $this->current_accademic_year = $helpers->getCurrentAccademicYear();
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
        $validity = Validator::make($data, ['campus_id'=>'required', 'teacher_id'=>'required', 'subject_id'=>'required', 'check_in'=>'required']);
        if($validity->fails()){
            throw new \Exception($validity->errors()->first());
        }
        $data['year_id'] = $this->current_accademic_year;
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

}