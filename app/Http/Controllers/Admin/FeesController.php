<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Payments;
use App\Models\SchoolUnits;
use App\Models\Students;
use App\Models\TeachersSubject;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FeesController extends Controller
{

    public function classes(Request  $request)
    {

        $title = "Classes";
        $classes = \App\Models\SchoolUnits::where('parent_id', $request->get('parent_id', '0'))->get();

        return view('admin.fee.classes', compact('classes', 'title'));
    }

    public function student(Request  $request, $class_id)
    {
        $class = SchoolUnits::find($class_id);
        $title = $class->name . " Students";
        $students = $class->students(Session::get('mode', \App\Helpers\Helpers::instance()->getCurrentAccademicYear()))->paginate(20);
        return view('admin.fee.students', compact('students', 'title'));
    }

    public function collect(Request  $request)
    {
        $title = "Collect Fee";
        return view('admin.fee.collect', compact('title'));
    }

    public function printFee(Request  $request)
    {
        $title = "Print Fee";
        return view('admin.fee.print', compact('title'));
    }

    public function printStudentFee(Request  $request, $student_id)
    {
        $student = Students::find($student_id);
        $year = \App\Helpers\Helpers::instance()->getYear();
        $numbers = [1, 2];
        return view('admin.fee.print_reciept', compact('student', 'year', 'numbers'));
    }

    public function daily_report(Request  $request)
    {
        $title = "Fee Daily Report for " . ($request->date ? $request->date : Carbon::now()->format('d/m/Y'));
        $fees = Payments::whereDate('created_at', $request->date ? $request->date : Carbon::now())->get();
        return view('admin.fee.daily_report', compact('fees', 'title'));
    }

    public function fee(Request  $request)
    {
        $type = request('type', 'completed');
        $title = $type . " fee ";
        $students = [];
        return view('admin.fee.fee', compact('students', 'title'));
    }

    public function drive(Request  $request)
    {
        $title = "Fee Drive";
        $students = [];
        return view('admin.fee.drive', compact('students', 'title'));
    }

    public function delete(Request  $request, $id)
    {
        Payments::find($id)->delete();
        Session::flash('success', "Fee collection deleted successfully!");
        return redirect()->back();
    }

    public function import()
    {
        # code...
        $data['title'] = "Import Fee";
        // $data['classes'] = [];
        // foreach (\App\Models\ProgramLevel::all() as $value) {
        //     # code...
        //     $data['classes'][] = [
        //         'id'=>$value->id,
        //         'name'=>$value->program()->first()->name.' :LEVEL '.$value->level()->first()->level
        //     ];

        // }
        // $data['classes'] = collect($data['classes'])->sortBy('name');
        return view('admin.fee.import', $data);
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
}
