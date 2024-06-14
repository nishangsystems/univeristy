<?php


namespace App\Http\Controllers\API\Student;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Resources\StudentResource3;
use App\Models\Batch;
use App\Models\Level;
use App\Models\School;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function profile(Request $request) 
    {
        $student = Auth('student_api')->user();

        return response([
            'status' => 200,
            'student' => new StudentResource3($student)
        ]);
    }

    
    public function semesters(Request $request) 
    {
        $student = Auth('student_api')->user();
        $semesters = Helpers::instance()->getSemesters($student->_class()->id);

        return response([
            'status' => 200,
            'semesters' => $semesters
        ]);
    }

    
    public function levels(Request $request) 
    {
        $student = Auth('student_api')->user();
        $program_id = $student->_class()->program_id;
        $levels = Level::join('program_levels', 'program_levels.level_id', '=', 'levels.id')->select(['levels.*'])->get();
        return response([
            'status' => 200,
            'levels' => $levels
        ]);
    }

    
    public function current_accademic_year(Request $request) 
    {
        return response()->json(['data'=> Batch::find(Helpers::instance()->getCurrentAccademicYear())]);
    }

    
    public function current_semester(Request $request) 
    {
        $student = auth('student_api')->user();
        $semester = Helpers::instance()->getSemester($student->_class()->id);
        return response()->json(['data'=> $semester]);
    }

    
    public function school_name(Request $request) 
    {
        $school = School::first();
        return response()->json(['data'=> $school]);
    }

    
}