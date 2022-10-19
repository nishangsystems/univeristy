<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
// use App\Models\Income;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ImportCenter extends Controller
{
    public function import_ca()
    {
        # code...
        $data['title'] = 'Import CA';
        return view('admin.imports.import_ca', $data);
    }
    

    public  function import_ca_save(Request  $request)
    {
        // Validate request
        $request->validate([
            'year'=>'required',
            'semester'=>'required',
            'file'=>'required',
            'reference'=>'required'
        ]);

        // used to track duplicate records that won't be inserted/saved.
        $duplicates = '';

        $file = $request->file('file');
        // File Details

        $extension = $file->getClientOriginalExtension();
        $filename = 'ca_'.date('Y-m-d_H:i:s').'.'. $extension;
        // Valid File Extensions;
        $valid_extension = array("csv");
        if (in_array(strtolower($extension), $valid_extension)) {
            // File upload location
            $location = public_path() . '/files/';
            // Upload file
            $file->move($location, $filename);
            $filepath = public_path('/files/' . $filename);
            
            $file = fopen($filepath, "r");

            $importData_arr = array();
            $i = 0;

            while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
                $num = count($filedata);
                for ($c = 0; $c < $num; $c++) {
                    $importData_arr[$i][] = $filedata[$c];
                }
                $i++;
            }
            fclose($file);
            // dd($importData_arr);

            DB::beginTransaction();
            try {
                for ($i=1; $i < count($importData_arr); $i++) { 
                    # code...
                    $student = \App\Models\Students::where('matric', '=', $importData_arr[$i][0])->first();
                    $subject = \App\Models\Subjects::where('code', '=', $importData_arr[$i][1])->first();
                    $class = \App\Models\StudentClass::where('student_id', '=', $student->id)->where('year_id', '=', $request->year)->first();
                    $data = [
                        'batch_id' => $request->year,
                        'student_id' => $student->id,
                        'class_id' => $class->class_id,
                        'sequence' => \App\Models\Sequence::where('term_id', '=', $request->semester)
                                        // ->whereIn('name', ['1st Sequence', '3rd Sequence', '5th Sequence'])
                                        ->first()->id,
                        'subject_id' => $subject->id,
                        'score' => $importData_arr[$i][2],
                        'coef' => \App\Models\ClassSubject::where('subject_id', '=', $subject->id)->where('class_id', '=', $class->class_id)->first()->coef,
                        'class_subject_id' => \App\Models\ClassSubject::where('subject_id', '=', $subject->id)->where('class_id', '=', $class->class_id)->first()->id,
                        'reference' => $request->reference
                    ];
                    \App\Models\Result::create($data);
                }
                // dd($student);

                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                return back()->with('error', $e->getMessage());
            }
            session('message', 'Import Successful.');
            //echo("<h3 style='color:#0000ff;'>Import Successful.</h3>");
        } else {
            //echo("<h3 style='color:#ff0000;'>Invalid File Extension.</h3>");

            session('message', 'Invalid File Extension.');
        }

        return redirect()->to(route('admin.students.index', [$request->program_id]))->with('success', $duplicates == '' ? 'Student Imported successfully!' : 'Student Imported successfully! The following students where not imported because they are already in the database;\n'.$duplicates);
    }

    public function importPost(Request $request)
    {
        # code...
        // return $request->all();
        $validate = Validator::make($request->all(), [
            'year'=>'required',
            'semester'=>'required',
            'file'=>'required',
            'reference'=>'required'
        ]);
        if ($validate->fails()) {
            return back()->with('error', $validate->errors()->first());
        }

        // try {
            //code...
            // make sure reference doesn't exist
            if (\App\Models\Result::where('reference', '=', $request->reference)->count() > 0) {
                # code...
                return back()->with('error', 'Could not import. Reference already exist.');
            }
    
            $file = $request->file('file');
            if ($file->getClientOriginalExtension() == 'csv') {
                # code...
                $filename = 'ca_'.date('Y-m-d@H:i:s').'.'.$file->getClientOriginalExtension();
                // File upload location
                $location = public_path('files');
                // Upload file
                return 
                $file->move($location, $filename);
                $file_path = $location . $filename;
    
                
                $import_array = [];
                $stream = fopen($file_path, 'r');
                while (($row = fgetcsv($stream)) !== null) {
                    # code...
                    $import_array[] = $row;
                }
    
                // start from index 1!0 to drop headings
                for ($i=1; $i < count($import_array); $i++) { 
                    # code...
                    $student = \App\Models\Students::where('matric', '=', $import_array[$i][0])->first();
                    $subject = \App\Models\Subjects::where('code', '=', $import_array[$i][1])->first();
                    $class = \App\Models\StudentClass::where('student_id', '=', $student->id)->where('year_id', '=', $request->year)->first();
                    $data = [
                        'batch_id' => $request->year,
                        'student_id' => $student->id,
                        'class_id' => $class->class_id,
                        'sequence' => \App\Models\Sequence::where('term_id', '=', $request->semester)
                                        // ->whereIn('name', ['1st Sequence', '3rd Sequence', '5th Sequence'])
                                        ->first()->id,
                        'subject_id' => $subject->id,
                        'score' => $import_array[$i][2],
                        'coef' => \App\Models\ClassSubject::where('subject_id', '=', $subject->id)->where('class_id', '=', $class->class_id)->first()->coef,
                        'class_subject_id' => \App\Models\ClassSubject::where('subject_id', '=', $subject->id)->where('class_id', '=', $class->class_id)->first()->id,
                        'reference' => $request->reference
                    ];
                    \App\Models\Result::create($data);
                }
                return back()->with('success', 'Done');
            }
            else{return back()->with('error', 'File must be of type .csv');}
        // } catch (\Throwable $th) {
        //     throw $th;
        //     // return back()->with('error', $th->getMessage());
        // }
    }

    public function import_exam()
    {
        # code...
        $data['title'] = 'Import Exams';
        return view('admin.imports.import_exam', $data);
    }
    
    public function import_exam_save(Request $request)
    {
        # code...
        $validate = Validator::make($request->all(), [
            'year'=>'required',
            'semester'=>'required',
            'file'=>'required',
            'reference'=>'required'
        ]);
        if ($validate->fails()) {
            return back()->with('error', $validate->errors()->first());
        }

        try {
            //code...
            // make sure reference doesn't exist
            if (\App\Models\Result::where('reference', '=', $request->reference)->count() > 0) {
                # code...
                return back()->with('error', 'Could not import. Reference already exist.');
            }
    
            $file = $request->file('file');
            if ($file->getClientOriginalExtension() == 'csv') {
                # code...
                $filename = 'ca_'.date('Y-m-d@H:i:s').'.'.$file->getClientOriginalExtension();
                $file->move(public_path('files'), $filename);
                $file_path = public_path('files/'.$filename);
    
                
                $import_array = [];
                $stream = fopen($file_path, 'r');
                while (($row = fgetcsv($stream)) !== null) {
                    # code...
                    $import_array[] = $row;
                }
    
                // start from index 1!0 to drop headings
                for ($i=1; $i < count($import_array); $i++) { 
                    # code...
                    $student = \App\Models\Students::where('matric', '=', $import_array[$i][0])->first();
                    $subject = \App\Models\Subjects::where('code', '=', $import_array[$i][1])->first();
                    $class = \App\Models\StudentClass::where('student_id', '=', $student->id)->where('year_id', '=', $request->year)->first();
                    $ca = [
                        'batch_id' => $request->year,
                        'student_id' => $student->id,
                        'class_id' => $class->class_id,
                        'sequence' => \App\Models\Sequence::where('term_id', '=', $request->semester)
                                        // ->whereIn('name', ['1st Sequence', '3rd Sequence', '5th Sequence'])
                                        ->orderBy('name', 'ASC')->first()->id,
                        'subject_id' => $subject->id,
                        'score' => $import_array[$i][2],
                        'coef' => \App\Models\ClassSubject::where('subject_id', '=', $subject->id)->where('class_id', '=', $class->class_id)->first()->coef,
                        'class_subject_id' => \App\Models\ClassSubject::where('subject_id', '=', $subject->id)->where('class_id', '=', $class->class_id)->first()->id,
                        'reference' => $request->reference
                    ];
                    $exam = [
                        'batch_id' => $request->year,
                        'student_id' => $student->id,
                        'class_id' => $class->class_id,
                        'sequence' => \App\Models\Sequence::where('term_id', '=', $request->semester)
                                        // ->whereIn('name', ['1st Sequence', '3rd Sequence', '5th Sequence'])
                                        ->orderBy('name', 'DESC')->first()->id,
                        'subject_id' => $subject->id,
                        'score' => $import_array[$i][3],
                        'coef' => \App\Models\ClassSubject::where('subject_id', '=', $subject->id)->where('class_id', '=', $class->class_id)->first()->coef,
                        'class_subject_id' => \App\Models\ClassSubject::where('subject_id', '=', $subject->id)->where('class_id', '=', $class->class_id)->first()->id,
                        'reference' => $request->reference
                    ];
                    \App\Models\Result::create($ca);
                    \App\Models\Result::create($exam);
                }
                return back()->with('success', 'Done');
            }
            else{return back()->with('error', 'File must be of type .csv');}
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    public function clear_ca()
    {
        # code...
        $data['title'] = 'Clear CA';
        return view('admin.imports.clear_ca', $data);
    }

    public function clear_ca_save(Request $request)
    {
        # code...
    }
    
    public function clear_exam()
    {
        # code...
        $data['title'] = 'Clear Exams';
        return view('admin.imports.clear_exam', $data);
    }

    public function clear_exam_save(Request $request)
    {
        # code...
    }

    public function clear_fee()
    {
        # code...
        $data['title'] = 'Clear Fees';
        return view('admin.imports.clear_fee', $data);
    }

    public function clear_fee_save(Request $request)
    {
        # code...
    }
}
