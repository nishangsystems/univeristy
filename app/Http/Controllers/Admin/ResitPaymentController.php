<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Resit;
use App\Models\ResitPayment;
use App\Models\StudentClass;
use App\Models\Students;
use App\Models\StudentSubject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ResitPaymentController extends Controller
{
    //

    public function index(Request $request){
        $data['title'] = "Pick Resit For Payment Collection";
        $data['resits'] = Resit::orderBy('id', 'DESC')->get();
        return view('admin.resit.payment.index', $data);
    }
    //

    public function student(Request $request, $resit_id){
        $data['title'] = "Pick Student For Resit Payment Collection";
        $data['resit'] = Resit::find($resit_id);
        return view('admin.resit.payment.student', $data);
    }
    //

    public function record(Request $request, $resit_id, $student_id){
        $student = Students::find($student_id);
        $program = $student->_class()->program??null;
        $data['resit_cost'] = $program->resit_cost??null;
        $data['title'] = "Record Payment For Resit";
        $data['resit'] = Resit::find($resit_id);
        $data['student'] = $student;
        $data['courses'] = StudentSubject::where(['student_id'=>$student_id, 'resit_id'=>$resit_id])->get();
        if($data['courses']->count() == 0){
            session()->flash('message', "This student has not registered any courses for this resit");
        }
        return view('admin.resit.payment.record', $data);
    }
    //

    public function save_record(Request $request, $resit_id, $student_id){
        $validity = Validator::make($request->all(), ['amount'=>'required|integer']);
        if($validity->fails()){
            return back()->with('error', $validity->errors()->first());
        }
        
        $data = ['resit_id'=>$resit_id, 'student_id'=>$student_id, 'amount'=>$request->amount, 'year_id'=>$this->current_accademic_year];
        $payment = ResitPayment::where(['resit_id'=>$resit_id, 'student_id'=>$student_id, 'year_id'=>$this->current_accademic_year])->first();
        ResitPayment::updateOrInsert(['resit_id'=>$resit_id, 'student_id'=>$student_id, 'year_id'=>$this->current_accademic_year], ['amount'=>(($payment->amount??0)+$request->amount)]);
        return back()->with('success', "Done");
    }
}