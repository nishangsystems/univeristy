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
use App\Models\Campus;
use App\Models\TeachersSubject;

class TeacherController
{

    public function profile(Request $request){

        $user = $request->has('teacher') ? User::find($request->teacher) : auth('api')->user();
        $classes  = \App\Models\ProgramLevel::join('teachers_subjects', ['teachers_subjects.class_id'=>'program_levels.id'])
            ->where(['teachers_subjects.teacher_id'=>$user->id])
            ->where(['teachers_subjects.batch_id'=>Helpers::instance()->getCurrentAccademicYear()])
            ->distinct()
            ->select(['program_levels.*', 'teachers_subjects.campus_id'])
            ->get()->each(function($row){
                $row->name = $row->name();
            });

        $subjects = \App\Models\Subjects::join('teachers_subjects', ['teachers_subjects.subject_id'=>'subjects.id'])
            ->where(['teachers_subjects.teacher_id'=>$user->id])
            ->where(['teachers_subjects.batch_id'=>Helpers::instance()->getCurrentAccademicYear()])
            ->where(function($q)use ($request){
                $request->has('campus') ? $q->where(['teachers_subjects.campus_id'=>$request->campus]) : null;
            })->distinct()
            ->select(['subjects.*','teachers_subjects.class_id as class', 'teachers_subjects.campus_id'])->get()
            ->each(function($row){
                $row->_class = ProgramLevel::find($row->class)->name();
                $row->campus = Campus::find($row->campus_id)->name??'';
            });

        $campuses = $classes->pluck('campus_id')->toArray();
        $levels = $classes->pluck('level_id')->toArray();

        $nt = Notification::where(function($query){
            $query->where('visibility', 'teachers')->orWhere('visibility', 'general');
            })->where(function($query)use($campuses){
                $query->whereIn('campus_id', $campuses)->orWhere('campus_id', null)->orWhere('campus_id', 0);
            })->where(function($query)use($levels){
            })->where(function($query)use($campuses){
                $query->where('status', 1)->whereDate('date', '<=', now());
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

        $course_notifications = \App\Models\CourseNotification::where(['user_id'=>$user->id])->groupBy('course_id')->groupBy('campus_id')->get();

        $data = [
            'user'=>$user, 
            'classes' => $classes->count(),
            'courses' => $subjects->count(),
            'notifications' => $nt->count(),
            'course_notifications' => $course_notifications,
        ];

        return response()->json($data);

    }

    public function classes(Request $request){
        $data['success'] = 200;
        
        $teacher = auth('api')->user();
        if($request->_class != null){
            $class = ProgramLevel::find($request->_class);
            return response()->json(['unit'=>$class]);
        }

        $units  = \App\Models\ProgramLevel::join('teachers_subjects', ['teachers_subjects.class_id'=>'program_levels.id'])
            ->where(['teachers_subjects.teacher_id'=>$teacher->id])
            ->distinct()
            ->select(['program_levels.*', 'teachers_subjects.campus_id'])
            ->get();

        return response()->json(["success"=>200, "classes"=> TeacherClassResource::collection($units)]);

    }

    public function notifications(Request $request){
        
        $teacher = auth('api')->user();

        if($request->notification != null){
            return response()->json(['notification'=>Notification::find($request->notification)]);
        }

        $classes  = \App\Models\ProgramLevel::join('teachers_subjects', ['teachers_subjects.class_id'=>'program_levels.id'])
            ->where(['teachers_subjects.teacher_id'=>$teacher->id])
            ->orderBy('id', 'DESC')->distinct()
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

    public function course_notifications(Request $request){
        
        $teacher = $request->user('api');

        $course_notifications = \App\Models\CourseNotification::where(['user_id'=>$teacher->id])
            ->orderBy('id', 'DESC')->distinct()->get()->each(function($rec){
                $rec->campus = $rec->campus->name??'';
                $rec->course = $rec->course->code??$rec->course->name??'';
                $rec->audience = $rec->audience();
            });
        $data['success'] = 200;
        $data['notifications'] = $course_notifications;
        return response()->json($data);
    }

    public function all_course_notifications(Request $request){
        
        $teacher = $request->user('api');
        if($request->notification != null){
            $not = \App\Models\CourseNotification::find($request->application);
            return response()->json(['notification'=>$not]);
        }

        $course_notifications = \App\Models\CourseNotification::where(['user_id'=>$teacher->id])
            ->where(function($query)use($request){
                if($request->course != null)
                $query->where('course_id', $request->course);
            })
            ->orderBy('id', 'DESC')->distinct()->get()->each(function($rec){
                $rec->campus = $rec->campus->name??'';
                $rec->course = $rec->course->code??$rec->course->name??'';
                $rec->audience = $rec->audience();
            });
        $data['success'] = 200;
        $data['notifications'] = $course_notifications;
        return response()->json($data);
    }

    public function store_notification(Request $request){
        
        $teacher = $request->user('api');
        $validity = \Illuminate\Support\Facades\Validator::make($request->all(), ['title'=>'required', 'message'=>'required', 'course_id'=>'required', 'campus_id'=>'nullable']);

        if($validity->fails()){
            return response()->json(['message'=>$validity->errors()->first(), 'error_type'=>'general-error'], 400);
        }

        $data = [
            'user_id'=>$teacher->id, 
            'title'=>$request->title, 
            'message'=>$request->message, 
            'date'=>now()->addDays(14), 
            'course_id'=>$request->course_id, 
            'campus_id'=>$request->campus_id
        ];

        $instance = new \App\Models\CourseNotification($data);
        $instance->save();

        $res['notification'] = $instance;
        $res['success'] = 200;
        return response()->json($res);
    }

    public function subjects(Request $request){

        if($request->course != null){
            $course = \App\Models\Subjects::find($request->course);
            return response()->json(['course'=>$course]);
        }
        
        $teacher = $request->user('api');
        
        $data['teacher_id'] = $teacher->id;
        $data['subjects'] = \App\Models\Subjects::join('teachers_subjects', ['teachers_subjects.subject_id'=>'subjects.id'])
            ->where(['teachers_subjects.teacher_id'=>$teacher->id])
            ->where(['teachers_subjects.batch_id'=>Helpers::instance()->getCurrentAccademicYear()])
            ->join('campuses', ['campuses.id'=>'teacher_subjects.campus_id'])
            ->where(function($query)use($request){
                if($request->_class != null ) $query->where(['teachers_subjects.class_id'=>$request->_class]);
            })
            ->where(function($q)use ($request){
                $request->campus != null ? $q->where(['teachers_subjects.campus_id'=>$request->campus]) : null;
            })->distinct()
            ->select(['subjects.*','teachers_subjects.class_id as class', 'teachers_subjects.campus_id', 'campuses.name'])->get();

        $data['success'] = 200;
        return response()->json($data);
    }

    public function class_list(Request $request){
        
        $teacher = auth('api')->user();
        
        $class = ProgramLevel::find($request->_class);
        if($class == null){
            return response()->json(['message'=>"No class specified"], 400);
        }

        $campuses = \App\Models\TeachersSubject::where('teacher_id', $teacher->id)
            ->where('batch_id', Helpers::instance()->getCurrentAccademicYear())
            ->where('class_id', $class->id)
            ->pluck('campus_id')->toArray();

        $data['class'] = $class;

        $data['students'] = StudentClass::where('student_classes.class_id', '=', $class->id)
            ->where('year_id', '=', \App\Helpers\Helpers::instance()->getCurrentAccademicYear())
            ->join('students', ['students.id'=>'student_classes.student_id'])
            ->whereIn('students.campus_id', $campuses)
            ->join('campuses', ['campuses.id' => 'students.campus_id'])
            ->orderBy('students.name', 'ASC')->get(['students.*', 'campuses.name as campus_name', 'campuses.id as campus_id'])
            ->groupBy('campus_name');

        $data['success'] = 200;
        return response()->json($data);
        
    }

    public function course_list(Request $request){
        
        $teacher = auth('api')->user();
        
        $course = \App\Models\Subjects::find($request->course);
        if($course == null){
            return response()->json(['message'=>"No course specified"], 400);
        }

        $campuses = \App\Models\TeachersSubject::where('teacher_id', $teacher->id)
            ->where('batch_id', Helpers::instance()->getCurrentAccademicYear())
            ->where('subject_id', $course->id)
            ->pluck('campus_id')->toArray();

        $data['course'] = $course;

        $data['students'] = \App\Models\StudentSubject::where(['student_courses.course_id'=> $course->id,'student_courses.year_id'=> \App\Helpers\Helpers::instance()->getCurrentAccademicYear()])
            ->join('students', ['students.id'=>'student_courses.student_id'])
            ->whereIn('students.campus_id', $campuses)
            ->join('campuses', ['campuses.id' => 'students.campus_id'])
            ->orderBy('students.name', 'ASC')->distinct()->get(['students.*', 'campuses.name as campus_name', 'campuses.id as campus_id'])
            ->groupBy('campus_name');

        $data['success'] = 200;
        return response()->json($data);
        
    }

    public function student_profile(Request $request){
        
        $student = Students::find($request->student);
        if($student == null){
            return response()->json(['message'=>"student not found"], 400);
        }

        $data['student'] = $student;
        $data['unit'] = $student->_class();
        $data['success'] = 200;
        return response()->json($data);
    }

    public function attendance(Request $request, $class_id){
        $data = json_decode($request->get("data"), true);
         if($request->teacher_id){
             $teacher = User::find($request->teacher_id);
         }else{
             $teacher = auth('api')->user();
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
            $teacher = auth('api')->user();
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

    public function update_course_notification(request $request, $id){
        $update = $request->all();
        if(($notification  = \App\Models\CourseNotification::find($id)) != null){
            $data = collect($update)->filter(function($val, $key){ return in_array($key, ['title', 'message', 'date', 'status', 'course_id', 'campus_id']) and $val != null;});
            // $data = array_filter($update, function($val, $key){ return in_array($key, ['title', 'message', 'date', 'status', 'course_id', 'campus_id']);});
            // return "DEBUGGING HOLE 1";
            if($data->count() > 0){ $notification->update($data->all()); }
            return response()->json(['data'=>$notification]);
        }
        return response()->json(['message'=>"No notification was found with specified ID"], 400);
    }

    public function delete_course_notification(request $request, $id){
        
        $notification = \App\Models\CourseNotification::find($id);
        if($notification == null){
            return response()->json(['message'=>'No course notification was found with specified ID'], 400);
        }
        $notification->delete();
        return response()->json(['data'=>true]);
    }
}