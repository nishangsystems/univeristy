<?php


namespace App\Http\Controllers\API;


use App\Http\Controllers\Controller;
use App\Http\Resources\StudentResource3;
use App\Models\Batch;
use App\Models\FAQ;
use App\Models\Guardian;
use App\Models\Students;
use Illuminate\Http\Request;
use App\Models\User;
use DateInterval;
use DateTime;
use DatePeriod;
use Carbon\Carbon;
use App\Models\StudentAttendance;

class PageController extends Controller
{
    public function faqs(Request $request)
    {
        return response([
            'status' => 200,
            'faqs' => FAQ::all()
        ]);
    }

    public function year(Request $request)
    {
        return response([
            'status' => 200,
            'years' => Batch::all()
        ]);
    }


    public function semester(Request $request)
    {
        $current_year = \App\Helpers\Helpers::instance()->getYear();
        if($request->student_id){
            $student = Students::find($request->student_id);
        }else{
            $student = Auth('student_api')->user();
        }

        return response([
            'status' => 200,
            'semesters' => \App\Models\ProgramLevel::find($student->_class($current_year)->id)->program()->first()->background()->first()->semesters()->get()
        ]);
    }

    public function students(Request $request)
    {
        $user = Auth('parent_api')->user();
        if(!isset($user) && $request->get('parent_id')){
            $user = Guardian::find($request->parent_id);
        }

        if(!isset($user)){
            return response([
                'status' => 200,
                'students' => []
            ]);
        }else{
            $students = Students::where('parent_phone_number', $user->phone)->get();

            return response([
                'status' => 200,
                'students' => StudentResource3::collection($students)
            ]);
        }
    }

    public function studentAttendance(Request $request){
        $array = array();

        // Variable that store the date interval
        // of period 1 day
        $interval = new DateInterval('P1D');

        if($request->student_id){
            $student = Students::find($request->student_id);
        }else{
            $student = Auth('student_api')->user();
        }

        $realEnd = new DateTime($request->get('end', Carbon::now()));
        $realEnd->add($interval);

        $period = new DatePeriod(new DateTime($request->get('start', Carbon::now()->startOfMonth())), $interval, $realEnd);

        // Use loop to store date into array
        foreach($period as $k=>$date) {
            array_push($array,['date'=>$date->format("Y-m-d"), 'present'=>StudentAttendance::where(["student_id"=>$student->id])->whereDate('created_at', $date)->count() > 0]);
        }
        return response([
            'status' => 200,
            'attendance' => $array
        ]);
    }

}