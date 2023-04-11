<?php

namespace App\Http\Controllers\Teacher;

use App\Helpers\Helpers;
use App\Models\Result;
use App\Models\SchoolUnits;
use App\Models\ClassSubject;
use App\Models\TeachersSubject;
use App\Models\Topic;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Campus;
use App\Models\CourseNotification;
use App\Models\OfflineResult;
use App\Models\ProgramLevel;
use App\Models\StudentClass;
use App\Models\Subjects;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use \Session;


class SubjectController extends Controller
{

    public function index(Request $request)
    {
        if ($request->class) {
           $unit = ProgramLevel::find($request->class);
           $data['title'] = 'My '.$unit->program()->first()->name.' : LEVEL '.$unit->level()->first()->level;
           $data['subjects'] = \App\Models\Subjects::join('teachers_subjects', ['teachers_subjects.subject_id'=>'subjects.id'])
                        ->where(['teachers_subjects.class_id'=>$request->class])
                        ->where(function($q)use ($request){
                            $request->has('campus') ? $q->where(['teachers_subjects.campus_id'=>$request->campus]):null;
                        })->distinct()->select(['subjects.*','teachers_subjects.class_id as class', 'teachers_subjects.campus_id'])->get();
        } else {
            $data['courses'] = \App\Models\TeachersSubject::where([
                'teacher_id' => auth()->id(),
                'batch_id' => \App\Helpers\Helpers::instance()->getCurrentAccademicYear(),
            ])->join('subjects', ['subjects.id'=>'teachers_subjects.subject_id'])
            ->distinct()->select('subjects.*', 'teachers_subjects.class_id as class', 'teachers_subjects.campus_id')->get();
        }
        // dd($data);
        return view('teacher.subjects')->with($data);
    }

    public function course_management(Request $request)
    {
        if ($request->class) {
           $unit = ProgramLevel::find($request->class);
           $data['title'] = 'My '.$unit->program()->first()->name.' : LEVEL '.$unit->level()->first()->level;
           $data['subjects'] = \App\Models\Subjects::join('teachers_subjects', ['teachers_subjects.subject_id'=>'subjects.id'])
                        ->where(['teachers_subjects.class_id'=>$request->class])
                        ->where(function($q)use ($request){
                            $request->has('campus') ? $q->where(['teachers_subjects.campus_id'=>$request->campus]):null;
                        })->distinct()->select(['subjects.*','teachers_subjects.class_id as class', 'teachers_subjects.campus_id'])->get();
        } else {
            $data['courses'] = \App\Models\TeachersSubject::where([
                'teacher_id' => auth()->id(),
                'batch_id' => \App\Helpers\Helpers::instance()->getCurrentAccademicYear(),
            ])->join('subjects', ['subjects.id'=>'teachers_subjects.subject_id'])
            ->distinct()->select('subjects.*', 'teachers_subjects.class_id as class', 'teachers_subjects.campus_id')->get();
        }
        // dd($data);
        return view('teacher.manage_courses')->with($data);
    }

    public function course_coverage_index()
    {
        # code...
        $data['title'] = "Course Coverage";
        $data['courses'] = \App\Models\TeachersSubject::where([
                    'teacher_id' => auth()->id(),
                    'batch_id' => \App\Helpers\Helpers::instance()->getCurrentAccademicYear(),
                ])->join('subjects', ['subjects.id'=>'teachers_subjects.subject_id'])
                ->distinct()->select('subjects.*', 'teachers_subjects.class_id as class', 'teachers_subjects.campus_id')->get();

        return view('teacher.course.coverage_index', $data);
    }

    public function course_coverage_show(Request $request)
    {
        # code...
        $data['title'] = "Course Coverage For ".Subjects::find($request->subject_id)->name;
        $data['topics'] = Topic::where('subject_id', $request->subject_id)->where('level', 1)->orWhere(function($q)use($request){
                $q->Where(['level'=>2, 'teacher_id'=>auth()->id()])->where('campus_id', $request->campus);
        })->get();
        $data['subject'] = Subjects::find($request->subject_id);
        // dd($data);
        return view('teacher.course.coverage', $data);
    }


    public function course_list(Request $request, $class_id, $course_id)
    {
        # code...

        $switch = $request->switch ?? null;
        if ($switch == 'true') {
            # code...
            $class = ProgramLevel::find($class_id);
            $data['title'] = "Course List For ".Subjects::find($course_id)->name.' [ '.Subjects::find($course_id)->code.' ] : '.\App\Models\Campus::find(request('campus_id'))->name;
            $data['students'] = StudentClass::where(['student_classes.year_id'=>Helpers::instance()->getCurrentAccademicYear()])
                        ->join('students', ['students.id'=>'student_classes.student_id'])
                        ->where(['students.campus_id'=>$request->campus_id])
                        ->join('student_courses', ['student_courses.student_id'=>'students.id'])
                        ->where(['student_courses.course_id'=>$course_id])
                        ->groupBy('student_classes.class_id', 'students.name')
                        ->select(['students.*', 'student_classes.class_id as class_id'])->get();
        } else {
            # code...
            $class = ProgramLevel::find($class_id);
            $data['title'] = "Class List For ".$class->program()->first()->name.': LEVEL '.$class->level()->first()->level.' ('.Subjects::find($course_id)->name.') : '.\App\Models\Campus::find(request('campus_id'))->name;
            $data['students'] = StudentClass::where(['student_classes.class_id'=>$class_id])
                        ->where(['student_classes.year_id'=>Helpers::instance()->getCurrentAccademicYear()])
                        ->join('students', ['students.id'=>'student_classes.student_id'])
                        ->where(['students.campus_id'=>$request->campus_id])
                        ->join('student_courses', ['student_courses.student_id'=>'students.id'])
                        ->where(['student_courses.course_id'=>$course_id])
                        ->select(['students.*'])->get();
        }
        

        
        return view('teacher.students', $data);
    }

    public function result($subject)
    {
       if(request('class')){
            $data['subject'] = ClassSubject::find($subject);
       }else{
            $data['subject'] = TeachersSubject::find($subject)->subject;
       }
        return view('teacher.result')->with($data);
    }

    public function store(Request $request)
    {
        $result = OfflineResult::where([
            'student_id' => $request->student,
            'class_id' => $request->class_id,
            'sequence' => $request->sequence,
            'subject_id' => $request->subject,
            'batch_id' => $request->year
        ])->first();

        if ($result == null) {
            $result = new OfflineResult();
        }

        $result->batch_id = $request->year;
        $result->student_id =  $request->student;
        $result->class_id =  $request->class_id;
        $result->sequence =  $request->sequence;
        $result->subject_id =  $request->subject;
        $result->score =  $request->score;
        $result->coef =  $request->coef;
        $result->remark = "";
        $result->class_subject_id =  $request->class_subject_id;
        $result->save();
    }


    // SECTION IN CHARGE OF COURSE NOTIFICATIONS
    public function notifications(Request $request, $course_id)
    {
        # code...
        $subject = Subjects::find($course_id);
        $data['title'] = "Course Notifications For ".$subject->code.' '.$subject->name;
        return view('teacher.course.notifications.index', $data);
    }
    public function create_notification(Request $request, $course_id)
    {
        # code...
        $subject = Subjects::find($course_id);
        $data['title'] = "Create Course Notification For ".$subject->code.' '.$subject->name;
        return view('teacher.course.notifications.create', $data);
    }
    public function save_notification(Request $request, $course_id)
    {
        # code...
        // return $request->all();
        $request->validate([
            'title'=>'required',
            'date'=>'required',
            'message'=>'required'
        ]);
        try {
            //code...
            $not = $request->all();
            $not['course_id'] = $course_id;
            $not['user_id'] = auth()->id();
            CourseNotification::create($not);
            return redirect(route('course.notification.index', $course_id))->with('success', 'Done');
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('error', 'Operation failed '.$th->getMessage());
        }
    }
    public function edit_notification(Request $request, $course_id, $id)
    {
        # code...
        $subject = Subjects::find($course_id);
        $data['item'] = CourseNotification::find($id);
        $data['title'] = "Create Course Notification For ".$subject->code.' '.$subject->name.' - '.$data['item']->title;
        return view('teacher.course.notifications.edit', $data);
    }
    public function update_notification(Request $request, $course_id, $id)
    {
        # code...
        $request->validate([
            'title'=>'required',
            'date'=>'required',
            'message'=>'required',
        ]);
        try {
            //code...
            $data = $request->all();
            $data['course_id'] = $course_id;
            $data['user_id'] = auth()->id();
            $not = CourseNotification::find($id);
            $not->fill($request->all());
            $not->save();
            return redirect(route('course.notification.index', $course_id))->with('success', 'Done');
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('error', 'Operation failed '.$th->getMessage());
        }
    }
    public function drop_notification(Request $request, $course_id, $id)
    {
        # code...
        CourseNotification::find($id)->delete();
        return back()->with('success', 'Done');
    }
    public function show_notification(Request $request, $course_id, $id)
    {
        # code...
        $subject = Subjects::find($course_id);
        $data['notification'] = CourseNotification::find($id);
        $data['title'] = "Create Course Notification For ".$subject->code.' '.$subject->name.' - '.$data['notification']->title;
        return view('teacher.course.notifications.show', $data);
    }

    public function result_template(Request $request)
    {
        # code...
        if($request->switch == 'true'){
            $data['title'] = "Course Result Template For ".Subjects::find($request->course_id)->name.' - '.ProgramLevel::find($request->class_id)->name().' - '.Campus::find($request->campus_id)->name.' - '.Batch::find($this->current_accademic_year)->name;
            $data['students'] = StudentClass::where(['student_classes.year_id'=>Helpers::instance()->getCurrentAccademicYear()])
                        ->where(['student_classes.class_id'=>$request->class_id])
                        ->join('students', ['students.id'=>'student_classes.student_id'])
                        ->where(['students.campus_id'=>$request->campus_id])
                        ->join('student_courses', ['student_courses.student_id'=>'students.id'])
                        ->where(['student_courses.course_id'=>$request->course_id])
                        ->select(['students.id', 'students.matric'])->distinct()->get();
        }else{
            $data['title'] = "Class Result Template For ".Subjects::find($request->course_id)->name.' - '.ProgramLevel::find($request->class_id)->name().' - '.Campus::find($request->campus_id)->name.' - '.Batch::find($this->current_accademic_year)->name;
            $data['students'] = ProgramLevel::find($request->class_id)->_students($this->current_accademic_year)->distinct()->get(['students.id', 'students.matric']);
            // dd($data);
        }
        return view('teacher.result_template', $data);
    }

    public function course_content(Request $request)
    {
        # code...
        
        $subject = Subjects::find($request->subject_id);
        // dd($subject);
        if(!($subject == null)){
            $campus = $request->campus??0;
            $data['title'] = "Course Content For ".$subject->name.", ".TeachersSubject::where(['teacher_id'=>auth()->id(), 'subject_id'=>$request->subject_id])->first()->class->name();
            if($request->parent_id != null && $request->parent_id != 0){
                $data['title'] = '<h4 class="text-danger"><label> Sub topics Under '.Topic::find($request->parent_id)->title.'</label><span class="text-secondary mx-1 fa fa-caret-right"></span> '.$subject->name.'<span class="text-secondary mx-1 fa fa-caret-right"></span>'.TeachersSubject::where(['teacher_id'=>auth()->id(), 'subject_id'=>$request->subject_id])->first()->class->name();
            }
            $data['content'] = Topic::where(['subject_id'=>$subject->id, 'level'=>$request->level??1, 'parent_id'=>$request->parent_id??0])
                                    ->where(function($q)use($request, $campus){
                                        $request->level??1 == 2 ? $q->where(['teacher_id'=>auth()->id(), 'campus_id'=>$campus]): null;
                                    })->orderBy('id', 'DESC')->get();
            $data['level'] = $request->level??1;
            $data['parent_id'] = $request->parent_id??0;
            $data['subject_id'] = $request->subject_id??0;
            return view('teacher.course.content', $data);
        }
    }

    public function course_content_edit(Request $request)
    {
        # code...
        
        $subject = Subjects::find($request->subject_id);
        // dd($subject);
        if(!($subject == null)){
            $campus = $request->campus??0;
            $item = Topic::find($request->topic_id);
            // dd($item);
            $data['title'] = "Edit : ".$item->title;
            $data['content'] = Topic::where(['subject_id'=>$subject->id, 'level'=>$item->level, 'parent_id'=>$item->parent_id??0])
                                ->where(function($q)use($item, $campus){
                                    $item->level == 2 ? $q->where(['teacher_id'=>auth()->id(), 'campus_id'=>$campus]): null;
                                })->orderBy('id', 'DESC')->get();
            $data['level'] = $item->level??1;
            $data['topic'] = $item;
            $data['parent_id'] = $item->parent_id??0;
            $data['subject_id'] = $request->subject_id??0;
            return view('teacher.course.edit_content', $data);
        }
    }

    public function create_content_save(Request $request)
    {
        # code...
        // return $request->all();
        $data = ['title'=>$request->title, 'subject_id'=>$request->subject_id, 'level'=>$request->level, 'parent_id'=>$request->parent_id, 'duration'=>$request->duration??null, 'week'=>$request->week??null, 'teacher_id'=>$request->teacher_id??null, 'campus_id'=>$request->campus_id??null];
        $instance = new Topic($data);
        $instance->save();
        return back()->with('success', __('text.word_done'));
    }

    public function course_content_update(Request $request)
    {
        // return $request->all();
        # code...
        $data = ['title'=>$request->title, 'duration'=>$request->duration??null, 'week'=>$request->week??null];
        $instance = Topic::find($request->topic_id);
        // return $data;
        $instance->update($data);
        // return $instance;
        // $instance->save();
        if($instance->level == 1){
            return redirect(route('user.subject.content', ['subject_id'=>$instance->subject_id]))->with('success', __('text.word_done'));
        }
        return redirect(route('user.subject.content', ['subject_id'=>$instance->subject_id, 'level'=>$instance->level, 'parent_id'=>$instance->parent_id]).'?campus='.$request->campus)->with('success', __('text.word_done'));
    }

    public function course_objective(Request $request){
        $data['title'] = "Set Course Objectives  For ".Subjects::find($request->subject_id)->name;
        $data['subject_id'] = $request->subject_id;
        $data['subject'] = Subjects::find($request->subject_id);
        return view('teacher.course.set_objective', $data);
    }

    public function course_objective_save(Request $request)
    {
        # code...
        // $request->validate(['objective'=>'required']);
        $subject = Subjects::find($request->subject_id);
        // dd($subject);
        if($subject != null){
            if($request->has('objective')){
                $subject->objective  = $request->objective;
                $subject->save();
                return back()->with('success', __('text.word_done'));
            }
            if($request->has('outcomes')){
                $subject->outcomes  = $request->outcomes;
                $subject->save();
                return back()->with('success', __('text.word_done'));
            }
            return back();
        }
        return back()->with('error', __('text.item_not_found', ['item'=>__('text.word_course')]));
    }
}
