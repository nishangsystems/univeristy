<?php

namespace App\Http\Controllers;

use App\Helpers\Helpers;
use App\Http\Resources\Fee;
use App\Http\Resources\StudentResource3;
use App\Http\Resources\StudentRank;
use App\Http\Resources\CollectBoardingFeeResource;
use App\Models\Rank;
use App\Models\SchoolUnits;
use App\Models\Sequence;
use App\Models\Students;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\SchoolUnitResource;
use App\Http\Resources\StudentFee;
use App\Models\Campus;
use App\Models\ProgramLevel;
use Illuminate\Support\Facades\Auth;
use Throwable;
use \PDF;

class HomeController extends Controller
{

    private $select = [
        'students.id as id',
        'students.name',
        'student_classes.year_id',
    ];
    private $select1 = [];
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return redirect()->to(route('login'));
    }

    public function children(Request $request,  $parent)
    {
        $id = trim($parent);
        $school_unit = SchoolUnits::where('parent_id',$id)->get();
        return response()->json([
            'data' => SchoolUnitResource::collection($school_unit),
            'total' => count($school_unit)
        ]);
    }
    public static function x_children($parent)
    {
        $id = trim($parent);
        $school_unit = SchoolUnits::where('parent_id',$id)->get();
        return $school_unit;
    }

    public function  subjects($parent)
    {
        $subjects = \App\Models\ClassSubject::where(['class_id' => $parent])
                    ->join('subjects', ['subjects.id' => 'class_subjects.subject_id'])
                    ->get(['subjects.id', 'subjects.name', 'subjects.code', 'class_subjects.class_id']);
        return response()->json([
            'array' => $subjects,
        ]);
    }

    public function student($name)
    {
        $students = \App\Models\Students::join('student_classes', ['students.id' => 'student_classes.student_id'])
            ->join('campuses', ['students.campus_id' => 'campuses.id'])
            ->where('student_classes.year_id', \App\Helpers\Helpers::instance()->getYear())
            ->join('program_levels', ['students.program_id' => 'program_levels.id'])
            ->join('school_units', ['program_levels.program_id' => 'school_units.id'])
            ->join('levels', ['program_levels.level_id' => 'levels.id'])
            ->where('students.name', 'LIKE', "%{$name}%")
            ->orWhere('students.matric', '=', $name)
            ->get(['students.*', 'campuses.name as campus']);

        return \response()->json(StudentFee::collection($students));
    }

    public function student_get()
    {
        $name = request('name');
        $students = \App\Models\Students::join('student_classes', ['students.id' => 'student_classes.student_id'])
            ->join('campuses', ['students.campus_id' => 'campuses.id'])
            ->where('student_classes.year_id', \App\Helpers\Helpers::instance()->getYear())
            ->join('program_levels', ['students.program_id' => 'program_levels.id'])
            ->join('school_units', ['program_levels.program_id' => 'school_units.id'])
            ->join('levels', ['program_levels.level_id' => 'levels.id'])
            ->where(function($query)use($name){
                $query->where('students.name', 'LIKE', "%{$name}%")
                ->orWhere('students.matric', 'LIKE', "%{$name}%");
            })->where(function($query){
                \auth()->user()->campus_id != null ? $query->where('students.campus_id', '=', \auth()->user()->campus_id) : null;
                        })
            ->get(['students.*', 'campuses.name as campus']);

        return \response()->json(StudentFee::collection($students));
    }
    public function searchStudents($name)
    {
        $name = str_replace('/', '\/', $name);
        try {
            //code...
            // $sql = "SELECT students.*, student_classes.student_id, student_classes.class_id, campuses.name as campus from students, student_classes, campuses where students.id = student_classes.student_id and students.campus_id = campuses.id and students.name like '%{$name}%' or students.matric like '%{$name}%'";

            // return DB::select($sql);
            $students  = DB::table('students')
                ->join('student_classes', ['students.id' => 'student_classes.student_id'])
                ->join('campuses', ['students.campus_id'=>'campuses.id'])
                ->where('students.name', 'LIKE', "%$name%")
                ->orWhere('students.matric', 'LIKE', "%$name%")
                ->get(['students.*', 'student_classes.student_id', 'student_classes.class_id', 'campuses.name as campus'])->toArray();
            return \response()->json(StudentResource3::collection($students));
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }
    public function searchStudents_get()
    {
        $name = request('key');
        // return $name;
        $name = str_replace('/', '\/', $name);
        try {
            //code...
            // $sql = "SELECT students.*, student_classes.student_id, student_classes.class_id, campuses.name as campus from students, student_classes, campuses where students.id = student_classes.student_id and students.campus_id = campuses.id and students.name like '%{$name}%' or students.matric like '%{$name}%'";

            // return DB::select($sql);
            $students  = DB::table('students')
                ->join('student_classes', ['students.id' => 'student_classes.student_id'])
                ->join('campuses', ['students.campus_id'=>'campuses.id'])
                ->where(function($query)use($name){
                    $query->where('students.name', 'LIKE', "%$name%")
                    ->orWhere('students.matric', 'LIKE', "%$name%");
                })
                ->where(function($query){
                    \auth()->user()->campus_id != null ? $query->where('students.campus_id', '=', \auth()->user()->campus_id) : null;
                })
                ->distinct()
                ->get(['students.*', 'student_classes.student_id', 'student_classes.class_id', 'campuses.name as campus'])
                ->toArray();
            
            return \response()->json(StudentResource3::collection($students));
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public function fee(Request  $request)
    {
        $type = request('type', 'completed');
        $year = request('year', \App\Helpers\Helpers::instance()->getCurrentAccademicYear());
        $class = ProgramLevel::find(\request('class'));

        $title = $type . " fee " . ($class != null ? "for " . $class->program()->first()->name .' : LEVEL '.$class->level()->first()->level : '');
        $students = [];
 
        $studs = \App\Models\StudentClass::where('student_classes.class_id', '=', $request->class)->where('year_id', '=', $year)->join('students', 'students.id', '=', 'student_classes.student_id');
        if(auth()->user()->campus_id != null) {
            # code...
            $studs = $studs->where('students.campus_id', '=', auth()->user()->campus_id);
        }
        $studs = $studs->pluck('students.id')->toArray();
        $results = [];
        

        $fees = array_map(function($stud) use ($year){
            return [
                'amount' => array_sum(
                    \App\Models\Payments::where('payments.student_id', '=', $stud)
                    ->join('payment_items', 'payment_items.id', '=', 'payments.payment_id')
                    ->where('payment_items.name', '=', 'TUTION')
                    ->where('payments.batch_id', '=', $year)
                    ->pluck('payments.amount')
                    ->toArray()
                ),
                'total' => 
                        \App\Models\CampusProgram::join('Program_levels', 'program_levels.id', '=', 'campus_programs.program_level_id')
                        ->join('payment_items', 'payment_items.campus_program_id', '=', 'campus_programs.id')
                        ->where('payment_items.name', '=', 'TUTION')
                        ->whereNotNull('payment_items.amount')
                        ->join('students', 'students.program_id', '=', 'program_levels.id')
                        ->where('students.id', '=', $stud)->pluck('payment_items.amount')[0] ?? 0
                    ,
                'stud' => $stud
            ];
        }, $studs);

        foreach ($fees as $key => $value) {
            # code...
            $stdt = Students::find($value['stud']);
            if(($value['amount'] == $value['total'] && $value['total'] > 0) && $type == 'completed'){
                $students[] = [
                    'id'=> $stdt->id,
                    'name'=> $stdt->name,
                    'link'=> route('admin.fee.student.payments.index', [$stdt->id]),
                    'total'=> $value['amount'],
                    'class'=>$class->program()->first()->name .' : LEVEL '.$class->level()->first()->level
                ];
            }
            if(($value['amount'] < $value['total'] || $value['total'] == 0) && $type == 'uncompleted'){
                $students[] = [
                    'id'=> $stdt->id,
                    'name'=> $stdt->name,
                    'link'=> route('admin.fee.student.payments.index', [$stdt->id]),
                    'total'=> $value['amount'],
                    'class'=> $class->program()->first()->name .' : LEVEL '.$class->level()->first()->level
                ];
            }
        }

        $students = collect($students)->sortBy('name')->toArray();

        return response()->json(['students' => $students, 'title' => $title]);
    }

    public function rank(Request  $request)
    {
        $seq =  Sequence::find($request->sequence);
        $unit = SchoolUnits::find($request->class);
        $title = $seq->name . " ranking " . ($unit != null ? "for " . $unit->name : '');
        $students = $unit->students($request->year)->get();
        return response()->json(['students' => StudentRank::collection($students), 'title' => $title]);
    }

    public function load($unit, $type, $bal)
    {
        $students = [];
        foreach ($unit->_students(Helpers::instance()->getYear())->get() as $student) {
            if ($type == 'completed' && $student->bal($student->id) == 0) {
                array_push($students, $student);
            } elseif ($type == 'uncompleted' && $student->bal($student->id) > 0) {
                array_push($students, $student);
            }
        }
        // foreach ($unit->unit as $unit) {
        //     $students = array_merge($students, $this->load($unit, $type, $bal));
        // }

        return $students;
    }

    /**
     * get all school student boarders
     *
     * @param string $name
     */
    public function getStudentBoarders($name)
    {

        $type = 'boarding';
        $students = DB::table('student_classes')
            ->join('students', ['students.id' => 'student_classes.student_id'])
            ->join('school_units', ['school_units.id' => 'student_classes.class_id'])
            ->where('students.type', '=', $type)
            ->where('students.name', 'LIKE', "%{$name}%")
            ->orWhere('students.matric', 'LIKE', "%{$name}%")
            ->select('students.id', 'students.name', 'students.matric', 'school_units.name as class_name', 'school_units.id as class_id')->get();

        return response()->json(['data' => CollectBoardingFeeResource::collection($students)]);
    }

    public function rankPost(Request  $request)
    {
        $sequence = $request->sequence;
        $students = $request->students;
        foreach ($students as $k => $student) {
            $rank = Rank::where([
                'student_id' => $student,
                'sequence_id' => $sequence
            ])->first();
            if (!isset($rank)) {
                $rank = new Rank();
            }

            $rank->student_id = $student;
            $rank->sequence_id = $sequence;
            $rank->position = ($k + 1);
            $rank->year_id = Helpers::instance()->getYear();
            $rank->save();
        }
        return response()->json(['title' => "'success"]);
    }
    
}
