<?php

namespace App\Http\Controllers\Teacher;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\ClassSubject;
use App\Models\ProgramLevel;
use App\Models\SchoolUnits;
use App\Models\Subjects;
use App\Models\Topic;
use App\Models\Campus;
use App\Models\CourseLog;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
class HomeController extends Controller
{

    public function index(){

        return view('teacher.dashboard');
    }

    public function program_levels($program_id)
    {
        $data['title'] = "Program Levels for ".\App\Models\SchoolUnits::find($program_id)->name;
        $data['program_levels'] =  \App\Models\ProgramLevel::where('program_id', $program_id)->pluck('level_id')->toArray();
        return view('teacher.units.program-levels', $data);
    }

    public function program_index($department_id)
    {
        # code...
        $data['title'] = "Manage Programs";
        $data['programs'] = \App\Models\SchoolUnits::where('unit_id', 4)->where('parent_id', $department_id)->get();
        // dd($data);
        return view('teacher.units.programs', $data);
    }

    public function manage_courses($program_level_id)
    {
        $parent = \App\Models\ProgramLevel::find($program_level_id);
        $data['parent'] = $parent;
        // return $parent;
        
        $data['title'] = "Manage subjects under " . $parent->program()->first()->name .' Level '.$parent->level()->first()->level;
        return view('teacher.units.manage_subjects')->with($data);
    }

    public function edit_course(Request $request)
    {
        $class_subject = ClassSubject::where(['class_id'=>$request->program_level_id, 'subject_id'=>$request->subject_id])->first();
        // return $parent;
        $data['class_subject'] = $class_subject;
        $data['title'] = "Edit course: ".$class_subject->class->name().'; ' . $class_subject->subject->name .' [ '.$class_subject->subject->code.' ] ';
        // dd($data);
        return view('teacher.units.edit_subject')->with($data);
    }

    public function update_course(Request $request)
    {
        # code...
        // $request->validate(['hours'=>'numeric', 'coef'=>'numeric']);
        // return $request->all();
        $class_subject = ClassSubject::find($request->_id);
        if($class_subject != null){
            // return $request->all();
            $class_subject->hours = $request->hours;
            $class_subject->coef = $request->coef;

            $class_subject->save();
            return redirect(route('user.programs.courses', ['program_level_id'=>$request->program_level_id]))->with('success', __('text.word_done'));
        }
        return back();
    }

    
    public function save_courses(Request  $request, $program_level_id)
    {
        $pl = ProgramLevel::find(request('program_level_id'));
        $class_subjects = [];
        $validator = Validator::make($request->all(), [
            'subjects' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $parent = $pl;

        $new_subjects = $request->subjects;
        // if($parent != null)
        foreach ($parent->subjects()->get() as $subject) {
            array_push($class_subjects, $subject->subject_id);
        }

        foreach ($new_subjects as $subject) {
            if (!in_array($subject, $class_subjects)) {
                if(\App\Models\ClassSubject::where('class_id', $pl->id)->where('subject_id', $subject)->count()>0){
                    continue;
                }
                \App\Models\ClassSubject::create([
                    'class_id' => $pl->id,
                    'subject_id' => $subject,
                    'status'=> \App\Models\Subjects::find($subject)->status,
                    'coef'=> \App\Models\Subjects::find($subject)->coef
                ]);
            }
        }

        foreach ($class_subjects as $k => $subject) {
            if (!in_array($subject, $new_subjects)) {
                ClassSubject::where('class_id', $pl->id)->where('subject_id', $subject)->count() > 0 ?
                ClassSubject::where('class_id', $pl->id)->where('subject_id', $subject)->first()->delete() : null;
            }
        }

        $data['title'] = "Manage subjects under " . $parent->name;
        return redirect()->back()->with('success', "Subjects Saved Successfully");
    }

    public function unit_courses($program_level_id)
    {
        $parent = \App\Models\ProgramLevel::find($program_level_id);
        $data['title'] = "Subjects under " . \App\Http\Controllers\Admin\StudentController::baseClasses()[$parent->program_id].' Level '.$parent->level()->first()->level;
        $data['parent'] = $parent;
        // dd($parent->subjects()->get());
        $data['subjects'] = ProgramLevel::find($program_level_id)->subjects()->get();
        return view('teacher.units.subjects')->with($data);
    }

    public function  edit_class_course($program_level_id, $id)
    {
        $data['parent'] = SchoolUnits::find($program_level_id);
        $data['subject'] = DB::table('class_subjects')
            ->join('school_units', ['school_units.id' => 'class_subjects.class_id'])
            ->join('subjects', ['subjects.id' => 'class_subjects.subject_id'])
            ->where('class_subjects.class_id', $program_level_id)
            ->where('class_subjects.subject_id', $id)
            ->select('class_subjects.subject_id', 'subjects.name', 'class_subjects.coef')
            ->first();
        $data['title'] = 'Edit ' . $data['subject']->name . ' for ' . $data['parent']->name;
        return view('teacher.class_subjects.edit')->with($data);
    }

    public function update_class_course(Request $request, $program_level_id, $id)
    {

        $class_subject = ClassSubject::where('class_id', $program_level_id)->where('subject_id', $id)->first();
        $class_subject->update([
            'coef' => $request->coef
        ]);
        return redirect()->route('teacher.units.subjects', $program_level_id)->with('success', 'Updated class subject successfully');
    }

    public function course_log_index(Request $request)
    {
        # code...
        // return $request->campus;
        $subject = Subjects::find($request->subject_id);
        $year = Helpers::instance()->getCurrentAccademicYear();
        $teacher = auth()->user();
        $data['title'] = "Sign Course Log For ".$subject->name.' [ '.$subject->code.' ] ';
        $data['attendance'] = collect([]);
        $data['content'] = collect([]);
        if ($request->has('campus') && $request->campus != null) {
            # code...
            $data['attendance'] = Attendance::where(['year_id'=>$year, 'campus_id'=>$request->campus??null, 'teacher_id'=>$teacher->id, 'subject_id'=>$subject->id])->orderBy('id', 'DESC')->get();
            // dd($data);
            $data['content'] = Topic::where(['subject_id'=>$subject->id, 'level'=>2, 'teacher_id'=>$teacher->id])->where('campus_id', $request->campus)->get();
        }
        return view('teacher.log.index', $data);
    }

    public function course_log_sign(Request $request)
    {
        # code...
        $subject = Subjects::find($request->subject_id);
        $campus = Campus::find($request->campus_id);
        $topic = Topic::find($request->topic_id);
        $attendance_record = Attendance::find($request->attendance_id);
        $data['title'] = "Sign Course Log For ".$subject->name.'['.$subject->code.'] <br><b class="text-primary pt-3 d-block">'.date('d/m/Y H:i', strtotime($attendance_record->check_in)).' - '.date('d/m/Y H:i', strtotime($attendance_record->check_out)).'</b>';
        $data['subject'] = $subject;
        $data['campus'] = $campus;
        $data['topic'] = $topic;
        $data['attendance_record'] = $attendance_record;
        $data['log_history'] = CourseLog::where(['year_id'=>$this->current_accademic_year])->join('topics', ['topics.id'=>'course_log.topic_id'])
                                ->where(['topics.subject_id'=>$subject->id, 'topics.teacher_id'=>auth()->id()])->orderBy('course_log.id', 'DESC')->distinct()
                                ->select(['course_log.*'])->get();
        
        return view('teacher.log.sign', $data);
    }

    public function course_log_save(Request $request)
    {
        # code...
        // return $request->all();
        $request->validate([
            'topic_id'=>'required', 'campus_id'=>'required', 'attendance_id'=>'required'
        ]);
        $data = ['topic_id'=>$request->topic_id, 'campus_id'=>$request->campus_id, 'attendance_id'=>$request->attendance_id, 'details'=>$request->details, 'year_id'=>$this->current_accademic_year];
        $instance = new CourseLog($data);
        $instance->save();
        return back()->with('success', __('text.word_done'));
    }

    public function delete_course_log(Request $request)
    {
        # code...
        $instance = CourseLog::find($request->log_id);
        if($instance != null){
            $instance->delete();
        }
        return back()->with('success', __('text.word_done'));
    }

    public function attendance_bycourse_index()
    {
        # code...
        $data['title'] = "Attendance By Course";
        $data['courses'] = \App\Models\TeachersSubject::where(['teacher_id' => auth()->id(),'batch_id' => \App\Helpers\Helpers::instance()->getCurrentAccademicYear(),
            ])->join('subjects', ['subjects.id'=>'teachers_subjects.subject_id'])
            ->distinct()->select('subjects.*', 'teachers_subjects.class_id as class', 'teachers_subjects.campus_id')->get();
        return view('teacher.attendance.bycourse_index', $data);
    }

    public function attendance_bycourse(Request $request)
    {
        # code...
        $data['title'] = 'Attendance By Course For <b class="text-primary">'.Subjects::find($request->subject_id)->name.'</b>';
        $data['attendance'] = Attendance::where(['subject_id'=>$request->subject_id, 'year_id'=>Helpers::instance()->getCurrentAccademicYear(), 'teacher_id'=>auth()->id()])->whereNotNull('check_out')
                                ->where(function($q)use($request){
                                   ( $request->has('campus') && $request->campus != null) ? $q->where('campus_id', $request->campus) : null;
                                })->get();
        // dd($data);
        $time_hours = 0;
        foreach($data['attendance'] as $row){
            $time_hours += Date::parse($row->check_out)->floatDiffInHours(Date::parse($row->check_in));
        }
        $data['total_hours'] = round($time_hours);
        return view('teacher.attendance.bycourse', $data);
    }

    public function attendance_bymonth_index(Request $request)
    {
        # code...
        
        $data['title'] = "Monthly Attendace Record ".($request->has('month')? '<b class="text-primary">For '.$request->month : null).'</b>';
        $data['courses'] = \App\Models\TeachersSubject::where(['teacher_id' => auth()->id(),'batch_id' => \App\Helpers\Helpers::instance()->getCurrentAccademicYear()])
                            ->join('subjects', ['subjects.id'=>'teachers_subjects.subject_id'])
                            ->distinct()->select('subjects.*', 'teachers_subjects.class_id as class_id', 'teachers_subjects.campus_id')->get();
        $data['attendance'] = Attendance::where(['year_id'=>Helpers::instance()->getCurrentAccademicYear(), 'teacher_id'=>auth()->id()])->whereIn('subject_id', $data['courses']->pluck('id')->toArray())->get();
        // dd($data);
        $details = array_map(function($course_id)use($request){
            $sum = 0;
            $_arr = Attendance::where(['year_id'=>Helpers::instance()->getCurrentAccademicYear(), 'teacher_id'=>auth()->id(), 'subject_id'=>$course_id])->whereNotNull('check_out')->where(function($q)use($request){
                ($request->has('month') and $request->month != null) ? $q->whereMonth('check_in', Date::parse($request->month)->month)->whereYear('check_in', Date::parse($request->month)->year) : null;
            })->get();
            foreach($_arr as $arr){
                $sum += Date::parse($arr->check_out)->floatDiffInHours(Date::parse($arr->check_in));
            }
            return ['id'=>$course_id, 'hours'=>round($sum)];
        }, $data['courses']->pluck('id')->toArray());
        $data['details'] = collect($details)->filter(function($el){return $el['hours'] > 0;});
        // dd($data);
        return view('teacher.attendance.monthly_index', $data);
    }

    public function course_report(Request $request)
    {
        # code...
        $filters = [
            'year' => $request->year ?? Helpers::instance()->getCurrentAccademicYear(),
            'campus' => $request->campus ?? auth()->user()->campus_id,
            'semester' => $request->semester ?? Helpers::instance()->getSemester($request->program_level_id)->id
        ];
        $class = ProgramLevel::find($request->program_level_id);
        $data['title'] = "Course Report For ".$class->name();
        $class_subjects = collect($class->class_subjects_by_semester($filters['semester']));
        // dd($class_subjects);
        $data['data'] = array_map(function($el)use($request, $class_subjects, $filters){
            $record['id'] = $el;
            $record['name'] = $class_subjects->firstWhere('id', '=', $el)->subject->name.' [ '.$class_subjects->firstWhere('id', '=', $el)->subject->code.' ] ';
            $record['status'] = $class_subjects->where('id', $el)->first()->status;
            $record['cv'] = $class_subjects->where('id', $el)->first()->coef;
            $record['hours'] = $class_subjects->where('id', $el)->first()->hours;
                $hours_covered = Attendance::where('subject_id', $class_subjects->firstWhere('id', '=', $el)->subject_id)
                            ->join('subjects', ['subjects.id'=>'attendance.subject_id'])
                            ->where(['campus_id'=>$filters['campus'], 'year_id'=>$filters['year']])
                            ->whereNotNull('check_out')
                            ->where('subjects.semester_id', $filters['semester'])
                            ->get(['attendance.*']);
                            
                $_hrs = 0;
                foreach ($hours_covered as $key => $_rw) {
                    $_hrs += Date::parse($_rw->check_out)->floatDiffInHours(Date::parse($_rw->check_in));
                }
            $record['hours_covered'] = round($_hrs);

            $record['topics'] = Topic::where('subject_id', '=' ,$class_subjects->firstWhere('id', '=', $el)->subject_id)
                                ->where('campus_id', $filters['campus'])->where('level', 2)->count();
            
            $record['topics_taught'] = Topic::where('subject_id', '=' ,$class_subjects->firstWhere('id', '=', $el)->subject_id)
                                ->where('topics.campus_id', $filters['campus'])->where('level', 2)
                                ->join('course_log', ['course_log.topic_id'=>'topics.id'])->distinct()->count();
            
            
            return $record;
        }, $class_subjects->pluck('id')->toArray());
        // dd($data);
        return view('teacher.course.report', $data);
    }

    public function notifications(Request $request)
    {
        # code...
        $classes  = \App\Models\ProgramLevel::join('teachers_subjects', ['teachers_subjects.class_id'=>'program_levels.id'])
            ->where(['teachers_subjects.teacher_id'=>auth()->id()])
            ->distinct()
            ->select(['program_levels.*', 'teachers_subjects.campus_id'])
            ->get();

        $campuses = $classes->pluck('campus_id')->toArray();
        $levels = $classes->pluck('level_id')->toArray();
        
        $data['notifications'] = Notification::where(function($query){
            $query->where('visibility', 'teachers')->orWhere('visibility', 'general');
        })->where(function($query)use($campuses){
            $query->whereIn('campus_id', $campuses)->orWhere('campus_id', null)->orWhere('campus_id', 0);
        })->where(function($query)use($levels){
            $query->whereIn('level_id', $levels)->orWhere('level_id', null);
        })->get()
        ->filter(function($row)use($classes){
                $sc_unit = SchoolUnits::find($row->school_unit_id);
                return ($row->school_unit_id == null && ($row->unit_id == 0 || $row->unit_id == null))
                || function()use($classes, $sc_unit){
                    foreach ($classes as $key => $class)
                        if($sc_unit->has_unit(SchoolUnits::find($class->program_id)))return true;
                    return false;
                };
        });
        $data['title'] = __('text.word_notifications');
        $data['can_create'] = false;
        // dd($data);
        return view('teacher.notification.my_index', $data);
    }
}
