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
        if($request->action == 'all'){

        }
        return view('admin.result.coded.index', $data);
    }

    public function import_course_codes(Request $request)
    {
        # code...
        $data['title'] = "Import Course Exam Codes";
        return view('admin.result.coded.import_course_codes', $data);
    }

    public function save_course_codes(Request $request)
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

    public function import_student_codes(Request $request)
    {
        # code...
        $data['title'] = "Import Student Exam Codes";
        return view('admin.result.coded.import_student_codes', $data);
    }

    public function save_student_codes(Request $request)
    {
        # code...
    }

    public function course(Request $request, $course_id)
    {
        # code...
        $course = \App\Models\Subjects::find($course_id);
        $year = \App\Models\Batch::find($this->current_accademic_year);
        $excode = \App\Models\ExamCourseCode::where('course_id', $course_id)->first();
        $data['title'] = "Coding for {$course->name} [$course->code] | {$year->name}";
        $data['course'] = $course;
        $data['year'] = $year;
        $data['exam_code'] = $excode;
        return view('admin.result.coded.course', $data);
    }

    public function save_course_code(Request $request, $course_id)
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
                $data = ['course_id'=>$subject->id, 'course_code'=>$subject->code, 'exam_code'=>$request->code, 'year_id'=>$this->current_accademic_year];
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

    public function import_results(Request $request)
    {
        # code...
        $data['title'] = "Import Exam [Coded Results]";
        return view('admin.result.coded.import_coded_exam', $data);
    }

    public function save_results(Request $request)
    {
        # code...
    }

    public function import_results_per_course(Request $request, $course_id)
    {
        # code...
        $course = \App\Models\Subjects::find($course_id);
        $year = \App\Models\Batch::find($this->current_accademic_year);
        $data['title'] = "Import Coded exam for {$course->name} [$course->code] | {$year->name}";
        $data['course'] = $course;
        $data['year'] = $year;
        return view('admin.result.coded.import_coded_exam', $data);
    }

    public function save_results_per_course(Request $request, $course_id)
    {
        # code...
    }
}
