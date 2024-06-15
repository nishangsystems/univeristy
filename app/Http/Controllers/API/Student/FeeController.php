<?php


namespace App\Http\Controllers\API\Student;


use App\Http\Controllers\Controller;
use App\Http\Resources\StudentResource3;
use App\Models\Batch;
use App\Models\Students;
use Illuminate\Http\Request;

class FeeController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->year ?? \App\Helpers\Helpers::instance()->getYear();
        $student = Auth('student_api')->user();
        
        $data['student'] = $student;
        $data['total_paid'] = number_format($student->total_paid( $year));
        $data['total_debt'] = number_format($student->bal($student->id, $year));
        $data['payments'] = $student->payments()->where(['batch_id'=>($year)])->get()
        ->each(function($_item){
            $_item->item??null;
        });
        if($data['payments']->count() == 0){
            return response()->json(['message'=>'No Fee payments found for '.(Batch::find($year)->name??''), 'error_type'=>'general-error'], 400);
        }
        return response()->json(['data'=>$data]);
    }
}