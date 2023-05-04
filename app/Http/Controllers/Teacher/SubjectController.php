<?php

namespace App\Http\Controllers\Teacher;

use App\Helpers\Helpers;
use App\Models\Result;
use App\Models\SchoolUnits;
use App\Models\ClassSubject;
use App\Models\Semester;
use App\Models\Students;
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
use Illuminate\Support\Facades\Validator;
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

    public function result($subject, $class_id)
    {
    //    if(request('class')){
    //         $data['subject'] = ClassSubject::find($subject);
    //    }else{
        //    }
        //    dd($data);
        $data['class'] = ProgramLevel::find($class_id);
        $data['subject'] = Subjects::find($subject);
        $data['ca_total'] = Helpers::instance()->ca_total(request('class_id'));
        $data['exam_total'] = Helpers::instance()->exam_total(request('class_id'));
        $data['semester'] = ProgramLevel::find(request('class_id'))->program->background->currentSemesters()->first();
        return view('teacher.result')->with($data);
    }

    public function store(Request $request)
    {
        $result = OfflineResult::where([
            'student_id' => $request->student_id,
            'student_matric' => $request->student_matric,
            'class_id' => $request->class_id,
            'semester_id' => $request->semester,
            'subject_id' => $request->subject,
            'batch_id' => $request->year
        ])->first();

        if ($result == null) {
            $result = new OfflineResult();
        }

        $result->batch_id = $request->year;
        $result->student_id =  $request->student;
        $result->student_matric =  $request->student_matric;
        $result->class_id =  $request->class_id;
        $result->semester_id =  $request->semester_id;
        $result->subject_id =  $request->subject;
        $result->subject_code =  Subjects::find($request->subject)->code;
        $result->ca_score =  $request->ca_score??$result->ca_score??null;
        $result->exam_score =  $request->exam_score??$result->exam_score??null;
        $result->coef =  $request->coef;
        $result->remark = "";
        $result->class_subject_id =  $request->class_subject_id;
        $result->save();
        return $request->all();
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

    public function import_results()
    {
        # code...
        // check if exam total is set for this program
        if (!Helpers::instance()->exam_total_isset(request('class_id'))) {
            # code...
            return back()->with('error',  __('text.exam_total_not_set_for', ['program'=>__('text.word_program')]));
        }
        
        $subject = Subjects::find(request('course_id'));
        $data['title'] = __('text.import_ca_and_exam_results_for', ['course'=>"[ ".$subject->code." ] ".$subject->name, 'class'=>ProgramLevel::find(request('class_id'))->name()]);
        return view('teacher.results.import_exam', $data);
    }

    public function import_results_save(Request $request)
    {
        # code...
        $check = Validator::make($request->all(), [
            'reference'=>'required',
            'file'=>'required|file'
        ]);
        if($check->fails()){
            return back()->with('error', $check->errors()->first());
        }

        $ca_total = Helpers::instance()->ca_total(request('class_id'));
        $exam_total = Helpers::instance()->exam_total(request('class_id'));

        $file = $request->file('file');
        if($file != null &&$file->getClientOriginalExtension() == 'csv'){
            $filename = 'ca_and_exam_'.random_int(1000, 9999).'_'.time().'.'.$file->getClientOriginalExtension();
            $file->move(storage_path('app/files'), $filename);

            $file_pointer = fopen(storage_path('app/files').'/'.$filename, 'r');

            $imported_data = [];
            $course = Subjects::find($request->course_id);
            $year = Helpers::instance()->getCurrentAccademicYear();
            $semester = $request->has('semester_id') ? Semester::find($request->semester_id) : Helpers::instance()->getSemester($request->class_id);
            
            while(($row = fgetcsv($file_pointer, 100, ',')) != null){
                if(is_numeric($row[1]))
                $imported_data[] = [$row[0], $row[1], $row[2]];
            }
            if(count($imported_data)==0){
                return back()->with('error', __('text.empty_or_wrong_data_format'));
            }

            $bad_results = 0;
            $null_students = '';
            $existing_results = '';
            foreach($imported_data as $data){
                if ($data[1] > $ca_total || $data[2] > $exam_total) {
                    # code...
                    $bad_results++;
                    continue;
                }
                $student = Students::where(['matric'=>$data[0]])->first() ?? null;
                if($student != null){
                    $base=[
                        'batch_id' => $year, 
                        // 'subject_id' => $request->course_id,
                        'subject_code' => Subjects::find($request->course_id)->code,
                        // 'student_id' => $student->id,
                        'student_matric' => $data[0],
                        'class_id' => $request->class_id,
                        'semester_id' => $semester->id,
                        'coef'=>$course->_class_subject($request->class_id)->coef??$course->coef,
                        'class_subject_id'=>$course->_class_subject($request->class_id)->id??0
                    ];
                    if(OfflineResult::where($base)->whereNotNull('ca_score')->count()>0){
                        $existing_results .= "<br> ".__('text.ca_results_already_exist_for', ['item'=>$data[0]]);
                    }elseif (!$data[1] == null) {
                        # code...
                        OfflineResult::updateOrCreate($base, ['ca_score'=>$data[1], 'reference'=>$request->reference, 'user_id'=>auth()->id(),  'campus_id'=>$student->campus_id, 'student_id'=>$student->id, 'subject_id' => $request->course_id]);
                    }
                    if(OfflineResult::where($base)->whereNotNull('exam_score')->count()>0){
                        $existing_results .= "<br> ".__('text.exam_results_already_exist_for', ['item'=>$data[0]]);
                    }elseif (!$data[2] == null) {
                        # code...
                        OfflineResult::updateOrCreate($base, ['exam_score'=>$data[1], 'reference'=>$request->reference, 'user_id'=>auth()->id(),  'campus_id'=>$student->campus_id, 'student_id'=>$student->id, 'subject_id' => $request->course_id]);
                    }
                }
                else{
                    $null_students .= __('text.student_matric_not_found', ['matric'=>$data[0]])." <br>";
                }
            }
            if($bad_results > 1){
                return back()->with('message', __('text.word_done').'. ' . ($bad_results == 0 ? '' : $bad_results . ' '.__('text.records_not_imported_phrase')) . $null_students . $existing_results);
            }
            return back()->with('success', __('text.word_done'));
        }else{
            return back()->with('error', __('text.file_type_constraint', ['type'=>'.csv']));
        }
    }

    public function import_ca()
    {
        # code...
        // check if CA total is set forthis program
        if (!Helpers::instance()->ca_total_isset(request('class_id'))) {
            # code...
            return back()->with('error',  __('text.CA_total_not_set_for', ['program'=>__('text.word_program')]));
        }

        $subject = Subjects::find(request('course_id'));
        $data['title'] = __('text.import_CA_results_for', ['course'=>"[ ".$subject->code." ] ".$subject->name, 'class'=>ProgramLevel::find(request('class_id'))->name()]);
        return view('teacher.results.import_ca', $data);
    }

    public function import_ca_save(Request $request)
    {
        # code...
        $check = Validator::make($request->all(), [
            'reference'=>'required',
            'file'=>'required|file'
        ]);

        $ca_total = Helpers::instance()->ca_total(request('class_id'));
        if($check->fails()){
            return back()->with('error', $check->errors()->first());
        }
        $file = $request->file('file');
        if($file != null &&$file->getClientOriginalExtension() == 'csv'){
            $filename = 'ca_'.random_int(1000, 9999).'_'.time().'.'.$file->getClientOriginalExtension();
            $file->move(storage_path('app/files'), $filename);

            $file_pointer = fopen(storage_path('app/files').'/'.$filename, 'r');

            $imported_data = [];
            $course = Subjects::find($request->course_id);
            $year = Helpers::instance()->getCurrentAccademicYear();
            $semester = $request->has('semester_id') ? Semester::find($request->semester_id) : Helpers::instance()->getSemester($request->class_id);
            
            while(($row = fgetcsv($file_pointer, 100, ',')) != null){
                if(is_numeric($row[1]))
                $imported_data[] = [$row[0], $row[1]];
            }
            if(count($imported_data)==0){
                return back()->with('error', __('text.empty_or_wrong_data_format'));
            }

            $bad_results = 0;
            $null_students = '';
            foreach($imported_data as $data){
                if ($data[1] > $ca_total) {
                    # code...
                    $bad_results++;
                    continue;
                }
                $student = Students::where(['matric'=>$data[0]])->first() ?? null;
                if($student != null){
                    $base=[
                        'batch_id' => $year, 
                        // 'subject_id' => $request->course_id,
                        'subject_code' => Subjects::find($request->course_id)->code,
                        // 'student_id' => $student->id,
                        'student_matric' => $data[0],
                        'class_id' => $request->class_id,
                        'semester_id' => $semester->id,
                        'coef'=>$course->_class_subject($request->class_id)->coef??$course->coef,
                        'class_subject_id'=>$course->_class_subject($request->class_id)->id??0
                    ];
                    if(OfflineResult::where($base)->whereNotNull('ca_score')->count() == 0){
                        OfflineResult::updateOrCreate($base, ['ca_score'=>$data[1], 'reference'=>$request->reference, 'user_id'=>auth()->id(), 'campus_id'=>$student->campus_id, 'student_id'=>$student->id, 'subject_id' => $request->course_id]);
                    }
                }else{
                    $null_students .= __('text.student_matric_not_found', ['matric'=>$data[0]]);
                }
            }
            if($bad_results > 1){
                return back()->with('message', __('text.word_done').'. ' .( $bad_results == 0 ? '' : $bad_results. ' '.__('text.records_not_imported_phrase')).$null_students);
            }
            return back()->with('success', __('text.word_done'));
        }else{
            return back()->with('error', __('text.file_type_constraint', ['type'=>'.csv']));
        }
    }

    public function import_exam()
    {
        # code...
        // check if exam total is set for this program
        if (!Helpers::instance()->exam_total_isset(request('class_id'))) {
            # code...
            return back()->with('error',  __('text.exam_total_not_set_for', ['program'=>__('text.word_program')]));
        }
        
        $subject = Subjects::find(request('course_id'));
        $data['title'] = __('text.import_exam_results_for', ['course'=>"[ ".$subject->code." ] ".$subject->name, 'class'=>ProgramLevel::find(request('class_id'))->name()]);
        return view('teacher.results.import_exam_only', $data);
    }

    public function import_exam_save(Request $request)
    {
        # code...
        $check = Validator::make($request->all(), [
            'reference'=>'required',
            'file'=>'required|file'
        ]);
        if($check->fails()){
            return back()->with('error', $check->errors()->first());
        }

        // $ca_total = Helpers::instance()->ca_total(request('class_id'));
        $exam_total = Helpers::instance()->exam_total(request('class_id'));

        $file = $request->file('file');
        if($file != null &&$file->getClientOriginalExtension() == 'csv'){
            $filename = 'exam_'.random_int(1000, 9999).'_'.time().'.'.$file->getClientOriginalExtension();
            $file->move(storage_path('app/files'), $filename);

            $file_pointer = fopen(storage_path('app/files').'/'.$filename, 'r');

            $imported_data = [];
            $course = Subjects::find($request->course_id);
            $year = Helpers::instance()->getCurrentAccademicYear();
            $semester = $request->has('semester_id') ? Semester::find($request->semester_id) : Helpers::instance()->getSemester($request->class_id);
            
            while(($row = fgetcsv($file_pointer, 100, ',')) != null){
                if(is_numeric($row[1]))
                $imported_data[] = [$row[0], $row[1]];
            }
            if(count($imported_data)==0){
                return back()->with('error', __('text.empty_or_wrong_data_format'));
            }

            $bad_results = 0;
            $null_students = '';
            $existing_results = '';
            foreach($imported_data as $data){
                if ($data[1] > $exam_total) {
                    # code...
                    $bad_results++;
                    continue;
                }
                $student = Students::where(['matric'=>$data[0]])->first() ?? null;
                if($student != null){
                    $base=[
                        'batch_id' => $year, 
                        // 'subject_id' => $request->course_id,
                        'subject_code' => Subjects::find( $request->course_id)->code,
                        // 'student_id' => $student->id,
                        'student_matric' => $data[0],
                        'class_id' => $request->class_id,
                        'semester_id' => $semester->id,
                        'coef'=>$course->_class_subject($request->class_id)->coef??$course->coef,
                        'class_subject_id'=>$course->_class_subject($request->class_id)->id??0
                    ];
                    if(OfflineResult::where($base)->whereNotNull('exam_score')->count() > 0){
                        $existing_results .= "<br> ".__('text.exam_results_already_exist_for', ['item'=>$data[0]]);
                    }elseif (!$data[1] == null) {
                        # code...
                        OfflineResult::updateOrCreate($base, ['exam_score'=>$data[1], 'reference'=>$request->reference, 'user_id'=>auth()->id(),  'campus_id'=>$student->campus_id, 'student_id'=>$student->id, 'subject_id' => $request->course_id]);
                    }
                }
                else{
                    $null_students .= __('text.student_matric_not_found', ['matric'=>$data[0]])." <br>";
                }
            }
            if($bad_results > 1){
                return back()->with('message', __('text.word_done').'. ' . ($bad_results == 0 ? '' : $bad_results . ' '.__('text.records_not_imported_phrase')) . $null_students . $existing_results);
            }
            return back()->with('success', __('text.word_done'));
        }else{
            return back()->with('error', __('text.file_type_constraint', ['type'=>'.csv']));
        }
    }
}
