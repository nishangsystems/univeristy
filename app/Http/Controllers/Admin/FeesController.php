<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolUnits;
use Illuminate\Http\Request;
use Session;
use Redirect;
use DB;
use Auth;

class FeesController extends Controller{

    public function classes(Request  $request){
        $title = "Classes";
        $classes = \App\Models\SchoolUnits::where('parent_id', $request->get('parent_id','0'))->get();
        return view('admin.fee.classes', compact('classes','title'));
    }

    public function student(Request  $request, $class_id){
        $class = SchoolUnits::find($class_id);
        $title = $class->name." Students";
        $students = $class->students(Session::get('mode', \App\Helpers\Helpers::instance()->getCurrentAccademicYear()))->paginate(20);
        return view('admin.fee.students', compact('students','title'));
    }
}
