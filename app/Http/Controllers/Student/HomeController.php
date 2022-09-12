<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\SchoolUnits;
use App\Models\Sequence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
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

    public function index()
    {
        return view('student.dashboard');
    }

    public function fee()
    {
        $data['title'] = "My fee Report";
        return view('student.fee')->with($data);
    }

    public function result()
    {
        $data['title'] = "My Result";
        $data['seqs'] = Sequence::orderBy('name')->get();
        $data['subjects'] = Auth('student')->user()->class(\App\Helpers\Helpers::instance()->getYear())->subjects;

        return view('student.result')->with($data);
    }

    public function subject()
    {
        $data['title'] = "My Subjects";
        //     dd($data);
        return view('student.subject')->with($data);
    }

    public function profile()
    {
        return view('student.edit_profile');
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|min:8',
            'phone' => 'required|min:9|max:15',
        ]);


        if ($validator->fails()) {
            return redirect()->back()->with(['e' => $validator->errors()->first()]);
        }

        $data['success'] = 200;
        $user = \Auth::user();
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->save();
        $data['user'] = \Auth::user();
        return redirect()->back()->with(['s' => 'Phone Number and Email Updated Successfully']);
    }

    public function __construct()
    {
        $this->middleware('auth:student');
        // $this->boarding_fee =  BoardingFee::first();
        //  $this->year = Batch::find(\App\Helpers\Helpers::instance()->getCurrentAccademicYear())->name;
        $this->batch_id = Batch::find(\App\Helpers\Helpers::instance()->getCurrentAccademicYear())->id;
        $this->years = Batch::all();
    }


    /**
     * get all notes for a subject offered by a student
     * 
     * @param integer subject_id
     * @return array
     */
    public function subjectNotes($id)
    {
        // dd($id);
        $class_subject_id = DB::table('class_subjects')
            ->join('subjects', 'subjects.id', '=', 'class_subjects.subject_id')
            ->where('subjects.id', $id)
            ->pluck('class_subjects.id')->first();
        $data['notes'] = $this->getSubjectNotes($id);
        $data['title'] = 'Subject Notes';
        return view('student.subject_notes')->with($data);
    }

    /**
     * get subject notes
     */
    public function getSubjectNotes($id)
    {

        $batch_id = Batch::find(\App\Helpers\Helpers::instance()->getCurrentAccademicYear())->id;
        $notes = DB::table('subject_notes')
            ->join('class_subjects', 'class_subjects.id', '=', 'subject_notes.class_subject_id')
            ->where('subject_notes.class_subject_id', $id)
            ->where('subject_notes.status', 1)
            ->where('subject_notes.batch_id', $batch_id)
            ->select(
                'subject_notes.id',
                'subject_notes.note_name',
                'subject_notes.note_path',
                'subject_notes.created_at'
            )
            ->paginate(5);
        return $notes;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function boarding()
    {
        $data['title'] = 'Boarding Fee Transactions Details';
        $data['years'] = $this->years;
        $data['school_units'] = SchoolUnits::where('parent_id', 0)->get();
        $data['paid_boarding_fee_details'] = $this->selectBoardingFee($this->batch_id);
        return view('student.index')->with($data);
    }

    /**
     * get boarding frees per year
     * 
     */
    public function getBoardingFeesYear(Request $request)
    {
        $this->validateRequest($request);
        $data['title'] = 'Boarding Fee Transactions Details';
        $data['paid_boarding_fee_details'] = DB::table('collect_boarding_fees')
            ->leftJoin('students', 'students.id', '=', 'collect_boarding_fees.student_id')
            ->join('boarding_amounts', 'collect_boarding_fees.id', '=', 'boarding_amounts.collect_boarding_fee_id')
            ->join('batches', 'batches.id', '=', 'collect_boarding_fees.batch_id')
            ->join('school_units', 'school_units.id', '=', 'collect_boarding_fees.class_id')
            ->where('collect_boarding_fees.student_id', Auth::id())
            ->where('collect_boarding_fees.batch_id', $request->batch_id)
            ->select($this->select_boarding)
            ->orderBy('boarding_amounts.created_at', 'ASC')
            ->paginate(5);
        $data['years'] = $this->years;
        $data['school_units'] = SchoolUnits::where('parent_id', 0)->get();
        return view('student.show')->with($data);
    }



    /**
     * select details for student boarding fee
     */
    private function selectBoardingFee($batch_id)
    {

        return DB::table('collect_boarding_fees')
            ->leftJoin('students', 'students.id', '=', 'collect_boarding_fees.student_id')
            ->join('boarding_amounts', 'collect_boarding_fees.id', '=', 'boarding_amounts.collect_boarding_fee_id')
            ->join('batches', 'batches.id', '=', 'collect_boarding_fees.batch_id')
            ->where('collect_boarding_fees.student_id', Auth::id())
            ->where('collect_boarding_fees.batch_id', $batch_id)
            ->select($this->select_boarding)
            ->orderBy('boarding_amounts.created_at', 'ASC')
            ->paginate(5);
    }
    private function validateRequest($request)
    {
        return $request->validate([

            'batch_id' => 'required|numeric'
        ]);
    }
}
