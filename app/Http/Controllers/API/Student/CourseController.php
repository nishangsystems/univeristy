<?php


namespace App\Http\Controllers\API\Student;


use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Resources\CourseResource;
use App\Http\Resources\StudentResource3;
use App\Models\ProgramLevel;
use App\Models\Students;
use App\Models\StudentSubject;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function courses(Request $request)
    {
        if($request->student_id){
            $student = Students::find($request->student_id);
        }else{
            $student = Auth('student_api')->user();
        }

        $_year = $request->year ?? Helpers::instance()->getYear();
        $_semester = $request->semester ?? Helpers::instance()->getSemester($student->_class(Helpers::instance()->getCurrentAccademicYear())->id)->id;
        $courses = StudentSubject::where(['student_courses.student_id'=>$student->id])->where(['student_courses.year_id'=>$_year])
            ->join('subjects', ['subjects.id'=>'student_courses.course_id'])->where(['subjects.semester_id'=>$_semester])
            ->join('class_subjects', ['class_subjects.subject_id'=>'subjects.id'])->distinct()->orderBy('subjects.name')->get(['subjects.*', 'class_subjects.coef as cv', 'class_subjects.status as status']);
        return response()->json(['cv_sum'=>collect($courses)->sum('cv'), 'courses'=> CourseResource::collection($courses)]);
    }


    public function register(Request $request)
    {
        // $student = $student;

        // return response([
        //     'status' => 200,
        //     'student' => new StudentResource3($student)
        // ]);
    }
}

