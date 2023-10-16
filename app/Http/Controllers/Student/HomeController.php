<?php

namespace App\Http\Controllers\Student;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Controllers\TransactionController;
use App\Models\Batch;
use App\Models\CampusProgram;
use App\Models\CampusSemesterConfig;
use App\Models\Charge;
use App\Models\ClassSubject;
use App\Models\CourseNotification;
use App\Models\Income;
use App\Models\Material;
use App\Models\NonGPACourse;
use App\Models\Notification;
use App\Models\PayIncome;
use App\Models\PaymentItem;
use App\Models\Payments;
use App\Models\PlatformCharge;
use App\Models\ProgramLevel;
use App\Models\Resit;
use App\Models\Result;
use App\Models\SchoolUnits;
use App\Models\Semester;
use App\Models\Sequence;
use App\Models\StudentClass;
use App\Models\Students;
use App\Models\StudentStock;
use App\Models\StudentSubject;
use App\Models\SubjectNotes;
use App\Models\Subjects;
use App\Models\Topic;
use App\Models\Transaction;
use App\Models\Transcript;
use App\Models\TranzakCredential;
use App\Models\TranzakTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Throwable;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Exception;
use GuzzleHttp\Exception\ConnectException;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Http;

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
        $year = Batch::find($request->year ?? Helpers::instance()->getCurrentAccademicYear());
        $class = auth('student')->user()->_class($year->id);
        $semester = $request->semester ? Semester::find($request->semester) : Helpers::instance()->getSemester($class->id);
        // dd($request->all());
        if($class == null){
            return back()->with('error', "No result found. Make sure you were admitted to this institution by or before the selected academic year");
        }
        $data['title'] = "My CA Result";
        $data['user'] = auth('student')->user();
        $data['year'] = $year;
        $data['class'] = $class;
        $data['ca_total'] = $class->program()->first()->ca_total;
        $data['semester'] = $semester;
        $data['grading'] = $class->program()->first()->gradingType->grading()->get() ?? [];
        $res = auth('student')->user()->result()->where('results.batch_id', '=', $year->id)->where('results.semester_id', '=', $semester->id)->pluck('subject_id')->toArray();
        $data['subjects'] = $class->subjects()->whereIn('subjects.id', $res)->get();
        $results = array_map(function($subject_id)use($data, $year, $semester){
            return [
                'id'=>$subject_id,
                'code'=>Subjects::find($subject_id)->code ?? '',
                'name'=>Subjects::find($subject_id)->name ?? '',
                'status'=>Subjects::find($subject_id)->status ?? '',
                'coef'=>Subjects::find($subject_id)->coef ?? '',
                'ca_mark'=>auth('student')->user()->result()->where('results.batch_id', '=', $year->id)->where('results.subject_id', '=', $subject_id)->where('results.semester_id', '=', $semester->id)->first()->ca_score ?? '',
            ];
        }, $res);
        // dd($data['results']);
        $data['results'] = collect($results)->filter(function($el){return $el != null;});
        return view('student.ca-result')->with($data);
    }

    public function ca_result_download(Request $request)
    {
        $year = Batch::find($request->year ?? Helpers::instance()->getCurrentAccademicYear());
        $class = auth('student')->user()->_class($year->id);
        $semester = $request->semester ? Semester::find($request->semester) : Helpers::instance()->getSemester($class->id);
        // dd($semester);
        
        $data['title'] = "My CA Result";
        $data['user'] = auth('student')->user();
        $data['year'] = $year;
        $data['class'] = $class;
        $data['ca_total'] = $class->program()->first()->ca_total;
        $data['semester'] = $semester;
        $data['grading'] = $class->program()->first()->gradingType->grading()->get() ?? [];
        $res = auth('student')->user()->result()->where('results.batch_id', '=', $year->id)->where('results.semester_id', '=', $semester->id)->pluck('subject_id')->toArray();
        $data['subjects'] = $class->subjects()->whereIn('subjects.id', $res)->get();
        $results = array_map(function($subject_id)use($data, $year, $semester){
            return [
                'id'=>$subject_id,
                'code'=>Subjects::find($subject_id)->code ?? '',
                'name'=>Subjects::find($subject_id)->name ?? '',
                'status'=>Subjects::find($subject_id)->status ?? '',
                'coef'=>Subjects::find($subject_id)->coef ?? '',
                'ca_mark'=>auth('student')->user()->result()->where('results.batch_id', '=', $year->id)->where('results.subject_id', '=', $subject_id)->where('results.semester_id', '=', $semester->id)->first()->ca_score ?? '',
            ];
        }, $res);
        $data['results'] = collect($results)->filter(function($el){return $el != null;});
        // dd($data);
        // return view('student.templates.ca-result-template',$data);
        $pdf = PDF::loadView('student.templates.ca-result-template',$data);
        return $pdf->download(auth('student')->user()->matric.'_'.$semester->name.'_CA_RESULTS.pdf');
    }

    public function _ca_result_download(Request $request)
    {
        dd($request->all());
        $year = $request->year ?? Helpers::instance()->getCurrentAccademicYear();
        $semester = $request->semester ? Semester::find($request->semester) : Helpers::instance()->getSemester(Students::find(auth('student')->id())->_class(Helpers::instance()->getCurrentAccademicYear())->id);
        // dd($semester);
        $data['year'] = Batch::find($year);
        $data['title'] = "My CA Result";
        $data['user'] = auth('student')->user();
        $data['class'] = $data['user']->_class($year);
        $data['ca_total'] = auth('student')->user()->_class(Helpers::instance()->getCurrentAccademicYear())->program()->first()->ca_total;
        $data['semester'] = $semester;
        $data['grading'] = auth('student')->user()->_class(Helpers::instance()->getCurrentAccademicYear())->program()->first()->gradingType->grading()->get() ?? [];
        $res = auth('student')->user()->result()->where('results.batch_id', '=', $year)->where('results.semester_id', '=', $semester->id)->pluck('subject_id')->toArray();
        $data['subjects'] = Auth('student')->user()->_class(Helpers::instance()->getYear())->subjects()->whereIn('subjects.id', $res)->get();
        $data['results'] = array_map(function($subject_id)use($data, $year, $semester){
            return [
                'id'=>$subject_id,
                'code'=>$data['subjects']->where('id', '=', $subject_id)->first()->code ?? '',
                'name'=>$data['subjects']->where('id', '=', $subject_id)->first()->name ?? '',
                'status'=>$data['subjects']->where('id', '=', $subject_id)->first()->status ?? '',
                'coef'=>$data['subjects']->where('id', '=', $subject_id)->first()->coef ?? '',
                'ca_mark'=>auth('student')->user()->result()->where('results.batch_id', '=', $year)->where('results.subject_id', '=', $subject_id)->where('results.semester_id', '=', $semester->id)->first()->ca_score ?? '',
            ];
        }, $res);
        dd($data['results']);
        $pdf = PDF::loadView('student.templates.ca-result-template',$data);
        // return $pdf->render(auth('student')->user()->matric.'_'.$semester->name.'_CA_RESULTS.pdf');
        // return view('student.templates.ca-result-template')->with($data);
    }

    public function exam_result(Request $request)
    {
        
        // return $request->all();
        $year = Batch::find($request->year ?? Helpers::instance()->getCurrentAccademicYear());
        $class = auth('student')->user()->_class($year->id);
        $semester = $request->semester ? Semester::find($request->semester) : Helpers::instance()->getSemester($class->id);
        
        // check if results are published
        if(!$semester->result_is_published($year->id)){
            return back()->with('error', 'Results Not Yet Published For This Semester.');
        }

        // check if semester result fee is set && that student has payed 
        $plcharge = PlatformCharge::where(['year_id'=>$year->id])->first();
        $amount = $plcharge->result_amount ?? null;
        if($amount != null && $amount > 0){
            $charge = Charge::where(['year_id'=>$year, 'semester_id'=>$semester->id, 'student_id'=>auth('student')->id(), 'type'=>'RESULTS'])->first();
            if($charge == null){
                return redirect(route('student.result.pay'))->with('error', 'Pay Semester Result Charges to continue');
            }
        }

        if($class == null){
            return back()->with('error', "No result found. Make sure you were admitted to this institution by or before the selected academic year");
        }

        // CODE TO CHECK FOR PAYMENT OF REQUIRED PLATFORM PAYMENTS; WILL BE COMMENTED OUT TILL IT SHOULD TAKE EFFECT
        // if(){
        //     return back()->with('error', "You have not paid plaftorm or semester result charges for the selected semester");
        // }

        // END OF CHECK FOR PAYMENT OF REQUIRED PLATFORM PAYMENTS
        
        $data['title'] = "My Exam Result";
        $data['user'] = auth('student')->user();
        $data['semester'] = $semester;
        $data['class'] = $class;
        $data['year'] = $year;
        $data['ca_total'] = $class->program()->first()->ca_total;
        $data['exam_total'] = $class->program()->first()->exam_total;
        $data['grading'] = $class->program()->first()->gradingType->grading()->get() ?? [];
        $res = auth('student')->user()->result()->where('results.batch_id', '=', $year->id)->where('results.semester_id', $semester->id)->distinct()->pluck('subject_id')->toArray();
        $data['subjects'] = $class->subjects()->whereIn('subjects.id', $res)->get();
        $non_gpa_courses = Subjects::whereIn('code', NonGPACourse::pluck('course_code')->toArray())->pluck('id')->toArray();
        // $non_gpa_courses = [];
        // return $non_gpa_courses;
        $results = array_map(function($subject_id)use($data, $year, $semester){
            $ca_mark = auth('student')->user()->result()->where('results.batch_id', '=', $year->id)->where('results.subject_id', '=', $subject_id)->where('results.semester_id', '=', $semester->id)->first()->ca_score ?? 0;
            $exam_mark = auth('student')->user()->result()->where('results.batch_id', '=', $year->id)->where('results.subject_id', '=', $subject_id)->where('results.semester_id', '=', $semester->id)->first()->exam_score ?? 0;
            $total = $ca_mark + $exam_mark;
            $rol = [
                'id'=>$subject_id,
                'code'=>Subjects::find($subject_id)->code ?? '',
                'name'=>Subjects::find($subject_id)->name ?? '',
                'status'=>Subjects::find($subject_id)->status ?? '',
                'coef'=>Subjects::find($subject_id)->coef ?? '',
                'ca_mark'=>$ca_mark,
                'exam_mark'=>$exam_mark,
                'total'=>$total
            ];
            foreach ($data['grading'] as $key => $value) {
                # code...
                if ($total >= $value->lower && $total <= $value->upper) {
                    # code...
                    $grade = $value;
                    $rol['grade'] = $grade->grade;
                    $rol['remark'] = $grade->remark;
                    $rol['weight'] = $grade->weight;
                }
            }
            if(!array_key_exists('grade', $rol)){
                $rol['grade'] = null;
                $rol['remark'] = null;
                $rol['weight'] = null;
            }
            return $rol; 
            
            // dd($grade);
        }, $res);
        // dd($res);
        $data['results'] = collect($results)->filter(function($el){return $el != null;});
        $sum_cv = $data['results']->sum('coef');
        $sum_earned_cv = collect($results)->filter(function($el){return ($el != null) && ($el['ca_mark']+$el['exam_mark'] >= 50);})->sum('coef');
        $gpa_cv = $data['results']->whereNotIn('id', $non_gpa_courses)->sum('coef');
        $gpa_cv_earned = $data['results']->whereNotIn('id', $non_gpa_courses)->filter(function($el){return ($el != null) && ($el['ca_mark']+$el['exam_mark'] >= 50);})->sum('coef');
        $sum_gpts = $data['results']->whereNotIn('id', $non_gpa_courses)->sum(function($item){
            return $item['coef'] * $item['weight'];
        });
        $gpa = $sum_gpts/$gpa_cv;
        // dd($sum_gpts);
        $gpa_data['sum_cv'] = $sum_cv;
        $gpa_data['gpa_cv'] = $gpa_cv;
        $gpa_data['sum_cv_earned'] = $sum_earned_cv;
        $gpa_data['gpa_cv_earned'] = $gpa_cv_earned;
        $gpa_data['gpa'] = $gpa;

        $data['gpa_data'] = $gpa_data;

        $student = auth('student')->id();
        $fee = [
            'total_debt'=>auth('student')->user()->total_debts($year->id),
            'total_paid'=>auth('student')->user()->total_paid($year->id),
            'total' => auth('student')->user()->total($year->id),
            'balance' => auth('student')->user()->bal(null, $year->id),
            'total_balance' => auth('student')->user()->total_balance(null, $year->id),
            'fraction' => $semester->semester_min_fee
        ];
        // TOTAL PAID - TOTAL DEBTS FOR THIS YEAR = AMOUNT PAID FOR THIS YEAR
        $data['min_fee'] = $fee['total']*$fee['fraction'];
        $data['total_balance'] = auth('student')->user()->total_balance($student, $year->id);
        $data['access'] = ($fee['total'] - $fee['total_balance']) >= $data['min_fee'] || Students::find($student)->classes()->where(['year_id'=>$year->id, 'result_bypass_semester'=>$semester->id, 'bypass_result'=>1])->count() > 0;
        // dd($data);
        if ($class->program->background->background_name == "PUBLIC HEALTH") {
            # code...
            return view('student.public_health_exam_result')->with($data);
        }
        return view('student.exam-result')->with($data);
    }

    public function exam_result_download(Request $request)
    {
        // return $request->all();
        $year = $request->year ?? Helpers::instance()->getCurrentAccademicYear();
        $semester = $request->semester ? Semester::find($request->semester) : Helpers::instance()->getSemester(Students::find(auth('student')->id())->_class(Helpers::instance()->getCurrentAccademicYear())->id);
        $seqs = $semester->sequences()->get('id')->toArray();
        $data['title'] = "";
        $data['user'] = auth('student')->user();
        $data['class'] = $data['user']->_class($year);
        $data['year'] = $year;
        $data['semester'] = $semester;
        $data['ca_total'] = auth('student')->user()->_class($year)->program()->first()->ca_total;
        $data['exam_total'] = auth('student')->user()->_class($year)->program()->first()->exam_total;
        $data['grading'] = auth('student')->user()->_class($year)->program()->first()->gradingType->grading()->get() ?? [];
        $non_gpa_courses = NonGPACourse::pluck('id')->toArray();
        $res = auth('student')->user()->result()->where('results.batch_id', '=', $year)->where('results.semester_id', $semester->id)->distinct()->pluck('subject_id')->toArray();
        $data['subjects'] = Auth('student')->user()->_class($year)->subjects()->whereIn('subjects.id', $res)->get();
        $results = array_map(function($subject_id)use($data, $year, $semester){
            $ca_mark = auth('student')->user()->result()->where('results.batch_id', '=', $year)->where('results.subject_id', '=', $subject_id)->where('results.semester_id', '=', $semester->id)->first()->ca_score ?? 0;
            $exam_mark = auth('student')->user()->result()->where('results.batch_id', '=', $year)->where('results.subject_id', '=', $subject_id)->where('results.semester_id', '=', $semester->id)->first()->exam_score ?? 0;
            $total = $ca_mark + $exam_mark;
            $rol = [
                'id'=>$subject_id,
                'code'=>Subjects::find($subject_id)->code ?? '',
                'name'=>Subjects::find($subject_id)->name ?? '',
                'status'=>Subjects::find($subject_id)->status ?? '',
                'coef'=>Subjects::find($subject_id)->coef ?? '',
                'ca_mark'=>$ca_mark,
                'exam_mark'=>$exam_mark,
                'total'=>$total
            ];
            foreach ($data['grading'] as $key => $value) {
                # code...
                if ($total >= $value->lower && $total <= $value->upper) {
                    # code...
                    $grade = $value;
                    $rol['grade'] = $grade->grade;
                    $rol['remark'] = $grade->remark;
                    $rol['weight'] = $grade->weight;
                }
            }
            if(!array_key_exists('grade', $rol)){
                $rol['grade'] = null;
                $rol['remark'] = null;
                $rol['weight'] = null;

            }
            return $rol;
            
            // dd($grade);
        }, $res);

        $data['results'] = collect($results)->filter(function($el){return $el != null;});
        $sum_cv = $data['results']->sum('coef');
        $sum_earned_cv = collect($results)->filter(function($el){return ($el != null) && ($el['ca_mark']+$el['exam_mark'] >= 50);})->sum('coef');
        $gpa_cv = $data['results']->whereNotIn('id', $non_gpa_courses)->sum('coef');
        $gpa_cv_earned = $data['results']->whereNotIn('id', $non_gpa_courses)->filter(function($el){return ($el != null) && ($el['ca_mark']+$el['exam_mark'] >= 50);})->sum('coef');
        $sum_gpts = $data['results']->whereNotIn('id', $non_gpa_courses)->sum(function($item){
            return $item['coef'] * $item['weight'];
        });
        $gpa = $sum_gpts/$gpa_cv;
        // dd($gpa);
        $gpa_data['sum_cv'] = $sum_cv;
        $gpa_data['gpa_cv'] = $gpa_cv;
        $gpa_data['sum_cv_earned'] = $sum_earned_cv;
        $gpa_data['gpa_cv_earned'] = $gpa_cv_earned;
        $gpa_data['gpa'] = $gpa;

        $data['gpa_data'] = $gpa_data;


        $data['results'] = collect($results)->filter(function($el){return $el != null;});

        // dd($data);
        if ($data['class']->program->background->background_name == "PUBLIC HEALTH") {
            # code...
            $pdf = PDF::loadView('student.templates.public_health_exam_result',$data);
        }else{
            $pdf = PDF::loadView('student.templates.exam-result-template',$data);
        }
        return $pdf->download(auth('student')->user()->matric.'_'.$semester->name.'_EXAM_RESULTS.pdf');
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
        $user = auth('student')->user();
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->save();
        $data['user'] = auth('student')->user();
        return redirect()->back()->with(['s' => 'Phone Number and Email Updated Successfully']);
    }

    public function __construct()
    {
        // $this->middleware('isStudent');
        // $this->boarding_fee =  BoardingFee::first();
        //  $this->year = Batch::find(Helpers::instance()->getCurrentAccademicYear())->name;
        $this->batch_id = Batch::find(Helpers::instance()->getCurrentAccademicYear())->id;
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

        $batch_id = Batch::find(Helpers::instance()->getCurrentAccademicYear())->id;
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
        $data['title'] = "Course Registration For " .Helpers::instance()->getSemester(Students::find(auth('student')->id())->_class(Helpers::instance()->getCurrentAccademicYear())->id)->name ?? ''." ".Batch::find(Helpers::instance()->getYear())->name;
        $data['student_class'] = ProgramLevel::find(StudentClass::where(['student_id'=>auth('student')->id()])->where(['year_id'=>Helpers::instance()->getYear()])->first()->class_id);
        $data['cv_total'] = ProgramLevel::find(Students::find(auth('student')->id())->_class(Helpers::instance()->getCurrentAccademicYear())->id)->program()->first()->max_credit;        
        
        $student = auth('student')->id();
        $year = Helpers::instance()->getYear();
        $semester = Helpers::instance()->getSemester(Students::find(auth('student')->id())->_class(Helpers::instance()->getCurrentAccademicYear())->id);
        $_semester = Helpers::instance()->getSemester(Students::find(auth('student')->id())->_class(Helpers::instance()->getCurrentAccademicYear())->id)->background->semesters()->orderBy('sem', 'DESC')->first()->id;
        if ($semester->id == $_semester) {
            # code...
            return redirect(route('student.home'))->with('error', 'Resit registration can not be done here. Do that under "Resit Registration"');
        }


        $fee = [
            'amount' => array_sum(
                Payments::where('payments.student_id', '=', $student)
                ->join('payment_items', 'payment_items.id', '=', 'payments.payment_id')
                ->where('payment_items.name', '=', 'TUTION')
                ->where('payments.batch_id', '=', $year)
                ->pluck('payments.amount')
                ->toArray()
            ),
            'total' => 
                    CampusProgram::join('program_levels', 'program_levels.id', '=', 'campus_programs.program_level_id')
                    ->join('payment_items', 'payment_items.campus_program_id', '=', 'campus_programs.id')
                    ->where('payment_items.name', '=', 'TUTION')
                    ->whereNotNull('payment_items.amount')
                    ->join('students', 'students.program_id', '=', 'program_levels.id')
                    ->where('students.id', '=', $student)->pluck('payment_items.amount')[0] ?? 0,
            'fraction' => $semester->courses_min_fee
        ];
        $conf = CampusSemesterConfig::where(['campus_id'=>auth('student')->user()->campus_id])->where(['semester_id'=>$semester->id])->first();
        if ($conf != null) {
            # code...
            $data['on_time'] = strtotime($conf->courses_date_line) >= strtotime(date('d-m-Y'));
        }else{
            return redirect(route('student.home'))->with('error', 'Can not sign courses for this program at the moment. Date limit not set. Contact registry.');
        }
        // return __DIR__;
        // dd($data);
        $data['min_fee'] = number_format($fee['total']*$fee['fraction']);
        $data['access'] = ($fee['total'] + Students::find($student)->total_debts($year)) >= $data['min_fee']  || Students::find($student)->classes()->where(['year_id'=>Helpers::instance()->getCurrentAccademicYear()])->first()->bypass_result;
        return view('student.courses.register', $data);
    }

    public function resit_registration()
    {
        # code...
        $c_year = Helpers::instance()->getCurrentAccademicYear();
        $is_current_student = !(auth('student')->user()->_class($c_year) == null);
        $resit = Helpers::instance()->available_resit(auth('student')->user()->_class($c_year)->id ?? auth('student')->user()->_class()->id);
        if($resit == null){
            return back()->with('error', 'Resit not open');
        }
        
        if(!$is_current_student){
            return back()->with('error', 'You are not a current student');
        }
        $data['title'] = "Resit Registration For " .$resit->name??(Helpers::instance()->getSemester(auth('student')->user()->_class()->id)->name." ".Batch::find(Helpers::instance()->getCurrentAccademicYear())->name);
        $data['student_class'] =  auth('student')->user()->_class($is_current_student ? $c_year : null);
        $data['cv_total'] = auth('student')->user()->_class($is_current_student ? $c_year : null)->program()->first()->max_credit;
        // resit course price is set in campus_programs table //
        // campus_class = $data['student_class']->campus_programs()->where('campus_id', '=', auth('student')->user()->campus_id)
        
        $student = auth('student')->id();
        $year = Helpers::instance()->getYear();
        $data['unit_cost'] = auth('student')->user()->_class($year)->program->resit_cost ?? null;
        if($data['unit_cost'] == null){
            return back()->with('error', "Resit price not set. Contact administration");
        }
        $fee = [
            'amount' => array_sum(
                Payments::where('payments.student_id', '=', $student)
                ->join('payment_items', 'payment_items.id', '=', 'payments.payment_id')
                ->where('payment_items.name', '=', 'TUTION')
                ->where('payments.batch_id', '=', $year)
                ->pluck('payments.amount')
                ->toArray()
            ),
            'total' => 
                    CampusProgram::join('program_levels', 'program_levels.id', '=', 'campus_programs.program_level_id')
                    ->join('payment_items', 'payment_items.campus_program_id', '=', 'campus_programs.id')
                    ->where('payment_items.name', '=', 'TUTION')
                    ->whereNotNull('payment_items.amount')
                    ->join('students', 'students.program_id', '=', 'program_levels.id')
                    ->where('students.id', '=', $student)->pluck('payment_items.amount')[0] ?? 0,
            'fraction' => Helpers::instance()->getSemester(auth('student')->user()->_class($c_year)->id)->courses_min_fee
        ];
        // $conf = CampusSemesterConfig::where(['campus_id'=>auth('student')->user()->campus_id])->where(['semester_id'=>Helpers::instance()->getSemester(auth('student')->user()->_class($c_year)->id)->id])->first();
        // if ($conf != null) {
        //     # code...
        //     $data['on_time'] = strtotime($conf->courses_date_line) >= strtotime(date('d-m-Y', time()));
        // }else{
        //     return redirect(route('student.home'))->with('error', 'Can not sign courses for this program at the moment. Date limit not set. Contact registry.');
        // }
        $data['min_fee'] = number_format($fee['total']*$fee['fraction']);
        $data['access'] =  Helpers::instance()->resit_available(auth('student')->user()->_class($c_year)->id);
        $data['unpaid'] = StudentSubject::where(['resit_id'=>$resit->id, 'student_id'=>auth('student')->id(), 'year_id'=>$c_year])->whereNull('paid')->join('subjects', 'subjects.id', '=', 'student_courses.course_id')->distinct()->get(['subjects.*'])->count();
        if($data['access']){
            $data['resit_id'] =  $resit->id;
            // dd($data['access']);
            return view('student.resit.register', $data);
        }else{return back()->with('error', "Resit not open.");}
    }

    
    public function register_resit(Request $request)//takes class course id
    {
        # code...
        // first clear all registered courses for the year, semester, student then rewrite
        $year = Helpers::instance()->getYear();
        $resit_id = $request->resit_id;
        try {
            $user = auth('student')->id();


            $courses = array_unique($request->courses == null ? [] : $request->courses);
            $student_courses = StudentSubject::where(['student_id'=>$user, 'year_id'=>$year, 'resit_id'=>$resit_id]);
            $old_course_ids = $student_courses->pluck('course_id')->toArray();
            // DB::beginTransaction();
            $new_courses = array_filter($courses, function($el)use($old_course_ids){
                return !in_array($el, $old_course_ids);
            } );
            $dropped_courses = array_filter($old_course_ids, function($el)use($courses){
                return !in_array($el, $courses);
            });
            foreach (StudentSubject::where(['student_id'=>$user, 'year_id'=>$year, 'resit_id'=>$resit_id])->whereIn('course_id', $dropped_courses)->get() as $key => $value) {
                # code...
                $value->delete();
            }
            // return $request->all();
            # code...
            foreach ($new_courses as $key => $value) {
                # code...
                StudentSubject::create(['year_id'=>$year, 'resit_id'=>$resit_id, 'student_id'=>$user, 'course_id'=>$value]);
            }
            // DB::commit();


            return back()->with('success', "!Done");
        } catch (\Throwable $th) {
            // DB::rollBack();
            return back()->with('error', $th->getFile().' : '.$th->getLine().' :: '.$th->getMessage());
        }
    }
    
    public static function registered_resit_courses($year = null, $student = null )
    {
        try {
            //code...
            $_student = $student ?? auth('student')->id();
            $_year = $year ?? Helpers::instance()->getCurrentAccademicYear();
            // get resit semester for the student's background
            $resit_id = request('resit_id');
            # code...
            $courses = StudentSubject::where(['student_courses.student_id'=>$_student, 'student_courses.year_id'=>$_year, 'student_courses.resit_id'=>$resit_id])
                    ->join('subjects', ['subjects.id'=>'student_courses.course_id'])
                    ->distinct()->orderBy('subjects.name')->get(['subjects.*', 'subjects.coef as cv', 'subjects.status as status']);
            return response()->json(['ids'=>$courses->pluck('id'), 'cv_sum'=>collect($courses)->sum('cv'), 'courses'=>$courses]);
        } catch (\Throwable $th) {
            return $th->getMessage();
            
        }
    }

    public function form_b()
    {
        # code...
        $data['title'] = "Registered Courses ".Helpers::instance()->getSemester(auth('student')->user()->classes()->where(['year_id'=>Helpers::instance()->getCurrentAccademicYear()])->first()->class_id)->name." ".\App\Models\Batch::find(Helpers::instance()->getYear())->name;
        $data['student_class'] = ProgramLevel::find(\App\Models\StudentClass::where(['student_id'=>auth('student')->id()])->where(['year_id'=>Helpers::instance()->getYear()])->first()->class_id);
        $data['cv_total'] = ProgramLevel::find(auth('student')->user()->_class(Helpers::instance()->getCurrentAccademicYear())->id)->program()->first()->max_credit;        
        
        $student = auth('student')->id();
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
            'fraction' => Helpers::instance()->getSemester(auth('student')->user()->_class(Helpers::instance()->getCurrentAccademicYear())->id)->courses_min_fee
        ];
        $data['min_fee'] = number_format($fee['total']*$fee['fraction']);
        $data['access'] = ($fee['total'] + Students::find($student)->total_debts($year)) >= $data['min_fee']  || Students::find($student)->classes()->where(['year_id'=>Helpers::instance()->getCurrentAccademicYear()])->first()->bypass_result;
        return view('student.courses.form_b', $data);
    }

    public function registered_courses(Request $request)
    {
        # code...
        $data['title'] = "Registered Courses ".Helpers::instance()->getSemester(Students::find(auth('student')->id())->_class(Helpers::instance()->getCurrentAccademicYear())->id)->name." ".\App\Models\Batch::find(Helpers::instance()->getYear())->name;
        $data['student_class'] = ProgramLevel::find(\App\Models\StudentClass::where(['student_id'=>auth('student')->id()])->where(['year_id'=>Helpers::instance()->getYear()])->first()->class_id);
        $data['cv_total'] = ProgramLevel::find(Students::find(auth('student')->id())->_class(Helpers::instance()->getCurrentAccademicYear())->id)->program()->first()->max_credit;        
        
        $student = auth('student')->id();
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
            'fraction' => Helpers::instance()->getSemester(Students::find(auth('student')->id())->_class(Helpers::instance()->getCurrentAccademicYear())->id)->courses_min_fee
        ];
        $data['min_fee'] = number_format($fee['total']*$fee['fraction']);
        $data['access'] = (($fee['total'] + Students::find($student)->total_debts($year)) >= $data['min_fee']) || Students::find($student)->classes()->where(['year_id'=>Helpers::instance()->getCurrentAccademicYear()])->first()->bypass_result;
        return view('student.courses.index', $data);
    }


    public static function registerd_courses($year = null, $semester = null, $student = null )
    {
        try {
            //code...
            $_student = $student ?? auth('student')->id();
            $_year = $year ?? Helpers::instance()->getYear();
            $_semester = $semester ?? Helpers::instance()->getSemester(Students::find(auth('student')->id())->_class(Helpers::instance()->getCurrentAccademicYear())->id)->id;
            # code...
            $courses = StudentSubject::where(['student_courses.student_id'=>$_student])->where(['student_courses.year_id'=>$_year])
                    ->join('subjects', ['subjects.id'=>'student_courses.course_id'])->where(['subjects.semester_id'=>$_semester])
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
            // $pl = DB::table('students')->find(auth('student')->id())->program_id;
            // $program = ProgramLevel::find($pl);
            $subjects = Subjects::where(function($q)use($request){
                            $q->where('subjects.code', 'like', '%'.$request->value.'%')
                            ->orWhere('subjects.name', 'like', '%'.$request->value.'%');
                        })
                        ->select(['subjects.*', 'subjects.coef as cv', 'subjects.status as status'])->orderBy('name')->distinct()->paginate(15);
            return $subjects;
        }
        catch(Throwable $th){return $th->getLine() . '  '.$th->getMessage();}
    }

    public function class_subjects($level)
    {
        # code...
        
        try{
            $pl = Students::find(auth('student')->id())->classes()->where('year_id', '=', Helpers::instance()->getCurrentAccademicYear())->first()->class_id;
            $program_id = ProgramLevel::find($pl)->program_id;
            $subjects = ProgramLevel::where(['program_levels.program_id'=>$program_id])->where(['program_levels.level_id'=>$level])
                        ->join('class_subjects', ['class_subjects.class_id'=>'program_levels.id'])->join('subjects', ['subjects.id'=>'class_subjects.subject_id'])
                        ->where(['subjects.semester_id'=>Helpers::instance()->getSemester($pl)->id])
                        ->get(['subjects.*', 'class_subjects.coef as cv', 'class_subjects.status as status'])->sortBy('name')->toArray();
            return $subjects;
        }
        catch(Throwable $th){return $th->getLine() . '  '.$th->getMessage();}
    }

    public function register_courses(Request $request)//takes class course id
    {
        // return $request->all();
        # code...
        // first clear all registered courses for the year, semester, student then rewrite
        $year = Helpers::instance()->getYear();
        $semester = Helpers::instance()->getSemester(Students::find(auth('student')->id())->_class(Helpers::instance()->getCurrentAccademicYear())->id)->id;
        $_semester = Helpers::instance()->getSemester(Students::find(auth('student')->id())->_class(Helpers::instance()->getCurrentAccademicYear())->id)->background->semesters()->orderBy('sem', 'DESC')->first()->id;
        $user = auth('student')->id();
        try {
            if ($semester == $_semester) {
                # code...
                return back()->with('error', 'Resit registration can not be done here. Do that under \"Resit Registration\"');
            }
            if ($request->has('courses')) {
                // DB::beginTransaction();
                $ids = StudentSubject::where(['student_id'=>$user])->where(['year_id'=>$year])->where(['semester_id'=>$semester])->pluck('id');
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
        $data['user'] = auth('student')->user();
        
        $pdf = PDF::loadView('student.courses.form_b_template',$data);
        return $pdf->download(auth('student')->user()->matric.'_FORM_B.pdf');
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
            ])->count() > 0 && (auth('student')->user()->phone != $request->phone || auth('student')->user()->email != $request->email)
        ){
            return back()->with('error', __('text.validation_phrase1'));
        }
        
        $data = $request->all();
        $data['parent_phone_number'] = $request->parent_phone_code .''. $request->parent_phone_number;
        // return $data;
        Students::find(auth('student')->id())->update($data);
        return redirect(route('student.home'))->with('success', __('text.word_Done'));
    }
    public function stock_report(Request $request)
    {
        # code...
        $data['title'] = "Stock Report For ".Batch::find($request->year)->name;
        $data['stock'] = StudentStock::where(['student_id'=>auth('student')->id()])->get();
        return view('student.stock-report', $data);
    }

    public function apply_transcript()
    {
        # code...
        // return 1111111;
        // if charges are not set
        if(PlatformCharge::where(['year_id'=>Helpers::instance()->getCurrentAccademicYear()])->count() == 0){
            $data['title'] = "Apply For Transcript";
            return view('student.transcript.apply', $data);
        }
        // check if student is former, platform charges set and payed,
        $class = auth('student')->user()->_class(Helpers::instance()->getCurrentAccademicYear());
        if($class == null){
            $_charges = Charge::where(['year_id'=>Helpers::instance()->getCurrentAccademicYear(),'type'=>'TRANSCRIPT', 'student_id'=>auth('student')->id(), 'used'=>0])->orderBy('id', 'DESC')->get();
            if($_charges->count() > 0){
                $data['charge_id'] = $_charges->first()->id;
                $data['title'] = "Apply For Transcript";
                return view('student.transcript.apply', $data);                
            }
            return redirect(route('student.transcript.pay'));
        } 
        else{
            $charge = PlatformCharge::where(['year_id'=>Helpers::instance()->getCurrentAccademicYear()])->first();
            if($charge == null || $charge->yearly_amount == 0 || $charge->yearly_amount == null){
                $data['title'] = "Apply For Transcript";
                return view('student.transcript.apply', $data);
            }else{
                $charges = Charge::where(['year_id'=>Helpers::instance()->getCurrentAccademicYear(), 'type'=>'PLATFORM', 'student_id'=>auth('student')->id(), 'used'=>0])->orderBy('id', 'DESC')->get();
                if($charges->count() > 0){
                    $data['charge_id'] = $charges->first()->id;
                    $data['title'] = "Apply For Transcript";
                    return view('student.transcript.apply', $data);
                }
                else{return redirect(route('student.platform_charge.pay'));}
            }
        }

    }

    public function apply_save_transcript(Request $request)
    {
        # code...
        $validator = Validator::make($request->all(),
        [
            'tel'=>'required|numeric|min:9',
            'amount'=>'required|numeric',
            // 'callback_url'=>'required|url',
            'student_id'=>'required|numeric',
            'year_id'=>'required|numeric',
            'payment_purpose'=>'required',
            'payment_id'=>'required|numeric'
        ]);
        # code...
        if($validator->fails()){
            return back()->with('error', $validator->errors()->first());
        }

        $data = [
            'config_id'=>$request->config_id,
            'student_id'=>auth('student')->id(),
            'status'=>auth('student')->user()->_class(Helpers::instance()->getCurrentAccademicYear()) == null ? 'FORMER' : 'CURRENT',
            'delivery_format'=>$request->delivery_format,
            'tel'=>$request->contact,
            'description'=>$request->description ?? null,
            'paid'=>true,
            'paid_by'=>'TRANZAK_MOMO',
            'year_id'=>$request->year_id??Helpers::instance()->getCurrentAccademicYear()
        ];

        session()->put(config('tranzak.tranzak.transcript_data'), $data);

        
        if(Helpers::instance()->payChannel() == 'tranzak'){
            return $this->tranzak_pay($request->payment_purpose, $request);
        }

        

        
        // create transcript application and save id for updates after payment is made
        // cache('__MOMO_TRANSCRIPT_DATA__', json_encode($data));

        // MAKE API CALL TO PERFORM PAYMENT OF APPLICATION FEE
        // check if token exist and hasn't expired or get new token otherwise
        if(cache('tranzak_api_token') == null or Carbon::parse(cache('tranzak_api_token_expire'))->isAfter(now())){
            // get and cache different token
            $response = Http::post(config('tranzak.tranzak.base').config('tranzak.tranzak.token'), ['appId'=>config('tranzak.tranzak.app_id'), 'appKey'=>config('tranzak.tranzak.api_key')]);
            if($response->status() == 200){
                cache(['tranzak_api_token'=> json_decode($response->body())->data->token]);
                cache(['tranzak_api_token_expire'=>Carbon::createFromTimestamp(time() + json_decode($response->body())->data->expiresIn)]);
            }
        }
        // Assumed there is a valid api token
        // Over to perform the payment request proper
        $headers = ['Authorization'=>'Bearer '.cache('tranzak_api_token')];
        $request_data = ['mobileWalletNumber'=>'237'.$request->tel, 'mchTransactionRef'=>'_trans_fee_'.time().'_'.random_int(1, 9999), "amount"=> $request->amount, "currencyCode"=> "XAF", "description"=>"Payment for transcript fee to ST LOUIS UNIVERSITY INSTITUTE"];
        $_response = Http::withHeaders($headers)->post(config('tranzak.tranzak.base').config('tranzak.tranzak.direct_payment_request'), $request_data);
        if($_response->status() == 200){
            // return json_decode($_response->body())->data;
            // save transaction and track it status

            session()->put('processing_tranzak_transaction_details', json_encode(json_decode($_response->body())->data));
            // return $this->pending_payment(array_push((), ['application_id']));
            return redirect()->to(route('student.tranzak.payment.processing', ['type'=>'TRANSCRIPT']))->with(['data'=>json_decode($_response->body())->data]);
        }else{
            return back()->with('error', $_response->body());
        }
    }

    public function transcript_history()
    {
        # code...
        $data['title'] = "Transcript History";
        $data['data'] = Transcript::where(['student_id' => auth('student')->id()])->orderBy('id', 'DESC')->get();
        return view('student.transcript.history', $data);
    }

    public function resit_index()
    {
        # code...
        $data['title'] = "My Resits";
        return view('student.resit.forms', $data);
    }

    public function resit_download(Request $request, $id)
    {
        # code...
        $resit = Resit::find($id);
        $unit_cost = auth('student')->user()->_class($resit->year_id)->program->resit_cost;
        $data['title'] = $resit->year->name.' - '.'RESIT FOR '.$resit->background->background_name.' [ FROM '.date('d-m-Y', strtotime($resit->start_date)).' TO '.date('d-m-Y', strtotime($resit->end_date)).' ]';
        $data['courses'] = $resit->courses(auth('student')->id())->get();
        $data['unit_cost'] = number_format($unit_cost ?? 0).' '.__('text.currency_cfa');
        if($data['unit_cost'] == null){
            $data['total_cost'] = '----';
        }else{
            $data['total_cost'] = number_format((float)$unit_cost * $data['courses']->count()).' '.__('text.currency_cfa');
        }
        // dd($data);
        // return view('student.resit.courses', $data); // <--- load your view into theDOM wrapper;
        $pdf = PDF::loadView('student.resit.courses', $data); // <--- load your view into theDOM wrapper;
        $fileName =  $data['title'].'_'.time().'.'. 'pdf' ; // <--giving the random filename,
        return $pdf->download($fileName);
    }

    // PAYMENT OF SCHOOL FEE, OTHER ITEMS, TRANSCRIPTS, RESIT; INTO THE SCHOOL ACCOUNT DIRECTLY

    
    public function resit_pay(Request $request)
    {
        # code...
        $validity = Validator::make($request->all(), [
            'tel'=>'required|numeric|min:9',
            'amount'=>'required|numeric',
            'student_id'=>'required|numeric',
            'year_id'=>'required|numeric',
            'payment_purpose'=>'required',
            'payment_id'=>'required|numeric'
        ]);
        if($validity->fails()){
            return back()->with('error', $validity->errors()->first());
        }
        
        // return $request->all();
        if(Helpers::instance()->payChannel() == 'tranzak'){
            $data = $request->all();
            session()->put(config('tranzak.tranzak.resit_data'), $data);
            return $this->tranzak_pay($request->payment_purpose, $request);
        }elseif(Helpers::instance()->payChannel() == 'momo'){
            // handle payment directly into a momo account
            return $this->pay_fee_momo($request);
        }
    }

    public function pay_fee()
    {
        # code...
        $data['title'] = "Pay Fee";
        $data['student'] = auth('student')->user();
        $data['balance'] = auth('student')->user()->bal(auth('student')->id());
        $data['scholarship'] = Helpers::instance()->getStudentScholarshipAmount(auth('student')->id());
        $data['total_fee'] = auth('student')->user()->total();

        if ($data['total_fee'] <= 0) {

            return redirect(route('student.home'))->with('error', 'Fee not set');
        }
        return view('student.pay_fee', $data);
    }

    public function pay_fee_momo(Request $request)
    {
        // return $request->all();
        # code...
        $validator = Validator::make($request->all(),
        [
            'tel'=>'required|numeric|min:9',
            'amount'=>'required|numeric',
            // 'callback_url'=>'required|url',
            'student_id'=>'required|numeric',
            'year_id'=>'required|numeric',
            'payment_purpose'=>'required',
            'payment_id'=>'required|numeric'
        ]);


        if ($validator->fails()) {
            # code...
            return back()->with('error', $validator->errors()->first());
        }

        try {
            //code...
            $data = $request->all();
            $response = Http::post(env('PAYMENT_URL'), $data);
            if(!$response->ok()){
                return back()->with('error', 'Operation failed. '.$response->__toString());
            }
            
            if($response->ok()){
            
                $_data['title'] = "Pending Confirmation";
                $_data['transaction_id'] = $response->collect()->first();
                return view('student.payment_waiter', $_data);
            }
        } 
        catch(ConnectException $e){
            return back()->with('error', $e->getMessage());
        }
        catch (Throwable $th) {
            return back()->with('error', $th->getMessage());
            // throw $th;
        }
    }

    public function pay_other_incomes(Request $request)
    {
        # code...
        $data['title'] = "Pay Other Incomes";
        return view('student.pay_others', $data);
    }

    public function pay_other_incomes_momo(Request $request)
    {
        # code...
        
        $this->pay_fee_momo($request);
    }

    public function complete_transaction(Request $request, $ts_id)
    {
        # code...

        $transaction = Transaction::where(['transaction_id'=>$ts_id])->first();
        if($transaction != null){
            // update transaction
            $transaction->status = "SUCCESSFUL";
            $transaction->financialTransactionId = $request->financialTransactionId;
            $transaction->save();
            // return $transaction;
            // update payment record
            // CHECK PAYMENT PURPOSE, EITHER 
            switch($transaction->payment_purpose){
                case 'TUTION':
                    $paymentInstance = new Payments();
                    $data = [
                        "payment_id"=>$transaction->payment_id,
                        "student_id"=>$transaction->student_id,
                        "batch_id"=>$transaction->year_id,
                        'unit_id'=>Students::find($transaction->student_id)->_class($transaction->year_id)->id,
                        "amount"=>$transaction->amount,
                        "reference_number"=>$transaction->transaction_id,
                        'user_id'=>0
                    ];
                    $paymentInstance->fill($data);
                    $paymentInstance->save();
                    try {
                        //code...
                        DB::beginTransaction();
                        // return $request->all();
                        $student = Students::find($transaction->student_id);
                        $total_fee = $student->total($transaction->student_id);
                        $balance =  $student->bal($transaction->student_id);
                        $debt = 0;
                        $_data = [];
                        
                        $__amount = $transaction['amount'];
                        
                        foreach (Batch::orderBy('name')->pluck('id')->toArray() as $key => $year_id) {
                            # code...
                            if($year_id > Helpers::instance()->getCurrentAccademicYear()) break;
                            $class = $student->_class($year_id);
                            if($class != null){
                                $cpid = $class->campus_programs->where('campus_id', $student->campus_id);
                                if($cpid != null){
                                    $payment_id = $year_id == Helpers::instance()->getCurrentAccademicYear() ? $transaction->payment_id : PaymentItem::where(['campus_program_id'=>$cpid->id, 'year_id'=>$year_id])->first()->id??null;
                                    $total_balance = $student->total_balance($student->id, $year_id);
                                    if($total_balance > 0){
                                        $amount = 0; $debt = 0;
                                        if($__amount >= $total_balance){
                                            $__amount -= $total_balance;
                                            $amount = $total_balance;
                                        }else{
                                            $amount = $__amount;
                                            $__amount = 0;
                                        }
                                        if($year_id == Helpers::instance()->getCurrentAccademicYear()){
                                            $debt = $__amount > 0 ? -$__amount : 0;
                                        }else{$debt = 0;}
            
                                        $data = [
                                            "payment_id" => $payment_id,
                                            "student_id" => $student->id,
                                            "unit_id" => $class->id,
                                            "batch_id" => $year_id,
                                            "amount" => $amount,
                                            // "date" => $request->date,
                                            'reference_number' => $request->reference_number.time().'_'.random_int(1000000, 99999999),
                                            'user_id' => auth('parents')->user()->id,
                                            'payment_year_id'=>Helpers::instance()->getCurrentAccademicYear(),
                                            'debt' => $debt,
                                            'paid_by' => 'MOMO',
                                            'created_at'=>date(DATE_ATOM, time()),
                                            'updated_at'=>date(DATE_ATOM, time())
                                        ];
                                        if ($data['reference_number'] == null || (Payments::where(['reference_number' => $data['reference_number']])->count() == 0)) {
                                            $_data[] = $data;
                                        }else{return back()->with('error', __('text.reference_already_exist'));}
                                    };
                                }
                            }
                        }
                        // dd($_data);
                        Payments::insert($_data);
                        DB::commit();
            
                    } catch (\Throwable $th) {
                        DB::rollBack();
                        return back()->with('error', $th->getMessage());
                    }
                    return redirect(route('student.pay_fee'))->with('success', 'Payment complete');
                    break;
                case 'OTHERS':
                    $incomeInstance = new PayIncome();
                    $data = [
                        'income_id'=>$transaction->payment_id,
                        'batch_id'=>$transaction->year_id,
                        'class_id'=>Students::find($transaction->student_id)->_class($transaction->year_id)->id,
                        'student_id'=>$transaction->student_id,
                        'user_id'=>0
                    ];
                    $incomeInstance->fill($data);
                    return redirect(route('student.pay_others'))->with('success', 'Payment complete');
                    break;
                case 'TRANSCRIPT':
                    $transcript_id = $transaction->payment_id;
                    if($transcript_id != null){
                        $transcript = Transcript::find($transcript_id);
                        $transcript->paid = true;
                        $transcript->save();
                        return redirect(route('student.transcript.history'))->with('success', 'Done');
                    }
                    return redirect(route('student.transcript.history'))->with('error', 'Operation Failed');
                    break;
                case 'RESIT':
                    $transcript_id = $transaction->payment_id;
                    if($transcript_id != null){
                        StudentSubject::where(['resit_id'=>$transaction->payment_id, 'year_id'=>$transaction->year_id, 'student_id'=>$transaction->student_id])->update(['paid'=>$transaction->id]);
                        return redirect(route('student.transcript.history'))->with('success', 'Done');
                    }
                    return redirect(route('student.transcript.history'))->with('error', 'Operation Failed');
                    break;
            }
        }
    }

    public function failed_transaction(Request $request, $ts_id)
    {
        # code...
        $transaction = Transaction::where(['transaction_id'=>$ts_id])->first();
        if($transaction != null){
            // update transaction
            $transaction->status = "FAILED";
            $transaction->financialTransactionId = $request->financialTransactionId;
            $transaction->save();
            switch($transaction->payment_purpose){
                case 'TRANSCRIPT':
                    DB::table('transcripts')->where(['student_id'=>$transaction->student_id, 'paid'=>0])->delete();
                    return redirect(route('student.transcript.history'))->with('error', 'Operation Failed');
                    // break;
                case 'TUTION':
                    return redirect(route('student.pay_fee'))->with('error', 'Transaction failed');
                case 'OTHERS':
                    return redirect(route('student.pay_others'))->with('error', 'Transaction failed');
                case 'RESIT':
                    return redirect(route('student.home'))->with('error', 'Transaction failed');
            }

            // redirect user
            return redirect(route('student.home'))->with('error', 'Operation failed.');
        }
    }

    // PAYMENTS FOR PLATFORM CHARGES, SEMESTER RESULT CHARGES AND TRANSCRIPT CHARGES(FOR FORMER STUDENTS ONLY) INTO THE COMPANY's ACCOUNT

    public function pay_semester_results(Request $request)
    {
        # code...
        $semester = $request->has('semester_id') ? Semester::find($request->semester_id) : Helpers::instance()->getSemester(auth('student')->user()->_class()->id);
        $plcharge = PlatformCharge::where(['year_id'=>$request->year_id ?? Helpers::instance()->getCurrentAccademicYear()])->first();
        $charge = $plcharge == null ? null : $plcharge->result_amount;
        if($charge == 0 || $charge == null){return back()->with('error', 'Semester result charges are not required.');}
        if(Charge::where(['year_id'=>$request->year_id ?? Helpers::instance()->getCurrentAccademicYear(), 'semester_id'=>$semester->id, 'student_id'=>auth('student')->id(), 'type'=>'RESULTS'])->count() > 0){
            return redirect(route('student.result.exam'))->with('message', 'Already paid SEMESTER RESULT CHARGES for specified semester');
        }
        $data['title'] = "Pay Semester Result Charges";
        $data['amount'] = $charge;
        $data['purpose'] = 'RESULTS';
        $data['year_id'] = $request->year_id ?? null;
        $data['semester'] = $semester;
        $data['semester_id'] = $request->semester_id ?? null;
        $data['payment_id'] = $request->semester_id ?? null;
        return view('student.platform.charges', $data);
    }

    public function pay_platform_charges(Request $request)
    {
        # code...
        $semester = Helpers::instance()->getSemester(auth('student')->user()->_class()->id);
        $charge = PlatformCharge::first();
        // if($charge == null || $charge->yearly_amount == null || $charge->yearly_amount == 0){return back()->with('error', 'Platform charges not set.');}
        $data['title'] = "Pay Platform Charges";
        $data['amount'] = $charge->yearly_amount;
        $data['purpose'] = 'PLATFORM';
        $data['year_id'] = $request->year_id ?? null;
        $data['semester_id'] = $semester->id;
        $data['semester'] = null;
        $data['payment_id'] = $charge->id;
        return view('student.platform.charges', $data);
    }

    public function pay_transcript_charges(Request $request)
    {
        // return 1234;
        # code...
        $charges = Charge::where(['type'=>'TRANSCRIPT', 'student_id'=>auth('student')->id(), 'used'=>0])->orderBy('id', 'DESC')->get();
        $c_class = auth('student')->user()->_class(Helpers::instance()->getCurrentAccademicYear());
        if($c_class == null){
            // dd($charges);
            if($charges->count() > 0){
                $data['charge_id'] = $charges->first()->id;
                $data['title'] = "Apply For Transcript";
                return view('student.transcript.apply', $data)->with('success', 'You have an unused transcript charges');
            }else{
                $charge = PlatformCharge::first();
                if($charge == null || $charge->transcript_amount == null || $charge->transcript_amount == 0){return back()->with('error', 'Transcript charges not set.');}
                $data['title'] = "Pay Transcript Charges";
                $data['amount'] = $charge->transcript_amount;
                $data['purpose'] = '_TRANSCRIPT_';
                $data['year_id'] = Helpers::instance()->getCurrentAccademicYear();
                $data['payment_id'] = 0;
                return view('student.platform.charges', $data);
            }
        }

        // Check if there are platform charges and if the student has payed
        $plcharge = PlatformCharge::first();
        if($plcharge == null || $plcharge->yearly_amount == 0 || $plcharge->yearly_amount == null){
            $data['title'] = "Apply For Transcript";
            return view('student.transcript.apply', $data);
        }
        else{
            // check if student has payed platform charges
            if($charges->count() == 0 || $charges->first()->yearly_amount == 0 || $charges->first()->yearly_amount == null){
                return redirect(route('student.platform_charge.pay'))->with('message', 'Pay PLATFORM CHARGES to proceed');
            }
            $data['title'] = "Apply For Transcript";
            $data['charge_id'] = $charges->first()->id;
            return view('student.transcript.apply', $data);
        }
    }

    public function pay_charges_save(Request $request)
    {
        # code...
        $validator = Validator::make($request->all(),
        [
            'tel'=>'required|numeric|min:9',
            'amount'=>'required|numeric',
            // 'callback_url'=>'required|url',
            'student_id'=>'required|numeric',
            'year_id'=>'required|numeric',
            'payment_purpose'=>'required',
            'payment_id'=>'required|numeric'
        ]);
        
        
        if ($validator->fails()) {
            # code...
            return back()->with('error', $validator->errors()->first());
        }
        // return $request->all();

        // BRIDGE PROCESS BY PAYING WITH TRANZAK
        {
            $data = $request->all();
            $data_key = $request->payment_purpose == '_TRANSCRIPT_' ? config('tranzak.tranzak._transcript_data') : config('tranzak.tranzak.platform_data');
            session()->put($data_key, $data);
            return $this->tranzak_pay($request->payment_purpose, $request);
        }

        try {
            //code...
            $data = $request->all();
            $response = Http::post(env('CHARGES_PAYMENT_URL'), $data);
            // dd($response->body());
            if(!$response->ok()){
                // throw $response;
                return back()->with('error', 'Operation failed. '.$response->body());
                // dd($response->body());
            }
            
            if($response->ok()){
            
                $_data['title'] = "Pending Confirmation";
                $_data['transaction_id'] = $response->collect()->first();
                // return $_data;
                return view('student.platform.payment_waiter', $_data);
            }
        } 
        catch(ConnectException $e){
            return back()->with('error', $e->getMessage());
        }
    }

    public function complete_charges_transaction(Request $request, $ts_id)
    {
        # code...

        $transaction = Transaction::where(['transaction_id'=>$ts_id])->first();
        if($transaction != null){
            // update transaction
            $transaction->status = "SUCCESSFUL";
            $transaction->is_charges = true;
            $transaction->financialTransactionId = $request->financialTransactionId;
            $transaction->save();
            // return $transaction;
            // update payment record
            // CHECK PAYMENT PURPOSE, EITHER 
            switch($transaction->payment_purpose){
                case 'PLATFORM':
                case 'RESULTS':
                    $charge = new Charge();
                    $data = [
                        "student_id"=>$transaction->student_id,
                        "year_id"=>$transaction->year_id,
                        'semester_id'=>$transaction->semester_id ?? Helpers::instance()->getSemester(Students::find($transaction->student_id)->_class($transaction->year_id)->id),
                        'type'=>$transaction->payment_purpose,
                        "item_id"=>$transaction->payment_id,
                        "amount"=>$transaction->amount,
                        "financialTransactionId"=>$request->financialTransactionId,
                    ];
                    $charge->fill($data);
                    $charge->save();
                    return redirect($transaction->payment_purpose == 'PLATFORM' ? route('student.transcript.apply') : route('student.result.exam'))->with('success', 'Payment complete');
                    break;

                case 'TRANSCRIPT':
                    // set used to 0 on transactions to indicate that the transcript associated to the transaction is not yet done.


                    $charge = new Charge();
                    $data = [
                        "student_id"=>$transaction->student_id,
                        "year_id"=>$transaction->year_id,
                        'semester_id'=>$transaction->semester_id ?? null,
                        'type'=>$transaction->payment_purpose,
                        "item_id"=>$transaction->payment_id,
                        "amount"=>$transaction->amount,
                        "financialTransactionId"=>$request->financialTransactionId,
                        'used'=>false
                    ];
                    $charge->fill($data);
                    $charge->save();
                    $_data['title'] = "Apply For Transcript";
                    $_data['charge_id'] = $charge->id;
                    return view('student.transcript.apply', $_data)->with('success', 'Payment complete');
                    break;

            }
        }
    }

    public function failed_charges_transaction(Request $request, $ts_id)
    {
        # code...
        $transaction = Transaction::where(['transaction_id'=>$ts_id])->first();
        if($transaction != null){
            // update transaction
            $transaction->status = "FAILED";
            $transaction->financialTransactionId = $request->financialTransactionId;
            $transaction->is_charges = 'true';
            $transaction->save();
            switch($transaction->payment_purpose){
                case 'TRANSCRIPT':
                case 'RESULTS':
                case 'PLATFORM':
                    // DB::table('transcripts')->where(['student_id'=>auth('student')->id(), 'paid'=>0])->delete();
                    return redirect(route('student.home'))->with('error', 'Operation Failed');
                    break;
            }

            // redirect user
            return redirect(route('student.home'))->with('error', 'Operation failed.');
        }
    }

    public function online_payment_history(Request $request)
    {
        # code...
        $year = $request->year ?? null;
        $filter = $request->fiilter ?? null;
        $user = auth('student')->user();
        $data['title'] = "My Transactions";
        $data['transactions'] = $user->transactions()->where(function($row){
            $row->where('payment_purpose', '=', 'PLATFORM')
                ->orWhere('payment_purpose', '=', 'RESULTS')
                ->orWhere('payment_purpose', '=', 'TRANSCRIPT');
        })
        ->where(['status'=>'SUCCESSFUL'])
        ->get();
        $data['fees'] = Payments::where('student_id', auth('student')->id())->whereNotNull('transaction_id')->get();
        $data['other_payments'] = PayIncome::where('student_id', auth('student')->id())->whereNotNull('transaction_id')->get();
        $data['transcripts'] = Transcript::where('student_id', auth('student')->id())->whereNotNull('transaction_id')->get();
        $data['charges'] = Charge::where('student_id', auth('student')->id())->whereNotNull('financialTransactionId')->get();
        // return $data;
        return view('student.online_payment_history', $data);
    }

    public function course_content_index(Request $request){
        $year = Helpers::instance()->getCurrentAccademicYear();
        $subject = Subjects::find($request->subject_id);
        $cl_sub = $subject->class_subject()->where(['class_id'=>auth('student')->user()->_class($year)])->first();
        $teachers = $cl_sub == null ? [] : $cl_sub->teachers;
        // dd($teachers);
        $data['title'] = "Course Content For [".$subject->code."]".$subject->name;
        $data['subject'] = $subject;
        $data['teachers'] = $teachers;
        $data['topics'] = Topic::where(['subject_id'=>$subject->id])->get();;
        return view('student.courses.content', $data);
    }
 
    public function resit_payment(Request $request){
        
        $data['title'] = "Payment For Resit Registration";
        $year = Helpers::instance()->getCurrentAccademicYear();
        $semester = Helpers::instance()->getSemester(Students::find(auth('student')->id())->_class(Helpers::instance()->getCurrentAccademicYear())->id)->background->semesters()->orderBy('sem', 'DESC')->first()->id;
        $user = auth('student')->id();
        try {
            if ($request->has('courses') || $request->has('resit_id')) {
                // DB::beginTransaction();
                // get id-array for already registered courses that are available in the request
                $registered_courses = StudentSubject::where(['student_id'=>$user, 'year_id'=>$year, 'semester_id'=>$semester])
                    ->where(function($query)use($request){
                        $request->resit_id == null ? $query->whereIn('course_id', array_unique($request->courses)) : $query->where('resit_id', $request->resit_id);
                    })->get();
                $already_registered_courses = $registered_courses->count();
                $registered_course_ids = $registered_courses->pluck('course_id')->toArray();
                $courses = $registered_course_ids;
                $unit_cost = auth('student')->user()->_class($year)->program->resit_cost;
                if($unit_cost == null){
                    return back()->with('error', "Resit price for resit not set. Contact administration");
                }
                $data['unit_cost'] = auth('student')->user()->_class($year)->program->resit_cost;
                $data['resit_id'] = $request->resit_id;
                $data['courses'] = StudentSubject::where(['resit_id'=>$request->resit_id, 'student_id'=>$user, 'year_id'=>$year])->whereNull('paid')->join('subjects', 'subjects.id', '=', 'student_courses.course_id')->distinct()->get(['subjects.*']);
                $data['quantity'] = $data['courses']->count();
                $data['amount'] = $data['quantity'] * $unit_cost;
                // return $data;
                return view('student.resit.payment', $data);
            }
            // DB::commit();
            return back()->with('error', "!Payment request has no courses");
        } catch (\Throwable $th) {
            // DB::rollBack();
            return back()->with('error', $th->getFile().' : '.$th->getLine().' :: '.$th->getMessage());
        }
    }


    // TRANZAK PAYMENT FOR FEE, RESULTS, OTHER_INCOME AND TRANSCRIPT, RESIT, 
    public function tranzak_processing(Request $request, $type)
    {
        # code...
        $data['title'] = "Processing Transaction";
        $data['item_type'] = $type;
        switch ($type) {
            case 'TRANSCRIPT':
                # code...
                $data['cache_token_key'] = config('tranzak.tranzak.transcript_token');
                $data['tranzak_app_id'] = config('tranzak.tranzak.transcript_app_id');
                $data['tranzak_api_key'] = config('tranzak.tranzak.transcript_api_key');
                $data['transaction_data'] = config('tranzak.tranzak.transcript_transaction');
                $data['transaction'] = session()->get($data['transaction_data']);
                break;
                
            case 'TUTION':
                # code...
                $data['cache_token_key'] = config('tranzak.tranzak.tution_token');
                $data['tranzak_app_id'] = config('tranzak.tranzak.tution_app_id');
                $data['tranzak_api_key'] = config('tranzak.tranzak.tution_api_key');
                $data['transaction_data'] = config('tranzak.tranzak.tution_transaction');
                $data['transaction'] = session()->get($data['transaction_data']);
                break;
                    
            case 'OTHERS':
                # code...
                $data['cache_token_key'] = config('tranzak.tranzak.others_token');
                $data['tranzak_app_id'] = config('tranzak.tranzak.others_app_id');
                $data['tranzak_api_key'] = config('tranzak.tranzak.others_api_key');
                $data['transaction_data'] = config('tranzak.tranzak.others_transaction');
                $data['transaction'] = session()->get($data['transaction_data']);
                break;
                    
            case 'RESIT':
                # code...
                $data['cache_token_key'] = config('tranzak.tranzak.resit_token');
                $data['tranzak_app_id'] = config('tranzak.tranzak.resit_app_id');
                $data['tranzak_api_key'] = config('tranzak.tranzak.resit_api_key');
                $data['transaction_data'] = config('tranzak.tranzak.resit_transaction');
                $data['transaction'] = session()->get($data['transaction_data']);
                break;
                    
            case 'PLATFORM':
                # code...
                $data['cache_token_key'] = config('tranzak.tranzak.platform_token');
                $data['tranzak_app_id'] = config('tranzak.tranzak.platform_app_id');
                $data['tranzak_api_key'] = config('tranzak.tranzak.platform_api_key');
                $data['transaction_data'] = config('tranzak.tranzak.platform_transaction');
                $data['transaction'] = session()->get($data['transaction_data']);
                break;
                    
            case '_TRANSCRIPT':
                # code...
                $data['cache_token_key'] = config('tranzak.tranzak._transcript_token');
                $data['tranzak_app_id'] = config('tranzak.tranzak._transcript_app_id');
                $data['tranzak_api_key'] = config('tranzak.tranzak._transcript_api_key');
                $data['transaction_data'] = config('tranzak.tranzak._transcript_transaction');
                $data['transaction'] = session()->get($data['transaction_data']);
                break;
            
        }
        // return $data;
        return view('student.momo.processing', $data);
        
    }

    public function tranzak_complete(Request $request, $type)
    {
        # code...
        try {
            //code...
            // return $request;
            switch ($request->status) {
                case 'SUCCESSFUL':
                    # code...
                    // save transaction and update application_form
                    DB::beginTransaction();
                    $transaction = ['request_id'=>$request->requestId??'', 'amount'=>$request->amount??'', 'currency_code'=>$request->currencyCode??'', 'purpose'=>$request->payment_purpose??'', 'mobile_wallet_number'=>$request->mobileWalletNumber??'', 'transaction_ref'=>$request->mchTransactionRef??'', 'app_id'=>$request->appId??'', 'transaction_id'=>$request->transactionId??'', 'transaction_time'=>$request->transactionTime??'', 'payment_method'=>$request->payer['paymentMethod']??'', 'payer_user_id'=>$request->payer['userId']??'', 'payer_name'=>$request->payer['name']??'', 'payer_account_id'=>$request->payer['accountId']??'', 'merchant_fee'=>$request->merchant['fee']??'', 'merchant_account_id'=>$request->merchant['accountId']??'', 'net_amount_recieved'=>$request->merchant['netAmountReceived']??''];
                    if(TranzakTransaction::where($transaction)->count() == 0){
                        $transaction_instance = new TranzakTransaction($transaction);
                        $transaction_instance->save();
                    }else{
                        $transaction_instance = TranzakTransaction::where($transaction)->first();
                    }
    
                    if($type == 'TRANSCRIPT'){
                        $trans = session()->get(config('tranzak.tranzak.transcript_data'));
                        $trans['transaction_id'] = $transaction_instance->id;
                        $trans['paid'] = 1;
                        if(Transcript::where($trans)->count() == 0)
                            (new Transcript($trans))->save();
                        $message = "Hello ".(auth('student')->user()->name??'').", You have successfully applied for transcript with ST. LOUIS UNIVERSITY INSTITUTE. You paid ".($transaction_instance->amount??'')." for this operation";
                        $this->sendSmsNotificaition($message, [auth('student')->user()->phone]);
                    }elseif($type == 'TUTION'){
                        $trans = session()->get(config('tranzak.tranzak.tution_data'));
                        $trans['transaction_id'] = $transaction_instance->id;
                        // (new Payments($trans))->save();

                        try {
                            //code...
                            DB::beginTransaction();
                            // return $request->all();
                            $student = Students::find($trans['student_id']);
                            $total_fee = $student->total($trans['student_id']);
                            $balance =  $student->bal($trans['student_id']);
                            $debt = 0;
                            $_data = [];
                            
                            $__amount = $transaction['amount'];
                            
                            // foreach (Batch::orderBy('name')->distinct()->pluck('id')->toArray() as $key => $year_id) {
                            //     # code...
                            //     if($year_id > Helpers::instance()->getCurrentAccademicYear()) break;
                            //     $class = $student->_class($year_id);
                            //     if($class != null){
                            //         $cpid = $class->campus_programs->where('campus_id', $student->campus_id)->first();
                            //         if($cpid != null){
                            //             $payment_id = $year_id == Helpers::instance()->getCurrentAccademicYear() ? $trans['payment_id'] : PaymentItem::where(['campus_program_id'=>$cpid->id, 'year_id'=>$year_id])->first()->id??null;
                            //             $total_balance = $student->total_balance($student->id, $year_id);
                            //             if($total_balance > 0){
                            //                 $amount = 0; $debt = 0;
                            //                 if($__amount >= $total_balance){
                            //                     $__amount -= $total_balance;
                            //                     $amount = $total_balance;
                            //                 }else{
                            //                     $amount = $__amount;
                            //                     $__amount = 0;
                            //                 }
                            //                 if($year_id == Helpers::instance()->getCurrentAccademicYear()){
                            //                     $debt = $__amount > 0 ? -$__amount : 0;
                            //                 }else{$debt = 0;}
                
                            //                 $data = [
                            //                     "payment_id" => $payment_id,
                            //                     "student_id" => $student->id,
                            //                     "unit_id" => $class->id,
                            //                     "batch_id" => $year_id,
                            //                     "amount" => $amount,
                            //                     // "date" => $request->date,
                            //                     'reference_number' => $request->reference_number.time().'_'.random_int(1000000, 99999999),
                            //                     'user_id' => auth('student')->id(),
                            //                     'payment_year_id'=>Helpers::instance()->getCurrentAccademicYear(),
                            //                     'debt' => $debt,
                            //                     'transaction_id'=>$transaction_instance->id,
                            //                     'paid_by' => auth('student')->id(),
                            //                     'created_at'=>date(DATE_ATOM, time()),
                            //                     'updated_at'=>date(DATE_ATOM, time())
                            //                 ];
                            //                 if ($data['reference_number'] == null || (Payments::where(['reference_number' => $data['reference_number']])->count() == 0)) {
                            //                     $_data[] = $data;
                            //                 }else{return back()->with('error', __('text.reference_already_exist'));}
                            //             };
                            //         }
                            //     }
                            // }

                            foreach (Batch::orderBy('name')->pluck('id')->toArray() as $key => $year_id) {
                                # code...
                                if($year_id > Helpers::instance()->getCurrentAccademicYear()) break;
                                $class = $student->_class($year_id);
                                if($class != null){
                                    $cpid = $class->campus_programs->where('campus_id', $student->campus_id)->first();
                                    if($cpid != null){
                                        $payment_id = $year_id == Helpers::instance()->getCurrentAccademicYear() ? $trans['payment_id'] : PaymentItem::where(['campus_program_id'=>$cpid->id, 'year_id'=>$year_id])->first()->id??null;
                                        $total_balance = $student->total_balance($student->id, $year_id);
                                        if($total_balance > 0){
                                            $amount = 0; $debt = 0;
                                            if($__amount >= $total_balance){
                                                $__amount -= $total_balance;
                                                $amount = $total_balance;
                                            }else{
                                                $amount = $__amount;
                                                $__amount = 0;
                                            }
                                            if($year_id == Helpers::instance()->getCurrentAccademicYear()){
                                                $debt = $__amount > 0 ? -$__amount : 0;
                                            }else{$debt = 0;}
                
                                            $data = [
                                                "payment_id" => $payment_id,
                                                "student_id" => $student->id,
                                                "unit_id" => $class->id,
                                                "batch_id" => $year_id,
                                                "amount" => $amount,
                                                'reference_number' => $request->reference_number.time().'_'.random_int(1000000, 99999999),
                                                'user_id' => auth('student')->id(),
                                                'payment_year_id'=>Helpers::instance()->getCurrentAccademicYear(),
                                                'debt' => $debt,
                                                'transaction_id'=>$transaction_instance->id,
                                                'paid_by' => auth('student')->id(),
                                                'created_at'=>date(DATE_ATOM, time()),
                                                'updated_at'=>date(DATE_ATOM, time())
                                            ];
                                            if ($data['reference_number'] == null || (Payments::where(['reference_number' => $data['reference_number']])->count() == 0)) {
                                                $_data[] = $data;
                                            }else{return back()->with('error', __('text.reference_already_exist'));}
                                        };
                                    }
                                }
                            }
                            // dd($_data);
                            Payments::insert($_data);
                            DB::commit();
                            $message = "Hello ".(auth('student')->user()->name??'').", You have successfully paid a sum of ".($transaction_instance->amount??'')." as part/all of TUTION for ".($transaction_instance->year->name??'')." ST. LOUIS UNIVERSITY INSTITUTE.";
                            $this->sendSmsNotificaition($message, [auth('student')->user()->phone]);
                
                        } catch (\Throwable $th) {
                            DB::rollBack();
                            // throw $th;
                            return back()->with('error', $th->getMessage().'('.$th->getLine().')');
                        }
                    }elseif($type == 'OTHERS'){
                        $trans = session()->get(config('tranzak.tranzak.others_data'));
                        $trans['transaction_id'] = $transaction_instance->id;
                        if(PayIncome::where($trans)->count() == 0)
                        ($instance = new PayIncome($trans))->save();
                        $message = "Hello ".(auth('student')->user()->name??'').", You have successfully paid a sum of ".($transaction_instance->amount??'')." as ".($instance->income->name??'')." for ".($transaction_instance->year->name??'')." ST. LOUIS UNIVERSITY INSTITUTE.";
                        $this->sendSmsNotificaition($message, [auth('student')->user()->phone]);
                    }elseif($type == 'RESIT'){
                        $trans = session()->get(config('tranzak.tranzak.resit_data'));
                        StudentSubject::where(['resit_id'=>$trans['payment_id'], 'student_id'=>$trans['student_id'], 'year_id'=>$trans['year_id']])->update(['paid'=>$transaction_instance->id]);
                        $message = "Hello ".(auth('student')->user()->name??'').", You have successfully paid a sum of ".($transaction_instance->amount??'')." as ".($trans['payment_purpose']??'')." for ".($transaction_instance->year->name??'')." ST. LOUIS UNIVERSITY INSTITUTE.";
                        $this->sendSmsNotificaition($message, [auth('student')->user()->phone]);
                    }elseif($type == 'PLATFORM'){
                        $trans = session()->get(config('tranzak.tranzak.platform_data'));
                        $data = ['student_id'=>$trans['student_id'], 'year_id'=>$trans['year_id'], 'type'=>'PLATFORM', 'item_id'=>$trans['payment_id'], 'amount'=>$transaction_instance->amount, 'financialTransactionId'=>$transaction_instance->transaction_id, 'used'=>1];
                        $instance = new Charge($data);
                        $instance->save();
                        $message = "Hello ".(auth('student')->user()->name??'').", You have successfully paid a sum of ".($transaction_instance->amount??'')." as ".($trans['payment_purpose']??'')." for ".($transaction_instance->year->name??'')." ST. LOUIS UNIVERSITY INSTITUTE.";
                        $this->sendSmsNotificaition($message, [auth('student')->user()->phone]);
                    }elseif($type == '_TRANSCRIPT'){
                        $trans = session()->get(config('tranzak.tranzak._transcript_data'));
                        $data = ['student_id'=>$trans['student_id'], 'year_id'=>$trans['year_id'], 'type'=>'TRANSCRIPT', 'item_id'=>$trans['payment_id'], 'amount'=>$transaction_instance->amount, 'financialTransactionId'=>$transaction_instance->transaction_id, 'used'=>1];
                        $instance = new Charge($data);
                        $instance->save();
                        $message = "Hello ".(auth('student')->user()->name??'').", You have successfully paid a sum of ".($transaction_instance->amount??'')." as ".($trans['payment_purpose']??'')." for ".($transaction_instance->year->name??'')." ST. LOUIS UNIVERSITY INSTITUTE.";
                        $this->sendSmsNotificaition($message, [auth('student')->user()->phone]);
                    }
                    DB::commit();
                    return redirect(route('student.home'))->with('success', "Payment successful.");
                    break;
                
                case 'CANCELLED':
                    # code...
                    // notify user
                    return redirect(route('student.home'))->with('message', 'Payment Not Made. The request was cancelled.');
                    break;
                
                case 'FAILED':
                    # code...
                    return redirect(route('student.home'))->with('error', 'Payment failed.');
                    break;
                
                case 'REVERSED':
                    # code...
                    return redirect(route('student.home'))->with('message', 'Payment failed. The request was reversed.');
                    break;
                
                default:
                    # code...
                    break;
            }

            return redirect(route('student.home'))->with('error', 'Payment failed. Unrecognised transaction status.');
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function tranzak_payment_history()
    {
        return $this->online_payment_history(request());
    }
 
    public function tranzak_pay_fee()
    {
        # code...
        $data['title'] = "Pay Fee";
        $data['student'] = auth('student')->user();
        $data['balance'] = auth('student')->user()->bal(auth('student')->id());
        $data['scholarship'] = Helpers::instance()->getStudentScholarshipAmount(auth('student')->id());
        $data['total_fee'] = auth('student')->user()->total();

        if ($data['total_fee'] <= 0) {

            return redirect(route('student.home'))->with('error', 'Fee not set');
        }
        return view('student.pay_fee', $data);
    } 

    public function tranzak_pay_other_incomes()
    {
        $data['title'] = "Pay Other Incomes";
        return view('student.pay_others', $data);
    }

    public function tranzak_pay_fee_momo(Request $request)
    {
        $validator = Validator::make($request->all(),
        [
            'tel'=>'required|numeric|min:9',
            'amount'=>'required|numeric',
            // 'callback_url'=>'required|url',
            'student_id'=>'required|numeric',
            'year_id'=>'required|numeric',
            'payment_purpose'=>'required',
            'payment_id'=>'required|numeric'
        ]);
        # code...
        if($validator->fails()){
            return back()->with('error', $validator->errors()->first());
        }

        $data = ["payment_id"=>$request->payment_id,"student_id"=>auth('student')->id(),"batch_id"=>$request->year_id,'unit_id'=>auth('student')->user()->_class()->id,"amount"=>$request->amount,"reference_number"=>'fee.tranzak_momo_payment_'.time().'_'.random_int(100000, 999999).'_'.auth('student')->id(), 'paid_by'=>'TRANZAK_MOMO'];
        session()->put(config('tranzak.tranzak.tution_data'), $data);

        return $this->tranzak_pay($request->payment_purpose, $request);
    }

    public function tranzak_pay_other_incomes_momo(Request $request)
    {
        $validator = Validator::make($request->all(),
        [
            'tel'=>'required|numeric|min:9',
            'amount'=>'required|numeric',
            // 'callback_url'=>'required|url',
            'student_id'=>'required|numeric',
            'year_id'=>'required|numeric',
            'payment_purpose'=>'required',
            'payment_id'=>'required|numeric'
        ]);
        # code...
        if($validator->fails()){
            return back()->with('error', $validator->errors()->first());
        }

        $data = ['income_id'=>$request->payment_id, 'batch_id'=>$request->year_id, 'class_id'=>auth('student')->user()->_class()->id, 'student_id'=>auth('student')->id(), 'paid_by'=>'TRANZAK_MOMO'];
        session()->put(config('tranzak.tranzak.others_data'), $data);

        return $this->tranzak_pay($request->payment_purpose, $request);
    }

    public function tranzak_pay(string $purpose, Request $request){

        $validator = Validator::make($request->all(),
        [
            'tel'=>'required|numeric|min:9',
            'amount'=>'required|numeric',
            // 'callback_url'=>'required|url',
            'student_id'=>'required|numeric',
            'year_id'=>'required|numeric',
            'payment_purpose'=>'required',
            'payment_id'=>'required|numeric'
        ]);
        
        // return cache('tranzak_credentials_token');

        // check if token exist and hasn't expired or get new token otherwise
        $student = auth('student')->user();
        // $cache_token_key = null;
        // $tranzak_app_id = null;
        // $tranzak_api_key = null;
        // $transaction_data = null;
        switch($request->payment_purpose){
            case "TRANSCRIPT":
                $cache_token_key = config('tranzak.tranzak.transcript_token');
                $tranzak_app_id = config('tranzak.tranzak.transcript_app_id');
                $tranzak_api_key = config('tranzak.tranzak.transcript_api_key');
                $transaction_data = config('tranzak.tranzak.transcript_transaction');
                break;
                
            case "TUTION":
                $cache_token_key = config('tranzak.tranzak.tution_token');
                $tranzak_app_id = config('tranzak.tranzak.tution_app_id');
                $tranzak_api_key = config('tranzak.tranzak.tution_api_key');
                $transaction_data = config('tranzak.tranzak.tution_transaction');
                break;

            case "OTHERS":
                $cache_token_key = config('tranzak.tranzak.others_token');
                $tranzak_app_id = config('tranzak.tranzak.others_app_id');
                $tranzak_api_key = config('tranzak.tranzak.others_api_key');
                $transaction_data = config('tranzak.tranzak.others_transaction');
                break;

            case "RESIT":
                $cache_token_key = config('tranzak.tranzak.resit_token');
                $tranzak_app_id = config('tranzak.tranzak.resit_app_id');
                $tranzak_api_key = config('tranzak.tranzak.resit_api_key');
                $transaction_data = config('tranzak.tranzak.resit_transaction');
                break;

            case "PLATFORM":
                $cache_token_key = config('tranzak.tranzak.platform_token');
                $tranzak_app_id = config('tranzak.tranzak.platform_app_id');
                $tranzak_api_key = config('tranzak.tranzak.platform_api_key');
                $transaction_data = config('tranzak.tranzak.platform_transaction');
                break;

            case "_TRANSCRIPT":
                $cache_token_key = config('tranzak.tranzak._transcript_token');
                $tranzak_app_id = config('tranzak.tranzak._transcript_app_id');
                $tranzak_api_key = config('tranzak.tranzak._transcript_api_key');
                $transaction_data = config('tranzak.tranzak._transcript_transaction');
                break;

        }
        
        // $tranzak_credentials = TranzakCredential::where('campus_id', $student->campus_id)->first();
        if(cache($cache_token_key) == null or Carbon::parse(cache($cache_token_key.'_expiry'))->isAfter(now())){
            // get and cache different token
            GEN_TOKEN:
            $response = Http::post(config('tranzak.tranzak.base').config('tranzak.tranzak.token'), ['appId'=>$tranzak_app_id, 'appKey'=>$tranzak_api_key]);
            if($response->status() == 200){
                // cache token and token expirationtot session
                cache([$cache_token_key => json_decode($response->body())->data->token]);
                cache([$cache_token_key.'_expiry'=>Carbon::createFromTimestamp(time() + json_decode($response->body())->data->expiresIn)]);
            }
        }
        // Assumed there is a valid api token
        // Moving to performing the payment request proper
        $headers = ['Authorization'=>'Bearer '.cache($cache_token_key)];
        $request_data = ['mobileWalletNumber'=>'237'.$request->tel, 'mchTransactionRef'=>'_'.str_replace(' ', '_', $request->payment_purpose).'_payment_'.time().'_'.random_int(1, 9999), "amount"=> $request->amount, "currencyCode"=> "XAF", "description"=>"Payment for {$request->payment_purpose} - ST LOUIS UNIVERSITY INSTITUTE."];
        $_response = Http::withHeaders($headers)->post(config('tranzak.tranzak.base').config('tranzak.tranzak.direct_payment_request'), $request_data);
        
        if($_response->status() == 200){
            // save transaction and track it status
            if($_response->collect()->toArray()['success'] == false){
                goto GEN_TOKEN;
            }
            // return $request->all();
            session()->put($transaction_data, json_decode($_response->body())->data);
            return redirect()->to(route('student.tranzak.processing', $purpose));
        }
    }

    public function tranzak_payment_processing()
    {
        # code...
        $data['title'] = "Processing Payment Request";
        $data['transaction'] = json_decode(session('processing_tranzak_transaction_details'));
        return view('student.momo.processing', $data);
    }

}
