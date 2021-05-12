<?php

namespace App\Http\Controllers;
use App\Helpers\Helpers;
use App\Http\Resources\Fee;
use App\Http\Resources\StudentFee;
use App\Models\SchoolUnits;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class HomeController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return redirect()->to(route('login'));
    }

    public function children($parent)
    {
        $parent = \App\Models\SchoolUnits::find($parent);

        return response()->json(['array'=>$parent->unit,
           // 'name'=>$parent->unit->first()?$parent->unit->first()->type->name:'',
            'valid'=> ($parent->parent_id != 0 && $parent->unit->count() == 0)?'1':0,
            'name'=> $parent->unit->first()?($parent->unit->first()->unit->count() == 0?'section':''):'section'
            ]);
    }

    public function  subjects($parent){
        $parent = \App\Models\SchoolUnits::find($parent);
        return response()->json([
            'array'=>$parent->subject()->with('subject')->get(),
        ]);
    }

    public function student($name){
        $students = \App\Models\Students::join('student_classes', ['students.id'=>'student_classes.student_id'])->where('student_classes.year_id',\App\Helpers\Helpers::instance()->getYear())
            ->where('students.name', 'LIKE', "%{$name}%")
            ->get();
        return \response()->json(StudentFee::collection($students));
    }

    public function fee(Request  $request){
        $type = request('type','completed');
        $unit = SchoolUnits::find(\request('section'));
        $title = $type." fee ". ($unit != null ?"for ".$unit->name:'');
        $students = [];
        if($unit){
           $students = array_merge($students, $this->load($unit, $type));
        }
        return response()->json(['students'=>Fee::collection($students),'title'=>$title]);
    }

    public function load(SchoolUnits $unit , $type){
        $students = [];
        foreach ($unit->students(Helpers::instance()->getYear())->get() as $student){
            if($type == 'completed' && $student->bal() == 0){
                array_push($students, $student);
            }elseif($type == 'uncompleted' && $student->bal() > 0){
                array_push($students, $student);
            }
        }
        foreach ($unit->unit as $unit){
            $students = array_merge($students, $this->load($unit, $type));
        }

        return $students;
    }


}
