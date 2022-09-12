<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\SchoolUnits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BoardingController extends Controller
{

    private $years;
    private $batch_id;
    private $select = [
        'students.id as student_id',
        'collect_boarding_fees.id',
        'students.name',
        'students.matric',
        'collect_boarding_fees.amount_payable',
        'collect_boarding_fees.status',
        'school_units.name as class_name'
    ];

    /**
     * constructor
     */
    public function __construct()
    {
        // $this->boarding_fee =  BoardingFee::first();
        //  $this->year = Batch::find(\App\Helpers\Helpers::instance()->getCurrentAccademicYear())->name;
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
            ->where('batches.id', $this->batch_id)
            ->where('collect_boarding_fees.student_id', Auth::id())
            ->select($this->select)->paginate(7);

        return view('student.index')->with($data);
    }
}
