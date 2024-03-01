<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ClearanceController extends Controller
{
    //

    public function fee_clearance()
    {
        # code...
        $data['title'] = "Print Fees Clearance";
        return view('admin.clearance.fee', $data);
    }
}
