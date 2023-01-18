<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Models\PaymentItem;
use App\Models\SchoolUnits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Session;
use Redirect;
use DB;
use Auth;

class ResultsAndTranscriptsController extends Controller{

    public function frequency_distribution()
    {
        # code...
        $data['title'] = "Frequency Distribution";
        return view('admin.res_and_trans.fre_dis', $data);
    }

    public function spread_sheet(Request $request)
    {
        # code...
        $data['title'] = "Spread Sheet";
        return view('admin.res_and_trans.spr_sheet', $data);
    }

}
