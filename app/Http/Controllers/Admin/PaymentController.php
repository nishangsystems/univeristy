<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Models\PaymentItem;
use App\Models\Payments;
use App\Models\SchoolUnits;
use App\Models\Students;
use Illuminate\Http\Request;
use Session;
use Redirect;
use DB;
use Auth;

class PaymentController extends Controller{

    public function index(Request $request, $student_id){
        $student = Students::find($student_id);
        $data['title'] = "Fee collections for ".$student->name;
        $data['student'] = $student;
        return view('admin.fee.payments.index')->with($data);
    }

    public function create(Request $request, $student_id){
        $student = Students::find($student_id);
        $data['student'] = $student;
        $data['title'] = "Collect Fee for ".$student->name;
        return view('admin.fee.payments.create')->with($data);
    }

    public function edit(Request $request, $student_id, $id){
        $student = Students::find($student_id);
        $data['student'] = $student;
        $data['payment'] = Payments::find($id);
        $data['title'] = "Collect Fee for ".$student->name;
        return view('admin.fee.payments.edit')->with($data);
    }

    public function store(Request $request, $student_id){
        $student = Students::find($student_id);

        $this->validate($request, [
            'item' =>  'required',
            'amount' => 'required',
        ]);

        Payments::create([
            "payment_id" => $request->item,
            "student_id" => $student->id,
            "batch_id" => Helpers::instance()->getYear(),
            "amount" => $request->amount
        ]);

        return redirect()->to(route('admin.fee.student.payments.index', $student_id))->with('success', "Fee collection recorded successfully !");
    }

    public function update(Request $request, $student_id, $id){
        $student = Students::find($student_id);

        $this->validate($request, [
            'item' =>  'required',
            'amount' => 'required',
        ]);
        $p =  Payments::find($id);
        $p->update([
            "payment_id" => $request->item,
            "amount" => $request->amount
        ]);

        return redirect()->to(route('admin.fee.student.payments.index', $student_id))->with('success', "Fee collection record updated successfully !");
    }

    public function destroy(Request $request, $student_id, $id){
        $p =  Payments::find($id);
        $p->delete();
        return redirect()->to(route('admin.fee.student.payments.index', $student_id))->with('success', "Fee collection record deleted successfully !");
    }

}
