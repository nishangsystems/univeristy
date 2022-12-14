<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Result;
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
    

    public function import_ca_save(Request $request)
    {
        # code...
        // return $request->all();
        ini_set('memory_limit', '2048M');
        $validate = Validator::make($request->all(), [
            'year'=>'required',
            'semester'=>'required',
            'file'=>'required|max:2048',
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
                $filename = 'ca_'.date('Y-m-d@H-i-s').'.'.$file->getClientOriginalExtension();
                // File upload location
                $location = public_path('files');
                // Upload file
                
                $file_path = $file->move($location, $filename)->getPath().'/'.$filename;
    
                
                $errors = '<br>';
                $stream = fopen($file_path, 'r');
                DB::beginTransaction();
                while ($row = fgetcsv($stream)) {
                    # code...
                    if($row == null)break;
                    if($row != false){
                        $student = \App\Models\Students::where('matric', '=', $row[0])->first() ?? null;
                        $subject = \App\Models\Subjects::where('code', '=', $row[1])->first() ?? null;
                        if ($student == null) {
                            # code...
                            $errors .= 'student with matricule <strong>'.$row[0].'</strong> not found. </br>';                        
                        }
                        else if ($subject == null) {
                            # code...
                            $errors .= 'Course with code <strong>'.$row[1].'</strong> not found. </br>';
                        }
                        else{
                            // return $row;
                            $class = \App\Models\StudentClass::where('student_id', '=', $student->id)->where('year_id', '=', $request->year)->first();
                            if ($class == null) {
                                # code...
                                $errors .= 'No class registered for student <strong>'.$row[0].'</strong> year '.Batch::find($request->year)->name.' </br>';
                                continue;
                                
                            }
                            $data = [
                                'batch_id' => $request->year,
                                'student_id' => $student->id,
                                'class_id' => $class->class_id,
                                'sequence' => \App\Models\Sequence::where('term_id', '=', $request->semester)
                                                // ->whereIn('name', ['1st Sequence', '3rd Sequence', '5th Sequence'])
                                                ->first()->id,
                                'subject_id' => $subject->id,
                                'score' => $row[2],
                                'coef' => \App\Models\ClassSubject::where('subject_id', '=', $subject->id)->where('class_id', '=', $class->class_id)->first()->coef ?? $subject->coef,
                                'class_subject_id' => \App\Models\ClassSubject::where('subject_id', '=', $subject->id)->where('class_id', '=', $class->class_id)->first()->id,
                                'reference' => $request->reference
                            ];
                            if (\App\Models\Result::where([
                                'batch_id'=>$data['batch_id'],
                                'student_id'=>$data['student_id'],
                                'class_id'=>$data['class_id'],
                                'sequence'=>$data['sequence'],
                                'subject_id'=>$data['subject_id'],
                                'coef'=>$data['coef'],
                                'class_subject_id'=>$data['class_subject_id']
                            ])->count()>0) {
                                # code...
                                $errors .= $row[0]." already has a CA mark for ".$row[1]." this academic year and will not be added. Clear or delete record and re-import to make sure all data is correct</br>";
                            }else{
                                \App\Models\Result::create($data);
                            }
                        }

                    }
                }
                DB::commit();
                return back()->with('message', $errors .'<br>!Done');
            }
            else{return back()->with('error', 'File must be of type .csv');}
        } catch (\Throwable $th) {
            DB::rollBack();
            // return back()->with('error', $th->getMessage());
            throw $th;
        }
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
        // return $request->all();
        ini_set('memory_limit', '2048M');
        $validate = Validator::make($request->all(), [
            'year'=>'required',
            'semester'=>'required',
            'file'=>'required|max:2048',
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
                $filename = 'ca_'.date('Y-m-d@H-i-s').'.'.$file->getClientOriginalExtension();
                // File upload location
                $location = public_path('files');
                // Upload file
                
                $file_path = $file->move($location, $filename)->getPath().'/'.$filename;
    
                
                $errors = '<br>';
                $stream = fopen($file_path, 'r');
                DB::beginTransaction();
                while ($row = fgetcsv($stream)) {
                    # code...
                    if($row == null)break;
                    if($row != false){
                        $student = \App\Models\Students::where('matric', '=', $row[0])->first() ?? null;
                        $subject = \App\Models\Subjects::where('code', '=', $row[1])->first() ?? null;
                        if ($student == null) {
                            # code...
                            $errors .= 'student with matricule <strong>'.$row[0].'</strong> not found. </br>';                        
                        }
                        else if ($subject == null) {
                            # code...
                            $errors .= 'Course with code <strong>'.$row[1].'</strong> not found. </br>';
                        }
                        else{
                            // return $row;
                            $class = \App\Models\StudentClass::where('student_id', '=', $student->id)->where('year_id', '=', $request->year)->first();
                            if ($class == null) {
                                # code...
                                $errors .= 'No class registered for student <strong>'.$row[0].'</strong> year '.Batch::find($request->year)->name.' </br>';
                                continue;
                                
                            }

                            // SAVE CA RESULT
                            $ca = [
                                'batch_id' => $request->year,
                                'student_id' => $student->id,
                                'class_id' => $class->class_id,
                                'sequence' => \App\Models\Sequence::where('term_id', '=', $request->semester)
                                                // ->whereIn('name', ['1st Sequence', '3rd Sequence', '5th Sequence'])
                                                ->orderBy('name', 'ASC')->first()->id,
                                'subject_id' => $subject->id,
                                'score' => $row[2],
                                'coef' => \App\Models\ClassSubject::where('subject_id', '=', $subject->id)->where('class_id', '=', $class->class_id)->first()->coef,
                                'class_subject_id' => \App\Models\ClassSubject::where('subject_id', '=', $subject->id)->where('class_id', '=', $class->class_id)->first()->id,
                                'reference' => $request->reference
                            ];
                            if (\App\Models\Result::where([
                                'batch_id'=>$ca['batch_id'],
                                'student_id'=>$ca['student_id'],
                                'class_id'=>$ca['class_id'],
                                'sequence'=>$ca['sequence'],
                                'subject_id'=>$ca['subject_id'],
                                'coef'=>$ca['coef'],
                                'class_subject_id'=>$ca['class_subject_id']
                            ])->count()>0) {
                                # code...
                                $errors .= $row[0]." already has a CA mark for ".$row[1]." this academic year and will not be added. Clear or delete record and re-import to make sure all data is correct</br>";
                            }else{
                                \App\Models\Result::create($ca);
                            }

                            // SAVE EXAM RESULT
                            $exam = [
                                'batch_id' => $request->year,
                                'student_id' => $student->id,
                                'class_id' => $class->class_id,
                                'sequence' => \App\Models\Sequence::where('term_id', '=', $request->semester)
                                                // ->whereIn('name', ['1st Sequence', '3rd Sequence', '5th Sequence'])
                                                ->orderBy('name', 'DESC')->first()->id,
                                'subject_id' => $subject->id,
                                'score' => $row[3],
                                'coef' => \App\Models\ClassSubject::where('subject_id', '=', $subject->id)->where('class_id', '=', $class->class_id)->first()->coef,
                                'class_subject_id' => \App\Models\ClassSubject::where('subject_id', '=', $subject->id)->where('class_id', '=', $class->class_id)->first()->id,
                                'reference' => $request->reference
                            ];
                            if (\App\Models\Result::where([
                                'batch_id'=>$exam['batch_id'],
                                'student_id'=>$exam['student_id'],
                                'class_id'=>$exam['class_id'],
                                'sequence'=>$exam['sequence'],
                                'subject_id'=>$exam['subject_id'],
                                'coef'=>$exam['coef'],
                                'class_subject_id'=>$exam['class_subject_id']
                            ])->count()>0) {
                                # code...
                                $errors .= $row[0]." already has exam mark for ".$row[1]." this academic year and will not be added. Clear or delete record and re-import to make sure all data is correct</br>";
                            }else{
                                \App\Models\Result::create($exam);
                            }
                        }

                    }
                }
                DB::commit();
                return back()->with('message', $errors .'<br>!Done');
            }
            else{return back()->with('error', 'File must be of type .csv');}
        } catch (\Throwable $th) {
            DB::rollBack();
            // return back()->with('error', $th->getMessage());
            throw $th;
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
        $validate = Validator::make($request->all(), ['reference'=>'required', 'year'=>'required']);
        if ($validate->fails()) {
            # code...
            return $validate->errors()->first();
        }
        $results = Result::where('reference', '=', $request->reference)->whereIn('sequence', [1,3,5])->where('batch_id', '=', $request->year)->get();
        foreach ($results as $key => $value) {
            # code...
            $value->delete();
        }
        return back()->with('success', 'Done.');
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
        $validate = Validator::make($request->all(), ['reference'=>'required', 'year'=>'required']);
        if ($validate->fails()) {
            # code...
            return $validate->errors()->first();
        }
        $results = Result::where('reference', '=', $request->reference)->whereIn('sequence', [2,4,6])->where('batch_id', '=', $request->year)->get();
        foreach ($results as $key => $value) {
            # code...
            $value->delete();
        }
        return back()->with('success', 'Done.');
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
