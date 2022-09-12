<?php

namespace App\Http\Controllers;

use App\Helpers\Helpers;
use App\Http\Resources\Fee;
use App\Http\Resources\StudentFee;
use App\Http\Resources\StudentResource2;
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

    public function  subjects($parent)
    {
        $parent = \App\Models\SchoolUnits::find($parent);
        return response()->json([
            'array' => $parent->subject()->with('subject')->get(),
        ]);
    }

    public function student($name)
    {
        $students = \App\Models\Students::join('student_classes', ['students.id' => 'student_classes.student_id'])
            ->where('student_classes.year_id', \App\Helpers\Helpers::instance()->getYear())
            ->where('students.name', 'LIKE', "%{$name}%")
            ->get();

        return \response()->json(StudentFee::collection($students));
    }
    public function searchStudents($name)
    {

        $students  = DB::table('students')
            ->join('student_classes', ['students.id' => 'student_classes.student_id'])
            ->where('students.name', 'LIKE', "%{$name}%")
            ->get()->toArray();

        return \response()->json(StudentResource2::collection($students));
    }

    public function fee(Request  $request)
    {
        $type = request('type', 'completed');
        $unit = SchoolUnits::find(\request('section'));
        $title = $type . " fee " . ($unit != null ? "for " . $unit->name : '');
        $students = [];
        if ($unit) {
            $students = array_merge($students, $this->load($unit, $type, $request->get('bal', 0)));
        }
        return response()->json(['students' => Fee::collection($students), 'title' => $title]);
    }

    public function rank(Request  $request)
    {
        $seq =  Sequence::find($request->sequence);
        $unit = SchoolUnits::find($request->class);
        $title = $seq->name . " ranking " . ($unit != null ? "for " . $unit->name : '');
        $students = $unit->students($request->year)->get();
        return response()->json(['students' => StudentRank::collection($students), 'title' => $title]);
    }

    public function load(SchoolUnits $unit, $type, $bal)
    {
        $students = [];
        foreach ($unit->students(Helpers::instance()->getYear())->get() as $student) {
            if ($type == 'completed' && $student->bal($student->id) == 0) {
                array_push($students, $student);
            } elseif ($type == 'uncompleted' && $student->bal($student->id) > $bal) {
                array_push($students, $student);
            }
        }
        foreach ($unit->unit as $unit) {
            $students = array_merge($students, $this->load($unit, $type, $bal));
        }

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
