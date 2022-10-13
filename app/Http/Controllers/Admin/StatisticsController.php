<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Level;
use App\Models\ProgramLevel;
use App\Models\SchoolUnits;
use Carbon\Carbon;
use DateTime;
use Doctrine\DBAL\Types\TimeType;
use FontLib\Table\Type\name;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Type\Time;
use Throwable;

class StatisticsController extends Controller
{
    //
    public function students(Request $request)
    {
        # code...
        // return $request->all();
        // $classes = \App\Http\Controllers\Admin\ProgramController::allUnitNames();
        $data['title'] = "Student Statistics";
        if ($request->has('filter_key')) {
            # code...
            switch ($request->filter_key) {
                case 'program':
                    # code...
                    $data['data'] = array_map(function($program_id) use ($request){
                        return [
                            'unit' => \App\Models\SchoolUnits::find($program_id)->name,
                            'total' => \App\Models\ProgramLevel::where('program_levels.program_id', '=', $program_id)
                                        ->join('student_classes', 'student_classes.class_id', '=', 'program_levels.id')
                                        ->where('student_classes.year_id', '=', $request->year)
                                        ->join('students', 'students.id', '=','student_classes.student_id')
                                        ->count(),
                            'males' => \App\Models\ProgramLevel::where('program_levels.program_id', '=', $program_id)
                                        ->join('student_classes', 'student_classes.class_id', '=', 'program_levels.id')
                                        ->where('student_classes.year_id', '=', $request->year)
                                        ->join('students', 'students.id', '=','student_classes.student_id')
                                        ->where('students.gender', '=', 'male')
                                        ->count(),
                            'females' => \App\Models\ProgramLevel::where('program_levels.program_id', '=', $program_id)
                                        ->join('student_classes', 'student_classes.class_id', '=', 'program_levels.id')
                                        ->where('student_classes.year_id', '=', $request->year)
                                        ->join('students', 'students.id', '=','student_classes.student_id')
                                        ->where('students.gender', '=', 'female')
                                        ->count(),
                        ];
                    }, \App\Models\SchoolUnits::where('unit_id', 4)->pluck('id')->toArray());
                    // dd($data);
                    break;
                    case 'level':
                        # code...
                        $data['data'] = array_map(function($level_id) use ($request){
                            return [
                                'unit' => 'LEVEL '.\App\Models\Level::find($level_id)->level,
                                'total' => \App\Models\ProgramLevel::where('program_levels.level_id', $level_id)
                                            ->join('students', ['students.program_id'=>'program_levels.id'])
                                            ->join('student_classes', ['student_classes.student_id'=>'students.id'])
                                            ->where('student_classes.year_id', $request->year)->count(),
                                'males' => \App\Models\ProgramLevel::where('program_levels.level_id', $level_id)
                                            ->join('students', ['students.program_id'=>'program_levels.id'])
                                            ->where('students.gender', 'male')
                                            ->join('student_classes', ['student_classes.student_id'=>'students.id'])
                                            ->where('student_classes.year_id', $request->year)->count(),
                                'females' => \App\Models\ProgramLevel::where('program_levels.level_id', $level_id)
                                            ->join('students', ['students.program_id'=>'program_levels.id'])
                                            ->where('students.gender', 'female')
                                            ->join('student_classes', ['student_classes.student_id'=>'students.id'])
                                            ->where('student_classes.year_id', $request->year)->count(),
                            ];
                        }, \App\Models\Level::pluck('id')->toArray());
                    break;
                case 'class':
                    # code...
                    $data['data'] = array_map(function($class_id) use ($request){
                        return [
                            'unit' => \App\Models\ProgramLevel::find($class_id)->program()->first()->name
                                    . ' : LEVEL '
                                    . \App\Models\ProgramLevel::find($class_id)->level()->first()->level,
                            'total' => \App\Models\ProgramLevel::find($class_id)->students()
                                        ->join('student_classes', ['student_classes.student_id'=>'students.id'])
                                        ->where('student_classes.year_id', '=', $request->year)->count(),
                            'males' => \App\Models\ProgramLevel::find($class_id)->students()
                                        ->where('students.gender', '=', 'male')
                                        ->join('student_classes', ['student_classes.student_id'=>'students.id'])
                                        ->where('student_classes.year_id', '=', $request->year)->count(),
                            'females' => \App\Models\ProgramLevel::find($class_id)->students()
                                        ->where('students.gender', '=', 'female')
                                        ->join('student_classes', ['student_classes.student_id'=>'students.id'])
                                        ->where('student_classes.year_id', '=', $request->year)->count(),
                            
                        ];
                    }, \App\Models\ProgramLevel::pluck('id')->toArray());
                    break;
                
                default:
                    # code...
                    break;
            }
            $data['data'] = collect($data['data'])->sortBy('unit');
            // return $data['data'];
        }
        return view('admin.statistics.students')->with($data);
    }
    

    // get fee statistics per program per academic year 
    function program_fee_stats($program_id, $year){
        $students = \App\Models\ProgramLevel::where('program_levels.program_id', '=', $program_id)
                                            ->join('student_classes', 'student_classes.class_id', '=', 'program_levels.id')
                                            ->where('student_classes.year_id', '=', $year)
                                            ->join('students', 'students.id', '=', 'student_classes.student_id')->pluck('students.id')->toArray();       
        $return = [
            'unit' => SchoolUnits::find($program_id)->name,
            'students' => count($students), 'complete'=> 0, 'incomplete'=>0, 
            'recieved' => 0, 'expected' => 0, 'per_completed'=>0, 
            'per_uncompleted' =>0, 'per_recieved'=>0];
        $fees = array_map(function($stud) use ($program_id, $year){
            return [
                'amount' => array_sum(
                    \App\Models\Payments::where('payments.student_id', '=', $stud)
                    ->join('payment_items', 'payment_items.id', '=', 'payments.payment_id')
                    ->where('payment_items.name', '=', 'TUTION')
                    ->where('payments.batch_id', '=', $year)
                    ->pluck('payments.amount')
                    ->toArray()
                ),
                'total' => 
                        \App\Models\CampusProgram::join('Program_levels', 'program_levels.id', '=', 'campus_programs.program_level_id')
                        ->join('payment_items', 'payment_items.campus_program_id', '=', 'campus_programs.id')
                        ->where('payment_items.name', '=', 'TUTION')
                        ->whereNotNull('payment_items.amount')
                        ->join('students', 'students.program_id', '=', 'program_levels.id')
                        ->where('students.id', '=', $stud)->pluck('payment_items.amount')[0] ?? 0
                    ,
                'stud' => $stud
            ];
        }, $students);
        // dd($fees);
        foreach ($fees as $key => $value) {
            # code...
            $return['recieved'] += $value['amount'];
            if ($value['amount'] == $value['total']) {$return['complete'] += 1;}
            else{$return['incomplete'] += 1;}
            
        }
        $return['expected'] = (int)\App\Models\CampusProgram::join('Program_levels', 'program_levels.id', '=', 'campus_programs.program_level_id')
                                ->join('payment_items', 'payment_items.campus_program_id', '=', 'campus_programs.id')
                                ->where('payment_items.name', '=', 'TUTION')
                                ->whereNotNull('payment_items.amount')
                                ->join('students', 'students.program_id', '=', 'program_levels.id')
                                ->whereIn('students.id', $students)->sum('payment_items.amount');
        $return['per_completed'] = $return['students'] == 0 ? 0 : $return['complete']*100/($return['students']);
        $return['per_uncompleted'] =$return['students'] == 0 ? 0 : 100 - ($return['complete']*100/($return['students']));
        $return['per_recieved'] = $return['expected'] == 0 ? 0 : ($return['recieved']*100/$return['expected']);
        return $return;
    }

    // get fee statistics per level per academic year
    public function level_fee_stats($level_id, $year)
    {
        # code...
        $students = \App\Models\ProgramLevel::where('program_levels.level_id', '=', $level_id)
                                            ->join('student_classes', 'student_classes.class_id', '=', 'program_levels.id')
                                            ->where('student_classes.year_id', '=', $year)
                                            ->join('students', 'students.id', '=', 'student_classes.student_id')->pluck('students.id')->toArray();       
        $return = [
            'unit' => 'LEVEL '.Level::find($level_id)->level,
            'students' => count($students), 'complete'=> 0, 'incomplete'=>0, 
            'recieved' => 0, 'expected' => 0, 'per_completed'=>0, 
            'per_uncompleted' =>0, 'per_recieved'=>0];
        $fees = array_map(function($stud) use ($level_id, $year){
            return [
                'amount' => array_sum(
                    \App\Models\Payments::where('payments.student_id', '=', $stud)
                    ->join('payment_items', 'payment_items.id', '=', 'payments.payment_id')
                    ->where('payment_items.name', '=', 'TUTION')
                    ->where('payments.batch_id', '=', $year)
                    ->pluck('payments.amount')
                    ->toArray()
                ),
                'total' => 
                        \App\Models\CampusProgram::join('Program_levels', 'program_levels.id', '=', 'campus_programs.program_level_id')
                        ->join('payment_items', 'payment_items.campus_program_id', '=', 'campus_programs.id')
                        ->where('payment_items.name', '=', 'TUTION')
                        ->whereNotNull('payment_items.amount')
                        ->join('students', 'students.program_id', '=', 'program_levels.id')
                        ->where('students.id', '=', $stud)->pluck('payment_items.amount')[0] ?? 0
                    ,
                'stud' => $stud
            ];
        }, $students);
        // dd($fees);
        foreach ($fees as $key => $value) {
            # code...
            $return['recieved'] += $value['amount'];
            if ($value['amount'] == $value['total']) {$return['complete'] += 1;}
            else{$return['incomplete'] += 1;}
            
        }
        $return['expected'] = (int)\App\Models\CampusProgram::join('Program_levels', 'program_levels.id', '=', 'campus_programs.program_level_id')
                                ->join('payment_items', 'payment_items.campus_program_id', '=', 'campus_programs.id')
                                ->where('payment_items.name', '=', 'TUTION')
                                ->whereNotNull('payment_items.amount')
                                ->join('students', 'students.program_id', '=', 'program_levels.id')
                                ->whereIn('students.id', $students)->sum('payment_items.amount');
        $return['per_completed'] = $return['students'] == 0 ? 0 : $return['complete']*100/($return['students']);
        $return['per_uncompleted'] =$return['students'] == 0 ? 0 : 100 - ($return['complete']*100/($return['students']));
        $return['per_recieved'] = $return['expected'] == 0 ? 0 : ($return['recieved']*100/$return['expected']);
        return $return;
    }

    // get fee statistics per class per academic year
    public function class_fee_stats($class_id, $year)
    {
        # code...
        $students = \App\Models\StudentClass::where('student_classes.class_id', '=', $class_id)
                                            ->where('student_classes.year_id', '=', $year)
                                            ->join('students', 'students.id', '=', 'student_classes.student_id')->pluck('students.id')->toArray();       
        $return = [
            'unit' => ProgramLevel::find($class_id)->program()->first()->name.': LEVEL '.ProgramLevel::find($class_id)->level()->first()->level,
            'students' => count($students), 'complete'=> 0, 'incomplete'=>0, 
            'recieved' => 0, 'expected' => 0, 'per_completed'=>0, 
            'per_uncompleted' =>0, 'per_recieved'=>0];
        $fees = array_map(function($stud) use ($class_id, $year){
            return [
                'amount' => array_sum(
                    \App\Models\Payments::where('payments.student_id', '=', $stud)
                    ->join('payment_items', 'payment_items.id', '=', 'payments.payment_id')
                    ->where('payment_items.name', '=', 'TUTION')
                    ->where('payments.batch_id', '=', $year)
                    ->pluck('payments.amount')
                    ->toArray()
                ),
                'total' => 
                        \App\Models\CampusProgram::join('Program_levels', 'program_levels.id', '=', 'campus_programs.program_level_id')
                        ->join('payment_items', 'payment_items.campus_program_id', '=', 'campus_programs.id')
                        ->where('payment_items.name', '=', 'TUTION')
                        ->whereNotNull('payment_items.amount')
                        ->join('students', 'students.program_id', '=', 'program_levels.id')
                        ->where('students.id', '=', $stud)->pluck('payment_items.amount')[0] ?? 0
                    ,
                'stud' => $stud
            ];
        }, $students);
        // dd($fees);
        foreach ($fees as $key => $value) {
            # code...
            $return['recieved'] += $value['amount'];
            if ($value['amount'] == $value['total']) {$return['complete'] += 1;}
            else{$return['incomplete'] += 1;}
            
        }
        $return['expected'] = (int)\App\Models\CampusProgram::join('Program_levels', 'program_levels.id', '=', 'campus_programs.program_level_id')
                                ->join('payment_items', 'payment_items.campus_program_id', '=', 'campus_programs.id')
                                ->where('payment_items.name', '=', 'TUTION')
                                ->whereNotNull('payment_items.amount')
                                ->join('students', 'students.program_id', '=', 'program_levels.id')
                                ->whereIn('students.id', $students)->sum('payment_items.amount');
        $return['per_completed'] = $return['students'] == 0 ? 0 : $return['complete']*100/($return['students']);
        $return['per_uncompleted'] =$return['students'] == 0 ? 0 : 100 - ($return['complete']*100/($return['students']));
        $return['per_recieved'] = $return['expected'] == 0 ? 0 : ($return['recieved']*100/$return['expected']);
        return $return;
    }
    // for each class, get the total number of students, fee per person, number of completed, number of owing, %age paid, %age owing, details
    public function fees(Request $request)
    {
        try{
            $data['title'] = 'Fee Statistics';
            if($request->has('filter_key')){
                // return $request->all();
                switch ($request->filter_key) {
                    case 'program':
                        # code...
                        $data['data'] = array_map(function($program_id) use ($request){
                            return $this->program_fee_stats($program_id, $request->year);
                        }, \App\Models\SchoolUnits::where('unit_id', 4)->pluck('id')->toArray());
                        // dd($data);
                        break;
                    case 'level':
                        # code...
                        $data['data'] = array_map(function($level_id) use ($request){
                            return $this->level_fee_stats($level_id, $request->year);
                        }, \App\Models\Level::pluck('id')->toArray());
                    break;
                    case 'class':
                        # code...
                        $data['data'] = array_map(function($class_id) use ($request){
                            return $this->class_fee_stats($class_id, $request->year);
                        }, \App\Models\ProgramLevel::pluck('id')->toArray());
                        break;
                    
                    default:
                        # code...
                        break;
                }
                $data['data'] = collect($data['data'])->sortBy('unit');
            }
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
        $data['title'] = "Results Statistics";
        return view('admin.statistics.results', $data);
    }
    //
    public function income(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'filter'=>'string',
            '$value'=>'month',
            'start_date'=>'date',
            'end_date'=>'date'
        ]);
        $data['title'] = "Income Statistics";
        try {
            $expenditureItems = null;
            if ($request->filter == null) {
                # code...
                return view('admin.statistics.income')->with($data);
            }
            if($validator->fails())
                {return back()->with('error', json_encode($validator->getMessageBag()->getMessages()));}
                $data['filter'] = $request->filter == 'year' 
                        ? 'year ' . $request->value 
                        : ($request->filter == 'month' 
                            ? DateTime::createFromFormat('!m', (int)date('m', strtotime($request->value)))->format('F') . ' ' . date('Y', strtotime($request->value)) 
                            : 'period ' . $request->start_date . ' to '. $request->end_date
                            ) ;
            switch ($request->filter) {
                case 'month': #having $value
                    # code...
                    $expenditureItems = DB::table('pay_incomes')
                        ->whereYear('pay_incomes.created_at', '=', date('Y', strtotime($request->value)))
                        ->whereMonth('pay_incomes.created_at', '=', date('m', strtotime($request->value)))
                        ->join('incomes', 'incomes.id', '=', 'pay_incomes.income_id')
                        ->get();
                        $names = array_unique($expenditureItems->pluck('name')->toArray());
                    $data['data'] = array_map(function($val) use ($expenditureItems){
                        return [
                            'name'=>$val,
                            'count'=>count($expenditureItems->where('name', '=', $val)->toArray()),
                            'cost'=>array_sum($expenditureItems->where('name', '=', $val)->pluck('amount')->toArray())
                        ];
                    }, $names);
                    $data['totals'] = [
                        'name'=>"Total",
                        'count'=>count($expenditureItems->toArray()),
                        'cost'=>array_sum($expenditureItems->pluck('amount')->toArray())
                    ];
                    return view('admin.statistics.income')->with($data);
                    break;
                case 'year':
                    # code...
                    $expenditureItems = DB::table('pay_incomes')
                        ->whereYear('pay_incomes.created_at', '=', date('Y',strtotime($request->value)))
                        ->join('incomes', 'incomes.id', '=', 'pay_incomes.income_id')
                        ->get();
                    $names = array_unique($expenditureItems->pluck('name')->toArray());
                    $data['data'] = array_map(function($val) use ($expenditureItems){
                        return [
                            'name'=>$val,
                            'count'=>count($expenditureItems->where('name', '=', $val)->toArray()),
                            'cost'=>array_sum($expenditureItems->where('name', '=', $val)->pluck('amount')->toArray())
                        ];
                    }, $names);
                    $data['totals'] = [
                        'name'=>"Total",
                        'count'=>count($expenditureItems->toArray()),
                        'cost'=>array_sum($expenditureItems->pluck('amount')->toArray())
                    ];
                    return view('admin.statistics.income')->with($data);
                    break;
                case 'range':
                    # has $from&$to or $start_date & $end_date
                    $expenditureItems = DB::table('pay_incomes')
                    ->whereDate('pay_incomes.created_at', '>=', date('Y-m-d', strtotime($request->start_date)))
                    ->whereDate('pay_incomes.created_at', '<=', date('Y-m-d', strtotime($request->end_date)))
                    ->join('incomes', 'incomes.id', '=', 'pay_incomes.income_id')
                    ->get();
                    $names = array_unique($expenditureItems->pluck('name')->toArray());
                    $data['data'] = array_map(function($val) use ($expenditureItems){
                        return [
                            'name'=>$val,
                            'count'=>count($expenditureItems->where('name', '=', $val)->toArray()),
                            'cost'=>array_sum($expenditureItems->where('name', '=', $val)->pluck('amount')->toArray())
                        ];
                    }, $names);
                    $data['totals'] = [
                        'name'=>"Total",
                        'count'=>count($expenditureItems->toArray()),
                        'cost'=>array_sum($expenditureItems->pluck('amount')->toArray())
                    ];
                    return view('admin.statistics.income')->with($data);
                    break;
                
                default:
                # code...
                    
                    // return view('admin.statistics.expenditure')->with($data);
                    break;
            }
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('error', $th->getMessage());
        }
        
    }
    //
    public function expenditure(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'filter'=>'string',
            '$value'=>'month',
            'start_date'=>'date',
            'end_date'=>'date'
        ]);
        $data['title'] = "Expenditure Statistics";
        try {
            if ($request->filter == null) {
                # code...
                return view('admin.statistics.expenditure')->with($data);
            }
            $expenditureItems = null;
            if($validator->fails())
            {return back()->with('error', json_encode($validator->getMessageBag()->getMessages()));}
            $data['filter'] = $request->filter == 'year' 
                        ? 'year ' . $request->value 
                        : ($request->filter == 'month' 
                            ? DateTime::createFromFormat('!m', (int)date('m', strtotime($request->value)))->format('F') . ' ' . date('Y', strtotime($request->value)) 
                            : 'period ' . $request->start_date . ' to '. $request->end_date
                            ) ;
            switch ($request->filter) {
                case 'month': #having $value
                    # code...
                    $expenditureItems = DB::table('expenses')
                        ->whereYear('date', '=', date('Y', strtotime($request->value)))
                        ->whereMonth('date', '=', date('m', strtotime($request->value)))
                        ->get();
                        $names = array_unique($expenditureItems->pluck('name')->toArray());
                    $data['data'] = array_map(function($val) use ($expenditureItems){
                        return [
                            'name'=>$val,
                            'count'=>count($expenditureItems->where('name', '=', $val)->toArray()),
                            'cost'=>array_sum($expenditureItems->where('name', '=', $val)->pluck('amount_spend')->toArray())
                        ];
                    }, $names);
                    $data['totals'] = [
                        'name'=>"Total",
                        'count'=>count($expenditureItems->toArray()),
                        'cost'=>array_sum($expenditureItems->pluck('amount_spend')->toArray())
                    ];
                    return view('admin.statistics.expenditure')->with($data);
                    break;
                case 'year':
                    # code...
                    $expenditureItems = DB::table('expenses')
                        ->whereYear('date', '=', date('Y',strtotime($request->value)))
                        ->get();
                    $names = array_unique($expenditureItems->pluck('name')->toArray());
                    $data['data'] = array_map(function($val) use ($expenditureItems){
                        return [
                            'name'=>$val,
                            'count'=>count($expenditureItems->where('name', '=', $val)->toArray()),
                            'cost'=>array_sum($expenditureItems->where('name', '=', $val)->pluck('amount_spend')->toArray())
                        ];
                    }, $names);
                    $data['totals'] = [
                        'name'=>"Total",
                        'count'=>count($expenditureItems->toArray()),
                        'cost'=>array_sum($expenditureItems->pluck('amount_spend')->toArray())
                    ];
                    return view('admin.statistics.expenditure')->with($data);
                    break;
                case 'range':
                    # has $from&$to or $start_date & $end_date
                    $expenditureItems = DB::table('expenses')
                    ->whereDate('date', '>=', date('Y-m-d', strtotime($request->start_date)))
                    ->whereDate('date', '<=', date('Y-m-d', strtotime($request->end_date)))
                    ->get();
                    $names = array_unique($expenditureItems->pluck('name')->toArray());
                    $data['data'] = array_map(function($val) use ($expenditureItems){
                        return [
                            'name'=>$val,
                            'count'=>count($expenditureItems->where('name', '=', $val)->toArray()),
                            'cost'=>array_sum($expenditureItems->where('name', '=', $val)->pluck('amount_spend')->toArray())
                        ];
                    }, $names);
                    $data['totals'] = [
                        'name'=>"Total",
                        'count'=>count($expenditureItems->toArray()),
                        'cost'=>array_sum($expenditureItems->pluck('amount_spend')->toArray())
                    ];
                    return view('admin.statistics.expenditure')->with($data);
                    break;
                
                default:
                # code...
                    
                    // return view('admin.statistics.expenditure')->with($data);
                    break;
            }
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('error', $th->getMessage());
        }
        
    }
}
