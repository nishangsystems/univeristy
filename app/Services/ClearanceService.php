<?php

namespace App\Services;

use App\Helpers\Helpers;
use App\Models\Students;
use App\Models\StudentScholarship;
use App\Models\Batch;
use App\Models\Payments;
use App\Models\PaymentItem;
use App\Models\StudentClass;
use App\Models\School;
use App\Models\FeeClearance;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ClearanceService{


    public function __construct()
    {
        # code...
    }


    public function feeClearance($student_id)
    {
        # code...
        $student = Students::find($student_id);
        // dd($total_paid);
        if($student != null){
            $ret =  $student->classes()
                ->join('program_levels', 'program_levels.id', '=', 'student_classes.class_id')
                ->join('campus_programs', 'campus_programs.program_level_id', '=', 'program_levels.id')
                ->where('campus_programs.campus_id', $student->campus_id)
                ->join('payment_items', 'payment_items.campus_program_id', '=', 'campus_programs.id')
                // ->where('payment_items.name', 'TUTION')
                ->select(['payment_items.*', 'student_classes.year_id as class_year_id'])
                ->orderBy('class_year_id')
                ->distinct()
                ->get()->filter(function($el){
                    return $el->year_id == $el->class_year_id;
                });

            

            $fee_ids = PaymentItem::where('name', 'TUTION')->pluck('id')->toArray();
            $reg_ids = PaymentItem::where('name', 'REGISTRATION')->pluck('id')->toArray();
            $total_paid = intval(Payments::where('student_id', $student_id)
                ->whereIn('payment_id', $fee_ids)->sum('amount'));
            $total_reg_paid = intval(Payments::where('student_id', $student_id)
                ->whereIn('payment_id', $reg_ids)->sum('amount'));

            $class = $student->_class();
            $admission_year = Batch::find($student->admission_batch_id);
            $data['program'] = $class->program;
            $data['degree'] = $data['program']->degree;
            $data['school'] = $data['program']->parent->parent??null;
            $data['total_reg_paid'] = $total_reg_paid;
            $data['student'] = $student;
            $data['ref'] = (intval(substr($student->matric, -4)) > 0 ? substr($student->matric, -4) : substr($student->matric, -3))
                            .('/'.(substr($admission_year->name, 2, 2))."/".now()->format('my'));
            $fees = $ret->where('name', 'TUTION');
            $regs = $ret->where('name', 'REGISTRATION');
             
            $ret->map(function($fxn)use($student_id, $regs){
                $fxn->_year = Batch::find($fxn->year_id)->name??'';
                $fxn->scholarship = StudentScholarship::where(['student_id'=>$student_id, 'batch_id'=>$fxn->year_id])->sum('amount');
                return $fxn;
            })->sortBy('year');
            
            $data['total_paid'] = $total_paid + $ret->sum('scholarship');
            foreach ($fees as $key => $fxn) {
                # code...
                if($total_paid <= $fxn->amount){
                    $fxn->paid = $total_paid;
                    $total_paid -= $total_paid;
                }else{
                    $fxn->paid = $fxn->amount;
                    $total_paid -= $fxn->amount;
                }
                if($regs->sum('amount') > 0){
                    if(($val = $regs->where('year_id', $fxn->year_id)->first()->amount) != null){
                        if($total_reg_paid <= $val){
                            $fxn->reg = $total_reg_paid;
                            $total_reg_paid -= $total_reg_paid;
                        }else{
                            $fxn->reg = $val;
                            $total_reg_paid -= $val;
                        }
                    }
                }
            }

            
            $data['clearance'] = $fees;
            $data['total_expected'] = $ret->sum('amount');
            $data['fee_cleared'] = ($data['total_paid'] + $data['total_reg_paid']) >= $data['total_expected'];
            if(!$data['fee_cleared']){
                $data['debt'] = ($data['total_expected'] - ($data['total_paid'] + $data['total_reg_paid']));
                $data['err_msg'] = "You still owe a sum of ".$data['debt'];
            }else{
                $this->saveClearance($student_id);
            }
            $data['institution'] = School::first();

            $data['qrcode'] = QrCode::size('200')->generate(route('student.fee_statement', $student_id));
            // dd($data);
            return $data;
        }
        return collect();
    }


    public function saveClearance($student_id)
    {
        # code...
        $student = Students::find($student_id);
        if($student != null){
            $classes = $student->classes()->orderBy('year_id')->get();
            FeeClearance::updateOrInsert(['student_id'=>$student_id], ['admission_year_id'=>$classes->first()->year_id, 'final_year_id'=>$classes->last()->year_id, 'updated_at'=>now()]);
            return 1;
        }
        throw new \Exception("Student instance not found");
    }


    public function lastClearance($student_id)
    {
        # code...
        $student = Students::find($student_id);
        if($student != null){
            return FeeClearance::where(['student_id'=>$student_id])->orderBy('id', 'DESC')->first();
        }
        throw new \Exception("Student instance not found");
    }

    public function student_fee_statement($student_id){
        $student = Students::find($student_id);
        if($student == null){
            return view('student_fee_statement', ['status'=>'error', 'message'=>'Student was not found on the system.']);
        }
        $clearance_record = \App\Models\FeeClearance::where('student_id', $student_id)->count();
        if($clearance_record == 0){
            return view('student_fee_statement', ['status'=>'error', 'message'=>'No fee clearance has been issued for this student.']);
        }
        // get expected fee for this student
        $expected_fee = Students::where('students.id', $student_id)
            ->join('student_classes', ['student_classes.student_id'=>'students.id'])
            ->join('program_levels', ['program_levels.id'=>'student_classes.class_id'])
            ->join('campus_programs', function($query){
                $query->on(['campus_programs.program_level_id'=>'program_levels.id'])
                    ->on(['campus_programs.campus_id'=>'students.campus_id']);
            })->join('payment_items', function($query){
                $query->on(['payment_items.campus_program_id'=>'campus_programs.id'])
                    ->on(['payment_items.year_id'=>'student_classes.year_id']);
            })->sum('amount') 
            + \App\Models\ExtraFee::where('student_id', $student_id)->sum('amount');

        $covered_fee = \App\Models\StudentScholarship::where('student_id', $student_id)->sum('amount')
            + \App\Models\Payments::where('student_id', $student_id)->sum('amount');


        if($covered_fee >= $expected_fee){
            return view('student_fee_statement', ['status'=>'success', 'message'=>"Fee payments completed."]);
        }else{
            return view('student_fee_statement', ['status'=>'message', 'message'=>"Fee payments not yet completed."]);
        }

    }


}