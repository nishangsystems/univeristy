<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BoardingFee;
use App\Models\BoardingFeeInstallment;
use App\Models\SchoolUnits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BoardingFeeController extends Controller
{

    /**
     * show boarding fee
     *
     */

    public function index()
    {
        $data['title'] = 'Boarding Fees';
        $data['boarding_fees'] = BoardingFee::paginate(7);
        return view('admin.boarding.index')->with($data);
    }

    /**
     * show form to set baording fee
     */
    public function create()
    {
        $data['title'] = 'Set Boarding Fee';
        $data['main_sections'] = SchoolUnits::where('parent_id', 0)->get();
        return view('admin.boarding.create')->with($data);
    }

    /***
     * store boarding fee
     */
    public function store(Request $request)
    {
        $validate_data = $request->validate([
            'amount_old_student' => 'required|numeric',
            'amount_new_student' => 'required|numeric',
            'boarding_type' => 'required'
        ]);
        $parent_id = SchoolUnits::where('id', $request->boarding_type)->first()->parent_id;
        $data['name'] = SchoolUnits::where('id', $parent_id)->first()->name;
        $created = BoardingFee::create($validate_data);
        $data['boarding_fees'] = BoardingFee::paginate(7);
        return view('admin.boarding.index')->with($data);
    }

    /**
     * show form to update boarding fee
     * @param int $id
     */
    public function edit($id)
    {
        $data['title'] = 'Update Boarding Fee';
        $data['boarding_fee'] = BoardingFee::findOrFail($id);
        return view('admin.boarding.edit')->with($data);
    }

    /**
     * update baording fee
     * @param int $id
     * @param Request
     */
    public function update(Request $request, $id)
    {
        $validate_data = $request->validate([
            'amount_old_student' => 'required|numeric',
            'amount_new_student' => 'required|numeric'
        ]);
        $updated = BoardingFee::findOrFail($id)->update($request->all());
        return redirect()->route('admin.boarding_fee.index')->with('success', 'Updated Boarding Fee Successfully');
    }

    /**
     * delete boarding fee
     *
     * @param int $id
     */
    public function destroy($id)
    {
        $deleted = BoardingFee::findOrFail($id)->delete();
        return back()->with('success', 'Delete Boarding Fee Successfully');
    }

    public function createInstallments($id){

        $data['title'] = 'Boarding Fee Payment Installments';
        $data['id'] = $id;
        $data['boarding_fee_installments']= DB::table('boarding_fee_installments')
                                        ->join('boarding_fees', 'boarding_fees.id', '=', 'boarding_fee_installments.boarding_fee_id')
                                        ->select('boarding_fee_installments.id', 'boarding_fee_installments.installment_name', 'boarding_fee_installments.installment_amount', 'boarding_fee_installments.boarding_fee_id')
                                        ->where('boarding_fee_installments.boarding_fee_id', $id)->get();
        return view('admin.boarding.installments')->with($data);
    }

    public function addInstallments(Request $request, $id)
    {

        if($request->installment_amount > $this->verifyPaymentInstallments($id)){
            return back()->with('error', 'Installment Amount can not be  more than the Total Dormitory Fee');
        }
        $this->validateBoardingPaymentInstallment($request);
        DB::insert('insert into boarding_fee_installments (installment_name, installment_amount, boarding_fee_id) values (?, ?,?)', [$request->installment_name, $request->installment_amount, $id]);
        return redirect()->route('admin.boarding_fee.installments', $id)->with('success', 'Add Payment Installment succcessfully');
    }

    private function verifyPaymentInstallments($id)
    {
        return BoardingFee::where('id', $id)->first()->amount_new_student;
    }

    public function editBoardingPaymentInstallment($id, $installment_id)
    {
        $data['title'] = 'Edit Boarding Fee Payment Installment';
        $data['id'] = $installment_id;
        $data['installment'] = BoardingFeeInstallment::findOrFail($installment_id);

        return view('admin.boarding.editInstallment')->with($data);
    }

    public function updateBoardingPaymentInstallment(Request $request, $id, $installment_id)
    {
        if($request->installment_amount > $this->verifyPaymentInstallments($id)){
            return back()->with('error', 'Installment Amount can not be more than the Total Dormitory Fee');
        }
       $this->validateBoardingPaymentInstallment($request);
        BoardingFeeInstallment::findOrFail($installment_id)->update($request->all());
        return redirect()->route('admin.boarding_fee.installments', $id)->with('success', 'Updated Payment Installment succcessfully');
    }

    public function deleteBoardingPaymentInstallment($id,$installment_id)
    {
        BoardingFeeInstallment::findOrFail($installment_id)->delete();
        return redirect()->route('admin.boarding_fee.installments', $id)->with('success', 'Deleted Payment Installment succcessfully');
    }

    private function validateBoardingPaymentInstallment(Request $request){
        return $request->validate([
            'installment_name' => 'required',
            'installment_amount' => 'required',
        ]);
    }
}
