<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Students;
use App\Services\ClearanceService;
use Illuminate\Http\Request;

class ClearanceController extends Controller
{
    //
    protected $clearanceService;
    public function __construct(ClearanceService $clearanceService)
    {
        # code...
        $this->clearanceService = $clearanceService;
    }

    public function fee_clearance()
    {
        # code...
        $data['title'] = "Print Fees Clearance";
        return view('admin.fee.clearance.index', $data);
    }

    public function generate_fee_clearance (Request $request, $student_id)
    {
        # code...
        $student = Students::find($student_id);
        $clearance = $this->clearanceService->feeClearance($student_id);
        $data['title'] = "Fee Clearance For ".$student->name??'';
        $data['data'] = $clearance;
        // dd($clearance);
        if (isset($clearance['err_msg'])) {
            # code...
            session()->flash('error', $clearance['err_msg']);
        }
        return view('admin.fee.clearance.fee', $data);
    }

    public function save_fee_clearance (Request $request, $student_id)
    {
        # code...
        try {
            //code...
            $record = $this->clearanceService->saveClearance($student_id);
            return response()->json(['message'=>'Successfully recorded']);
        } catch (\Throwable $th) {
            //throw $th;
            return response("Error saving record to database ".$th->getMessage(), 500);
        }

    }

    public function check_fee_clearance (Request $request, $student_id)
    {

        # code...
        try {
            //code...
            $record = $this->clearanceService->lastClearance($student_id);
            return response()->json(['data'=>$record]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message'=>$th->getMessage()], 500);
        }

    }
}
