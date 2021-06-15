<?php

namespace App\Http\Controllers\Scholarship;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Scholarship;
use App\Models\Students;
use App\Models\User;
use App\Models\UserScholarship;
use Illuminate\Http\Request;

class UserScholarshipController extends Controller
{

    /**
     * list of student with scholars(scholars)
     */
    public function index()
    {
    }

    /**
     * store scholarship for students
     * @param Illuminate\Http\Request
     * @param int $id
     */
    public function store(Request $request, $id)
    {
        $this->validateRequest($request);
        $user_scholarship = new UserScholarship();
        $user_scholarship->user_id  = $id;
        $user_scholarship->scholarship_id = $request->scholarship_id;
        $user_scholarship->year = $request->year;
        $user_scholarship->save();
        return redirect()->route('admin.scholarship.eligible');
    }

    /**
     * show list of eligible student to award scholarship
     */
    public function students_eligible()
    {
        $students = Students::paginate(10);
        return view('admin.scholarship.eligible_students', compact('students'));
    }

    /**
     * show form to add user scholarship
     * @param int $id
     */
    public function create($id)
    {
        $student = Students::findOrFail($id);
        $scholarships = Scholarship::all();
        $years = Batch::all();
        return view('admin.scholarship.award', compact(['student', 'scholarships', 'years']));
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
