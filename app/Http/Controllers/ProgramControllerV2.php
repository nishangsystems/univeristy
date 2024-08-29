<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProgramControllerV2 extends Controller
{
    //
    public function edit_programs(Request $request){
        $data['title'] = "Edit Programs";
        $data['programs'] = \App\Models\SchoolUnits::where('unit_id', 4)->orderBy('name')->get()
            ->each(function($rec){
                $fparent = $rec->parent;
                if($fparent != null && $fparent->unit_id == 1){
                    $rec->school = $fparent->name;
                }
                $sparent = $fparent->parent??null;
                if($sparent != null && $sparent->unit_id == 1){
                    $rec->school = $sparent->name;
                }
                $tparent = $sparent->parent??null;
                if($tparent != null && $tparent->unit_id == 1){
                    $rec->school = $tparent->name;
                }
                
            });
        return view('admin.edit_programs.index', $data);
    }
}
