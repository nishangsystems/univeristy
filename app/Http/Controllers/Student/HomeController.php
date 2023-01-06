<?php

namespace App\Http\Controllers\Student;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\CampusSemesterConfig;
use App\Models\ClassSubject;
use App\Models\CourseNotification;
use App\Models\Material;
use App\Models\Notification;
use App\Models\ProgramLevel;
use App\Models\Result;
use App\Models\SchoolUnits;
use App\Models\Semester;
use App\Models\Sequence;
use App\Models\Students;
use App\Models\StudentStock;
use App\Models\StudentSubject;
use App\Models\SubjectNotes;
use App\Models\Subjects;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Throwable;
use PDF;

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
        $data['title'] = "Tution Report";
        return view('student.fee')->with($data);
    }

    public function other_incomes()
    {
        $data['title'] = "Other Payments Report";
        return view('student.other_incomes', $data);
    }

    public function result(Request $request)
    {
        # code...
        $data['title'] = "My Result";
        return view('student.result')->with($data);
    }

    public function ca_result(Request $request)
    {
        $year = $request->year ?? Helpers::instance()->getCurrentAccademicYear();
        $semester = $request->semester ? Semester::find($request->semester) : Helpers::instance()->getSemester(Students::find(auth()->id())->_class(Helpers::instance()->getCurrentAccademicYear())->id);
        // dd($semester);
        $ca_seq = $semester->sequences->first()->id;
        $data['title'] = "My CA Result";
        $data['user'] = auth()->user();
        $data['ca_total'] = auth()->user()->_class(Helpers::instance()->getCurrentAccademicYear())->program()->first()->ca_total;
        $data['semester'] = \App\Helpers\Helpers::instance()->getSemester(Students::find(auth()->id())->_class(Helpers::instance()->getCurrentAccademicYear())->id);
        $data['grading'] = auth()->user()->_class(Helpers::instance()->getCurrentAccademicYear())->program()->first()->gradingType->grading()->get() ?? [];
        $res = auth('student')->user()->result()->where('results.batch_id', '=', $year)->where('results.sequence', '=', $ca_seq)->pluck('subject_id')->toArray();
        $data['subjects'] = Auth('student')->user()->_class(\App\Helpers\Helpers::instance()->getYear())->subjects()->whereIn('subjects.id', $res)->get();
        $data['results'] = array_map(function($subject_id)use($data, $year, $ca_seq){
            return [
                'id'=>$subject_id,
                'code'=>$data['subjects']->where('id', '=', $subject_id)->first()->code ?? '',
                'name'=>$data['subjects']->where('id', '=', $subject_id)->first()->name ?? '',
                'status'=>$data['subjects']->where('id', '=', $subject_id)->first()->status ?? '',
                'coef'=>$data['subjects']->where('id', '=', $subject_id)->first()->coef ?? '',
                'ca_mark'=>auth('student')->user()->result()->where('results.batch_id', '=', $year)->where('results.subject_id', '=', $subject_id)->where('results.sequence', '=', $ca_seq)->first()->score ?? '',
            ];
        }, $res);
        // dd($data['results']);
        return view('student.ca-result')->with($data);
    }

    public function ca_result_download(Request $request)
    {
        $year = $request->year ?? Helpers::instance()->getCurrentAccademicYear();
        $semester = $request->semester ? Semester::find($request->semester) : Helpers::instance()->getSemester(Students::find(auth()->id())->_class(Helpers::instance()->getCurrentAccademicYear())->id);
        // dd($semester);
        $ca_seq = $semester->sequences->first()->id;
        $data['title'] = "My CA Result";
        $data['user'] = auth()->user();
        $data['ca_total'] = auth()->user()->_class(Helpers::instance()->getCurrentAccademicYear())->program()->first()->ca_total;
        $data['semester'] = \App\Helpers\Helpers::instance()->getSemester(Students::find(auth()->id())->_class(Helpers::instance()->getCurrentAccademicYear())->id);
        $data['grading'] = auth()->user()->_class(Helpers::instance()->getCurrentAccademicYear())->program()->first()->gradingType->grading()->get() ?? [];
        $res = auth('student')->user()->result()->where('results.batch_id', '=', $year)->where('results.sequence', '=', $ca_seq)->pluck('subject_id')->toArray();
        $data['subjects'] = Auth('student')->user()->_class(\App\Helpers\Helpers::instance()->getYear())->subjects()->whereIn('subjects.id', $res)->get();
        $data['results'] = array_map(function($subject_id)use($data, $year, $ca_seq){
            return [
                'id'=>$subject_id,
                'code'=>$data['subjects']->where('id', '=', $subject_id)->first()->code ?? '',
                'name'=>$data['subjects']->where('id', '=', $subject_id)->first()->name ?? '',
                'status'=>$data['subjects']->where('id', '=', $subject_id)->first()->status ?? '',
                'coef'=>$data['subjects']->where('id', '=', $subject_id)->first()->coef ?? '',
                'ca_mark'=>auth('student')->user()->result()->where('results.batch_id', '=', $year)->where('results.subject_id', '=', $subject_id)->where('results.sequence', '=', $ca_seq)->first()->score ?? '',
            ];
        }, $res);
        // dd($data['results']);
        $pdf = PDF::loadView('student.templates.ca-result-template',$data);
        return $pdf->download(auth()->user()->matric.'_'.$semester->name.'_CA_RESULTS.pdf');
        // return view('student.templates.ca-result-template')->with($data);
    }

    public function exam_result(Request $request)
    {
        $year = $request->year ?? Helpers::instance()->getCurrentAccademicYear();
        $semester = $request->semester ? Semester::find($request->semester) : Helpers::instance()->getSemester(Students::find(auth()->id())->_class(Helpers::instance()->getCurrentAccademicYear())->id);
        $seqs = $semester->sequences()->get('id')->toArray();
        $data['title'] = "My Exam Result";
        $data['user'] = auth()->user();
        $data['semester'] = $semester;
        $data['ca_total'] = auth()->user()->_class($year)->program()->first()->ca_total;
        $data['exam_total'] = auth()->user()->_class($year)->program()->first()->exam_total;
        $data['grading'] = auth()->user()->_class($year)->program()->first()->gradingType->grading()->get() ?? [];
        $res = auth('student')->user()->result()->where('results.batch_id', '=', $year)->whereIn('results.sequence', $seqs)->distinct()->pluck('subject_id')->toArray();
        $data['subjects'] = Auth('student')->user()->_class(\App\Helpers\Helpers::instance()->getYear())->subjects()->whereIn('subjects.id', $res)->get();
        $data['results'] = array_map(function($subject_id)use($data, $year, $seqs){
            $ca_mark = auth('student')->user()->result()->where('results.batch_id', '=', $year)->where('results.subject_id', '=', $subject_id)->where('results.sequence', '=', $seqs[0])->first()->score ?? 0;
            $exam_mark = auth('student')->user()->result()->where('results.batch_id', '=', $year)->where('results.subject_id', '=', $subject_id)->where('results.sequence', '=', $seqs[1])->first()->score ?? 0;
            $total = $ca_mark + $exam_mark;
            $grade = function()use($data, $total){
                foreach ($data['grading'] as $key => $value) {
                    # code...
                    if ($total >= $value->lower && $total <= $value->upper) {
                        # code...
                        return $value;
                    }
                }
            };
            // dd($grade());
            return [
                'id'=>$subject_id,
                'code'=>$data['subjects']->where('id', '=', $subject_id)->first()->code ?? '',
                'name'=>$data['subjects']->where('id', '=', $subject_id)->first()->name ?? '',
                'status'=>$data['subjects']->where('id', '=', $subject_id)->first()->status ?? '',
                'coef'=>$data['subjects']->where('id', '=', $subject_id)->first()->coef ?? '',
                'ca_mark'=>$ca_mark,
                'exam_mark'=>$exam_mark,
                'total'=>$total,
                'grade'=>$grade()->grade,
                'remark'=>$grade()->remark
            ];
        }, $res);
        $student = auth('student')->id();
        $fee = [
            'amount' => array_sum(
                \App\Models\Payments::where('payments.student_id', '=', $student)
                ->join('payment_items', 'payment_items.id', '=', 'payments.payment_id')
                ->where('payment_items.name', '=', 'TUTION')
                ->where('payments.batch_id', '=', $year)
                ->pluck('payments.amount')
                ->toArray()
            ),
            'total' => 
                    \App\Models\CampusProgram::join('program_levels', 'program_levels.id', '=', 'campus_programs.program_level_id')
                    ->join('payment_items', 'payment_items.campus_program_id', '=', 'campus_programs.id')
                    ->where('payment_items.name', '=', 'TUTION')
                    ->whereNotNull('payment_items.amount')
                    ->join('students', 'students.program_id', '=', 'program_levels.id')
                    ->where('students.id', '=', $student)->pluck('payment_items.amount')[0] ?? 0,
            'fraction' => Helpers::instance()->getSemester(Students::find(auth()->id())->_class($year)->id)->semester_min_fee
        ];
        $data['min_fee'] = $fee['total']*$fee['fraction'];
        $data['access'] = $fee['amount'] >= $data['min_fee'] || Students::find($student)->classes()->where(['year_id'=>$year, 'result_bypass_semester'=>Helpers::instance()->getSemester(Students::find($student)->_class($year)->id)])->first()->bypass_result;
        // dd($data);
        return view('student.exam-result')->with($data);
    }

    public function exam_result_download(Request $request)
    {
        $year = $request->year ?? Helpers::instance()->getCurrentAccademicYear();
        $semester = $request->semester ? Semester::find($request->semester) : Helpers::instance()->getSemester(Students::find(auth()->id())->_class(Helpers::instance()->getCurrentAccademicYear())->id);
        $seqs = $semester->sequences()->get('id')->toArray();
        $data['title'] = "My Exam Result";
        $data['user'] = auth()->user();
        $data['semester'] = \App\Helpers\Helpers::instance()->getSemester(Students::find(auth()->id())->_class(Helpers::instance()->getCurrentAccademicYear())->id);
        $data['ca_total'] = auth()->user()->_class(Helpers::instance()->getCurrentAccademicYear())->program()->first()->ca_total;
        $data['exam_total'] = auth()->user()->_class(Helpers::instance()->getCurrentAccademicYear())->program()->first()->exam_total;
        $data['grading'] = auth()->user()->_class(Helpers::instance()->getCurrentAccademicYear())->program()->first()->gradingType->grading()->get() ?? [];
        $res = auth('student')->user()->result()->where('results.batch_id', '=', $year)->whereIn('results.sequence', $seqs)->distinct()->pluck('subject_id')->toArray();
        $data['subjects'] = Auth('student')->user()->_class(\App\Helpers\Helpers::instance()->getYear())->subjects()->whereIn('subjects.id', $res)->get();
        $data['results'] = array_map(function($subject_id)use($data, $year, $seqs){
            $ca_mark = auth('student')->user()->result()->where('results.batch_id', '=', $year)->where('results.subject_id', '=', $subject_id)->where('results.sequence', '=', $seqs[0])->first()->score ?? 0;
            $exam_mark = auth('student')->user()->result()->where('results.batch_id', '=', $year)->where('results.subject_id', '=', $subject_id)->where('results.sequence', '=', $seqs[1])->first()->score ?? 0;
            $total = $ca_mark + $exam_mark;
            $grade = function()use($data, $total){
                foreach ($data['grading'] as $key => $value) {
                    # code...
                    if ($total >= $value->lower && $total <= $value->upper) {
                        # code...
                        return $value;
                    }
                }
            };
            // dd($grade());
            return [
                'id'=>$subject_id,
                'code'=>$data['subjects']->where('id', '=', $subject_id)->first()->code ?? '',
                'name'=>$data['subjects']->where('id', '=', $subject_id)->first()->name ?? '',
                'status'=>$data['subjects']->where('id', '=', $subject_id)->first()->status ?? '',
                'coef'=>$data['subjects']->where('id', '=', $subject_id)->first()->coef ?? '',
                'ca_mark'=>$ca_mark,
                'exam_mark'=>$exam_mark,
                'total'=>$total,
                'grade'=>$grade()->grade,
                'remark'=>$grade()->remark
            ];
        }, $res);
        $pdf = PDF::loadView('student.templates.exam-result-template',$data);
        return $pdf->download(auth()->user()->matric.'_'.$semester->name.'_EXAM_RESULTS.pdf');
        // return view('student.templates.exam-result-template')->with($data);
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

    public function course_registration()
    {
        # code...
        $data['title'] = "Course Registration For " .Helpers::instance()->getSemester(Students::find(auth()->id())->_class(Helpers::instance()->getCurrentAccademicYear())->id)->name ?? ''." ".\App\Models\Batch::find(\App\Helpers\Helpers::instance()->getYear())->name;
        $data['student_class'] = ProgramLevel::find(\App\Models\StudentClass::where(['student_id'=>auth()->id()])->where(['year_id'=>\App\Helpers\Helpers::instance()->getYear()])->first()->class_id);
        $data['cv_total'] = ProgramLevel::find(Students::find(auth()->id())->_class(Helpers::instance()->getCurrentAccademicYear())->id)->program()->first()->max_credit;        
        
        $student = auth()->id();
        $year = Helpers::instance()->getYear();
        $semester = Helpers::instance()->getSemester(Students::find(auth()->id())->_class(Helpers::instance()->getCurrentAccademicYear())->id)->id;
        $_semester = Helpers::instance()->getSemester(Students::find(auth()->id())->_class(Helpers::instance()->getCurrentAccademicYear())->id)->background->semesters()->orderBy('sem', 'DESC')->first()->id;
        if ($semester == $_semester) {
            # code...
            return redirect(route('student.home'))->with('error', 'Resit registration can not be done here. Do that under "Resit Registration"');
        }


        $fee = [
            'amount' => array_sum(
                \App\Models\Payments::where('payments.student_id', '=', $student)
                ->join('payment_items', 'payment_items.id', '=', 'payments.payment_id')
                ->where('payment_items.name', '=', 'TUTION')
                ->where('payments.batch_id', '=', $year)
                ->pluck('payments.amount')
                ->toArray()
            ),
            'total' => 
                    \App\Models\CampusProgram::join('program_levels', 'program_levels.id', '=', 'campus_programs.program_level_id')
                    ->join('payment_items', 'payment_items.campus_program_id', '=', 'campus_programs.id')
                    ->where('payment_items.name', '=', 'TUTION')
                    ->whereNotNull('payment_items.amount')
                    ->join('students', 'students.program_id', '=', 'program_levels.id')
                    ->where('students.id', '=', $student)->pluck('payment_items.amount')[0] ?? 0,
            'fraction' => Helpers::instance()->getSemester(Students::find(auth()->id())->_class(Helpers::instance()->getCurrentAccademicYear())->id)->courses_min_fee
        ];
        $conf = CampusSemesterConfig::where(['campus_id'=>auth('student')->user()->campus_id])->where(['semester_id'=>Helpers::instance()->getSemester(Students::find(auth()->id())->_class(Helpers::instance()->getCurrentAccademicYear())->id)->id])->first();
        if ($conf != null) {
            # code...
            $data['on_time'] = strtotime($conf->courses_date_line) >= strtotime(date('d-m-Y'));
        }else{
            return redirect(route('student.home'))->with('error', 'Can not sign courses for this program at the moment. Date limit not set. Contact registry.');
        }
        // return __DIR__;
        // dd($data);
        $data['min_fee'] = number_format($fee['total']*$fee['fraction']);
        $data['access'] = $fee['amount'] >= $data['min_fee']  || Students::find($student)->classes()->where(['year_id'=>Helpers::instance()->getCurrentAccademicYear()])->first()->bypass_result;
        return view('student.courses.register', $data);
    }

    public function resit_registration()
    {
        # code...
        $data['title'] = "Resit Registration For " .Helpers::instance()->getSemester(Students::find(auth()->id())->_class(Helpers::instance()->getCurrentAccademicYear())->id)->name." ".\App\Models\Batch::find(\App\Helpers\Helpers::instance()->getYear())->name;
        $data['student_class'] = ProgramLevel::find(\App\Models\StudentClass::where(['student_id'=>auth()->id()])->where(['year_id'=>\App\Helpers\Helpers::instance()->getYear()])->first()->class_id);
        $data['cv_total'] = ProgramLevel::find(Students::find(auth()->id())->_class(Helpers::instance()->getCurrentAccademicYear())->id)->program()->first()->max_credit;
        // resit course price is set in campus_programs table //
        // campus_class = $data['student_class']->campus_programs()->where('campus_id', '=', auth('student')->user()->campus_id)
        $data['unit_cost'] = 3000;
        
        $student = auth()->id();
        $year = Helpers::instance()->getYear();
        $fee = [
            'amount' => array_sum(
                \App\Models\Payments::where('payments.student_id', '=', $student)
                ->join('payment_items', 'payment_items.id', '=', 'payments.payment_id')
                ->where('payment_items.name', '=', 'TUTION')
                ->where('payments.batch_id', '=', $year)
                ->pluck('payments.amount')
                ->toArray()
            ),
            'total' => 
                    \App\Models\CampusProgram::join('program_levels', 'program_levels.id', '=', 'campus_programs.program_level_id')
                    ->join('payment_items', 'payment_items.campus_program_id', '=', 'campus_programs.id')
                    ->where('payment_items.name', '=', 'TUTION')
                    ->whereNotNull('payment_items.amount')
                    ->join('students', 'students.program_id', '=', 'program_levels.id')
                    ->where('students.id', '=', $student)->pluck('payment_items.amount')[0] ?? 0,
            'fraction' => Helpers::instance()->getSemester(Students::find(auth()->id())->_class(Helpers::instance()->getCurrentAccademicYear())->id)->courses_min_fee
        ];
        $conf = CampusSemesterConfig::where(['campus_id'=>auth('student')->user()->campus_id])->where(['semester_id'=>Helpers::instance()->getSemester(Students::find(auth()->id())->_class(Helpers::instance()->getCurrentAccademicYear())->id)->id])->first();
        if ($conf != null) {
            # code...
            $data['on_time'] = strtotime($conf->courses_date_line) >= strtotime(date('d-m-Y'));
        }else{
            return redirect(route('student.home'))->with('error', 'Can not sign courses for this program at the moment. Date limit not set. Contact registry.');
        }
        $data['min_fee'] = number_format($fee['total']*$fee['fraction']);
        $data['access'] = $fee['amount'] >= $data['min_fee']  || Students::find($student)->classes()->where(['year_id'=>Helpers::instance()->getCurrentAccademicYear()])->first()->bypass_result;
        return view('student.resit.register', $data);
    }

    public function form_b()
    {
        # code...
        $data['title'] = "Registered Courses ".Helpers::instance()->getSemester(Students::find(auth()->id())->classes()->where(['year_id'=>Helpers::instance()->getCurrentAccademicYear()])->first()->class_id)->name." ".\App\Models\Batch::find(\App\Helpers\Helpers::instance()->getYear())->name;
        $data['student_class'] = ProgramLevel::find(\App\Models\StudentClass::where(['student_id'=>auth()->id()])->where(['year_id'=>\App\Helpers\Helpers::instance()->getYear()])->first()->class_id);
        $data['cv_total'] = ProgramLevel::find(Students::find(auth()->id())->_class(Helpers::instance()->getCurrentAccademicYear())->id)->program()->first()->max_credit;        
        
        $student = auth()->id();
        $year = Helpers::instance()->getYear();
        $fee = [
            'amount' => array_sum(
                \App\Models\Payments::where('payments.student_id', '=', $student)
                ->join('payment_items', 'payment_items.id', '=', 'payments.payment_id')
                ->where('payment_items.name', '=', 'TUTION')
                ->where('payments.batch_id', '=', $year)
                ->pluck('payments.amount')
                ->toArray()
            ),
            'total' => 
                    \App\Models\CampusProgram::join('program_levels', 'program_levels.id', '=', 'campus_programs.program_level_id')
                    ->join('payment_items', 'payment_items.campus_program_id', '=', 'campus_programs.id')
                    ->where('payment_items.name', '=', 'TUTION')
                    ->whereNotNull('payment_items.amount')
                    ->join('students', 'students.program_id', '=', 'program_levels.id')
                    ->where('students.id', '=', $student)->pluck('payment_items.amount')[0] ?? 0,
            'fraction' => Helpers::instance()->getSemester(Students::find(auth()->id())->_class(Helpers::instance()->getCurrentAccademicYear())->id)->courses_min_fee
        ];
        $data['min_fee'] = number_format($fee['total']*$fee['fraction']);
        $data['access'] = $fee['amount'] >= $data['min_fee']  || Students::find($student)->classes()->where(['year_id'=>Helpers::instance()->getCurrentAccademicYear()])->first()->bypass_result;
        return view('student.courses.form_b', $data);
    }

    public function registered_courses(Request $request)
    {
        # code...
        $data['title'] = "Registered Courses ".Helpers::instance()->getSemester(Students::find(auth()->id())->classes()->where(['year_id'=>Helpers::instance()->getCurrentAccademicYear()])->first()->class_id)->name." ".\App\Models\Batch::find(\App\Helpers\Helpers::instance()->getYear())->name;
        $data['student_class'] = ProgramLevel::find(\App\Models\StudentClass::where(['student_id'=>auth()->id()])->where(['year_id'=>\App\Helpers\Helpers::instance()->getYear()])->first()->class_id);
        $data['cv_total'] = ProgramLevel::find(Students::find(auth()->id())->_class(Helpers::instance()->getCurrentAccademicYear())->id)->program()->first()->max_credit;        
        
        $student = auth()->id();
        $year = Helpers::instance()->getYear();
        $fee = [
            'amount' => array_sum(
                \App\Models\Payments::where('payments.student_id', '=', $student)
                ->join('payment_items', 'payment_items.id', '=', 'payments.payment_id')
                ->where('payment_items.name', '=', 'TUTION')
                ->where('payments.batch_id', '=', $year)
                ->pluck('payments.amount')
                ->toArray()
            ),
            'total' => 
                    \App\Models\CampusProgram::join('program_levels', 'program_levels.id', '=', 'campus_programs.program_level_id')
                    ->join('payment_items', 'payment_items.campus_program_id', '=', 'campus_programs.id')
                    ->where('payment_items.name', '=', 'TUTION')
                    ->whereNotNull('payment_items.amount')
                    ->join('students', 'students.program_id', '=', 'program_levels.id')
                    ->where('students.id', '=', $student)->pluck('payment_items.amount')[0] ?? 0,
            'fraction' => Helpers::instance()->getSemester(Students::find(auth()->id())->_class(Helpers::instance()->getCurrentAccademicYear())->id)->courses_min_fee
        ];
        $data['min_fee'] = number_format($fee['total']*$fee['fraction']);
        $data['access'] = ($fee['amount'] >= $data['min_fee']) || Students::find($student)->classes()->where(['year_id'=>Helpers::instance()->getCurrentAccademicYear()])->first()->bypass_result;
        return view('student.courses.index', $data);
    }

    // public function registered_resit_courses(Request $request)
    // {
    //     # code...
    //     $data['title'] = "Registered Courses ".Helpers::instance()->getSemester(Students::find(auth()->id())->classes()->where(['year_id'=>Helpers::instance()->getCurrentAccademicYear()])->first()->class_id)->name." ".\App\Models\Batch::find(\App\Helpers\Helpers::instance()->getYear())->name;
    //     $data['student_class'] = ProgramLevel::find(\App\Models\StudentClass::where(['student_id'=>auth()->id()])->where(['year_id'=>\App\Helpers\Helpers::instance()->getYear()])->first()->class_id);
    //     $data['cv_total'] = ProgramLevel::find(Students::find(auth()->id())->_class(Helpers::instance()->getCurrentAccademicYear())->id)->program()->first()->max_credit;        
        
    //     $student = auth()->id();
    //     $year = Helpers::instance()->getYear();
    //     $fee = [
    //         'amount' => array_sum(
    //             \App\Models\Payments::where('payments.student_id', '=', $student)
    //             ->join('payment_items', 'payment_items.id', '=', 'payments.payment_id')
    //             ->where('payment_items.name', '=', 'TUTION')
    //             ->where('payments.batch_id', '=', $year)
    //             ->pluck('payments.amount')
    //             ->toArray()
    //         ),
    //         'total' => 
    //                 \App\Models\CampusProgram::join('program_levels', 'program_levels.id', '=', 'campus_programs.program_level_id')
    //                 ->join('payment_items', 'payment_items.campus_program_id', '=', 'campus_programs.id')
    //                 ->where('payment_items.name', '=', 'TUTION')
    //                 ->whereNotNull('payment_items.amount')
    //                 ->join('students', 'students.program_id', '=', 'program_levels.id')
    //                 ->where('students.id', '=', $student)->pluck('payment_items.amount')[0] ?? 0,
    //         'fraction' => Helpers::instance()->getSemester(Students::find(auth()->id())->_class(Helpers::instance()->getCurrentAccademicYear())->id)->courses_min_fee
    //     ];
    //     $data['min_fee'] = number_format($fee['total']*$fee['fraction']);
    //     $data['access'] = $fee['amount'] >= $data['min_fee'];
    //     return view('student.courses.index', $data);
    // }

    public static function registerd_courses($year = null, $semester = null, $student = null )
    {
        try {
            //code...
            $_student = $student ?? auth()->id();
            $_year = $year ?? Helpers::instance()->getYear();
            $_semester = $semester ?? Helpers::instance()->getSemester(Students::find(auth()->id())->_class(Helpers::instance()->getCurrentAccademicYear())->id)->id;
            # code...
            $courses = StudentSubject::where(['student_courses.student_id'=>$_student])->where(['student_courses.year_id'=>$_year])
                    ->join('subjects', ['subjects.id'=>'student_courses.course_id'])->where(['subjects.semester_id'=>$_semester])
                    ->join('class_subjects', ['class_subjects.subject_id'=>'subjects.id'])->distinct()->orderBy('subjects.name')->get(['subjects.*', 'class_subjects.coef as cv', 'class_subjects.status as status']);
                    return response()->json(['ids'=>$courses->pluck('id'), 'cv_sum'=>collect($courses)->sum('cv'), 'courses'=>$courses]);
        } catch (\Throwable $th) {
            return $th->getMessage();
            
        }
    }

    public static function registered_resit_courses()
    {
        try {
            //code...
            $_student = $student ?? auth()->id();
            $_year = $year ?? Helpers::instance()->getYear();
            // get resit semester for the student's background
            $_semester = Helpers::instance()->getSemester(Students::find(auth()->id())->_class(Helpers::instance()->getCurrentAccademicYear())->id)->background->semesters()->orderBy('sem', 'DESC')->first()->id;
            // return $_semester;
            # code...
            $courses = StudentSubject::where(['student_courses.student_id'=>$_student])->where(['student_courses.year_id'=>$_year])->where(['student_courses.semester_id'=>$_semester])
                    ->join('subjects', ['subjects.id'=>'student_courses.course_id'])
                    ->join('class_subjects', ['class_subjects.subject_id'=>'subjects.id'])->distinct()->orderBy('subjects.name')->get(['subjects.*', 'class_subjects.coef as cv', 'class_subjects.status as status']);
                    return response()->json(['ids'=>$courses->pluck('id'), 'cv_sum'=>collect($courses)->sum('cv'), 'courses'=>$courses]);
        } catch (\Throwable $th) {
            return $th->getMessage();
            
        }
    }

    // Search course by name or course code as req
    public function search_course(Request $request)
    {
        # code...
        $validate = Validator::make($request->all(), ['value'=>'required']);
        // return $request->value;

        try{
            $pl = DB::table('students')->find(auth()->id())->program_id;
            $program = \App\Models\ProgramLevel::find($pl);
            $subjects = \App\Models\ProgramLevel::where(['program_levels.program_id'=>$program->program_id])->where('program_levels.level_id', '<=', $program->level_id)
                        ->join('class_subjects', ['class_subjects.class_id'=>'program_levels.id'])
                        ->join('subjects', ['subjects.id'=>'class_subjects.subject_id'])
                        ->where(function($q)use($request){
                            $q->where('subjects.code', 'like', '%'.$request->value.'%')
                            ->orWhere('subjects.name', 'like', '%'.$request->value.'%');
                        })
                        ->get(['subjects.*', 'class_subjects.coef as cv', 'class_subjects.status as status'])->sortBy('name')->toArray();
            return $subjects;
        }
        catch(Throwable $th){return $th->getLine() . '  '.$th->getMessage();}
    }

    public function class_subjects($level)
    {
        # code...
        
        try{
            $pl = Students::find(auth()->id())->classes()->where('year_id', '=', Helpers::instance()->getCurrentAccademicYear())->first()->class_id;
            $program_id = \App\Models\ProgramLevel::find($pl)->program_id;
            $subjects = \App\Models\ProgramLevel::where(['program_levels.program_id'=>$program_id])->where(['program_levels.level_id'=>$level])
                        ->join('class_subjects', ['class_subjects.class_id'=>'program_levels.id'])->join('subjects', ['subjects.id'=>'class_subjects.subject_id'])
                        ->where(['subjects.semester_id'=>Helpers::instance()->getSemester($pl)->id])
                        ->get(['subjects.*', 'class_subjects.coef as cv', 'class_subjects.status as status'])->sortBy('name')->toArray();
            return $subjects;
        }
        catch(Throwable $th){return $th->getLine() . '  '.$th->getMessage();}
    }

    public function register_resit(Request $request)//takes class course id
    {

        // TO MAKE THE PAYMENT, MAKE A REQUEST TO ANOTHER URL WHERE THE PAYMENT 
        // IS DONE AND RESPONSE RETURNED BACK HERE, THEN THE COURSES ARE REGISTERED 

        

        // return $request->all();
        # code...
        // first clear all registered courses for the year, semester, student then rewrite
        $year = Helpers::instance()->getYear();
        $semester = Helpers::instance()->getSemester(Students::find(auth()->id())->_class(Helpers::instance()->getCurrentAccademicYear())->id)->background->semesters()->orderBy('sem', 'DESC')->first()->id;
        $user = auth()->id();
        try {
            if ($request->has('courses')) {
                // DB::beginTransaction();
                $ids = \App\Models\StudentSubject::where(['student_id'=>$user])->where(['year_id'=>$year])->where(['semester_id'=>$semester])->pluck('id');
                foreach ($ids as $key => $value) {
                    # code...
                    StudentSubject::find($value)->delete();
                }
                # code...
                foreach (array_unique($request->courses) as $key => $value) {
                    # code...
                    StudentSubject::create(['year_id'=>$year, 'semester_id'=>$semester, 'student_id'=>$user, 'course_id'=>$value]);
                }
            }
            // DB::commit();
            return back()->with('success', "!Done");
        } catch (\Throwable $th) {
            // DB::rollBack();
            return back()->with('error', $th->getFile().' : '.$th->getLine().' :: '.$th->getMessage());
        }
    }

    public function register_courses(Request $request)//takes class course id
    {
        // return $request->all();
        # code...
        // first clear all registered courses for the year, semester, student then rewrite
        $year = Helpers::instance()->getYear();
        $semester = Helpers::instance()->getSemester(Students::find(auth()->id())->_class(Helpers::instance()->getCurrentAccademicYear())->id)->id;
        $_semester = Helpers::instance()->getSemester(Students::find(auth()->id())->_class(Helpers::instance()->getCurrentAccademicYear())->id)->background->semesters()->orderBy('sem', 'DESC')->first()->id;
        $user = auth()->id();
        try {
            if ($semester == $_semester) {
                # code...
                return back()->with('error', 'Resit registration can not be done here. Do that under \"Resit Registration\"');
            }
            if ($request->has('courses')) {
                // DB::beginTransaction();
                $ids = \App\Models\StudentSubject::where(['student_id'=>$user])->where(['year_id'=>$year])->where(['semester_id'=>$semester])->pluck('id');
                foreach ($ids as $key => $value) {
                    # code...
                    StudentSubject::find($value)->delete();
                }
                # code...
                foreach (array_unique($request->courses) as $key => $value) {
                    # code...
                    StudentSubject::create(['year_id'=>$year, 'semester_id'=>$semester, 'student_id'=>$user, 'course_id'=>$value]);
                }
            }
            // DB::commit();
            return back()->with('success', "!Done");
        } catch (\Throwable $th) {
            // DB::rollBack();
            return back()->with('error', $th->getFile().' : '.$th->getLine().' :: '.$th->getMessage());
        }
    }
    public function download_courses($year, $semester)//takes class course id
    {
        # code...
        $reg = $this->registerd_courses($year, $semester)->getData();
        $data['cv_sum'] = $reg->cv_sum;
        $data['courses'] = $reg->courses;
        $data['user'] = auth()->user();
        
        $pdf = PDF::loadView('student.courses.form_b_template',$data);
        return $pdf->download(auth()->user()->matric.'_FORM-B.pdf');
        // return view('student.courses.form_b_template', $data);
    }
    public function add_course()//takes class course id
    {
        // add course to current auth user for current academic year
        # code...
    }

    public function drop_course()//takes class course id
    {
        // drop course for current auth user for current academic year
        # code...
    }

    public function course_notes(Request $request, $id)
    {
        // get student class
        $class = auth('student')->user()->_class(Helpers::instance()->getCurrentAccademicYear());
        $class_subject_id = ClassSubject::where('class_id', '=', $class->id)->where('subject_id', '=', $id)->first()->id ?? 0;
        // $data['subject_info'] = $this->showSubject($class->id, $id);
        // dd($data);
        $data['notes'] = SubjectNotes::where('class_subject_id', $class_subject_id)->where('type', 'note')->where('status', 1)->get();
        $data['title'] = 'Notes For '.Subjects::find($id)->name.' - '. $class->program()->first()->name.' : Level '.$class->level()->first()->level;
        // dd($data);
        return view('student.subject_material')->with($data);
    }

    public function assignment(Request $request, $id)
    {
        // get student class
        $class = auth('student')->user()->_class(Helpers::instance()->getCurrentAccademicYear());
        $class_subject_id = ClassSubject::where('class_id', '=', $class->id)->where('subject_id', '=', $id)->first()->id ?? 0;
        // $data['subject_info'] = $this->showSubject($class->id, $id);
        // dd($data);
        $data['notes'] = SubjectNotes::where('class_subject_id', $class_subject_id)->where('type', 'assignment')->where('status', 1)->get();
        $data['title'] = 'Assignments For '.Subjects::find($id)->name.' - '. $class->program()->first()->name.' : Level '.$class->level()->first()->level;
        // dd($data);
        return view('student.subject_material')->with($data);
    }

    public function notification(Request $request, $id)
    {
        # code...
        $data['title'] = "Course Notifications For ".Subjects::find($request->course_id)->code.' - '.Subjects::find($request->course_id)->name;
        $data['notifications'] = CourseNotification::where('course_id', '=', $request->course_id)->where('status', '=', 1)->get();
        return view('student.notification.index', $data);
    }

    public function show_notification($id)
    {
        # code...
        $data['notification'] = CourseNotification::find($id);
        $data['title'] = $data['notification']->title;
        return view('student.notification.show', $data);
    }
    public function _program_notifications_show($id)
    {
        # code...
        $data['notification'] = Notification::find($id);
        $data['title'] = $data['notification']->title;
        return view('student.notification.show', $data);
    }

    public function _notifications_index(Request $request)
    {
        # code...
        $data['title'] = "Material";
        $data['class'] = auth('student')->user()->_class(Helpers::instance()->getCurrentAccademicYear());
        $data['program'] = $data['class']->program;
        $data['department'] = $data['program']->parent;

        return view('student.notification.home', $data);
    }
    public function _class_notifications(Request $request, $class_id, $campus_id)
    {
        # code...
        $class = ProgramLevel::find($class_id);
        $data['title'] = "Class Notifications";
        $data['notifications'] = Notification::where('school_unit_id', '=', $class->program_id)->where('level_id', '=', $class->level_id)->where('campus_id', '=', $campus_id)->where(function($q){
            $q->where('visibility', '=', 'students')->orWhere('visibility', '=', 'general');
        })->get();
        return view('student.notification.index', $data);
    }
    public function _class_material(Request $request, $class_id, $campus_id)
    {
        # code...
        $class = ProgramLevel::find($class_id);
        $data['title'] = "Class Material";
        $data['notifications'] = Material::where('school_unit_id', '=', $class->program_id)->where('level_id', '=', $class->level_id)->where('campus_id', '=', $campus_id)->where(function($q){
            $q->where('visibility', '=', 'students')->orWhere('visibility', '=', 'general');
        })->get();
        return view('student.notification.material_index', $data);
    }
    public function _department_notifications(Request $request, $department_id, $campus_id)
    {
        # code...
        $data['title'] = "Department Notifications";
        $data['notifications'] = Notification::where('school_unit_id', '=', $department_id)->where('campus_id', '=', $campus_id)->where(function($q){
            $q->where('visibility', '=', 'students')->orWhere('visibility', '=', 'general');
        })->get();
        return view('student.notification.index', $data);
    }
    public function _department_material(Request $request, $department_id, $campus_id)
    {
        # code...
        $data['title'] = "Department Material";
        $data['notifications'] = Material::where('school_unit_id', '=', $department_id)->where('campus_id', '=', $campus_id)->where(function($q){
            $q->where('visibility', '=', 'students')->orWhere('visibility', '=', 'general');
        })->get();
        return view('student.notification.material_index', $data);
    }
    public function _program_notifications(Request $request, $program_id, $campus_id)
    {
        # code...
        $data['title'] = "Program Notifications";
        $data['notifications'] = Notification::where('school_unit_id', '=', $program_id)->where('campus_id', '=', $campus_id)->where(function($q){
            $q->where('visibility', '=', 'students')->orWhere('visibility', '=', 'general');
        })->get();
        return view('student.notification.index', $data);
    }
    public function _school_notifications(Request $request, $campus_id = null)
    {
        // dd(auth('student')->user()->_class(Helpers::instance()->getCurrentAccademicYear())->level_id);
        # code...
        $data['title'] = "School Notifications";
        $data['notifications'] = Notification::whereNull('school_unit_id')
        ->where(function($q){
            $q->WhereNull('level_id')->orWhere('level_id', '=', auth('student')->user()->_class(Helpers::instance()->getCurrentAccademicYear())->level_id ?? null);
        })
        ->where(function($q)use($campus_id){
             $q->where('campus_id', '=', $campus_id)->orWhere('campus_id', '=', 0);
        })
        ->where(function($q){
            $q->where('visibility', '=', 'students')->orWhere('visibility', '=', 'general');
        })
        ->get();
        return view('student.notification.index', $data);
    }
    public function _program_material(Request $request, $program_id, $campus_id)
    {
        # code...
        $data['title'] = "Program Material";
        $data['notifications'] = Material::where('school_unit_id', '=', $program_id)->where('campus_id', '=', $campus_id)->where(function($q){
            $q->where('visibility', '=', 'students')->orWhere('visibility', '=', 'general');
        })->get();
        return view('student.notification.material_index', $data);
    }
    public function _school_material(Request $request, $campus_id = null)
    {
        # code...
        $data['title'] = "Program Material";
        $data['notifications'] = Material::whereNull('school_unit_id')->whereNull('level_id')
        ->where(function($q){
            $q->where('level_id', '=', auth('student')->user()->_class(Helpers::instance()->getCurrentAccademicYear())->level_id ?? null)->orWhereNull('level_id');
        })
        ->where(function($q)use($campus_id){
            $campus_id==null ? null : $q->where('campus_id', '=', $campus_id)->orWhereNull('campus_id');
        })
        ->where(function($q){
            $q->where('visibility', '=', 'students')->orWhere('visibility', '=', 'general');
        })
        ->get();
        return view('student.notification.material_index', $data);
    }
    public function edit_profile()
    {
        # code...
        $data['title'] = "Edit Profile";
        return view('student.edit_profile', $data);
    }
    public function update_profile(Request $request)
    {
        # code...
        if(
            Students::where([
                'email' => $request->email, 'phone' => $request->phone
            ])->count() > 0 && (auth()->user()->phone != $request->phone || auth()->user()->email != $request->email)
        ){
            return back()->with('error', __('text.validation_phrase1'));
        }
        
        $data = $request->all();
        Students::find(auth()->id())->update($data);
        return redirect(route('student.home'))->with('success', __('text.word_Done'));
    }
    public function stock_report(Request $request)
    {
        # code...
        $data['title'] = "Stock Report For ".Batch::find($request->year)->name;
        $data['stock'] = StudentStock::where(['student_id'=>auth()->id()])->where(['year_id'=>$request->year])->get();
        return view('student.stock-report', $data);
    }

}
