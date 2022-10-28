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
        $data['scholarships'] = Scholarship::paginate(5);
        $data['title'] = 'Available Schollarship';
        return view('admin.scholarship.index')->with($data);
    }

    /**
     * show a form to create  scholarship
     * 
     * 
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['title'] = 'Create Scholarship Award';
        return view('admin.scholarship.create')->with($data);
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

        $scholarship->status = 1;
        $scholarship->save();
        return redirect()->back()->with('success', 'Success! Scholarship saved successfully !');
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


        ]);
    }

    /**
     * show information abouta scholarrship reesource
     * 
     * @param int $id
     */
    public function show($id)
    {
        $data['data'] = Scholarship::findOrFail($id);
        $data['title'] = 'Scholarship Details';
        return view('admin.scholarship.show')->with($data);
    }

    /**
     * delete a scholarship
     * 
     * @param int $id
     */
    public function destroy($id)
    {
        $deleted = Scholarship::findOrFail($id)->delete();
        return back()->with('success', 'Scholarship deleted Successfully');
    }

    /**
     * show form to edit scholarship
     * 
     * @param int $id
     */
    public function edit($id)
    {
        $data['scholarship'] = Scholarship::findOrFail($id);
        $data['title'] = 'Edit Scholarship';
        return view('admin.scholarship.edit')->with($data);
    }

    /**
     * updatea scholarship
     * 
     * @param Illuminate\Http\Request
     * @param int $id
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'status' => 'required|numeric'
        ]);
        $updated = Scholarship::findOrFail($id)->update($request->all());
        return redirect()->route('admin.scholarship.index')->with('success', 'Scholarship updated successfully !');
    }
}
