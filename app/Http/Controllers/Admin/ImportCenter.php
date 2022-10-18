<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
// use App\Models\Income;
use Illuminate\Http\Request;
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
    
    public function import_save(Request $request)
    {
        # code...
        return $request->all();
        $validator = Validator::make($request->all(), [
            'file'=>'required|file',
            // 'unit_id'=>'required',
            'batch'=>'reuired',
        ]);

        if ($validator->fails()) {
            # code...
            return back()->with('error', $validator->errors()->first());
        }
        
        try {
            //code...
            $file = $request->file('file');
            if($file->getClientOriginalExtension() == 'csv'){
                // save file
                $filename = 'fee-'.date('Y_m_d@H_i_s',time()).'_'.random_int(100, 999).'.'.$file->getClientOriginalExtension();
                $file->move(public_path('files'), $filename);
                $file_path = public_path('files/'.$filename);
    
                $pointer = fopen($file_path, 'r');
                $file_data = [];
                while (($data_row = fgetcsv($pointer, 1000, ',')) !== false) {
                    $file_data[] = $data_row;
                }
                // return $file_data;
    
                $matric_probs = '';
                $ref_probs = '';
                $fee_settings_probs = '';
                if (count($file_data) > 0){
                    DB::beginTransaction();
                    $payments = [];
                    foreach ($file_data as $value) {
                        # code...
                        $student = \App\Models\Students::where('matric', '=', $value[0])->first() ?? null;
                        if ($student !== null) {
                            # code...
                            $p_i = \App\Models\CampusProgram::where('campus_id', '=', $student->campus_id)
                            ->where('program_level_id', '=', $student->program_id)
                            ->join('payment_items', 'campus_program_id', '=', 'campus_programs.id')
                            ->where('name', '=', 'TUTION')->whereNotNull('amount');
                            // check if fee is set
                            if ($p_i->count() == 0) {
                                # code...
                                $prog = \App\Models\ProgramLevel::find($student->program_id);
                                $cmps = \App\Models\Campus::find($student->campus_id)->name;
                                $str = " Tution not set for Program : ".$prog->program()->first()->name." LEVEL ".$prog->level()->first()->level.' in '.$cmps.' campus';
                                $fee_settings_probs .= str_contains($fee_settings_probs, $str) ? '' : $str;
                                continue;
                            }
                            $payments[] = [
                                'payment_id' => $p_i->pluck('payment_items.id')[0],
                                'student_id' => $student->id,
                                'batch_id' => $request->batch_id,
                                'unit_id' => \App\Models\StudentClass::where('student_id', '=', $student->id)->pluck('class_id')[0],
                                'amount' => $value[1],
                                'reference_number' => $value['2'] ?? ''
                            ];
                        }else{
                            $matric_probs .= ' matricule '.$value[0].' not found,';
                        }
                    }
                    // return $payments;
                    foreach ($payments as $value) {
                        if ($value['reference_number'] != '' && \App\Models\Payments::where('reference_number', '=', $value['reference_number'])->count() == 0) {
                            # code...
                            \App\Models\Payments::create($value);
                        }
                        else{
                            $ref_probs .= " reference error with ".\App\Models\Students::find($value['student_id'])->matric;
                        }
                    }
                    DB::commit();
                    return (strlen($matric_probs) || strlen($fee_settings_probs) || strlen($ref_probs) > 0) ?
                    back()->with('error', $matric_probs.'. '.$fee_settings_probs.'. '.$ref_probs):
                    back()->with('success', 'Done');
                }
                else {
                    return back()->with('error', 'No data could be read from file');
                }
            }
            else{
                return back()->with('error', 'File must be of type .csv');
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
            // return back()->with('error', 'Error encountered. Process failed');
        }
    }
    public function import_ca_save(Request $request)
    {
        # code...
        return 1234;
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
                $filename = 'ca-'.date('Y-m-d@H:i:s').'.'.$file->getClientOriginalExtension();
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
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
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
                $filename = 'ca-'.date('Y-m-d@H:i:s').'.'.$file->getClientOriginalExtension();
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
