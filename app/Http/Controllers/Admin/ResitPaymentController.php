<?php

namespace App\Http\Controllers\admin;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Resit;
use App\Models\ResitPayment;
use App\Models\StudentClass;
use App\Models\Students;
use App\Models\StudentSubject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $data['resit'] = Resit::find($resit_id);
        $data['student'] = $student;
        $data['title'] = "Record Resit Payment For {$data['resit']->name} :: {$student->name} [{$student->matric}]";
        $data['courses'] = StudentSubject::where(['student_id'=>$student_id, 'resit_id'=>$resit_id])->get();
        $data['payments'] = ResitPayment::where(['student_id'=>$student_id, 'resit_id'=>$resit_id])->get();
        $data['collected'] = ResitPayment::where(['student_id'=>$student_id, 'resit_id'=>$resit_id])->sum('amount');
        if($data['courses']->count() == 0){
            session()->flash('message', "This student has not registered any courses for this resit");
        }
        return view('admin.resit.payment.record', $data);
    }
    //

    public function save_record(Request $request, $resit_id, $student_id){
        // dd($request->all());
        $validity = Validator::make($request->all(), ['amount'=>'required|integer|min:1']);
        if($validity->fails()){
            return back()->with('error', $validity->errors()->first());
        }
        
        $data = ['resit_id'=>$resit_id, 'student_id'=>$student_id, 'amount'=>$request->amount, 'year_id'=>$this->current_accademic_year, 'recorded_by'=>auth()->id(), 'created_at'=>now(), 'updated_at'=>now()];
        $payment = ResitPayment::where(['resit_id'=>$resit_id, 'student_id'=>$student_id, 'year_id'=>$this->current_accademic_year])->first();
        ResitPayment::create($data);
        return back()->with('success', "Done");
    }
    //

    public function report(Request $request, $resit_id, $class_id=null, $year_id = null){
        $resit = Resit::find($resit_id);
        $data['resit'] = $resit;
        $data['title'] = "Payment Report For {$resit->name}, {$resit->year->name}";
        $data['years'] = Batch::all();
        $data['classes'] = Controller::sorted_program_levels();
        $class = null;
        if($class_id != null){
            $class = \App\Models\ProgramLevel::find($class_id);
            $data['title'] = "{$class->name()} Payment Report For {$resit->name}, {$resit->year->name}";
        }

        // studennt 1: join student_courses for existing student-courses OR 2: join resit_payments for existing student-payments
        
        $_year_id = $year_id != null ? $year_id : $resit->year_id;
        $student_courses = StudentSubject::where(['resit_id'=>$resit_id, 'year_id'=>$_year_id])->distinct()->get();
        $resit_payments = ResitPayment::where(['resit_id'=>$resit_id, 'year_id'=>$_year_id])->select(['resit_payments.*', DB::raw('SUM(amount) as _amt')])->groupBy('student_id')->distinct()->get();
        $sids = array_unique(array_merge($student_courses->pluck('student_id')->toArray(), $resit_payments->pluck('student_id')->toArray()));
        $students = Students::join('student_classes', 'student_classes.student_id', '=', 'students.id')
            ->whereIn('students.id', $sids)
            ->where(function($qry)use($class_id){
                $class_id == null ? null : $qry->where('student_classes.class_id', $class_id);
            })
            ->distinct()
            ->get(['students.*'])
            ->each(function($rec)use($student_courses, $resit_payments){
                $program = $rec->_class()->program??null;
                $resit_cost = $program->resit_cost??0;
                $rec->unit_cost = $resit_cost;
                if(($rp = $resit_payments->where('student_id', $rec->id))->count() != 0){
                    $rec->year_id = $rp->first()->year_id;
                    $rec->cash_payment = $rp->first()->_amt;
                }else{$rec->cash_payment = 0;}
                if(($sc = $student_courses->where('student_id', $rec->id))->count() != 0){
                    $rec->year_id = $sc->first()->year_id;
                    $rec->n_courses = $sc->count();
                    $rec->expected_amount = $resit_cost * $rec->n_courses;
                    $rec->paid_online = $sc->where('paid', 1)->count() * $resit_cost;
                }else{$rec->n_courses = 0; $rec->expected_amount = 0; $rec->paid_online = 0;}
            });
        
        $data['report'] = $students;
        // dd($students);
        if($request->print == 1)
            return view('admin.resit.payment.payment_report', $data);
        return view('admin.resit.payment.payment_report_index', $data);
    }
}
