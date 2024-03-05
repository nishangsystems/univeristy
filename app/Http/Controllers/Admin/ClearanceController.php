<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
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
        $clearance = $this->clearanceService->feeClearance($student_id);
        $data['title'] = "Fee Clearance For {$clearance['student']->name}";
        $data['data'] = $clearance;
        // dd($clearance);
        if (isset($clearance['err_msg'])) {
            # code...
            session()->flash('error', $clearance['err_msg']);
        }
        return view('admin.fee.clearance.fee', $data);
    }
}
