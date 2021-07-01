<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BoardingFee;
use Illuminate\Http\Request;

class BoardingFeeController extends Controller
{

    /**
     * show boarding fee
     * 
     */

    public function index()
    {
        $data['title'] = 'Boarding Fee';
        $data['boarding_fees'] = BoardingFee::paginate(5);
        return view('admin.boarding.index')->with($data);
    }

    /**
     * show form to set baording fee
     */
    public function create()
    {
        $data['title'] = 'Set Boarding Fee';
        return view('admin.boarding.create')->with($data);
    }

    /***
     * store boarding fee
     */
    public function store(Request $request)
    {
        $validate_data = $request->validate([
            'amount_old_student' => 'required|numeric',
            'amount_new_student' => 'required|numeric'
        ]);
        $created = BoardingFee::create($validate_data);
        return redirect()->route('admin.boarding_fee.index')->with('success', 'Set Boarding Fee succcessfully');
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
}
