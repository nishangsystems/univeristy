<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Controllers\HomeController;
use App\Models\Batch;
use App\Models\Payments;
use App\Models\ProgramLevel;
use App\Models\SchoolUnits;
use App\Models\Students;
use App\Models\TeachersSubject;
use Carbon\Carbon;
use Error;
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
        $fees = Payments::whereDate('payments.created_at', $request->date ?? date('Y-m-d', time()))
        ->join('students', 'students.id', '=', 'payments.student_id')
        ->where(function($q) {
            # code...
            auth()->user()->campus_id == null ? null : $q->where('students.campus_id', '=', auth()->user()->campus_id);
        })
        ->get(['payments.*', 'students.id as student_id', 'students.matric', 'students.name', 'students.campus_id', 'students.program_id']);
        // return $fees;
        return view('admin.fee.daily_report', compact('fees', 'title'));
    }

    public function fee(Request  $request)
    {
        $type = request('type', 'completed');
        $data['title'] = $type . " fee ";
        // dd($data);
        return view('admin.fee.fee', $data);
    }

    public function fee_situation(Request $request)
    {
        # code...

        $class = ProgramLevel::find($request->class);
        $data['title'] = 'Fee Situation';
        return view('admin.fee.fee_situation', $data);
    }

    public function fee_situation_list(Request $request)
    {
        # code...
        $class = ProgramLevel::find($request->class);
        $data['title'] = 'Fee Situation For '.$class->program->name.' : Level '.$class->level->level.' - '.Batch::find($request->year)->name;
        $data['data'] = HomeController::fee_situation($request);
        // return $data;
        return view('admin.fee.fee_situation_list', $data);
    }

    public function fee_list(Request  $request)
    {
        $type = request('type', 'completed');
        $data['title'] = $type . " Fee ";
        $data['students'] = HomeController::_fee($request);

        // return $data;
        return view('admin.fee.fee_listing', $data);
    }

    public function drive(Request  $request)
    {
        $title = "Fee Drive";
        $students = [];
        return view('admin.fee.drive', compact('students', 'title'));
    }

    public function drive_listing(Request $request)
    {
        // return $request->all();
        # code...
        $data = HomeController::_fee($request);
        $data['title'] = $data['title'].' Who Have Paid Atleast '.$request->amount;
        return view('admin.fee.drive_listing', $data);
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
        return view('admin.fee.import', $data);
    }

    public function import_save(Request $request)
    {
        # code...
        $validator = Validator::make($request->all(), [
            'file'=>'required|file',
            'import_reference'=>'required',
            'batch'=>'reuired',
        ]);

        if ($validator->fails()) {
            # code...
            return back()->with('error', $validator->errors()->first());
        }
        try {
            //code...

            // cancel operation if provided import_reference already exist
            if(Payments::where('import_reference', $request->import_reference)->count() > 0){
                return back()->with('error', "Import failed. Import already exists with import reference : ".$request->import_reference);
            }

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
                $campus_access = '';
                $campus_access_prefix= "Permission denied. Wrong campus. Can not import for ";
                $fee_settings_probs = '';
                if (count($file_data) > 0){
                    DB::beginTransaction();
                    $payments = [];
                    foreach ($file_data as $value) {
                        # code...
                        $student = \App\Models\Students::where('matric', '=', $value[0])->first() ?? null;
                        if (auth()->user()->campus_id != null && $student != null && $student->campus_id != auth()->user()->campus_id) {
                            # code...
                            $campus_access .= $student->matric .', ';
                            continue;
                        }
                        if ($student != null) {
                            # code...

                            // GET TUTION FEE FOR PROGRAM IN CAMPUS
                            $p_i = \App\Models\CampusProgram::where('campus_id', '=', $student->campus_id)
                            ->where('program_level_id', '=', $student->program_id)
                            ->join('payment_items', 'campus_program_id', '=', 'campus_programs.id')
                            ->where('name', '=', 'TUTION')->whereNotNull('amount');
                            // check if fee is set
                            // CHECK FEE SETTINGS FOR THE GIVEN PROGRAM IN THE GIVEN CAMPUS
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
                    if(strlen($campus_access) > 0){throw new Error($campus_access_prefix.$campus_access);}
                    if(strlen($fee_settings_probs) > 0) {throw new Error($fee_settings_probs);}
                    if(strlen($matric_probs) > 0) {throw new Error($matric_probs);}
                    if(strlen($ref_probs) > 0){throw new Error($ref_probs);}
                    return back()->with('success', 'Done');
                    return $request->all();
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
            // throw $th;
            return back()->with('error', $th->getMessage());
        }
    }

    public function import_undo(Request $request)
    {
        # code...
        $records = Payments::where('import_reference', $request->import_reference);
        if($records->count() > 0){
            foreach ($records->get() as $key => $record) {
                # code...
                $record->delete();
            }
        }
        return back()->with('success', 'Done');
    }
}
