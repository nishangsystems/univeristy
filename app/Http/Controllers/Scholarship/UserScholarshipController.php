<?php

namespace App\Http\Controllers\Scholarship;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Scholarship;
use App\Models\Students;
use App\Models\StudentScholarship;
use App\Models\User;
use App\Models\UserScholarship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserScholarshipController extends Controller
{

    private $select = [
        'students.id',
        'students.name',
        'students.matric',
        'students.campus_id',
        'students.program_id',
        'students.address',
        'student_scholarships.amount',
        'student_scholarships.reason',
        'student_scholarships.id as sc_id',
        'student_scholarships.batch_id as sc_year'
    ];
    /**
     * @param Illuminate\Http\Request
     * list of student with scholars(scholars)
     */
    public function index(Request $request)
    {
        $data['students'] = $this->getScholars();
        $data['years'] = Batch::all();
        $data['title'] = 'Our Scholars';
        return view('admin.scholarship.scholars',$data);
    }

    /**
     * get all schools per year
     * @param Illuminate\Http\Request
     */
    public function getScholarsPerYear(Request $request)
    {
        $data['years'] = Batch::all();
        $data['students'] = Students::join('student_scholarships', 'student_scholarships.student_id', '=', 'students.id')
            ->where(function($query){
                auth()->user()->campus_id != null ? $query->where('students.campus_id', '=', auth()->user()->campus_id) : null;
            })
            ->join('batches', 'batches.id', '=', 'student_scholarships.batch_id')
            ->where('student_scholarships.batch_id', $request->year)
            ->select(['students.*', 'student_scholarships.amount', 'student_scholarships.reason', 'student_scholarships.id as sc_id', 'student_scholarships.batch_id as sc_year'])->get();
            $data['title'] = 'Our Scholars';
        // return $data;
        return view('admin.scholarship.scholars')->with($data);
    }

    /**
     * get all scholars
     * 
     */
    public function getScholars()
    {

        return Students::join('student_scholarships', 'student_scholarships.student_id', '=', 'students.id')
                ->join('batches', 'batches.id', '=', 'student_scholarships.batch_id')->where(function($query){
                    auth()->user()->campus_id != null ? $query->where('students.campus_id','=', auth()->user()->campus_id): null;
                })
                ->select($this->select)->get();
    }
    /**
     * store scholarship for students
     * @param Illuminate\Http\Request
     * @param int $id
     */
    public function store(Request $request, $id)
    {
        // return $request->all();
        $this->validateRequest($request);
        $user_scholarship = new StudentScholarship();
        $user_scholarship->student_id  = $id;
        $user_scholarship->amount = $request->amount;
        $user_scholarship->batch_id = $request->year;
        $user_scholarship->reason = $request->reason;
        $user_scholarship->user_id = Auth::id();
        $user_scholarship->save();
        return redirect()->route('admin.scholarship.awarded_students')->with('success', 'Awarded Scholarship successfully !');
    }
    /**
     * update scholarship for students
     * @param Illuminate\Http\Request
     * @param int $id
     */
    public function update(Request $request, $id)
    {
        // return $request->all();
        $this->validateRequest($request);
        $user_scholarship = StudentScholarship::find($id);
        $user_scholarship->student_id  = $id;
        $user_scholarship->amount = $request->amount;
        $user_scholarship->batch_id = $request->year;
        $user_scholarship->reason = $request->reason;
        $user_scholarship->user_id = Auth::id();
        $user_scholarship->save();
        return back()->with('success', 'Awarded Scholarship successfully !');
    }

    /**
     * show list of eligible student to award scholarship
     */
    public function students_eligible()
    {
        $data['students'] = Students::paginate(10);
        $data['title'] = 'Eligible Students';
        return view('admin.scholarship.eligible_students')->with($data);
    }
    
    /**
     * show form to add user scholarship
     * @param int $id
     */
    public function create($id)
    {
        $data['student'] = Students::findOrFail($id);
        $data['scholarships'] = DB::table('scholarships')->where('status', 1)->get()->toArray();
        $data['years'] = Batch::all();
        $data['title'] = 'Award Scholarship to ' . $data['student']->name;
        return view('admin.scholarship.award')->with($data);
    }
    
    /**
     * show form to edit user scholarship
     * @param int $id
     */
    public function edit($id)
    {
        $data['scholarship'] = StudentScholarship::find($id);
        $data['student'] = $data['scholarship']->student;
        $data['scholarships'] = DB::table('scholarships')->where('status', 1)->get()->toArray();
        $data['years'] = Batch::all();
        $data['title'] = 'Award Scholarship to ' . $data['student']->name;
        return view('admin.scholarship.edit')->with($data);
    }

    /**
     * validate the data
     * @param Illuminate\Http\Request
     */
    public function validateRequest($request)
    {
        return $request->validate([

            'amount' => 'required|numeric',
            'year' => 'required'
        ]);
    }


    public function delete_scholarship(Request $request, $id)
    {
        # code...
        StudentScholarship::find($id)->delete();
        return back()->with('success', 'Done');
    }
}
