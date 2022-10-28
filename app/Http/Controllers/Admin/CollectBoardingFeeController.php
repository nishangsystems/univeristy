<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\BoardingAmount;
use App\Models\BoardingFee;
use App\Models\BoardingFeeInstallment;
use App\Models\CollectBoardingFee;
use App\Models\SchoolUnits;
use App\Models\Students;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

class CollectBoardingFeeController extends Controller
{
    private  $msg = [
        '0' => 'Paid part',
        '1' => 'Completed'
    ];

    private $select_boarding = [
        'students.id as student_id',
        'collect_boarding_fees.id as collect_boarding_fees_id',
        'boarding_amounts.created_at',
        'boarding_amounts.amount_payable',
        'boarding_amounts.status',
        'boarding_amounts.id'
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
            ->select(
                \DB::raw("students.id as student_id,
                students.name,
                students.matric,
                school_units.name as class_name,
                MAX(boarding_amounts.total_amount) as total_amount,
                MIN(boarding_amounts.balance) as balance,
                collect_boarding_fees.id
                ")
            )
            ->groupBy('collect_boarding_fees.student_id')
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
            ->paginate(10);
        $data['years'] = $this->years;
        $data['student_id'] = $student_id;
        $data['school_units'] = SchoolUnits::where('parent_id', 0)->get();
        $data['id'] = $id;
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
    public function checkBoardingFee()
    {
        $boarding_fee = BoardingFee::first();
        if (empty($boarding_fee)) {
            return false;
        } else {
            return true;
        }
    }
    /**
     * check if some has completed boarding fee
     */
    private function checkCompletedBoardingFee($id)
    {
        $check = DB::table('collect_boarding_fees')
            ->leftJoin('students', 'students.id', '=', 'collect_boarding_fees.student_id')
            ->leftJoin('boarding_amounts', 'collect_boarding_fees.id', '=', 'boarding_amounts.collect_boarding_fee_id')
            ->where('students.id', $id)
            ->where('boarding_amounts.status', 1)
            ->select('students.id')
            ->first();
        return $check;
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
        $this->validateRequest($request);
        $boarding_fee_amount = $this->checkBoardingFee();
        if (!$boarding_fee_amount) {
            return redirect()->route('admin.boarding_fee.index')->with('error', 'Boarding Fee not set, please set Boarding Fee');
        }
        $student = $this->getStudent($student_id, $this->year);
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
        $data['balance'] = $this->getBalanceBoardingFee($student_id, $data['boarding_fee']->total_amount);
        return view('admin.collect_boarding_fee.edit')->with($data);
    }

    /**
     * get balnace fo boarding fee
     *
     */
    private function getBalanceBoardingFee($student_id, $amount_pay)
    {
        $boarding_fee = $this->getTotalboardingAmount($student_id);
        $balance = ($boarding_fee - $amount_pay);
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

        $updated_boarding_fee = $this->updateBoardingFee($student_id, $request->amount_payable, $updated_amount, $this->boarding_fee, $collected_boarding_fee);

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

        $data['total_amount'] = $this->getTotalboardingAmount($student_id);
        if (!$this->checkBoardingFee()) {
            return redirect()->route('admin.boarding_fee.index')->with('error', 'Boarding Fee not set, please set Boarding Fee ');
        }
        $check_user = CollectBoardingFee::where('student_id', $student_id)->first();
        $check_completed = $this->checkCompletedBoardingFee($student_id);

        if (empty($check_user)) {
            $student = Students::where('id', $student_id)->first();
            $data['title'] = 'Collect Boarding fee: ' . $student->name;
            $data['years'] = $this->years;
            $data['student_id'] = $student_id;
            $data['class_id'] = $class_id;
        } elseif (!empty($check_completed)) {
            return redirect()->route('admin.collect_boarding_fee.index')->with('success', "This Student has already completed Boarding Fee!");
        } else {
            return redirect()->route('admin.collect_boarding_fee.edit', [$check_user->id, $student_id]);
        }

        return view('admin.collect_boarding_fee.collect')->with($data);
    }

    /**
     * get the total amount to be paid a boarding student
     */
    private function getTotalboardingAmount($student_id)
    {
        $total = 0;
        $student = $this->getStudent($student_id, $this->year);
        $studentClass_id = $this->getStudentClass($student_id);
        $studentClassParent = $this->getStudentClassParent($studentClass_id[0]->class_id);
        $studentClassParentParent = $this->getStudentClassParent($studentClassParent[0]->parent_id);
        $boarding_fee = $this->getBoardingFeeAmount($studentClassParent[0]->parent_id, $studentClassParentParent[0]->parent_id);
        $year =  date('Y', strtotime($student[0]->created_at));
        if(!empty($boarding_fee))
        {

            if (!empty($student)) {
                if($year === explode('/',$this->year)[1]){
                    $total = $boarding_fee[0]->amount_new_student;

                }else {
                    $total = $boarding_fee[0]->amount_old_student;

                }
            }
        }else {
            return redirect()->route('admin.boarding_fee.index')->with("error", "Please Set Boarding fee");
        }
        return $total;


    }



    public function totalBoardingAmount($id)
    {
       $url = URL::previous();
       $length = strlen($url);
       $id = substr($url, $length-1, $length);
       $student_id = (int) $id;
       $balance = $this->getTotalboardingAmount($student_id);
        return response()->json(['data' => $balance]);
    }


    private function getStudentClass($student_id)
    {

        return DB::select('select student_classes.class_id from student_classes, students, school_units
         where students.id = student_classes.student_id AND
         school_units.id= student_classes.class_id AND student_classes.student_id = ?', [$student_id]);
    }

    private function getStudentClassParent($class_id)
    {
        return DB::select('select school_units.parent_id from school_units where id = ?', [$class_id]);
    }

    private function getBoardingFeeAmount($boarding_type, $parent_id)
    {
        return DB::select('SELECT * FROM boarding_fees WHERE boarding_type = ? AND parent_id = ?', [$boarding_type, $parent_id]);
    }


    /**
     * get student
     *
     * @param int $current_year
     * @param int $student_id
     */
    private function getStudent($student_id, $current_year)
    {
        return DB::select('SELECT * FROM students WHERE id = ?', [$student_id]);
    }

    /**
     * get student status
     */
    private function paidStudentBoarding($student, $request, $boarding_fee, $class_id, $student_id)
    {
        $collectedBoarding = null;
        $balance =  $this->getBalanceBoardingFee($student_id, $request->amount_payable);
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
        $balance =  $this->getBalanceBoardingFee($student_id, $updated_amount);
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
            ->select(
                \DB::raw("students.id as student_id,
                students.name,
                students.matric,
                school_units.name as class_name,
                MAX(boarding_amounts.total_amount) as total_amount,
                MIN(boarding_amounts.balance) as balance,
                collect_boarding_fees.id
                ")
            )
            ->groupBy('collect_boarding_fees.student_id')
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
        $data['id'] = $id;
        $data['school_units'] = SchoolUnits::where('parent_id', 0)->get();
        return view('admin.collect_boarding_fee.show')->with($data);
    }


    public function printBoardingFee($student_id, $id)
    {
        $student  = Students::findOrFail($student_id);
        $boarding_fee  = BoardingAmount::findOrFail($id);
        $year = $this->year;
        return view("admin.collect_boarding_fee.print_receipt", compact(['student', 'boarding_fee', 'year']));
    }




}
