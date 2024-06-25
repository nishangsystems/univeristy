<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProgramControllerV2 extends Controller
{
    //
    public function edit_programs(Request $request){
        $data['title'] = "Edit Programs";
        $data['programs'] = \App\Models\SchoolUnits::where('unit_id', 4)->orderBy('name')->get();
        return view('admin.edit_programs.index', $data);
    }
}
