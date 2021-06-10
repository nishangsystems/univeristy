<?php

namespace App\Http\Controllers\Teacher;
use App\Models\ClassMaster;
use App\Models\SchoolUnits;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use \Session;

class ClassController extends Controller{

    public function index(){
        if(\request('type') == 'master'){
            $data['classes'] = ClassMaster::where('batch_id',\App\Helpers\Helpers::instance()->getCurrentAccademicYear())->where('user_id', Auth::user()->id)->get();
            return view('teacher.class_master')->with($data);
        }else{
            $data['units']  = Auth::user()->classR(\App\Helpers\Helpers::instance()->getCurrentAccademicYear());
            return view('teacher.class')->with($data);
        }
    }

    public function reportCard( $class, $student_id){
        $class = SchoolUnits::find($class);
        $data['class'] = $class;
        $data['user'] = \App\Models\Students::find($student_id);
        return view('teacher.report_card')->with($data);
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

    public function classes(){
        $data['year'] = \App\Helpers\Helpers::instance()->getCurrentAccademicYear();
        $data['classes'] = ClassMaster::where('batch_id',\App\Helpers\Helpers::instance()->getCurrentAccademicYear())->where('user_id', Auth::user()->id)->get();
        return view('teacher.classmaster')->with($data);
    }

    public function rank($class_id){
        $class = ClassMaster::find($class_id)->class;
        $data['class'] = $class;
        $data['year'] = \App\Helpers\Helpers::instance()->getCurrentAccademicYear();
        $data['students'] = $class->students(\Session::get('mode', \App\Helpers\Helpers::instance()->getCurrentAccademicYear()))->paginate(15);
        return view('teacher.rank')->with($data);
    }
}
