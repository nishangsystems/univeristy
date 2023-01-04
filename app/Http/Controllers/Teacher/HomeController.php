<?php

namespace App\Http\Controllers\Teacher;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Models\ClassSubject;
use App\Models\ProgramLevel;
use App\Models\Result;
use App\Models\SchoolUnits;
use App\Models\Students;
use App\Models\Subjects;
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


    // HANDLING COURSE RESULT INPUT

    
    public function course_ca_fill(Request $request, $class_id, $course_id)
    {
        # code...
        
                $data['title'] = "Fill CA Results";
                return view('teacher.result.fill_ca', $data);
    }

    public function course_ca_import(){

        // check if CA total is set forthis program

        $data['title'] = "Import CA Results";
        return view('teacher.result.import_ca', $data);
    }

    public function course_ca_import_save(Request $request, $class_id, $course_id){
        // return $request->all();
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
            $file->storeAs('/files', $filename);

            $file_pointer = fopen(storage_path('app/files').'/'.$filename, 'r');

            $imported_data = [];
            $course = Subjects::find($request->course_id);
            $year = \App\Helpers\Helpers::instance()->getCurrentAccademicYear();
            $semester = \App\Helpers\Helpers::instance()->getSemester($request->class_id);
            
            while(($row = fgetcsv($file_pointer, 100, ',')) != null){
                if(is_numeric($row[1]))
                $imported_data[] = [$row[0], $row[1]];
            }
            if(count($imported_data)==0){
                return back()->with('error', 'No data or wrong data format.');
            }

            $bad_results = 0;
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
                        'subject_id' => $request->course_id,
                        'student_id' => $student->id,
                        'class_id' => $request->class_id,
                        'semester_id' => $semester->id
                    ];
                    Result::updateOrCreate($base, ['ca_score'=>$data[1], 'reference'=>$request->reference, 'coef'=>$course->_class_subject($request->class_id)->coef??$course->coef, 'user_id'=>auth()->id(), 'class_subject_id'=>$course->_class_subject($request->class_id)->id??0]);
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
                if ($data[1] > $ca_total || $data[2] > $exam_total) {
                    # code...
                    $bad_results++;
                    continue;
                }
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
}
