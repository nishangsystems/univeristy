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
use Illuminate\Support\Facades\DB;

class UserScholarshipController extends Controller
{

    private $select = [
        'students.id',
        'students.name',
        'students.email',
        'students.gender',
        'students.phone',
        'students.address',
        'scholarships.type',
        'scholarships.amount'
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
        return view('admin.scholarship.scholars')->with($data);
    }

    /**
     * get all schools per year
     * @param Illuminate\Http\Request
     */
    public function getScholarsPerYear(Request $request)
    {
        $data['years'] = Batch::all();
        $data['students'] = DB::table('student_scholarships')
            ->join('students', 'students.id', '=', 'student_scholarships.student_id')
            ->join('scholarships', 'scholarships.id', '=', 'student_scholarships.scholarship_id')
            ->join('batches', 'batches.id', '=', 'student_scholarships.batch_id')
            ->where('student_scholarships.batch_id', $request->year)
            ->select($this->select)->paginate(10);
        $data['title'] = 'Our Scholars';
        return view('admin.scholarship.scholars')->with($data);
    }

    /**
     * get all scholars
     * 
     */
    public function getScholars()
    {
        return DB::table('student_scholarships')
            ->join('students', 'students.id', '=', 'student_scholarships.student_id')
            ->join('scholarships', 'scholarships.id', '=', 'student_scholarships.scholarship_id')
            ->join('batches', 'batches.id', '=', 'student_scholarships.batch_id')
            ->select($this->select)->paginate(10);
    }
    /**
     * store scholarship for students
     * @param Illuminate\Http\Request
     * @param int $id
     */
    public function store(Request $request, $id)
    {
        $this->validateRequest($request);
        $user_scholarship = new StudentScholarship();
        $user_scholarship->student_id  = $id;
        $user_scholarship->scholarship_id = $request->scholarship_id;
        $user_scholarship->batch_id = $request->year;
        $user_scholarship->save();
        return redirect()->route('admin.scholarship.eligible')->with('success', 'Awarded Scholarship successfully !');
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
        $data['scholarships'] = Scholarship::all();
        $data['years'] = Batch::all();
        $data['title'] = 'Award Schoalrship to ' . $data['student']->name;
        return view('admin.scholarship.award')->with($data);
    }

    /**
     * validate the data
     * @param Illuminate\Http\Request
     */
    public function validateRequest($request)
    {
        return $request->validate([

            'scholarship_id' => 'required|numeric',
            'year' => 'required'
        ]);
    }
}
