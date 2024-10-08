<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use \App\Models\ProgramLevel;
use App\Models\StudentClass;

class CodedResultsController extends Controller
{
    //

    public function index(Request $request)
    {
        # code...
        try {
            //code...
            $data['title'] = "Manage Exam Encoding";
            $data['semesters'] = \App\Models\Semester::orderBy('name')->get();
            if($request->year_id != null)
                $data['year']  = \App\Models\Batch::find($request->year_id);
            if($request->semester_id != null){
                $data['semester']  = \App\Models\Semester::find($request->semester_id);
                $data['title2'] = $data['title']." &rangle;&rangle; {$data['semester']->name} &rangle;&rangle; {$data['year']->name}";
                // if($request->course_code != null){
                //     \App\Models\Subjects::where('code', $request->course_code)->first();
                //     $data['courses'] = \App\Models\Result::where(['semester_id'=>$request->semester_id, 'batch_id'=>$request->year_id])->whereNotNull('exam_code')->select(['*', DB::raw("COUNT(*) as encoded_records")])->groupBy('subject_id')->orderBy('updated_at')->distinct()->get();
                // }else{
                    $data['courses'] = \App\Models\Result::where(['semester_id'=>$request->semester_id, 'batch_id'=>$request->year_id])->whereNotNull('exam_code')->select(['*', DB::raw("COUNT(*) as encoded_records")])->groupBy('subject_id')->orderBy('updated_at')->distinct()->get();
                // }
            }
            if($request->course_code != null){
                $data['course_code'] = $request->course_code;
                $data['_course'] = \App\Models\Subjects::where('code', $request->course_code)->first();
            }
            return view('admin.result.coded.index', $data);
        } catch (\Throwable $th) {
            //throw $th;
            session()->flash('error', "F::{$th->getFile()}, L::{$th->getLine()}, M::{$th->getMessage()}");
            return back()->withInput();
        }
    }
    //

    public function decoder_index(Request $request)
    {
        # code...
        try {
            //code...
            $data['title'] = "Manage Exam Decoding";
            if($request->year_id != null)
                $data['year']  = \App\Models\Batch::find($request->year_id);
            if($request->semester_id != null){
                $data['semester']  = \App\Models\Semester::find($request->semester_id);
                $data['title2'] = $data['title']." &rangle;&rangle; {$data['semester']->name} &rangle;&rangle; {$data['year']->name}";
                $data['courses'] = \App\Models\Result::where(['semester_id'=>$request->semester_id, 'batch_id'=>$request->year_id])->whereNotNull('exam_code')->whereNotNull('exam_score')->select(['*', DB::raw("COUNT(*) as decoded_records")])->groupBy('subject_id')->orderBy('updated_at')->distinct()->get();
            }
            return view('admin.result.coded.decoder_index', $data);
        } catch (\Throwable $th) {
            //throw $th;
            session()->flash('error', "F::{$th->getFile()}, L::{$th->getLine()}, M::{$th->getMessage()}");
            return back()->withInput();
        }
    }

    public function import_course_codes(Request $request, $year_id, $semester_id)
    {
        # code...
        try {
            //code...
            $semester  = \App\Models\Semester::find($semester_id);
            $year  = \App\Models\Batch::find($year_id);
            $data['title'] = "Import Exam Course Codes For {$semester->name} | {$year->name}";
            return view('admin.result.coded.import_course_codes', $data);
        } catch (\Throwable $th) {
            //throw $th;
            session()->flash('error', "F::{$th->getFile()}, L::{$th->getLine()}, M::{$th->getMessage()}");
            return back()->withInput();
        }
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
                
                // READ INPUT FILE
                $file_data = [];
                // dd($request->all());
                DB::beginTransaction();
                $fileStream = fopen($filename, 'r');
                while(($row = fgetcsv($fileStream, 1000))){
                    if(count($row) == 0 || ($row[0]??0) == null)
                        continue;
                    $file_data[] = ['course_code'=>$row[0], 'exam_code'=>$row[1], 'matric'=>$row[2]];
                }
                // dd(collect($file_data)->filter(function($rec){$rec == null;}));

                $missing_matric = '';
                $missing_course = '';
                $input_data = collect($file_data)->map(function($rec)use($year_id, $semester_id, $missing_matric, $missing_course){
                        $course = \App\Models\Subjects::where('code', $rec['course_code'])->first();
                        $student = \App\Models\Students::where('matric', $rec['matric'])->first();
                        $class = $student == null ? null : $student->_class($year_id);
                        $rec['course_id'] = optional($course)->id??null;
                        $rec['student_id'] = optional($student)->id??null;
                        $rec['class_id'] = optional($class)->id??null;
                        
                        // dd($rec);
                        return $rec;
                    });

                // dd($input_data);
                
                $missing_matric = implode(', ', $input_data->where('student_id', null)->pluck('matric')->all());
                $missing_course = implode(', ', $input_data->where('course_id', null)->pluck('course_code')->unique()->all());
                $ipdata = $input_data->where('course_id', '!=', null)->where('student_id', '!=', null);
                // dd($ipdata);
                foreach ($ipdata as $key => $rec) {
                    # code...
                    \App\Models\Result::updateOrInsert(
                        [
                            'subject_id'=>$rec['course_id'], 'batch_id'=>$year_id, 'semester_id'=>$semester_id, 'student_id'=>$rec['student_id']
                        ], 
                        [
                            'exam_code'=>$rec['exam_code'], 'class_id'=>$rec['class_id']
                        ]
                    );
                }
                DB::commit();
                $error = (strlen($missing_matric) > 0 ? " No students were found with following matric(s): {$missing_matric}. ........" : '')
                    .(strlen($missing_course) > 0 ? " No courses were found with the following course codes: {$missing_course}. ........" : '');
                session()->flash('message', $error);
                return back()->with('success', "Done");
            }
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            session()->flash('error', "F::{$th->getFile()}, L::{$th->getLine()}, M::{$th->getMessage()}");
            return back()->withInput();
        }
    }

    public function decode_save(Request $request, $year_id, $semester_id)
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
                
                // READ INPUT FILE
                $file_data = [];
                // dd($request->all());
                DB::beginTransaction();
                $fileStream = fopen($filename, 'r');
                while(($row = fgetcsv($fileStream, 1000))){
                    if(count($row) == 0 || ($row[0]??0) == null)
                        continue;
                    $file_data[] = ['course_code'=>$row[0], 'exam_code'=>$row[1], 'exam_score'=>$row[2]];
                }

                $input_data = collect($file_data)->map(function($rec)use($year_id, $semester_id){
                        $course = \App\Models\Subjects::where('code', $rec['course_code'])->first();
                        $rec['course_id'] = $course->id??null;
                        // dd($rec);
                        return $rec;
                    });
                // dd($input_data);


                $decoded = 0;
                foreach ($input_data as $key => $rec) {
                    # code...
                    if(\App\Models\Result::where(['subject_id'=>$rec['course_id'], 'batch_id'=>$year_id, 'exam_code'=>$rec['exam_code'], 'semester_id'=>$semester_id])->whereNotNull('exam_score')->count() == 0){
                        \App\Models\Result::where(['subject_id'=>$rec['course_id'], 'batch_id'=>$year_id, 'exam_code'=>$rec['exam_code'], 'semester_id'=>$semester_id])->update(['exam_score'=>$rec['exam_score']]);
                        $decoded++;
                    }
                }
                DB::commit();
                session()->flash('message', $decoded." Records decoded");
                return back()->with('success', 'Done');
            }
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            session()->flash('error', "F::{$th->getFile()}, L::{$th->getLine()}, M::{$th->getMessage()}");
            return back()->withInput();
        }
    }

    public function import_student_codes(Request $request, $year_id, $semester_id, $course_id)
    {
        # code...
        try {
            //code...
            $course  = \App\Models\Subjects::find($course_id);
            $semester  = \App\Models\Semester::find($semester_id);
            $year  = \App\Models\Batch::find($year_id);
            $results = \App\Models\Result::where(['subject_id'=>$course_id, 'semester_id'=>$semester_id, 'batch_id'=>$year_id])->whereNotNull('exam_code')->orderBy('id', 'DESC')->get();
            $data['classes'] = $course->classes;
            $data['coded_students'] = $results;
            $data['title'] = "Import Student Exam Codes For {$course->name} [{$course->code}] | {$semester->name} | {$year->name}";
            return view('admin.result.coded.import_student_codes', $data);
        } catch (\Throwable $th) {
            //throw $th;
            session()->flash('error', "F::{$th->getFile()}, L::{$th->getLine()}, M::{$th->getMessage()}");
            return back()->withInput();
        }
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
                
                
                // READ INPUT FILE
                $file_data = [];
                // dd($request->all());
                DB::beginTransaction();
                $fileStream = fopen($filename, 'r');
                while(($row = fgetcsv($fileStream, 1000))){
                    if(count($row) == 0 || ($row[0]??0) == null)
                        continue;
                    $file_data[] = ['matric'=>$row[0], 'exam_code'=>$row[1]];
                }

                $input_data = collect($file_data)->map(function($rec)use($year_id){
                        $student = \App\Models\Students::where('matric', $rec['matric'])->first();
                        $rec['student_id'] = $student->id??null;
                        $rec['class_id'] = $student->_class($year_id)->id??null;
                        // dd($rec);
                        return $rec;
                    });
                // dd($input_data);

                foreach ($input_data as $key => $rec) {
                    # code...
                    \App\Models\Result::updateOrInsert(['subject_id'=>$course_id, 'student_id'=>$rec['student_id'], 'batch_id'=>$year_id, 'semester_id'=>$semester_id, 'class_id'=>$rec['class_id']], ['exam_code'=>$rec['exam_code']]);
                }
                DB::commit();
                return back()->with('success', 'Done');
            }
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            session()->flash('error', "F::{$th->getFile()}, L::{$th->getLine()}, M::{$th->getMessage()}");
            return back()->withInput();
        }
    }
    
    public function undo_student_code_import(Request $request, $year_id, $semester_id, $course_id)
    {
        # code...
        try {
            //code...
            $class = $request->class_id??null;
            DB::beginTransaction();
            if($class == null){
                // nullify 'exam_code' for all students for the set semester and accademi9c year
                \App\Models\Result::where(['batch_id'=>$year_id, 'semester_id'=>$semester_id, 'subject_id'=>$course_id])->update(['exam_code'=>null]);
            }else{
                \App\Models\Result::where(['batch_id'=>$year_id, 'semester_id'=>$semester_id, 'subject_id'=>$course_id, 'class_id'=>$class])->update(['exam_code'=>null]);
            }
            DB::commit();
            return back()->with('success', "Done");
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            session()->flash('error', "F::{$th->getFile()}, L::{$th->getLine()}, M::{$th->getMessage()}");
            return back()->withInput();
        }
    }
    
    public function undo_student_decoding(Request $request, $year_id, $semester_id, $course_id)
    {
        # code...
        try {
            //code...
            $class = $request->class_id??null;
            DB::beginTransaction();
            if($class == null){
                // nullify 'exam_code' for all students for the set semester and accademi9c year
                \App\Models\Result::where(['batch_id'=>$year_id, 'semester_id'=>$semester_id, 'subject_id'=>$course_id])->update(['exam_score'=>null]);
            }else{
                \App\Models\Result::where(['batch_id'=>$year_id, 'semester_id'=>$semester_id, 'subject_id'=>$course_id, 'class_id'=>$class])->update(['exam_score'=>null]);
            }
            DB::commit();
            return back()->with('success', "Done");
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            session()->flash('error', "F::{$th->getFile()}, L::{$th->getLine()}, M::{$th->getMessage()}");
            return back()->withInput();
        }
    }

    public function course(Request $request, $year_id, $semester_id, $course_id)
    {
        # code...
        try {
            //code...
            $course  = \App\Models\Subjects::find($course_id);
            $semester  = \App\Models\Semester::find($semester_id);
            $year  = \App\Models\Batch::find($year_id);
            $encoding_summary = \App\Models\Result::where(['results.subject_id'=> $course_id, 'results.semester_id'=>$semester_id, 'results.batch_id'=>$year_id])->whereNotNull('exam_code')
                ->join('student_classes', function($query){
                    $query->on(['student_classes.student_id'=>'results.student_id'])
                        ->on(['student_classes.year_id'=>'results.batch_id']);
                })->where(['student_classes.year_id'=>$year_id])
                ->join('program_levels', ['program_levels.id'=>'student_classes.class_id'])
                ->select(['program_levels.*', 'program_levels.id as class_id', DB::raw("COUNT(results.exam_code) as size")])
                ->groupBy('program_levels.id')->distinct()->get();

            if($request->class_id != null){
                $data['class'] = ProgramLevel::find($request->class_id);
                $data['student_codes'] = \App\Models\Result::where(['results.subject_id'=> $course_id, 'results.semester_id'=>$semester_id, 'results.batch_id'=>$year_id])->whereNotNull('exam_code')
                    ->join('student_classes', function($query){
                        $query->on(['student_classes.student_id'=>'results.student_id'])
                            ->on(['student_classes.year_id'=>'results.batch_id']);
                    })->where(['student_classes.year_id'=>$year_id, 'student_classes.class_id'=>$request->class_id])
                    ->join('program_levels', ['program_levels.id'=>'student_classes.class_id'])
                    ->join('students', ['students.id'=>'results.student_id'])
                    ->select(['students.name', 'students.matric', 'results.id', 'results.exam_code', 'results.student_id', 'results.exam_score'])->distinct()->get();
            }
            $data['title'] = "Exam encoding for {$course->name} [$course->code] &rangle;&rangle; {$semester->name} &rangle;&rangle; {$year->name}";
            $data['course'] = $course;
            $data['year'] = $year;
            $data['semester'] = $semester;
            $data['encoding_summary'] = $encoding_summary;
            $data['has_exam'] = \App\Models\Result::where(['subject_id'=>$course_id, 'semester_id'=>$semester_id, 'batch_id'=>$year_id])->whereNotNull('exam_score')->count() > 0;

            // dd($data);
            return view('admin.result.coded.course', $data);
        } catch (\Throwable $th) {
            //throw $th;
            session()->flash('error', "Operation failed. L::{$th->getLine()}, F::{$th->getFile()}, M::{$th->getMessage()}");
            return back()->withInput();
        }
    }

    public function decoder_course(Request $request, $year_id, $semester_id, $course_id, $class_id = null)
    {
        # code...
        try {
            //code...
            $course  = \App\Models\Subjects::find($course_id);
            $semester  = \App\Models\Semester::find($semester_id);
            $year  = \App\Models\Batch::find($year_id);
            $decoding_summary = \App\Models\Result::where(['results.subject_id'=> $course_id, 'results.semester_id'=>$semester_id, 'results.batch_id'=>$year_id])->whereNotNull('exam_code')->whereNotNull('exam_score')
                ->join('student_classes', function($query){
                    $query->on(['student_classes.student_id'=>'results.student_id'])
                        ->on(['student_classes.year_id'=>'results.batch_id']);
                })->where(['student_classes.year_id'=>$year_id])
                ->join('program_levels', ['program_levels.id'=>'student_classes.class_id'])
                ->select(['program_levels.*', 'program_levels.id as class_id', DB::raw("COUNT(results.exam_code) as size")])
                ->groupBy('program_levels.id')->distinct()->get();

            if($class_id != null){
                $data['class'] = ProgramLevel::find($class_id);
                $data['student_codes'] = \App\Models\Result::where(['results.subject_id'=> $course_id, 'results.semester_id'=>$semester_id, 'results.batch_id'=>$year_id])->whereNotNull('exam_code')->whereNotNull('exam_score')
                    ->join('student_classes', function($query){
                        $query->on(['student_classes.student_id'=>'results.student_id'])
                            ->on(['student_classes.year_id'=>'results.batch_id']);
                    })->where(['student_classes.year_id'=>$year_id, 'student_classes.class_id'=>$request->class_id])
                    ->join('program_levels', ['program_levels.id'=>'student_classes.class_id'])
                    ->join('students', ['students.id'=>'results.student_id'])
                    ->select(['students.name', 'students.matric', 'results.id', 'results.exam_code', 'results.student_id', 'results.exam_score'])->distinct()->get();
            }
            $data['title'] = "Exam decoding for {$course->name} [$course->code] &rangle;&rangle; {$semester->name} &rangle;&rangle; {$year->name}";
            $data['course'] = $course;
            $data['year'] = $year;
            $data['semester'] = $semester;
            $data['decoding_summary'] = $decoding_summary;
            $data['has_exam'] = \App\Models\Result::where(['subject_id'=>$course_id, 'semester_id'=>$semester_id, 'batch_id'=>$year_id])->whereNotNull('exam_score')->count() > 0;

            // dd($data);
            return view('admin.result.coded.course_decode_details', $data);
        } catch (\Throwable $th) {
            //throw $th;
            session()->flash('error', "Operation failed. L::{$th->getLine()}, F::{$th->getFile()}, M::{$th->getMessage()}");
            return back()->withInput();
        }
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
                $data = ['course_id'=>$subject->id, 'course_code'=>$subject->code, 'exam_code'=>$request->code, 'year_id'=>$year_id, 'semester_id'=>$semester_id];
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
        try {
            \App\Models\ExamCourseCode::where(['course_id'=>$course_id, 'semester_id'=>$semester_id, 'year_id'=>$year_id])->first()->delete();
            return back()->with('success', 'Done');
        } catch (\Throwable $th) {
            //throw $th;
            session()->flash('error', "F::{$th->getFile()}, L::{$th->getLine()}, M::{$th->getMessage()}");
            return back()->withInput();
        }
    }

    public function uncode(Request $request, $year_id, $semester_id, $course_id, $result_id){
        $record = \App\Models\Result::find($result_id);
        if($record != null){
            if($record->exam_score != null){
                session()->flash('error', "Operation not allowed. Result record already has an exam mark.");
                return back();
            }
            $record->update(['exam_code'=>null]);
            session()->flash('success', "Operation complete");
            return back();
        }
        session()->flash('error', "Operation failed. Result record not found.");
        return back();
    }

    public function import_ca_only(Request $request, $year_id, $semester_id, $course_id)
    {
        # code...
        try {
            //code...
            $course  = \App\Models\Subjects::find($course_id);
            $semester  = \App\Models\Semester::find($semester_id);
            $year  = \App\Models\Batch::find($year_id);
            $results = \App\Models\Result::where(['subject_id'=>$course_id, 'semester_id'=>$semester_id, 'batch_id'=>$year_id])->orderBy('id', 'DESC')->get();
            $data['course'] = $course;
            $data['classes'] = $course->classes;
            $data['results'] = $results;
            $data['title'] = "Import CA Only For {$course->name} [{$course->code}] | {$semester->name} | {$year->name}";
            return view('admin.result.coded.import_ca_only', $data);
        } catch (\Throwable $th) {
            //throw $th;
            session()->flash('error', "Operation failed. L::{$th->getLine()}, F::{$th->getFile()}, M::{$th->getMessage()}");
            return back()->withInput();
        }
    }

    public function save_ca_only(Request $request, $year_id, $semester_id, $course_id)
    {
        # code...
        try {
            //code...
            $validity = Validator::make($request->all(), ['file'=>'required|file|mimesd:csv']);
            if($validity->fails()){
                session()->flash('error', $validity->errors()->first());
                return back()->withInput();
            }
            
            if(($file = $request->file('file')) != null){
                $filepath = public_path('uploads/files/');
                $filename = public_path('uploads/files/ca_only.csv');
                $file->move($filepath, 'ca_only.csv');
                
                
                // READ INPUT FILE
                $file_data = [];
                // dd($request->all());
                DB::beginTransaction();
                $fileStream = fopen($filename, 'r');
                while(($row = fgetcsv($fileStream, 1000))){
                    if(count($row) == 0 || ($row[0]??0) == null)
                        continue;
                    $file_data[] = ['matric'=>$row[0], 'ca_mark'=>$row[1]];
                }

                $input_data = collect($file_data)->map(function($rec)use($year_id){
                        $student = \App\Models\Students::where('matric', $rec['matric'])->first();
                        $rec['student_id'] = $student->id??null;
                        $rec['class_id'] = $student->_class($year_id)->id??'';
                        // dd($rec);
                        return $rec;
                    });
                // dd($input_data);

                foreach ($input_data as $key => $rec) {
                    # code...
                    \App\Models\Result::updateOrInsert(['subject_id'=>$course_id, 'student_id'=>$rec['student_id'], 'batch_id'=>$year_id, 'semester_id'=>$semester_id, 'class_id'=>$rec['class_id']], ['ca_score'=>$rec['ca_mark']]);
                }
                DB::commit();
                return back()->with('success', 'Done');
            }
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            session()->flash('error', "F::{$th->getFile()}, L::{$th->getLine()}, M::{$th->getMessage()}");
            return back()->withInput();
        }
    }

    public function undo_ca_import(Request $request, $year_id, $semester_id, $course_id)
    {
        # code...
        try {
            //code...
            $class = $request->class_id??null;
            DB::beginTransaction();
            if ($class == null) {
                # code...
                \App\Models\Result::where(['subject_id'=>$course_id, 'batch_id'=>$year_id, 'semester_id'=>$semester_id])->update(['ca_score'=>null]);
            } else {
                # code...
                \App\Models\Result::where(['subject_id'=>$course_id, 'batch_id'=>$year_id, 'semester_id'=>$semester_id, 'class_id'=>$class])->update(['ca_score'=>null]);
            }
            DB::commit();
            return back()->with('success', "Done");
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            session()->flash('error', "F::{$th->getFile()}, L::{$th->getLine()}, M::{$th->getMessage()}");
            return back()->withInput();
        }
        
    }

    public function import_exam(Request $request, $year_id, $semester_id, $course_id)
    {
        # code...
        try {
            //code...
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
        } catch (\Throwable $th) {
            //throw $th;
            session()->flash('error', "F::{$th->getFile()}, L::{$th->getLine()}, M::{$th->getMessage()}");
            return back()->withInput();
        }
    }

    public function save_exam(Request $request, $year_id, $semester_id, $course_id)
    {
        # code...
        try {
            //code...
            $validity = Validator::make($request->all(), ['file'=>'required|file|mimesd:csv']);
            if($validity->fails()){
                session()->flash('error', $validity->errors()->first());
                return back()->withInput();
            }
            
            if(($file = $request->file('file')) != null){
                $filepath = public_path('uploads/files/');
                $filename = public_path('uploads/files/exam_only.csv');
                $file->move($filepath, 'exam_only.csv');
                
                
                // READ INPUT FILE
                $file_data = [];
                // dd($request->all());
                DB::beginTransaction();
                $fileStream = fopen($filename, 'r');
                while(($row = fgetcsv($fileStream, 1000))){
                    if(count($row) == 0 || ($row[0]??0) == null)
                        continue;
                    $file_data[] = ['matric'=>$row[0], 'exam_mark'=>$row[1]];
                }

                $input_data = collect($file_data)->map(function($rec)use($year_id){
                        $student = \App\Models\Students::where('matric', $rec['matric'])->first();
                        $rec['student_id'] = $student->id??null;
                        $rec['class_id'] = $student->_class($year_id)->id??'';
                        // dd($rec);
                        return $rec;
                    });
                // dd($input_data);

                foreach ($input_data as $key => $rec) {
                    # code...
                    \App\Models\Result::updateOrInsert(['subject_id'=>$course_id, 'student_id'=>$rec['student_id'], 'batch_id'=>$year_id, 'semester_id'=>$semester_id, 'class_id'=>$rec['class_id']], ['exam_score'=>$rec['exam_mark']]);
                }
                DB::commit();
                return back()->with('success', 'Done');
            }
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            session()->flash('error', "F::{$th->getFile()}, L::{$th->getLine()}, M::{$th->getMessage()}");
            return back()->withInput();
        }
    }

    public function undo_exam_import(Request $request, $year_id, $semester_id, $course_id)
    {
        # code...
        try {
            //code...
            $class = $request->class_id??null;
            DB::beginTransaction();
            if ($class == null) {
                # code...
                \App\Models\Result::where(['subject_id'=>$course_id, 'batch_id'=>$year_id, 'semester_id'=>$semester_id])->update(['exam_score'=>null]);
            } else {
                # code...
                \App\Models\Result::where(['subject_id'=>$course_id, 'batch_id'=>$year_id, 'semester_id'=>$semester_id, 'class_id'=>$class])->update(['exam_score'=>null]);
            }
            DB::commit();
            return back()->with('success', "Done");
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            session()->flash('error', "F::{$th->getFile()}, L::{$th->getLine()}, M::{$th->getMessage()}");
            return back()->withInput();
        }
    }

    public function import_coded_exam(Request $request, $year_id, $semester_id, $course_id)
    {
        # code...
        try {
            //code...
            $course  = \App\Models\Subjects::find($course_id);
            $semester  = \App\Models\Semester::find($semester_id);
            $year  = \App\Models\Batch::find($year_id);
            $classes = $course->classes;
            $results = \App\Models\Result::where(['subject_id'=>$course_id, 'semester_id'=>$semester_id, 'batch_id'=>$year_id])->orderBy('id', 'DESC')->get();
            $data['title'] = "Import Coded Exam for {$course->name} [$course->code] | {$year->name}";
            $data['course'] = $course;
            $data['results'] = $results;
            $data['semester'] = $semester;
            $data['year'] = $year;
            $data['classes'] = $classes;
            return view('admin.result.coded.import_coded_exam', $data);
        } catch (\Throwable $th) {
            //throw $th;
            session()->flash('error', "F::{$th->getFile()}, L::{$th->getLine()}, M::{$th->getMessage()}");
            return back()->withInput();
        }
    }

    public function save_coded_exam(Request $request, $year_id, $semester_id, $course_id)
    {
        # code...
        try {
            //code...
            $validity = Validator::make($request->all(), ['file'=>'required|file|mimes:csv']);
            if($validity->fails()){
                session()->flash('error', $validity->errors()->first());
                return back()->withInput();
            }
            
            if(($file = $request->file('file')) != null){
                $filepath = public_path('uploads/files/');
                $filename = public_path('uploads/files/coded_exam_only.csv');
                $file->move($filepath, 'coded_exam_only.csv');
                
                DB::beginTransaction();
                
                // READ INPUT FILE
                $general_error = '';
                $file_data = [];
                // dd($request->all());
                $fileStream = fopen($filename, 'r');
                while(($row = fgetcsv($fileStream, 1000))){
                    if(count($row) == 0 || ($row[0]??0) == null)
                        continue;
                    $file_data[] = ['exam_code'=>$row[0], 'exam_mark'=>$row[1]];
                }

                $input_data = collect($file_data)->map(function($rec)use($year_id, $semester_id, $course_id, $general_error){
                        $result = \App\Models\Result::where(['subject_id'=>$course_id, 'semester_id'=>$semester_id, 'batch_id'=>$year_id, 'exam_code'=>$rec['exam_code']])->first();
                        if($result != null){
                            $rec['student_id'] = $result->student->id??null;
                            $rec['class_id'] = $result->student->_class($year_id)->id??'';
                        }else{
                            $general_error .= " \r - EXAM CODE: \'{$rec['exam_code']}\' has no matching record.";
                        }
                        // dd($rec);
                        return $rec;
                    });
                // dd($input_data);

                foreach ($input_data as $key => $rec) {
                    # code...
                    \App\Models\Result::updateOrInsert(['subject_id'=>$course_id, 'student_id'=>$rec['student_id'], 'batch_id'=>$year_id, 'semester_id'=>$semester_id, 'class_id'=>$rec['class_id']], ['exam_score'=>$rec['exam_mark']]);
                }
                DB::commit();
                return back()->with('success', "Done. {$general_error}");
            }
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            session()->flash('error', "F::{$th->getFile()}, L::{$th->getLine()}, M::{$th->getMessage()}");
            return back()->withInput();
        }
    }

    public function undo_coded_exam_import(Request $request, $year_id, $semester_id, $course_id, $class_id)
    {
        # code...
        return $this->undo_exam_import($request, $year_id, $semester_id, $course_id);
    }
}
