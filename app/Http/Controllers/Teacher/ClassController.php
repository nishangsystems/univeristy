<?php

namespace App\Http\Controllers\Teacher;

use App\Models\Batch;
use App\Models\ClassMaster;
use App\Models\SchoolUnits;
use App\Models\Term;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ProgramLevel;
use App\Models\StudentClass;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use \Session;

class ClassController extends Controller
{

    public function program_courses()
    {
        # code...
        $data['title'] = 'Courses For '.SchoolUnits::find(ClassMaster::where('user_id', '=', auth()->id)->first()->department_id)->name;
        // $data['courses'] = 
    }

    public function program_levels_list()
    {
        # code...
        $data['title'] = "Class List".(request()->has('campus_id') ? \App\Models\Campus::find(request('campus_id'))->first()->name : '').(request()->has('id') ? ' For '.\App\Models\ProgramLevel::find(request('id'))->program()->first()->name.' Level '.\App\Models\ProgramLevel::find(request('id'))->level()->first()->level : null);
        return view('teacher.class_list', $data);
    }

    public function index()
    {
        $data['options'] = \App\Http\Controllers\Admin\StudentController::baseClasses();
        if (\request('type') == 'master') {
            $data['classes'] = ClassMaster::where('batch_id', \App\Helpers\Helpers::instance()->getCurrentAccademicYear())->where('user_id', Auth::user()->id)->get();
            // dd($data);
            return view('teacher.class_master')->with($data);
        } else {
            $data['units']  = \App\Models\ProgramLevel::join('teachers_subjects', ['teachers_subjects.class_id'=>'program_levels.id'])
                                ->where(['teachers_subjects.teacher_id'=>auth()->id()])
                                ->select(['program_levels.*', 'teachers_subjects.campus_id'])
                                ->get();
            return view('teacher.class')->with($data);
        }
    }

    public function reportCard($class, $t,  $student_id)
    {
        $class = SchoolUnits::find($class);
        $data['class'] = $class;
        $data['term'] = Term::find($t);
        $year = Batch::find(\App\Helpers\Helpers::instance()->getYear());
        $data['year'] = $year;
        $data['subjects'] = \App\Models\Students::find($student_id)->class(\App\Helpers\Helpers::instance()->getYear())->subjects;
        $data['user'] = \App\Models\Students::find($student_id);
        return view('teacher.report_card')->with($data);
    }

    public function students($class_id)
    {
        $class = ProgramLevel::find($class_id);
        $data['class'] = $class;

        // $data['students'] = $class->students(\Session::get('mode', \App\Helpers\Helpers::instance()->getCurrentAccademicYear()))->paginate(15);
        $data['students'] = StudentClass::where('class_id', '=', $class_id)
                ->where('year_id', '=', \App\Helpers\Helpers::instance()->getCurrentAccademicYear())
                ->join('students', ['students.id'=>'student_classes.student_id'])
                ->where(function($q){
                    request()->has('campus') ? $q->where(['students.campus_id'=>request('campus')]) : null;
                })
                ->orderBy('students.name', 'ASC')->get('students.*');
        return view('teacher.student')->with($data);
    }

    public function master_sheet(Request $request)
    {
        # code...
        return view('teacher.master_sheet');
    }

    public function student($student_id)
    {
        $data['user'] = \App\Models\Students::find($student_id);
        return view('teacher.student_detail')->with($data);
    }

    public function classes()
    {
        $data['year'] = \App\Helpers\Helpers::instance()->getCurrentAccademicYear();
        $data['classes'] = ClassMaster::where('batch_id', \App\Helpers\Helpers::instance()->getCurrentAccademicYear())->where('user_id', Auth::user()->id)->get();
        return view('teacher.classmaster')->with($data);
    }

    public function rank($class_id)
    {
        $class = ClassMaster::find($class_id)->class;
        $data['class'] = $class;
        $data['year'] = \App\Helpers\Helpers::instance()->getCurrentAccademicYear();
        $data['students'] = $class->students(\Session::get('mode', \App\Helpers\Helpers::instance()->getCurrentAccademicYear()))->paginate(15);
        return view('teacher.rank')->with($data);
    }

    public function notifications(Request $request)
    {
        # code...
        $pl = ProgramLevel::find($request->id) ?? null;
        $data['title'] = 'Notifications For '.($pl->program()->first()->name.' : LEVEL '.$pl->level()->first()->level ?? '');
        
    }

    public function material(Request $request)
    {
        # code...
    }
}
