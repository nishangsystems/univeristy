<?php

namespace App\Http\Controllers\API\Teacher;

use App\Http\Resources\NotificationResource;
use App\Http\Resources\StudentAttendanceResource;
use App\Http\Resources\TeacherClassResource;
use App\Models\ClassMaster;
use App\Models\Notification;
use App\Models\ProgramLevel;
use App\Models\SchoolUnits;
use App\Models\StudentClass;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\DailyAttendance;
use App\Models\StudentAttendance;
use App\Models\Students;
use Carbon\Carbon;
use App\Helpers\Helpers;
use App\Models\TeachersSubject;

class TeacherController
{

    public function classes(Request $request){
        $data['success'] = 200;
        if($request->teacher_id){
            $teacher = User::find($request->teacher_id);
        }else{
            $teacher = Auth('api')->user();
        }

        $units  = \App\Models\ProgramLevel::join('teachers_subjects', ['teachers_subjects.class_id'=>'program_levels.id'])
            ->where(['teachers_subjects.teacher_id'=>$teacher->id])
            ->distinct()
            ->select(['program_levels.*', 'teachers_subjects.campus_id'])
            ->get();

        return response()->json(["success"=>200, "classes"=> TeacherClassResource::collection($units)]);

    }

    public function notifications(Request $request){
        if($request->teacher_id){
            $teacher = User::find($request->teacher_id);
        }else{
            $teacher = Auth('api')->user();
        }
        $classes  = \App\Models\ProgramLevel::join('teachers_subjects', ['teachers_subjects.class_id'=>'program_levels.id'])
            ->where(['teachers_subjects.teacher_id'=>$teacher->id])
            ->distinct()
            ->select(['program_levels.*', 'teachers_subjects.campus_id'])
            ->get();

        $campuses = $classes->pluck('campus_id')->toArray();
        $levels = $classes->pluck('level_id')->toArray();

        $nt = Notification::where(function($query){
            $query->where('visibility', 'teachers')->orWhere('visibility', 'general');
        })->where(function($query)use($campuses){
            $query->whereIn('campus_id', $campuses)->orWhere('campus_id', null)->orWhere('campus_id', 0);
        })->where(function($query)use($levels){
            $query->whereIn('level_id', $levels)->orWhere('level_id', null);
        })->get()
            ->filter(function($row) use ($classes){
                $sc_unit = SchoolUnits::find($row->school_unit_id);
                return ($row->school_unit_id == null && ($row->unit_id == 0 || $row->unit_id == null))
                    || (function() use ($classes, $sc_unit){
                        foreach ($classes as $key => $class)
                            if($sc_unit->has_unit(SchoolUnits::find($class->program_id)))return true;
                        return false;
                    });
        });

        $data['notifications'] = \App\Http\Resources\NotificationResource::collection($nt);
        $data['success'] = 200;
        return response()->json($data);
    }

    public function subjects(Request $request,$campus_id, $class_id){
        $unit = ProgramLevel::find($class_id);
        if($request->teacher_id){
            $teacher = User::find($request->teacher_id);
        }else{
            $teacher = Auth('api')->user();
        }
        $data['teacher_id'] = $teacher->id;
        $data['title'] = 'My '.$unit->program()->first()->name.' : LEVEL '.$unit->level()->first()->level;
        $data['subjects'] = \App\Models\Subjects::join('teachers_subjects', ['teachers_subjects.subject_id'=>'subjects.id'])
            ->where(['teachers_subjects.class_id'=>$class_id])
            ->where(['teachers_subjects.teacher_id'=>$teacher->id])
            ->where(function($q)use ($request, $campus_id){
                $request->has('campus') ? $q->where(['teachers_subjects.campus_id'=>$campus_id]):null;
            })->distinct()
            ->select(['subjects.*','teachers_subjects.class_id as class', 'teachers_subjects.campus_id'])->get();


        $data['success'] = 200;
        return response()->json($data);
    }


    public function students(Request $request, $campus_id, $_id){
        if($request->teacher_id){
            $teacher = User::find($request->teacher_id);
        }else{
            $teacher = Auth('api')->user();
        }

        if($request->get('type', 'course') === "class"){
            //if the _id is class id
            $class = ProgramLevel::find($_id);
            $data['class'] = $class;

            $data['students'] = StudentClass::where('class_id', '=', $_id)
                ->where('year_id', '=', \App\Helpers\Helpers::instance()->getCurrentAccademicYear())
                ->join('students', ['students.id'=>'student_classes.student_id'])
                ->where(function($q) use ($campus_id){
                    request()->has('campus') ? $q->where(['students.campus_id'=>$campus_id]) : null;
                })
                ->orderBy('students.name', 'ASC')->get('students.*');

            $data['success'] = 200;
            return response()->json($data);
        }else{
            //it is course it
            $class = ProgramLevel::find($_id);
            $data['class'] = $class;

            $year = $request->year ?? Helpers::instance()->getCurrentAccademicYear();
            $teacherSubject = TeachersSubject::where(['teacher_id'=>$teacher->id,'subject_id'=>$_id])->orderBy('id','DESC')->first();
            $semester = Helpers::instance()->getSemester(isset($teacherSubject)?$teacherSubject->class_id:"");
        
            $data['semester'] = $semester;
            $data['students'] = Students::whereHas('course_pivot', function($query) use ($year, $_id, $semester){
                                    $query->WHERE(['year_id'=>$year, 'course_id'=>$_id, 'semester_id'=>$semester]);
                                })->get();
            $data['success'] = 200;
            return response()->json($data);

        }
    }

    public function attendance(Request $request, $class_id){
        $data = json_decode($request->get("data"), true);
         if($request->teacher_id){
             $teacher = User::find($request->teacher_id);
         }else{
             $teacher = Auth('api')->user();
         }
 
         $year = \App\Helpers\Helpers::instance()->getYear();
         $course_id = $class_id;
         $dailyAttendance = DailyAttendance::where(["course_id"=>$course_id, "year"=>$year, 'teacher_id'=>$teacher->id])->whereDate('created_at',Carbon::today())->first();
         if(isset($dailyAttendance)){
             foreach ($dailyAttendance->attedance as $at){
                 $at->delete();
             }
             $dailyAttendance->delete();
         }
         $dailyAttendance = new DailyAttendance();
         $dailyAttendance->year = $year;
         $dailyAttendance->course_id = $course_id;
         $dailyAttendance->teacher_id = $teacher->id;
         $dailyAttendance->save();
 
        foreach ($data as $d){
            $student_id = $d["student_id"];
            $present = $d["status"]; // 1 , present , 0 absent
 
           if($present){
               $stA = new StudentAttendance();
               $stA->student_id = $student_id;
               $stA->year = $year;
               $stA->attendance = $dailyAttendance->id;
               $stA->course_id = $course_id;
               $stA->teacher_id = $teacher->id;
               $stA->save();
           }
        }
 
         $data['success'] = 200;
         $data['message'] = "Success";
         return response()->json($data);
     }

    public function studentAttendance(Request $request){
        if($request->teacher_id){
            $teacher = User::find($request->teacher_id);
        }else{
            $teacher = Auth('api')->user();
        }
        $course_id = $request->get('course_id');
        $class = ProgramLevel::find($course_id);
        $data['class'] = $class;
        $year = $request->year ?? Helpers::instance()->getCurrentAccademicYear();
        $teacherSubject = TeachersSubject::where(['teacher_id'=>$teacher->id,'subject_id'=>$course_id])->orderBy('id','DESC')->first();
        $semester = Helpers::instance()->getSemester(isset($teacherSubject)?$teacherSubject->class_id:"");

        $data['semester'] = $semester;
        $students = Students::whereHas('course_pivot', function($query) use ($year, $course_id, $semester){
            $query->WHERE(['year_id'=>$year, 'course_id'=>$course_id, 'semester_id'=>$semester]);
        })->get();
        $data['success'] = 200;
        $data['students'] = StudentAttendanceResource::collection($students);
        return response()->json($data);
    }
}