<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\BoardingAmount;
use App\Models\BoardingFee;
use App\Models\CollectBoardingFee;
use App\Models\SchoolUnits;
use App\Models\Students;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CollectBoardingFeeController extends Controller
{
    private  $msg = [
        '0' => 'Paid part',
        '1' => 'Completed'
    ];
    private $select = [
        'students.id as student_id',
        'collect_boarding_fees.id',
        'students.name',
        'students.matric',
        'school_units.name as class_name'
    ];
    private $select_boarding = [
        'students.id as student_id',
        'students.name',
        'students.matric',
        'collect_boarding_fees.id',
        'boarding_amounts.created_at',
        'boarding_amounts.amount_payable',
        'boarding_amounts.total_amount',
        'boarding_amounts.status',
        'boarding_amounts.balance'
    ];
    private  $boarding_fee;
    private $year;
    private $batch_id;
    private $years;

    public function __construct()
    {
        $this->boarding_fee =  BoardingFee::first();
        $this->year = Batch::find(\App\Helpers\Helpers::instance()->getCurrentAccademicYear())->name;
        $this->batch_id = Batch::find(\App\Helpers\Helpers::instance()->getCurrentAccademicYear())->id;
        $this->years = Batch::all();
    }



    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $data['title'] = 'Paid Boarding Fees';
        $data['years'] = $this->years;
        $data['school_units'] = SchoolUnits::where('parent_id', '=', 0)->get();
        $data['boarding_fees'] = DB::table('collect_boarding_fees')
            ->join('students', 'students.id', '=', 'collect_boarding_fees.student_id')
            ->join('batches', 'batches.id', '=', 'collect_boarding_fees.batch_id')
            ->join('school_units', 'school_units.id', '=', 'collect_boarding_fees.class_id')
            ->join('boarding_amounts', 'collect_boarding_fees.id', '=', 'boarding_amounts.collect_boarding_fee_id')
            ->where('batches.id', $this->batch_id)
            ->select($this->select)
            ->distinct()
            ->orderBy('collect_boarding_fees.created_at', 'ASC')
            ->paginate(7);

        return view('admin.collect_boarding_fee.index')->with($data);
    }

    /**
     * show all boarding fee payments made by a student 
     */
    public function show($student_id, $id)
    {
        $data['title'] = 'Boarding Fee Transactions Details';
        $data['paid_boarding_fee_details'] = DB::table('collect_boarding_fees')
            ->leftJoin('students', 'students.id', '=', 'collect_boarding_fees.student_id')
            ->join('boarding_amounts', 'collect_boarding_fees.id', '=', 'boarding_amounts.collect_boarding_fee_id')
            ->join('batches', 'batches.id', '=', 'collect_boarding_fees.batch_id')
            ->where('collect_boarding_fees.student_id', $student_id)
            ->where('collect_boarding_fees.batch_id', $this->batch_id)
            ->where('collect_boarding_fees.id', $id)
            ->select($this->select_boarding)
            ->orderBy('boarding_amounts.created_at', 'ASC')
            ->paginate(5);
        $data['years'] = $this->years;
        $data['school_units'] = SchoolUnits::where('parent_id', 0)->get();
        return view('admin.collect_boarding_fee.show')->with($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['title'] = 'Collect Boarding Fee';
        return view('admin.collect_boarding_fee.create')->with($data);
    }

    /**
     * validate request
     */
    private function validateRequest($request)
    {
        $request->validate([
            'amount_payable' => 'required|numeric',
            'batch_id' => 'required|numeric',
            'status' => 'numeric',

        ]);
    }

    /**
     * check if boarding fee has bee set
     * 
     */
    private function checkBoardingFee()
    {
        $boarding_fee = BoardingFee::first();
        if (empty($boarding_fee)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @param int $student_id
     * @param int $class_id
     * 
     */
    public function store(Request $request, $class_id, $student_id)
    {
        //validate request
        $this->validateRequest($request);

        //checkout if boarding fee has been set;
        $boarding_fee_amount = $this->checkBoardingFee();
        if (!$boarding_fee_amount) {
            return redirect()->route('admin.boarding_fee.index')->with('error', 'Boarding Fee not set, please set Boarding Fee');
        }

        //get student
        $student = $this->getStudent($student_id, $this->year);

        //  verify if student is old or new and make payment for boarding
        $paid_boarding = $this->paidStudentBoarding($student, $request, $this->boarding_fee, $class_id, $student_id);

        if (!$paid_boarding) {
            return redirect()->back()->with('error', 'The Amount Paid is more than the specified amount for Boarding Fee.');
        }
        return redirect()->route('admin.collect_boarding_fee.index')->with('success', $this->msg[$paid_boarding->status] . " Boarding Fee");
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @param int $student_id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, $student_id)
    {

        $student = Students::where('id', $student_id)->first();
        $data['boarding_fee'] = $this->getCollectedBoardingFee($id);
        $data['title']  = 'Complete Boarding Fee: ' . $student->name;
        $data['student_id'] = $student->id;
        $data['balance'] = $this->getBalanceBoardingFee($student_id, $this->year, $data['boarding_fee']->total_amount);
        return view('admin.collect_boarding_fee.edit')->with($data);
    }

    /**
     * get balnace fo boarding fee
     * 
     */
    private function getBalanceBoardingFee($student_id, $year, $amount_pay)
    {
        $balance = null;
        $student  = $this->getStudent($student_id, $year);
        if (empty($student)) {
            $balance = $this->boarding_fee->amount_old_student - $amount_pay;
        } else {
            $balance = $this->boarding_fee->amount_new_student - $amount_pay;
        }
        return $balance;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @param  int  $student_id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id, $student_id)
    {
        $validate_data = [
            'amount_payable' => 'required|numeric',
        ];
        $collected_boarding_fee = $this->getCollectedBoardingFee($id);;
        $student = $this->getStudent($student_id, $this->year);
        $updated_amount = $request->amount_payable + $collected_boarding_fee->total_amount;
        // dd($updated_amount);
        $updated_boarding_fee = $this->updateBoardingFee($student_id, $request->amount_payable, $updated_amount, $this->boarding_fee, $collected_boarding_fee);
        // dd($updated_boarding_fee);
        if (!$updated_boarding_fee) {
            return redirect()->back()->with('error', 'The Amount Paid is more than the specified amount for Boarding Fee.');
        }
        return redirect()->route('admin.collect_boarding_fee.index')->with('success', $this->msg[$updated_boarding_fee->status] . " Boarding Fee");
    }

    private function getCollectedBoardingFee($id)
    {
        return DB::table('collect_boarding_fees')
            ->join('boarding_amounts', 'collect_boarding_fees.id', '=', 'boarding_amounts.collect_boarding_fee_id')
            ->where('boarding_amounts.collect_boarding_fee_id', $id)
            ->select(
                'collect_boarding_fees.id',
                'boarding_amounts.amount_payable',
                'boarding_amounts.total_amount',
                'boarding_amounts.id as boarding_amount_id'
            )->orderBy('boarding_amounts.created_at', 'DESC')->first();
    }



    /**
     * collect boarding fee
     * 
     * @param int $student_id
     *  @param int $class_id
     */
    public function collect($class_id, $student_id)
    {
        //   dd($student_id);
        $check_user = CollectBoardingFee::where('student_id', $student_id)->first();

        if (empty($check_user)) {
            $student = Students::where('id', $student_id)->first();
            $data['title'] = 'Collect Boarding fee: ' . $student->name;
            $data['years'] = $this->years;
            $data['student_id'] = $student_id;
            $data['class_id'] = $class_id;
        } else {
            return redirect()->route('admin.collect_boarding_fee.edit', [$check_user->id, $student_id]);
        }

        return view('admin.collect_boarding_fee.collect')->with($data);
    }

    /**
     * get student
     * 
     * @param int $current_year
     * @param int $student_id
     */
    private function getStudent($student_id, $current_year)
    {
        return DB::table('students')
            ->where('students.id', $student_id)
            ->where('students.type', 'boarding')
            ->whereYear('students.created_at', '!=', substr($current_year, 5))
            ->select(
                'students.id',
                'students.name',
                'students.matric',
                'students.type',
                'students.email'
            )->first();
    }

    /**
     * get student status
     */
    private function paidStudentBoarding($student, $request, $boarding_fee, $class_id, $student_id)
    {
        $collectedBoarding = null;
        $balance =  $this->getBalanceBoardingFee($student_id, $this->year, $request->amount_payable);
        if (empty($student)) {
            if ($request->amount_payable < $boarding_fee->amount_old_student) {
                $collectedBoarding = $this->insertBoardingFee($student_id, $class_id, 0, $request->amount_payable, $request->batch_id, $balance);
            } else if ($request->amount_payable == $boarding_fee->amount_old_student) {
                $collectedBoarding = $this->insertBoardingFee($student_id, $class_id, 1, $request->amount_payable, $request->batch_id, $balance);
            } else {
                return  false;
            }
        } else {
            if ($request->amount_payable < $boarding_fee->amount_new_student) {
                $collectedBoarding = $this->insertBoardingFee($student_id, $class_id, 0, $request->amount_payable, $request->batch_id, $balance);
            } else if ($request->amount_payable == $boarding_fee->amount_new_student) {
                $collectedBoarding = $this->insertBoardingFee($student_id, $class_id, 1, $request->amount_payable, $request->batch_id, $balance);
            } else {
                return false;
            }
        }
        return $collectedBoarding;
    }

    /**
     * insert a new boarding fee
     */
    private function insertBoardingFee($student_id, $class_id, $status, $amount_payable, $batch_id, $balance)
    {
        $collected_boarding_fee_id = DB::table('collect_boarding_fees')
            ->insertGetId([
                'class_id' => $class_id,
                'student_id' => $student_id,
                'batch_id' => $batch_id

            ]);
        $collected_amount = BoardingAmount::create([
            'amount_payable' => $amount_payable,
            'total_amount' => $amount_payable,
            'status' => $status,
            'collect_boarding_fee_id' => $collected_boarding_fee_id,
            'balance' => $balance
        ]);
        return $collected_amount;
    }

    /**
     * update boarding fee
     */
    private function updateBoardingFee($student_id, $amount_payable, $updated_amount, $boarding_fee, $collected_boarding_fee)
    {
        $updated_boarding_amount = null;
        $balance =  $this->getBalanceBoardingFee($student_id, $this->year, $updated_amount);
        if (empty($student)) {
            if ($updated_amount == $boarding_fee->amount_old_student) {
                $updated_boarding_amount = $this->updatePaidBoardingAmount($amount_payable, $updated_amount, $collected_boarding_fee, 1, $balance);
            } else if ($updated_amount < $boarding_fee->amount_old_student) {
                $updated_boarding_amount = $this->updatePaidBoardingAmount($amount_payable, $updated_amount, $collected_boarding_fee, 0, $balance);
            } else {
                return false;
            }
        } else {
            if ($updated_amount == $boarding_fee->amount_new_student) {
                $updated_boarding_amount = $this->updatePaidBoardingAmount($amount_payable, $updated_amount, $collected_boarding_fee, 1, $balance);
            } else if ($updated_amount < $boarding_fee->amount_new_student) {
                $updated_boarding_amount = $this->updatePaidBoardingAmount($amount_payable, $updated_amount, $collected_boarding_fee, 0, $balance);
            } else {
                return false;
            }
        }

        return $updated_boarding_amount;
    }

    /**
     * update boarding amount paid by a student
     * @param int $amount_payable
     * @param int $updated_amount
     * @param object CollectBoardingFee
     * 
     */
    private function updatePaidBoardingAmount($amount_payable, $updated_amount, $collected_boarding_fee, $status, $balance)
    {
        $updated_boarding_amount = BoardingAmount::create([
            'amount_payable' => $amount_payable,
            'total_amount' => $updated_amount,
            'status' => $status,
            'collect_boarding_fee_id' => $collected_boarding_fee->id,
            'balance' => $balance
        ]);
        return $updated_boarding_amount;
    }

    /**
     * get boarding fees by year
     */
    public function getBoardingFeePerYear(Request $request)
    {
        $this->validateData($request);
        $data['years'] = $this->years;
        $data['school_units'] = SchoolUnits::where('parent_id', '=', 0)->get();
        $data['boarding_fees'] = DB::table('collect_boarding_fees')
            ->leftJoin('students', 'students.id', '=', 'collect_boarding_fees.student_id')
            ->join('boarding_amounts', 'collect_boarding_fees.id', '=', 'boarding_amounts.collect_boarding_fee_id')
            ->join('batches', 'batches.id', '=', 'collect_boarding_fees.batch_id')
            ->join('school_units', 'school_units.id', '=', 'collect_boarding_fees.class_id')
            ->where('batches.id', $request->batch_id)
            ->where('boarding_amounts.status', $request->status)
            ->where('collect_boarding_fees.class_id', $request->class_id)
            ->select($this->select)
            ->distinct()
            ->orderBy('collect_boarding_fees.created_at', 'ASC')
            ->paginate(7);
        $class_name = $this->getSchoolUnit($request->class_id);

        $data['title'] = 'Paid Boarding Fees: ' . $class_name;
        return view('admin.collect_boarding_fee.index')->with($data);
    }

    private function validateData($request)
    {
        return $request->validate([
            'class_id' => 'required|numeric',
            'batch_id' => 'required|numeric',
            'status' => 'required|numeric',
            'section_id' => 'required|numeric',
            'circle' => 'required|numeric'
        ]);
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

    /**
     * get paid boarding fees details for student per class and year
     * 
     */
    public function collectBoardingFeeDetails(Request $request, $student_id, $id)
    {
        $data['title'] = 'Boarding Fee Transactions Details';
        $data['paid_boarding_fee_details'] = DB::table('collect_boarding_fees')
            ->leftJoin('students', 'students.id', '=', 'collect_boarding_fees.student_id')
            ->join('boarding_amounts', 'collect_boarding_fees.id', '=', 'boarding_amounts.collect_boarding_fee_id')
            ->join('batches', 'batches.id', '=', 'collect_boarding_fees.batch_id')
            ->join('school_units', 'school_units.id', '=', 'collect_boarding_fees.class_id')
            ->where('collect_boarding_fees.student_id', $student_id)
            ->where('collect_boarding_fees.batch_id', $request->batch_id)
            ->where('collect_boarding_fees.id', $id)
            ->where('school_units.id', $request->class_id)
            ->select($this->select_boarding)
            ->orderBy('boarding_amounts.created_at', 'ASC')
            ->paginate(5);
        $data['years'] = $this->years;
        $data['student_id'] = $student_id;
        $data['school_units'] = SchoolUnits::where('parent_id', 0)->get();
        return view('admin.collect_boarding_fee.show')->with($data);
    }
}
