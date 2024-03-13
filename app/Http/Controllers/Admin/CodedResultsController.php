<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CodedResultsController extends Controller
{
    //

    public function index(Request $request)
    {
        # code...
        $data['title'] = "Manage Coded Results";
        if($request->year_id != null)
            $data['year']  = \App\Models\Batch::find($request->year_id);
        if($request->semester_id != null)
            $data['semester']  = \App\Models\Semester::find($request->semester_id);
        if($request->action == 'all'){

        }
        return view('admin.result.coded.index', $data);
    }

    public function import_course_codes(Request $request, $year_id, $semester_id)
    {
        # code...
        $semester  = \App\Models\Semester::find($semester_id);
        $year  = \App\Models\Batch::find($year_id);
        $data['title'] = "Import Exam Course Codes For {$semester->name} | {$year->name}";
        return view('admin.result.coded.import_course_codes', $data);
    }

    public function save_course_codes(Request $request, $year_id, $semester_id)
    {
        # code...
        try {
            $validity = Validator::make($request->all(), ['file'=>'required|file']);

            if($validity->fails()){
                session()->flash('error', $validity->errors()->first());
                return back()->withInput();
            }
            if(($file = $request->file('file')) != null){
                $filepath = public_path('uploads/files/');
                $filename = public_path('uploads/files/exam_course_codes.csv');
                $file->move($filepath, 'exam_course_codes.csv');
                
                $year_id = $this->current_accademic_year;
                // READ INPUT FILE
                $file_data = [];
                // dd($request->all());
                $fileStream = fopen($filename, 'r');
                while(($row = fgetcsv($fileStream, 1000))){
                    if(count($row) == 0 || ($row[0]??0) == null)
                        continue;
                    $file_data[] = ['course_code'=>$row[0], 'exam_code'=>$row[1]];
                }

                $input_data = collect($file_data)->map(function($rec)use($year_id){
                        $course = \App\Models\Subjects::where('code', $rec['course_code'])->first();
                        $rec['course_id'] = $course->id??null;
                        $rec['year_id'] = $year_id;
                        // dd($rec);
                        return $rec;
                    });
                // dd($input_data);

                foreach ($input_data as $key => $rec) {
                    # code...
                    \App\Models\ExamCourseCode::updateOrInsert(['course_id'=>$rec['course_id'], 'course_code'=>$rec['course_code'], 'year_id'=>$rec['year_id']], $rec);
                }
                return back()->with('success', 'Done');
            }
        } catch (\Throwable $th) {
            //throw $th;
            session()->flash('error', "F::{$th->getFile()}, L::{$th->getLine()}, M::{$th->getMessage()}");
            return back()->withInput();
        }
    }

    public function import_student_codes(Request $request, $year_id, $semester_id, $course_id)
    {
        # code...
        $course  = \App\Models\Subjects::find($course_id);
        $semester  = \App\Models\Semester::find($semester_id);
        $year  = \App\Models\Batch::find($year_id);
        $data['title'] = "Import Student Exam Codes For {$course->name} [{$course->code}] | {$semester->name} | {$year->name}";
        return view('admin.result.coded.import_student_codes', $data);
    }

    public function save_student_codes(Request $request, $year_id, $semester_id, $course_id)
    {
        # code...
        try {
            $validity = Validator::make($request->all(), ['file'=>'required|file']);

            if($validity->fails()){
                session()->flash('error', $validity->errors()->first());
                return back()->withInput();
            }
            if(($file = $request->file('file')) != null){
                $filepath = public_path('uploads/files/');
                $filename = public_path('uploads/files/exam_student_codes.csv');
                $file->move($filepath, 'exam_student_codes.csv');
                
                $year_id = $this->current_accademic_year;
                // READ INPUT FILE
                $file_data = [];
                // dd($request->all());
                $fileStream = fopen($filename, 'r');
                while(($row = fgetcsv($fileStream, 1000))){
                    if(count($row) == 0 || ($row[0]??0) == null)
                        continue;
                    $file_data[] = ['course_code'=>$row[0], 'exam_code'=>$row[1]];
                }

                $input_data = collect($file_data)->map(function($rec)use($year_id){
                        $course = \App\Models\Subjects::where('code', $rec['course_code'])->first();
                        $rec['course_id'] = $course->id??null;
                        $rec['year_id'] = $year_id;
                        // dd($rec);
                        return $rec;
                    });
                // dd($input_data);

                foreach ($input_data as $key => $rec) {
                    # code...
                    \App\Models\ExamCourseCode::updateOrInsert(['course_id'=>$rec['course_id'], 'course_code'=>$rec['course_code'], 'year_id'=>$rec['year_id']], $rec);
                }
                return back()->with('success', 'Done');
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function course(Request $request, $year_id, $semester_id, $course_id)
    {
        # code...
        $course  = \App\Models\Subjects::find($course_id);
        $semester  = \App\Models\Semester::find($semester_id);
        $year  = \App\Models\Batch::find($year_id);
        $excode = \App\Models\ExamCourseCode::where('course_id', $course_id)->first();
        $codes = \App\Models\ExamCourseCode::where(['semester_id'=>$semester_id, 'year_id'=>$year_id])->orderBy('updated_at', 'DESC')->get();
        $data['title'] = "Update Exam Course Coding for {$course->name} [$course->code] | {$semester->name} | {$year->name}";
        $data['course'] = $course;
        $data['year'] = $year;
        $data['semester'] = $semester;
        $data['codes'] = $codes;
        $data['exam_code'] = $excode;
        return view('admin.result.coded.course', $data);
    }

    public function save_course_code(Request $request, $year_id, $semester_id, $course_id)
    {
        # code...
        try {
            //code...
            $validator = Validator::make($request->all(), ['code'=>'required']);
            if($validator->fails()){
                session()->flash('error', $validator->errors()->first());
                return back()->withInput();
            }
            $subject = \App\Models\Subjects::find($course_id);
            if($subject != null){
                $data = ['course_id'=>$subject->id, 'course_code'=>$subject->code, 'exam_code'=>$request->code, 'year_id'=>$this->current_accademic_year, 'semester_id'=>$semester_id];
                $exam_code = new \App\Models\ExamCourseCode($data);
                $exam_code->save();
                return back()->with('success', 'Done');
            }
            session()->flash('error', "Course not found");
            return back()->withInput();
        } catch (\Throwable $th) {
            //throw $th;
            session()->flash('error', "Operation failed. L::{$th->getLine()}, F::{$th->getFile()}, M::{$th->getMessage()}");
            return back()->withInput();
        }
    }

    public function drop_course_code(Request $request, $year_id, $semester_id, $course_id)
    {
        # code...
        $course  = \App\Models\Subjects::find($course_id);
        $semester  = \App\Models\Semester::find($semester_id);
        $year  = \App\Models\Batch::find($year_id);
    }

    public function import_ca_only(Request $request, $year_id, $semester_id, $course_id)
    {
        # code...
        $course  = \App\Models\Subjects::find($course_id);
        $semester  = \App\Models\Semester::find($semester_id);
        $year  = \App\Models\Batch::find($year_id);
        $data['course'] = $course;
        $data['title'] = "Import CA Only For {$course->name} [{$course->code}] | {$semester->name} | {$year->name}";
        return view('admin.result.coded.import_ca_only', $data);
    }

    public function save_ca_only(Request $request, $year_id, $semester_id, $course_id)
    {
        # code...
    }

    public function undo_ca_import(Request $request, $year_id, $semester_id, $course_id, $class_id = null)
    {
        # code...
        $course  = \App\Models\Subjects::find($course_id);
        $semester  = \App\Models\Semester::find($semester_id);
        $year  = \App\Models\Batch::find($year_id);
    }

    public function import_exam(Request $request, $year_id, $semester_id, $course_id)
    {
        # code...
        $course  = \App\Models\Subjects::find($course_id);
        $semester  = \App\Models\Semester::find($semester_id);
        $year  = \App\Models\Batch::find($year_id);
        $classes = $course->classes;
        $results = \App\Models\Result::where(['subject_id'=>$course_id, 'semester_id'=>$semester_id, 'batch_id'=>$year_id])->orderBy('id', 'DESC')->get();
        $data['title'] = "Import Exam for {$course->name} [$course->code] | {$year->name}";
        $data['course'] = $course;
        $data['results'] = $results;
        $data['semester'] = $semester;
        $data['year'] = $year;
        $data['classes'] = $classes;
        return view('admin.result.coded.import_exam', $data);
    }

    public function save_exam(Request $request, $year_id, $semester_id, $course_id)
    {
        # code...
    }

    public function undo_exam_import(Request $request, $year_id, $semester_id, $course_id)
    {
        # code...
        $courses  = $request->input('class_id') == null ? 
            \App\Models\Result::where(['subject_id'=>$course_id, 'batch_id'=>$year_id, 'semester_id'=>$semester_id]):
            \App\Models\Result::where(['subject_id'=>$course_id, 'batch_id'=>$year_id, 'semester_id'=>$semester_id, 'class_id'=>$request->class_id]);

        if($courses->count() > 0){
            $courses->update(['exam_score', 'null']);
        }
        return back()->with('success', 'Done');
    }

    public function import_coded_exam(Request $request, $year_id, $semester_id, $course_id)
    {
        # code...
    }

    public function save_coded_exam(Request $request, $year_id, $semester_id, $course_id)
    {
        # code...
    }

    public function undo_coded_exam_import(Request $request, $year_id, $semester_id, $course_id, $class_id)
    {
        # code...
        $course  = \App\Models\Subjects::find($course_id);
        $semester  = \App\Models\Semester::find($semester_id);
        $year  = \App\Models\Batch::find($year_id);
    }
}
