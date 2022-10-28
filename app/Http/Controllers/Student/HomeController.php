<?php

namespace App\Http\Controllers\Student;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\ProgramLevel;
use App\Models\SchoolUnits;
use App\Models\Sequence;
use App\Models\Students;
use App\Models\StudentSubject;
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

    public function course_registration()
    {
        # code...
        $data['title'] = "Course Registration For " .Helpers::instance()->getSemester(Students::find(auth()->id())->program_id)->name." ".\App\Models\Batch::find(\App\Helpers\Helpers::instance()->getYear())->name;
        $data['student_class'] = ProgramLevel::find(\App\Models\StudentClass::where(['student_id'=>auth()->id()])->where(['year_id'=>\App\Helpers\Helpers::instance()->getYear()])->first()->class_id);
        $data['cv_total'] = ProgramLevel::find(auth()->user()->program_id)->program()->first()->max_credit;        
        
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
            'fraction' => Helpers::instance()->getSemester(auth()->user()->program_id)->courses_min_fee
        ];
        $data['min_fee'] = number_format($fee['total']*$fee['fraction']);
        $data['access'] = $fee['amount'] >= $data['min_fee'];
        return view('student.courses.register', $data);
    }

    public function form_b()
    {
        # code...
        $data['title'] = "Registered Courses ".Helpers::instance()->getSemester(Students::find(auth()->id())->program_id)->name." ".\App\Models\Batch::find(\App\Helpers\Helpers::instance()->getYear())->name;
        $data['student_class'] = ProgramLevel::find(\App\Models\StudentClass::where(['student_id'=>auth()->id()])->where(['year_id'=>\App\Helpers\Helpers::instance()->getYear()])->first()->class_id);
        $data['cv_total'] = ProgramLevel::find(auth()->user()->program_id)->program()->first()->max_credit;        
        
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
            'fraction' => Helpers::instance()->getSemester(auth()->user()->program_id)->courses_min_fee
        ];
        $data['min_fee'] = number_format($fee['total']*$fee['fraction']);
        $data['access'] = $fee['amount'] >= $data['min_fee'];
        return view('student.courses.form_b', $data);
    }

    public static function registerd_courses($year = null, $semester = null, $student = null )
    {
        try {
            //code...
            $_student = $student ?? auth()->id();
            $_year = $year ?? Helpers::instance()->getYear();
            $_semester = $semester ?? Helpers::instance()->getSemester(auth()->user()->program_id)->id;
            # code...
            $courses = StudentSubject::where(['student_courses.student_id'=>$_student])->where(['student_courses.year_id'=>$_year])
                    ->join('subjects', ['subjects.id'=>'student_courses.course_id'])->where(['subjects.semester_id'=>$_semester])
                    ->join('class_subjects', ['class_subjects.subject_id'=>'subjects.id'])->distinct()->orderBy('subjects.name')->get(['subjects.*', 'class_subjects.coef as cv', 'class_subjects.status as status']);
                    return response()->json(['ids'=>$courses->pluck('id'), 'cv_sum'=>collect($courses)->sum('cv'), 'courses'=>$courses]);
        } catch (\Throwable $th) {
            return $th->getMessage();
            
        }
    }

    public function class_subjects($level)
    {
        # code...
        
        try{
            $pl = DB::table('students')->find(auth()->id())->program_id;
            $program_id = \App\Models\ProgramLevel::find($pl)->program_id;
            $subjects = \App\Models\ProgramLevel::where(['program_levels.program_id'=>$program_id])->where(['program_levels.level_id'=>$level])
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
        $semester = Helpers::instance()->getSemester(auth()->user()->program_id)->id;
        $user = auth()->id();
        try {

            // DB::beginTransaction();
            $ids = \App\Models\StudentSubject::where(['student_id'=>$user])->where(['year_id'=>$year])->where(['semester_id'=>$semester])->pluck('id');
            foreach ($ids as $key => $value) {
                # code...
                StudentSubject::find($value)->delete();
            }
            if ($request->has('courses')) {
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
        return view('student.courses.form_b_template', $data);
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
}
