<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class StatisticsController extends Controller
{
    //
    public function students(Request $request)
    {
        # code...
        // $classes = \App\Http\Controllers\Admin\ProgramController::allUnitNames();
        $data['title'] = "Student Statistics";
        $sub_units = \App\Http\Controllers\Admin\ProgramController::orderedUnitsTree();

        // $_classes =  array_map(function($key) use ($classes){
        //         // dd($classes);
        //         return [(int)$key => $classes[(int)$key] ?? \App\Models\SchoolUnits::find((int)$key)->name];
        //     }, $sub_units);


        $data['data'] = array_map(function($key) use ($sub_units){
                return  [
                            'key'=>$key,
                            'class'=>$sub_units[$key], 
                            'males'=>$this->getMales($key, request('year') ?? \App\Helpers\Helpers::instance()->getCurrentAccademicYear()), 
                            'females'=>$this->getFemales($key, request('year') ?? \App\Helpers\Helpers::instance()->getCurrentAccademicYear()),
                            'day'=>$this->getDayStudents($key, request('year') ?? \App\Helpers\Helpers::instance()->getCurrentAccademicYear()),
                            'boarding'=>$this->getBoardingStudents($key, request('year') ?? \App\Helpers\Helpers::instance()->getCurrentAccademicYear())
                        ];
            }, array_keys($sub_units));
        // return $data['data'];
        return view('admin.statistics.students')->with($data);
    }
    //

    public function getMales($unit_id, $year_id)
    {
        # code...
        return count(DB::table('student_classes')
            ->whereIn('class_id', \App\Http\Controllers\Admin\ProgramController::subunitsOf($unit_id))
            ->where('year_id', '=', $year_id)
            ->join('students', 'students.id', '=', 'student_classes.student_id')
            ->where('students.gender','=', 'male')
            ->pluck('students.id')
            ->toArray());
    }

    public function getFemales($unit_id, $year_id)
    {
        return count(DB::table('student_classes')
            ->whereIn('class_id', \App\Http\Controllers\Admin\ProgramController::subunitsOf($unit_id))
            ->where('year_id', '=', $year_id)
            ->join('students', 'students.id', '=', 'student_classes.student_id')
            ->where('students.gender','=', 'female')
            ->pluck('students.id')
            ->toArray());

    }

    public function getDayStudents($unit_id, $year_id)
    {
        # code...
        return count(DB::table('student_classes')
            ->whereIn('class_id', \App\Http\Controllers\Admin\ProgramController::subunitsOf($unit_id))
            ->where('year_id', '=', $year_id)
            ->join('students', 'students.id', '=', 'student_classes.student_id')
            ->where('students.type','=', 'day')
            ->pluck('students.id')
            ->toArray());
    }
    
    public function getBoardingStudents($unit_id, $year_id)
    {
        return count(DB::table('student_classes')
            ->whereIn('class_id', \App\Http\Controllers\Admin\ProgramController::subunitsOf($unit_id))
            ->where('year_id', '=', $year_id)
            ->join('students', 'students.id', '=', 'student_classes.student_id')
            ->where('students.type','=', 'boarding')
            ->pluck('students.id')
            ->toArray());
    }
    // for each class, get the total number of students, fee per person, number of completed, number of owing, %age paid, %age owing, details

    public function fees(Request $request)
    {
        try{
            $year = request('year') ?? \App\Helpers\Helpers::instance()->getCurrentAccademicYear();
            $classes = \App\Http\Controllers\Admin\ProgramController::orderedUnitsTree();
            # code...
            $data = [];
            $data['title'] = "Fee Statistics";
            foreach ($classes as $key => $class) {
                # code...
                // get fee per unit
                $students = count(
                    DB::table('student_classes')
                    ->where('class_id', '=', $key)
                    ->where('year_id', '=', $year)
                    ->get(['id']));
                $unit_fee = array_sum(DB::table('payment_items')
                    ->where('unit', '=', $key)
                    ->where('year_id', '=', $year)
                    ->where('name', '=', 'TUITION')
                    ->pluck('amount')
                    ->toArray());
                // get students' fee
                $students_fee = DB::table('payment_items')
                        ->where('name', '=', 'TUITION')
                        ->join('payments', 'payments.payment_id', '=', 'payment_items.id')
                        ->where('payments.unit_id', '=', $key)
                        ->where('payments.batch_id', '=', $year)
                        ->get([
                            'payments.id as id',
                            'payments.student_id as student_id',
                            'payments.amount as amount'
                        ]);
                // get those that have completed and else
                $amounts = [];
                $counts = ['complete'=>0, 'incomplete'=>0];
                foreach ($students_fee as $key => $_fee) {
                    # code...
                    if(in_array($_fee->student_id, array_keys($amounts))){
                        $amounts[$_fee->student_id] += (float)$_fee->amount;
                    }else {
                        # code...
                        $amounts[$_fee->student_id] = (float)$_fee->amount;
                    }
                }
                foreach ($amounts as $key => $amount) {
                    # code...
                    if ((float)$amount < $unit_fee)
                        $counts['incomplete']++;
                    else $counts['complete']++;
                }



                $data['data'][] = [
                    'class_id'=>$key,
                    'class' => $class,
                    'students'=> $students ?? '-',
                    'fee' => $unit_fee ?? '-',
                    'complete' => $counts['complete'] ?? '-',
                    'incomplete' => $counts['incomplete'] ?? '-',
                    '%complete' => $students == 0 ? 0 : $counts['complete']*100/$students ?? '-',
                    '%incomplete' => $students == 0 ? 0 : $counts['incomplete']*100/$students ?? '-'
                ];
            }
            // dd($data);
            return view('admin.statistics.fees')->with($data);
        }
        catch(Throwable $th){
            
            return view('admin.statistics.fees');
        }
    }
    //
    public function results(Request $request)
    {
        # code...
        return view('admin.statistics.results');
    }
    //
    public function income(Request $request)
    {
        # code...
        return view('admin.statistics.income');
    }
    //
    public function expenditure(Request $request)
    {
        # code...
        return view('admin.statistics.expenditure');
    }
}
