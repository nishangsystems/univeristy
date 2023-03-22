<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\ClassSubject;
use App\Models\ProgramLevel;
use App\Models\Result;
use App\Models\Students;
use App\Models\Subjects;
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
            if (Result::where('reference', '=', $request->reference)->count() > 0) {
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
                        $student = Students::where('matric', '=', $row[0])->first() ?? null;
                        $subject = Subjects::where('code', '=', $row[1])->first() ?? null;
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
                            $class = Students::find($student->id)->_class($request->year);
                            if ($class == null) {
                                # code...
                                $errors .= 'No class registered for student <strong>'.$row[0].'</strong> year '.Batch::find($request->year)->name.' </br>';
                                continue;
                                
                            }
                            $ca_total = $student->_class($request->year)->program->ca_total;
                            if ($ca_total == null || $ca_total == 0) {
                                # code...
                                $errors .= "CA TOTAL not set for " . $student->_class($request->year)->program->name .' for student [ '.$student->matric.' ]</br>';
                                continue;
                            }else{
                                if($row[2] > $ca_total){
                                    $errors .= "CA mark for [ ".$subject->code.' ] '.$subject->name.' exceeds CA TOTALS for student [ '.$student->matric.' ]</br>';
                                    continue;
                                }
                            }
                            $data = [
                                'batch_id' => $request->year,
                                'student_id' => $student->id,
                                'class_id' => $class->id,
                                'semester_id' => $request->semester,
                                'subject_id' => $subject->id,
                                'ca_score' => $row[2],
                                'coef' => ClassSubject::where('subject_id', '=', $subject->id)->where('class_id', '=', $class->id)->first()->coef ?? $subject->coef,
                                'class_subject_id' => ClassSubject::where(['subject_id'=>$subject->id, 'class_id'=> $class->id])->count() > 0 ?
                                     ClassSubject::where(['subject_id'=>$subject->id, 'class_id'=> $class->id])->first()->id :
                                      null,
                                'reference' => $request->reference,
                                'user_id'=>auth()->id(),
                                'campus_id'=>$student->campus_id
                            ];
                            if (Result::where([
                                'batch_id'=>$data['batch_id'],
                                'student_id'=>$data['student_id'],
                                'class_id'=>$data['class_id'],
                                'semester_id'=>$data['semester_id'],
                                'subject_id'=>$data['subject_id'],
                                'coef'=>$data['coef'],
                                'class_subject_id'=>$data['class_subject_id']
                            ])->whereNotNull('ca_score')->count()>0) {
                                # code...
                                $errors .= $row[0]." already has a CA mark for ".$row[1]." this academic year and will not be added. Clear or delete record and re-import to make sure all data is correct</br>";
                            }else{
                                Result::create($data);
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
            // 'file'=>'required|max:4096',
            'file'=>'required',
            'reference'=>'required'
        ]);
        if ($validate->fails()) {
            return back()->with('error', $validate->errors()->first());
        }

        try {
            //code...
            // make sure reference doesn't exist
            if (Result::where('reference', '=', $request->reference)->count() > 0) {
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
                        $student = Students::where('matric', '=', $row[0])->first() ?? null;
                        $subject = Subjects::where('code', '=', $row[1])->first() ?? null;
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
                            $class = Students::find($student->id)->_class($request->year);
                            if ($class == null) {
                                # code...
                                $errors .= 'No class registered for student <strong>'.$row[0].'</strong> year '.Batch::find($request->year)->name.' </br>';
                                continue;
                                
                            }

                            $ca_total = $student->_class($request->year)->program->ca_total;
                            $exam_total = $student->_class($request->year)->program->exam_total;
                            if ($ca_total == null || $ca_total == 0) {
                                # code...
                                $errors .= "CA TOTAL not set for " . $student->_class($request->year)->program->name . ' for student [ '.$student->matric.' ]</br>';
                                continue;
                            }else{
                                if($row[2] > $ca_total){
                                    $errors .= "CA mark for [ ".$subject->code.' ] '.$subject->name.' exceeds CA TOTALS for student [ '.$student->matric.' ]</br>';
                                    continue;
                                }
                            }
                            if ($exam_total == null || $exam_total == 0) {
                                # code...
                                $errors .= "EXAM TOTAL not set for " . $student->_class($request->year)->program->name. ' for student [ '.$student->matric.' ]</br>';
                                continue;
                            }else{
                                if($row[3] > $exam_total){
                                    $errors .= "EXAM mark for [ ".$subject->code.' ] '.$subject->name.' exceeds EXAM TOTALS for student [ '.$student->matric.' ]</br>';
                                    continue;
                                }
                            }

                            // SAVE CA RESULT
                            $ca = [
                                'batch_id' => $request->year,
                                'student_id' => $student->id,
                                'class_id' => $class->id,
                                'semester_id' => $request->semester,
                                'subject_id' => $subject->id,
                                $row[2] == null ? null :'ca_score' => $row[2],
                                'exam_score' => $row[3],
                                'coef' => ClassSubject::where('subject_id', '=', $subject->id)->where('class_id', '=', $class->id)->first()->coef ?? $subject->coef,
                                'class_subject_id' => ClassSubject::where(['subject_id'=>$subject->id, 'class_id'=> $class->id])->count() > 0 ?
                                     ClassSubject::where(['subject_id'=>$subject->id, 'class_id'=> $class->id])->first()->id :
                                      null,
                                'reference' => $request->reference,
                                'campus_id'=>$student->campus_id
                            ];
                            $base = [
                                'batch_id' => $ca['batch_id'],
                                'student_id' => $ca['student_id'],
                                'class_id' => $ca['class_id'],
                                'semester_id' => $ca['semester_id'],
                                'subject_id' => $ca['subject_id'],
                                'coef' => $ca['coef'],
                                'class_subject_id' => $ca['class_subject_id']
                            ];
                            $update = [
                                $row[2] == null ? null : 'ca_score' => $row[2],
                                'exam_score' => $row[3],
                                'reference' => $request->reference,
                                'campus_id'=>$student->campus_id
                            ];
                            if (Result::where($base)->whereNotNull('ca_score')->count()>0) {
                                # code...
                                $errors .= $row[0]." already has CA mark for ".$row[1]." this academic year and will not be added. Clear or delete record and re-import to make sure all data is correct</br>";
                            }elseif(array_key_exists('ca_score', $ca)){
                                Result::updateOrCreate($base, ['ca_score'=>$ca['ca_score'], 'reference'=>$request->reference]);
                            }
                            if (Result::where($base)->whereNotNull('exam_score')->count()>0) {
                                # code...
                                $errors .= $row[0]." already has Exam mark for ".$row[1]." this academic year and will not be added. Clear or delete record and re-import to make sure all data is correct</br>";
                            }else{
                                Result::updateOrCreate($base, $update);
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
        $data['title'] = 'Clear Results';
        return view('admin.imports.clear_ca', $data);
    }

    public function clear_ca_save(Request $request)
    {
        # code...
        $validate = Validator::make($request->all(), ['reference'=>'required', 'year'=>'required', 'semester'=>'required']);
        if ($validate->fails()) {
            # code...
            return $validate->errors()->first();
        }
        $results = Result::where(['reference'=> $request->reference, 'semester_id'=> $request->semester, 'batch_id'=>$request->year])
                    ->join('students', ['students.id'=>'results.student_id'])
                    ->where(function($q){
                        auth()->user()->campus_id == null ? null : $q->where(['students.campus_id'=>auth()->user()->campus_id]);
                    })
                    ->get('results.*');
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
        $results = Result::where(['reference'=> $request->reference, 'semester_id'=> $request->semester, 'batch_id'=>$request->year])
                    ->join('students', ['students.id'=>'results.student_id'])
                    ->where(function($q){
                        auth()->user()->campus_id == null ? null : $q->where(['students.campus_id'=>auth()->user()->campus_id]);
                    })
                    ->get('results.*');
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
