<?php


namespace App\Http\Controllers\API\Student;


use App\Http\Controllers\Controller;
use App\Http\Resources\StudentResource3;
use App\Models\Students;
use Illuminate\Http\Request;

class FeeController extends Controller
{
    public function index(Request $request)
    {
        $current_year = \App\Helpers\Helpers::instance()->getYear();
        if($request->student_id){
            $student = Students::find($request->student_id);
        }else{
            $student = Auth('student_api')->user();
        }

        return view('api.fee', compact('student'));
    }
}