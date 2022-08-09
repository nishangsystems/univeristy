<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use NumberFormatter;
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
        $base_classes = DB::table('school_units')
                ->whereNotNull('base_class')
                ->pluck('id')->toArray();
        // $_classes =  array_map(function($key) use ($classes){
        //         // dd($classes);
        //         return [(int)$key => $classes[(int)$key] ?? \App\Models\SchoolUnits::find((int)$key)->name];
        //     }, $sub_units);


        $data['data'] = array_map(function($key) use ($sub_units, $base_classes){
                return  [
                            'key'=>$key,
                            'target'=>in_array($key, $base_classes) ? 1 : 0,
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
            $base_classes = DB::table('school_units')
                ->whereNotNull('base_class')
                ->pluck('id')->toArray();

            $year = request('year') ?? \App\Helpers\Helpers::instance()->getCurrentAccademicYear();
            $minimized_classes = \App\Http\Controllers\Admin\StudentController::getMainClasses();
            $all_classes = \App\Http\Controllers\Admin\ProgramController::orderedUnitsTree();
            $classes = array_map(function($k) use ($minimized_classes, $all_classes){
                return in_array($k, array_keys($minimized_classes)) ? $minimized_classes[$k] : $all_classes[$k];
            }, array_keys(($all_classes)));
            # code...
            // dd($classes);
            $data = [];
            $data['title'] = "Fee Statistics";
            foreach ($classes as $key => $class) {
                # code...
                // get fee per unit
                $students = count(
                    DB::table('student_classes')
                    ->whereIn('class_id', \App\Http\Controllers\Admin\ProgramController::subunitsOf($key))
                    ->where('year_id', '=', $year)
                    ->get(['id']));
                $unit_fee = array_sum(DB::table('payment_items')
                    ->whereIn('unit', \App\Http\Controllers\Admin\ProgramController::subunitsOf($key))
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
                        ->whereNotNull('payments.amount')
                        ->get([
                            'payments.id as id',
                            'payments.student_id as student_id',
                            'payments.amount as amount'
                        ]);
                // get those that have completed and else
                $amounts = [];
                $counts = ['complete'=>0, 'incomplete'=>0];
                foreach ($students_fee as $keyi => $_fee) {
                    # code...
                    if(in_array($_fee->student_id, array_keys($amounts))){
                        $amounts[$_fee->student_id] += (float)$_fee->amount;
                    }else {
                        # code...
                        $amounts[$_fee->student_id] = (float)$_fee->amount;
                    }
                }
                foreach ($amounts as $keyj => $amount) {
                    # code...
                    if ((float)$amount == $unit_fee)
                        $counts['complete']++;
                }
                $fee = array_sum(
                    DB::table('payment_items')
                    ->where('name', '=', 'TUITION')
                    ->join('payments', 'payments.payment_id', '=', 'payment_items.id')
                    ->where('payments.unit_id', '=', $key)
                    ->where('payments.batch_id', '=', $year)
                    ->whereNotNull('payments.amount')
                    ->get(['payments.amount as amount'])
                    ->pluck('amount')
                    ->toArray()
                );

                
                $data['data'][] = [
                    'class_id'=>$key,
                    'class' => $class,
                    'target' => in_array($key, $base_classes) ? 1 : 0,
                    'expected' => number_format( $students * $unit_fee, 0, '.', ','),
                    'recieved' => number_format($fee, 0, '.', ','),
                    'students'=> $students ?? '-',
                    'fee' => $unit_fee ?? '-',
                    'complete' => $counts['complete'] ?? '-',
                    'incomplete' => $students-$counts['complete'] ?? '-',
                    '%complete' => $students == 0 ? 0 : number_format($counts['complete']*100/$students, 2) ?? '-',
                    '%incomplete' => $students == 0 ? 0 : number_format(($students - $counts['complete'])*100/$students, 2) ?? '-'
                ];
            }
            // dd($data);
            return view('admin.statistics.fees')->with($data);
        }
        catch(Throwable $th){
            
            dd($th);
        }
    }

    // fee statistics per class
    public function unitFees(Request $request)
    {
        # code...
        try {
            $data = [];
            $classes = \App\Http\Controllers\Admin\ProgramController::orderedUnitsTree();
            $year = request('year') ?? \App\Helpers\Helpers::instance()->getCurrentAccademicYear();
            $class_id = $request->class_id;
            $data['title'] = $classes[$class_id]." Fee Details";
            
            $class_students = DB::table('student_classes')
                            ->whereIn('class_id', \App\Http\Controllers\Admin\ProgramController::subunitsOf($class_id))
                            ->where('year_id', '=', $year)
                            ->join('students', 'students.id', '=', 'student_classes.student_id')
                            ->get(['students.id as id', 'students.name as name']);

            $unit_fee = array_sum(DB::table('payment_items')
                ->whereIn('unit', \App\Http\Controllers\Admin\ProgramController::subunitsOf($class_id))
                ->where('year_id', '=', $year)
                ->where('name', '=', 'TUITION')
                ->pluck('amount')
                ->toArray());
            // get students' fee
            $students_fee = DB::table('payment_items')
                    ->where('name', '=', 'TUITION')
                    ->join('payments', 'payments.payment_id', '=', 'payment_items.id')
                    ->where('payments.unit_id', '=', $class_id)
                    ->where('payments.batch_id', '=', $year)
                    ->get([
                        'payments.id as id',
                        'payments.student_id as student_id',
                        'payments.amount as amount'
                    ]);

            foreach ($class_students as $key => $student) {
                # code...
                $paid = array_sum(
                    DB::table('payment_items')
                    ->where('name', '=', 'TUITION')
                    ->join('payments', 'payments.payment_id', '=', 'payment_items.id')
                    ->where('payments.unit_id', '=', $class_id)
                    ->where('payments.batch_id', '=', $year)
                    ->where('payments.student_id', '=', $student->id)
                    ->get(['payments.amount as amount'])
                    ->pluck('amount')
                    ->toArray()
                    );

                $data['data'][] = [
                  'name' => $student->name,
                  'paid' => $paid,
                  'left' => $unit_fee - $paid
                ];
            }
            return view('admin.statistics.unit')->with($data);
        } catch (\Throwable $th) {
            //throw $th;
            return view('admin.statistics.unit')->with($data);
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
