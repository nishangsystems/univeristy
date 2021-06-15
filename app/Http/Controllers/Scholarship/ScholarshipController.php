<?php

namespace App\Http\Controllers\Scholarship;

use App\Http\Controllers\Controller;
use App\Models\Scholarship;
use Illuminate\Http\Request;

class ScholarshipController extends Controller
{
    private $scholarship_to_code = [
        '1' => 'Tuition Fee Only',
        '2' => 'Partial Tuition Fee',
        '3' => 'Boarding Fee',
        '4' => 'Partial Boarding Fee',
        '5' => 'Student Expenses(PTA, T-shirts, Sporting Materials)',
        '6' => 'Full Time'
    ];
    /**
     * list all available scholarship
     */
    public function index()
    {
        $scholarships = Scholarship::all();
        return view('admin.scholarship.index', compact('scholarships'));
    }

    /**
     * show a form to create  scholarship
     * 
     * 
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.scholarship.create');
    }

    /**
     * store a scholarship
     *  @param Illuminate\Http\Request
     */
    public function store(Request $request)
    {
        $this->validateRequest($request);
        $scholarship = new Scholarship();
        $scholarship->name = $request->name;
        $scholarship->amount = $request->amount;
        $scholarship->type = $this->scholarship_to_code[$request->type];
        $scholarship->description = $request->description;
        $scholarship->status = 1;
        $scholarship->save();
        return redirect('/admin/scholarships');
    }

    /**
     * validate request
     */
    public function validateRequest($request)
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'type' => 'required|numeric',
            'description' => 'string',
        ]);
    }
}
