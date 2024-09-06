<?php

namespace App\Http\Controllers\Teacher;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Batch;
use App\Models\ClassSubject;
use App\Models\ProgramLevel;
use App\Models\Result;
use App\Models\SchoolUnits;
use App\Models\Subjects;
use App\Models\Topic;
use App\Models\Campus;
use App\Models\CourseLog;
use App\Models\Notification;
use App\Models\Period;
use App\Models\Semester;
use App\Models\Students;
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

    public function edit_course(Request $request, $class_id, $subject_id)
    {
        $class_subject = ClassSubject::where(['class_id'=>$class_id, 'subject_id'=>$subject_id])->first();
        if($class_subject ==  null){
            $subject = Subjects::find($subject_id);
            $class_subject = new ClassSubject(['class_id'=>$class_id, 'coef'=>$subject->coef, 'status'=>$subject->status, 'subject_id'=>$subject_id]);
            $class_subject->save();
        }
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

    public function delete_course($class_id, $subject_id)
    {
        # code...
        try {
            \App\Models\ClassSubject::updateOrInsert(['class_id'=>$class_id, 'subject_id'=>$subject_id], ['deleted_at'=>now()]);
            return back()->with('success', 'Done');
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('error', "F:{$th->getFile()} | L{$th->getLine()} | {$th->getMessage()}");
        }
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

        $data['title'] = "Manage subjects under " . $parent->name();
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
        $data['subject'] = DB::table('class_subjects')->whereNull('class_subjects.deleted_at')
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
            $data['attendance'] = Attendance::where(['campus_id'=>$request->campus??null, 'teacher_id'=>$teacher->id, 'subject_id'=>$subject->id])->orderBy('id', 'DESC')->get();
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
        $sub_topics = \App\Models\Topic::where(['subject_id'=> $subject->id, 'level'=>2, 'campus_id'=>$campus->id, 'teacher_id'=>$attendance->teacher_id])->pluck('id')->toArray();
        $data['title'] = "Sign Course Log For ".$subject->name.'['.$subject->code.'] <br><b class="text-primary pt-3 d-block">'.date('d/m/Y H:i', strtotime($attendance_record->check_in)).' - '.date('d/m/Y H:i', strtotime($attendance_record->check_out)).'</b>';
        $data['subject'] = $subject;
        $data['campus'] = $campus;
        $data['topic'] = $topic;
        $data['periods'] = Period::orderBy('starts_at')->get();
        $data['attendance_record'] = $attendance_record;
        $data['log_history'] = CourseLog::whereIn('topic_id', $sub_topics)->get();
        
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
        $instance->attendance()->update(['period_id'=>$request->id]);
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

    // HANDLING COURSE RESULT INPUT

    
    public function course_ca_fill(Request $request, $class_id, $course_id)
    {
        # code...
        
                $data['title'] = "Fill CA Results";
                return view('teacher.result.fill_ca', $data);
    }

    public function course_ca_import(Request $request, $class_id=null, $course_id=null, $year=null, $semester_id=null){

        $sem = \App\Helpers\Helpers::instance()->getSemester($class_id);
        $batch = \App\Models\Batch::find(\App\Helpers\Helpers::instance()->getCurrentAccademicYear());
        // dd($sem);
        $data['year_id'] = $batch->id;
        $data['semester_id'] = $sem->id;
        $data['class_id'] = $class_id;
        $subject = Subjects::find($course_id);
        $program_level = ProgramLevel::find($class_id);
        $data['title'] = "Import CA For [{$subject->code}] {$subject->name}";
        $data['semesters'] = Helpers::instance()->getSemesters($program_level->id)->where('is_main_semester', 1);
        $data['course_code'] = $subject->code;
        $data['classes'] = HomeController::sorted_program_levels();
        $data['course'] = $subject;
        if($sem != null){
            $data['semester'] = $sem;
            $data['year'] = $batch;
            $data['class'] = $program_level;
            $data['title2'] = __('text.import_CA_results_for', ['class'=>$program_level->name(), 'ccode'=>$subject->code, 'year'=>$batch->name, 'sem'=>$sem->name]);
            $data['_title2'] = __('text.word_course').' :: <b class="text-danger">'.$subject->name.'</b> || '.__('text.course_code').' :: <b class="text-danger">'. $subject->code .'</b> || '.__('text.word_class').' :: <b class="text-danger">'. $program_level->name() .'</b>'.__('text.word_semester').' :: <b class="text-danger">'. $sem->name .'</b>';
            $data['delete_label'] = __('text.clear_ca_for', ['year'=>$batch->name??'YR', 'class'=>$program_level->name(), 'ccode'=>$course_code??'CCODE', 'semester'=>$sem->name??'SEMESTER']);
            $data['results'] = Result::where(['batch_id'=>$batch->id, 'class_id'=>$class_id, 'semester_id'=>$sem->id, 'subject_id'=>$subject->id])->get();
            $data['delete_prompt'] = "You are about to delete all {$program_level->name()} CA for {$subject->code}, {$sem->name} {$batch->name}";
            $data['can_update_ca'] = !(now()->isAfter($sem->ca_upload_latest_date));
        }
        return view('teacher.result.import_ca', $data);
    }

    public function course_ca_import_save(Request $request, $class_id, $course_id, $year, $semester){
        if($request->file('file') == null){
            session()->flash('error', 'file field requried');
            return back()->withInput();
        }

        // save the file
        $file = $request->file('file');
        $fname = "exam_upload_".time().'.csv';
        $path = public_path('uploads/files');
        $file->move($path, $fname);

        // open file for reading
        $reading_stream = fopen($path.'/'.$fname, 'r');

        // read file data into an array
        $file_data = [];
        while(($row = fgetcsv($reading_stream, 1000)) != null){
            $file_data[] = ['matric'=>$row[0], 'ca_score'=>$row[1]];
        }
        fclose($reading_stream);

        // dd($file_data);
        // write CA results to database
        $missing_students = "";
        $subject = Subjects::find($course_id);
        if($subject == null){
            return back()->withInput()->with('error', "Course with course code {$subject->code} not found");
        }

        $class = ProgramLevel::find($class_id);
        $_sem = Semester::find($semester);
        foreach($file_data as $rec){
            $student = Students::where('matric', $rec['matric'])->first();
            
            if($student == null){
                (strstr($missing_students, $rec['matric']) == false) ? ($missing_students .= " {$rec['matric']},") : null;
                continue;
            }
            $class_subject = $class->class_subjects()->where('subject_id', $subject->id)->first();
            $data = [
                'batch_id'=>$year, 'student_id'=>$student->id, 'semester_id'=>$_sem->sem, 'subject_id'=>$subject->id, 
                'ca_score'=>$rec['ca_score'], 'coef'=>$class_subject->coef ?? $subject->coef,
                'class_subject_id'=>$class_subject->id??null, 'reference'=>'REF'.$year.$student->id.$class->id.$semester.$subject->id.$subject->coef, 
                'user_id'=>auth()->id(), 'campus_id'=>$student->campus_id, 'published'=>0
            ];
            $base = ['batch_id'=>$year, 'student_id'=>$student->id, 'class_id'=>$class_id, 'semester_id'=>$semester, 
            'subject_id'=>$subject->id];
    
            Result::updateOrInsert($base, $data);
        }
        if(strlen($missing_students) > 0){
            return back()->with('success', 'Done')->with('message', "Students with matricules {$missing_students} are not found");
        }
        return back()->with('success', 'Done');
        
    }

    public function course_ca_import_clear(Request $request, $class_id, $course_id, $year, $semester){
        // return $request->all();
        $sem = Semester::find($semester);
        Result::where(['batch_id'=>$year, 'semester_id'=>$sem->sem, 'subject_id'=>$course_id, 'class_id'=>$class_id])->each(function($row){
            $row->exam_score == null ? $row->delete() : $row->update(['ca_score'=>null]);
        });
        return back()->with('sucess', 'Done');
        
    }

    public function course_exam_fill(){

        // check if exam total is set for this program

        $data['title'] = "Fill Exam Results";
        return view('teacher.result.fill_exam', $data);
    }


    public function course_exam_import(){

        // check if exam total is set for this program

        $data['title'] = "Import Exam Results";
        return view('teacher.result.import_exam', $data);
    }

    public function course_exam_import_save(Request $request){
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
            $filename = 'ca_'.random_int(1000, 9999).'_'.time().'.'.$file->getClientOriginalExtension();
            $file->storeAs('/files', $filename);

            $file_pointer = fopen(storage_path('app/files').'/'.$filename, 'r');

            $imported_data = [];
            $course = Subjects::find($request->course_id);
            $year = \App\Helpers\Helpers::instance()->getCurrentAccademicYear();
            $semester = \App\Helpers\Helpers::instance()->getSemester($request->class_id);
            
            while(($row = fgetcsv($file_pointer, 100, ',')) != null){
                if(is_numeric($row[1]))
                $imported_data[] = [$row[0], $row[1], $row[2]];
            }
            if(count($imported_data)==0){
                return back()->with('error', 'No data or wrong data format.');
            }

            $bad_results = 0;
            foreach($imported_data as $data){
                
                $student = Students::where(['matric'=>$data[0]])->first() ?? null;
                if($student != null){
                    $base=[
                        'batch_id' => $year, 
                        'subject_id' => $request->course_id,
                        'student_id' => $student->id,
                        'class_id' => $request->class_id,
                        'semester_id' => $semester->id
                    ];
                    Result::updateOrCreate($base, ['ca_score'=>$data[1], 'exam_score'=>$data[2], 'reference'=>$request->reference, 'coef'=>$course->_class_subject($request->class_id)->coef??$course->coef, 'user_id'=>auth()->id(), 'class_subject_id'=>$course->_class_subject($request->class_id)->id??0]);
                }
            }
            if($bad_results > 1){
                return back()->with('success', 'Done. ' . $bad_results . ' records not imported. Unsupported values supplied.');
            }
            return back()->with('success', 'Done');
        }else{
            return back()->with('error', 'Empty or bad file type. CSV files only are accepted.');
        }
    }

    
    public function print_ca_sheet(Request $request, $class_id=null, $course_id=null, $year=null, $semester_id=null){

        $sem = \App\Helpers\Helpers::instance()->getSemester($class_id);
        $batch = \App\Models\Batch::find(\App\Helpers\Helpers::instance()->getCurrentAccademicYear());
        // dd($sem);
        $data['year_id'] = $batch->id;
        $data['semester_id'] = $sem->id;
        $data['class_id'] = $class_id;
        $subject = Subjects::find($course_id);
        $program_level = ProgramLevel::find($class_id);
        $data['title'] = "Import CA For [{$subject->code}] {$subject->name}";
        $data['semesters'] = Helpers::instance()->getSemesters($program_level->id)->where('is_main_semester', 1);
        $data['course_code'] = $subject->code;
        $data['classes'] = HomeController::sorted_program_levels();
        $data['course'] = $subject;
        if($sem != null){
            $data['semester'] = $sem;
            $data['year'] = $batch;
            $data['class'] = $program_level;
            $data['results'] = Result::where(['batch_id'=>$batch->id, 'class_id'=>$class_id, 'semester_id'=>$sem->id, 'subject_id'=>$subject->id])->get();
            // dd($data['results']);        
        }
        return view('teacher.result.ca_sheet', $data);
    }
}
