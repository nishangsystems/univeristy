<?php


namespace App\Http\Controllers\API\Student;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Resources\StudentResource3;
use App\Models\Batch;
use App\Models\CourseNotification;
use App\Models\Level;
use App\Models\Notification;
use App\Models\School;
use App\Models\StudentSubject;
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
    
    public function current_semester(Request $request) 
    {
        $student = auth('student_api')->user();
        $semester = Helpers::instance()->getSemester($student->_class()->id);
        return response()->json(['data'=> $semester]);
    }
    
    public function notifications(Request $request) 
    {
        $student = auth('student_api')->user();
        $class = $student->_class();
        $year = Helpers::instance()->getCurrentAccademicYear();
        
        $class_notificatoins = Notification::where('school_unit_id', '=', $class->program_id)
            ->where('level_id', '=', $class->level_id)
            ->where('campus_id', '=', $student->campus_id)->where(function($q){
                $q->where('visibility', '=', 'students')->orWhere('visibility', '=', 'general');
            })->get()->all();

        $program_notifications = Notification::where('school_unit_id', '=', $class->program_id)
            ->where('campus_id', '=', $student->campus_id)
            ->where(function($q){
                $q->where('visibility', '=', 'students')->orWhere('visibility', '=', 'general');
            })->get()->all();

        $departmental_notifications = Notification::where('school_unit_id', '=', $class->program->parent_id)
            ->where('campus_id', '=', $student->campus_id)->where(function($q){
                $q->where('visibility', '=', 'students')->orWhere('visibility', '=', 'general');
            })->get()->all();

        $school_notifications = Notification::whereNull('school_unit_id')
        ->where(function($q)use($class){
            $q->WhereNull('level_id')->orWhere('level_id', '=', $class->level_id ?? null);
        })->where(function($q)use($student){
             $q->where('campus_id', '=', $student->campus_id)->orWhere('campus_id', '=', 0);
        })->where(function($q){
            $q->where('visibility', '=', 'students')->orWhere('visibility', '=', 'general');
        })->get()->all();

        $semester = Helpers::instance()->getSemester($class->id);
        $course_ids = StudentSubject::where(['year_id'=>$year, 'student_id'=>$student->id, 'semester_id'=>$semester->id??null])->pluck('course_id');
        $course_notifications = CourseNotification::whereIn('course_id', $course_ids)
            ->where('status', '=', 1)->get()->each(function($rec){
                $rec->course = $rec->course;
            })->all();

        $notifications = array_merge($class_notificatoins, $program_notifications, $departmental_notifications, $school_notifications);
        return response()->json(['data'=>$notifications, 'course_notifications'=>$course_notifications]);
    }

}