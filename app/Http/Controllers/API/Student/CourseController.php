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
use Throwable;

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

    public function class_courses(Request $request, $level = null)
    {
        try{
            if($request->student_id){
                $student = Students::find($request->student_id);
            }else{
                $student = Auth('student_api')->user();
            }
            $pl = Students::find($student->id)->_class(Helpers::instance()->getCurrentAccademicYear())->select('program_levels.*')->first();
            $level_id = $level == null ? $pl->level_id : $level;
            $program_id = $pl->program_id;
            // return $level_id;
            $subjects = ProgramLevel::where('program_levels.program_id', $program_id)->where('program_levels.level_id',$level_id)
                        ->join('class_subjects', 'class_subjects.class_id', '=', 'program_levels.id')->join('subjects', 'subjects.id', '=', 'class_subjects.subject_id')
                        // ->where('subjects.semester_id', '=', Helpers::instance()->getSemester($pl->id)->id)
                        ->get(['subjects.*', 'class_subjects.coef as cv', 'class_subjects.status as status'])->sortBy('name')->toArray();
            return $subjects;
        }
        catch(Throwable $th){
            throw $th;
            return $th->getLine() . '  '.$th->getMessage();
        }
    }

    public function register(Request $request)//takes courses=[course_ids]
    {
        // return $request->all();
        # code...
        // first clear all registered courses for the year, semester, student then rewrite

        if($request->student_id){
            $student = Students::find($request->student_id);
        }else{
            $student = Auth('student_api')->user();
        }

        $year = Helpers::instance()->getYear();
        $semester = Helpers::instance()->getSemester($student->_class(Helpers::instance()->getCurrentAccademicYear())->id)->id;
        $_semester = Helpers::instance()->getSemester($student->_class(Helpers::instance()->getCurrentAccademicYear())->id)->background->semesters()->orderBy('sem', 'DESC')->first()->id;
        try {
            if ($semester == $_semester) {
                # code...
                return back()->with('error', 'Resit registration can not be done here. Do that under \"Resit Registration\"');
            }
            if ($request->has('courses')) {
                // DB::beginTransaction();
                $ids = StudentSubject::where(['student_id'=>$student->id])->where(['year_id'=>$year])->where(['semester_id'=>$semester])->pluck('id');
                foreach ($ids as $key => $value) {
                    # code...
                    StudentSubject::find($value)->delete();
                }
                # code...
                foreach (array_unique($request->courses) as $key => $value) {
                    # code...
                    StudentSubject::create(['year_id'=>$year, 'semester_id'=>$semester, 'student_id'=>$student->id, 'course_id'=>$value]);
                }
            }
            // DB::commit();
            return back()->with('success', "!Done");
        } catch (\Throwable $th) {
            // DB::rollBack();
            return back()->with('error', $th->getFile().' : '.$th->getLine().' :: '.$th->getMessage());
        }
    }


    // public function register(Request $request)
    // {
    //     // $student = $student;

    //     // return response([
    //     //     'status' => 200,
    //     //     'student' => new StudentResource3($student)
    //     // ]);
    // }
}

