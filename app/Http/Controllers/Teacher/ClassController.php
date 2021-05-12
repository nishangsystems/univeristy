<?php

namespace App\Http\Controllers\Teacher;
use App\Models\SchoolUnits;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use \Session;

class ClassController extends Controller{

    public function index(){
        $data['units']  = Auth::user()->classR(\App\Helpers\Helpers::instance()->getCurrentAccademicYear());
        return view('teacher.class')->with($data);
    }

    public function students($class_id){
        $class = SchoolUnits::find($class_id);
        $data['class'] = $class;
        $data['students'] = $class->students(\Session::get('mode', \App\Helpers\Helpers::instance()->getCurrentAccademicYear()))->paginate(15);
        return view('teacher.student')->with($data);
    }

    public function student($student_id){
        $data['user'] = \App\Models\Students::find($student_id);
        return view('teacher.student_detail')->with($data);
    }
}
