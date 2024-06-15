<?php

namespace App\Http\Controllers\api;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\FAQ;
use App\Models\Level;
use App\Models\School;
use App\Models\Semester;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    //
    public function faqs(Request $request){
        return response()->json(['data'=>FAQ::orderBy('question')->get()]);
    }
    //
    public function batches(Request $request){
        return response()->json(['data'=>Batch::all()]);
    }
    //
    public function semesters(Request $request){
        return response()->json(['data'=>Semester::orderBy('name')->get()]);
    }

    //
    public function levels(Request $request){
        return response()->json(['data'=>Level::all()]);
    }

        
    public function school(Request $request) 
    {
        $school = School::first();
        return response()->json(['data'=> $school]);
    }
        
        
    public function current_accademic_year(Request $request) 
    {
        return response()->json(['data'=> Batch::find(Helpers::instance()->getCurrentAccademicYear())]);
    }


}
