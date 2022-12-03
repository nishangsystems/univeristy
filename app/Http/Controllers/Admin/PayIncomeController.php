<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Resources\PayCashIncomeResource;
use App\Http\Resources\PayIncomeResource;
use App\Models\Batch;
use App\Models\Income;
use App\Models\PayIncome;
use App\Models\SchoolUnits;
use App\Models\Students;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class PayIncomeController extends Controller
{

    private $select = [
        'students.id',
        'students.campus_id',
        'student_classes.class_id',
        'pay_incomes.id as pay_income_id',
        'students.name as student_name',
        'incomes.name as income_name',
        'incomes.amount',
    ];



    /**
     * list all paid incomes
     */
    public function index()
    {
        $batch_id = Batch::find(\App\Helpers\Helpers::instance()->getCurrentAccademicYear())->id;
        $data['pay_incomes'] = DB::table('pay_incomes')
            ->join('incomes', 'incomes.id', '=', 'pay_incomes.income_id')
            ->join('students', 'students.id', '=', 'pay_incomes.student_id')
            ->join('student_classes', 'student_classes.student_id', '=', 'students.id')
            ->where('student_classes.year_id', '=', $batch_id)
            ->where(function($query){
                auth()->user()->campus_id != null ? $query->where('students.campus_id', '=', auth()->user()->campus_id) : null;
            })
            // ->join('school_units', 'school_units.id', '=', 'pay_incomes.class_id')
            ->where('pay_incomes.batch_id', $batch_id)
            ->select($this->select)->distinct()
            ->paginate(5);
        $data['title'] = 'Pay Incomes';
        $data['years'] = Batch::all();
        $data['school_units'] = SchoolUnits::where('parent_id', '=', 0)->get();
        //  dd($data['school_units']);
        return view('admin.payIncome.index')->with($data);
    }

    public function print($student_id, $pay_income_id)
    {
        $reselect = [
            'students.id',
            'pay_incomes.id as pay_income_id',
            'pay_incomes.created_at as created_at',
            'students.name as student_name',
            'incomes.name as income_name',
            'incomes.amount',
        ];
        $batch_id = Batch::find(\App\Helpers\Helpers::instance()->getCurrentAccademicYear())->id;
        $data['reciept'] = DB::table('pay_incomes')
            ->where('pay_incomes.id', '=', $pay_income_id)
            ->join('incomes', 'incomes.id', '=', 'pay_incomes.income_id')
            ->join('students', 'students.id', '=', 'pay_incomes.student_id')
            ->where('students.id', '=', $student_id)
            ->join('school_units', 'school_units.id', '=', 'pay_incomes.class_id')
            ->where('pay_incomes.batch_id', $batch_id)
            ->select($reselect)
            ->first();
   
        // dd($data);
        return view('admin.payIncome.print')->with($data);
    }


    /**
     * get pay_income for class for a year
     */
    public  function getPayIncomePerClassYear(Request $request)
    {
        $validate_data = $request->validate([
            'class_id' => 'required|numeric',
            'batch_id' => 'required|numeric',
            'section_id' => 'required|numeric',
            'circle' => 'required|numeric'
        ]);
        $data['pay_incomes'] = DB::table('pay_incomes')
            ->join('incomes', 'incomes.id', '=', 'pay_incomes.income_id')
            ->join('students', 'students.id', '=', 'pay_incomes.student_id')
            ->join('school_units', 'school_units.id', '=', 'pay_incomes.class_id')
            ->where('pay_incomes.batch_id', $request->batch_id)
            ->where('school_units.id', $request->class_id)
            ->select($this->select)
            ->paginate(5);
        $class_name = $this->getSchoolUnit($request->class_id);
        $data['title'] = 'Pay Incomes: ' . $class_name;
        $data['years'] = Batch::all();
        $data['school_units'] = SchoolUnits::where('parent_id', '=', 0)->get();
        return view('admin.payIncome.index')->with($data);
    }


    /**
     * show view form to find a student to collect income
     */
    public function create()
    {
        $data['title'] = 'Collect Income';
        return view('admin.payIncome.create')->with($data);
    }


    /**
     * show view form to find a student to collect income
     */
    public function create_cash()
    {
        $data['title'] = 'Collect Income';
        return view('admin.payIncome.create_cash')->with($data);
    }

    public function save_create_cash(Request $request)
    {
        # code...
        // CREATE INCOME FIRST, THEN THE PAYMENT
        $income = [
            'name'=>$request->name,
            'amount'=>$request->amount,
            'user_id'=>auth()->id(),
            'cash'=>1,
            'description'=>$request->description
        ];
        $income_id = DB::table('incomes')->insertGetId($income);

        $payment = [
            'income_id'=>$income_id,
            'batch_id'=>Helpers::instance()->getCurrentAccademicYear(),
            'user_id'=>auth()->id(),
            'payed_by'=>$request->user_id,
            'cash'=>1
        ];
        DB::table('pay_incomes')->insert($payment);
        return back()->with('success', 'Done!');
    }


    /**
     * get student by name or matricule
     */
    public function get_searchUser()
    {
        $name = request('name');
        $students = DB::table('users')
        ->where(function($query)use($name){
            $query->where('name', 'like', "%{$name}%")
            ->orWhere('email', 'like', "%{$name}%")
            ->orWhere('username', 'like', "%{$name}%");
        })
        ->distinct()
        // ->where(function($query){
        //         auth()->user()->campus_id != null ? $query->where('students.campus_id', '=', auth()->user()->campus_id) : null;
        //     })
        // ->where(function($query){
        //     auth()->user()->campus_id != null ? $query->where('students.campus_id', '=', auth()->user()->campus_id): null;
        // })
        ->select('users.*')->get();

        // return ['data'=>collect($students)];
        return response()->json(['data' => PayCashIncomeResource::collection($students)]);
    }


    /**
     * get student by name or matricule
     */
    public function get_searchStudent()
    {
        $name = request('name');
        $students = DB::table('student_classes')
            ->join('students', 'students.id', '=', 'student_classes.student_id')
            ->join('school_units', 'school_units.id', '=', 'student_classes.class_id')
            ->distinct()
            ->where(function($query)use($name){
                $query->where('students.name', 'like', "%{$name}%")
                ->orWhere('students.matric', 'like', "%{$name}%");
            })
            ->where(function($query){
                    auth()->user()->campus_id != null ? $query->where('students.campus_id', '=', auth()->user()->campus_id) : null;
                })
            // ->where(function($query){
            //     auth()->user()->campus_id != null ? $query->where('students.campus_id', '=', auth()->user()->campus_id): null;
            // })
            ->select('students.*')->get();

        return response()->json(['data' => PayIncomeResource::collection($students)]);
    }
    // public function searchStudent($name)
    // {
    //     $students = DB::table('student_classes')
    //         ->join('students', 'students.id', '=', 'student_classes.student_id')
    //         ->join('school_units', 'school_units.id', '=', 'student_classes.class_id')
    //         ->where(function($query)use($name){
    //             $query->where('students.name', 'like', "%{$name}%")
    //             ->orWhere('students.matric', '=', $name);
    //         })
    //         ->where(function($query){
    //             auth()->user()->campus_id != null ? $query->where('students.campus_id', '=', auth()->user()->campus_id) : null;
    //         })
    //         ->select('students.*')->get();

    //     return response()->json(['data' => PayIncomeResource::collection($students)]);
    // }




    /**
     * show form to collect income for a student
     * @param int $class_id
     * @param int $student_id
     */
    public function collect($class_id, $student_id)
    {

        $student = Students::where('id', $student_id)->first();
        $data['title'] = 'Collect Income for ' . $student->name;
        $data['class_id'] = $class_id;
        $data['incomes'] = Income::where('cash', '=', false)->get();
        $data['years'] = Batch::all();
        $data['student_id'] = $student_id;
        return view('admin.payIncome.collect')->with($data);
    }

    /**
     * checkout if student has pay income
     */
    private function checkStudentPaidIncome($student_id, $id, $class_id)
    {
        $student = DB::table('pay_incomes')
            ->join('students', 'students.id', '=', 'pay_incomes.student_id')
            ->join('incomes', 'incomes.id', '=', 'pay_incomes.income_id')
            ->join('school_units', 'school_units.id', '=', 'pay_incomes.class_id')
            ->where('pay_incomes.income_id', $id)
            ->where('pay_incomes.student_id', $student_id)
            ->where('pay_incomes.class_id', $class_id)
            ->select('students.id')->first();
        return $student;
    }

    /**
     * store paid income
     * @param int $class_id
     * @param int $student_id
     */
    public function store(Request $request, $class_id, $student_id)
    {


        $student = $this->checkStudentPaidIncome($student_id, $request->income_id, $class_id);
        if (!empty($student)) {
            return redirect()->route('admin.pay_income.index')->with('error', 'The Student has paid this Income Fee!');
        }

        $validate_data = $request->validate([
            'income_id' => 'required|numeric',
            'batch_id' => 'required|numeric'
        ]);
        $created = PayIncome::create([
            'income_id' => $validate_data['income_id'],
            'batch_id' => $validate_data['batch_id'],
            'class_id' => $class_id,
            'student_id' => $student_id,
            'user_id' => auth()->user()->id
        ]);
        return redirect()->route('admin.pay_income.index')->with('success', 'Payed Income successfully');
    }

    /**
     * get all sections of parent
     * @param int $id
     */
    public function getSections($id)
    {
        $sections = SchoolUnits::where('parent_id', $id)->orderBy('name', 'ASC')->get()->toArray();
        return response()->json(['data' => $sections]);
    }

    /**
     * get all classes of a section
     * 
     * @param int $id
     */
    public function getClasses($id)
    {
        $data = trim($id);
        $classes = SchoolUnits::where('parent_id', $data)->get()->toArray();
        return response()->json(['data' => $classes]);
    }

    /**
     * get schoolunit name
     * @param int id
     */
    private function getSchoolUnit($id)
    {
        $school_unit = SchoolUnits::where('id', $id)->pluck('name')[0];
        return $school_unit;
    }

    public function delete_income($student_id, $pay_income_id)
    {
        # code...
        PayIncome::find($pay_income_id)->delete();
        return back()->with('success', 'Done');
    }
}
