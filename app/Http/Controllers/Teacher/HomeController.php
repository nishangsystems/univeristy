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
use Illuminate\Http\Request;
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
        if ($request->has('campus') && $request->campus != null) {
            # code...
            $data['attendance'] = Attendance::where(['year_id'=>$year, 'campus_id'=>$request->campus??null, 'teacher_id'=>$teacher->id, 'subject_id'=>$subject->id])->orderBy('id', 'DESC')->get();
            // dd($data);
            $data['content'] = Topic::where('subject_id', $subject->id)->where(function($q)use($request, $teacher){
                $q->where(['level'=>1])
                    // ->orWhere(['level'=>2, 'campus_id'=>$request->campus, 'teacher_id'=>$teacher->id])->whereNotNull('campus_id');
                    ->orWhere(['level'=>2, 'teacher_id'=>$teacher->id])->where('campus_id', $request->campus);
            })->get();
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
        $data['title'] = "Sign Course Log For ".$subject->name.'['.$subject->code.'] '.$attendance_record->check_in.' - '.$attendance_record->check_out;
        $data['subject'] = $subject;
        $data['campus'] = $campus;
        $data['topic'] = $topic;
        $data['attendance_record'] = $attendance_record;
        $data['log_history'] = CourseLog::join('topics', ['topics.id'=>'course_log.topic_id'])
                                ->where(['topics.subject_id'=>$subject->id, 'topics.teacher_id'=>auth()->id()])->orderBy('id', 'DESC')->distinct()
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
        $data = ['topic_id'=>$request->topic_id, 'campus_id'=>$request->campus_id, 'attendance_id'=>$request->attendance_id, 'details'=>$request->details];
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
}
