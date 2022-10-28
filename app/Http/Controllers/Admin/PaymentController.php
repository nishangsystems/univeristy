<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\CampusProgram;
use App\Models\PaymentItem;
use App\Models\Payments;
use App\Models\SchoolUnits;
use App\Models\Students;
use App\Models\StudentScholarship;
use Illuminate\Http\Request;
use Session;
use Redirect;

use Auth;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{

    private $batch_id;

    public function __construct()
    {
        $this->batch_id = Batch::find(\App\Helpers\Helpers::instance()->getCurrentAccademicYear())->id;
    }
    public function index(Request $request, $student_id)
    {
        $student = Students::find($student_id);
        $data['title'] = "Fee collections for " . $student->name;
        $data['student'] = $student;
        return view('admin.fee.payments.index')->with($data);
    }

    public function create(Request $request, $student_id)
    {
        $student = Students::find($student_id);
        $data['student'] = $student;
        $data['scholarship'] = Helpers::instance()->getStudentScholarshipAmount($student_id);
        $data['total_fee'] = $student->total($student_id);
        // $data['total_fee'] = CampusProgram::where('campus_id', $student->campus_id)->where('program_level_id', $student->program_id)->first()->payment_items()->first()->amount;
        $data['balance'] =  $student->bal($student_id);
        $data['title'] = "Collect Fee for " . $student->name;
        

        if ($data['balance'] == 0) {

            return back()->with('error', 'Student has already completed fee');
        }
        if ($data['total_fee'] == -1) {

            return back()->with('error', 'Fee not set');
        }
        return view('admin.fee.payments.create')->with($data);
    }

    public function edit(Request $request, $student_id, $id)
    {
        $student = Students::find($student_id);
        $data['student'] = $student;
        $data['payment'] = Payments::find($id);
        $data['title'] = "Collect Fee for " . $student->name;
        return view('admin.fee.payments.edit')->with($data);
    }

    public function store(Request $request, $student_id)
    {

        $student = Students::find($student_id);
        $total_fee = $student->total($student_id);
        $balance =  $student->bal($student_id);
        $this->validate($request, [
            'item' =>  'required',
            'amount' => 'required',
            'date' => 'required|date'
        ]);
        if ($request->amount > $total_fee) {
            return back()->with('error', 'The amount deposited has exceeded the total fee amount');
        }if($request->amount >  $balance) {
            return back()->with('error', 'The amount deposited has exceeded the total fee amount');
        }
        Payments::create([
            "payment_id" => $request->item,
            "student_id" => $student->id,
            "unit_id" => $student->class(Helpers::instance()->getYear())->id,
            "batch_id" => Helpers::instance()->getYear(),
            "amount" => $request->amount,
            "date" => $request->date
        ]);

        return back()->with('success', "Fee collection recorded successfully !");
    }

    public function update(Request $request, $student_id, $id)
    {
        $student = Students::find($student_id);
        $this->validate($request, [
            'item' =>  'required',
            'amount' => 'required',
        ]);
        $total_fee = $student->total($student_id);
        $paid =  $student->paid();
        if ($request->amount > $total_fee) {
            return back()->with('error', 'The amount deposited has exceeded the total fee amount');
        }
        $p =  Payments::find($id);
        $new_balance = $paid - $p->amount;
        if(($new_balance + $request->amount) > $total_fee){
            return back()->with('error', 'The amount deposited has exceeded the total fee amount');
        }
        $p->update([
            "payment_id" => $request->item,
            "amount" => $request->amount,
            "unit_id" => $student->class(Helpers::instance()->getYear())->id,
        ]);

        return redirect()->to(route('admin.fee.student.payments.index', $student_id))->with('success', "Fee collection record updated successfully !");
    }

    public function destroy(Request $request, $student_id, $id)
    {
        $p =  Payments::find($id);
        $p->delete();
        return redirect()->to(route('admin.fee.student.payments.index', $student_id))->with('success', "Fee collection record deleted successfully !");
    }

    // private function checkScholars($student_id)
    // {
    //     $scholar = DB::table('student_scholarships')
    //         ->join('students', 'students.id',  '=', 'student_scholarships.student_id')
    //         ->join('batches', 'batches.id', '=', 'student_scholarships.batch_id')
    //         ->join('scholarships', 'scholarships.id', '=', 'student_scholarships.scholarship_id')
    //         ->where('student_scholarships.student_id', $student_id)
    //         ->where('student_scholarships.batch_id', $this->batch_id)
    //         ->select('students.id', 'scholarships.amount')->first();
    //     return $scholar;
    // }

    // private function getBalanceFee($class_id)
    // {
    //     $tuition = DB::table('payment_items')
    //         ->join('school_units', 'school_units.id',  '=', 'payment_items.unit')
    //         ->join('batches', 'batches.id', '=', 'payment_items.year_id')
    //         ->where('payment_items.unit', $class_id)
    //         ->where('payment_items.year_id', $this->batch_id)
    //         ->select('payment_items.amount')->get()->toArray();
    //     return $tuition;
    // }

    // private function getStudentClass($student_id)
    // {
    //     $class_id = DB::table('student_classes')
    //         ->join('students', 'students.id',  '=', 'student_classes.student_id')
    //         ->join('school_units', 'school_units.id',  '=', 'student_classes.class_id')
    //         ->join('batches', 'batches.id', '=', 'student_classes.year_id')
    //         ->where('students.id', $student_id)
    //         ->where('batches.id', $this->batch_id)
    //         ->select('school_units.id')->first();
    //     return $class_id;
    // }
}
