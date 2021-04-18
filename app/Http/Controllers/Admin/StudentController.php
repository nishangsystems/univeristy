<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudentClass;
use App\Models\Students;
use App\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Session;

class StudentController extends Controller{

    public function create(Request $request){
            $data['title'] = "Admit New Student";
            return view('admin.student.create')->with($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'phone' => 'required',
            'address' => 'nullable',
            'gender' => 'required',
            'section' => 'required',
        ]);
        try{
            \DB::beginTransaction();
            $input = $request->all();
            $input['password'] = Hash::make('password');
            $student = \App\Models\Students::create($input);

            $class = StudentClass::create([
                'student_id' => $student->id,
                'class_id' => $request->section,
                'year_id' => \App\Helpers\Helpers::instance()->getCurrentAccademicYear()
            ]);

            $student->admission_batch_id = $class->id;
            $student->save();
            DB::commit();
            return redirect()->to(route('admin.students.index', $request->section))->with('success', "Student saved successfully !");
        }catch(\Exception $e){
            DB::rollBack();
            echo $e;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $data['title'] = "Student Profile";
        $data['user'] = \App\Models\Students::find($id);
	    return view('admin.student.show')->with($data);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function edit(Request $request, $id){
        $data['title'] = "Edit Student Profile";
        $data['student'] = \App\Models\Students::find($id);
        return view('admin.student.edit')->with($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id){

        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'address' => 'nullable',
            'gender' => 'required',
            'section' => 'required',
        ]);
        try{
            \DB::beginTransaction();
            $input = $request->all();
            $student = Students::find($id);
            $student->update($input);
            $class = StudentClass::where('student_id', $student->id)->where('year_id', \App\Helpers\Helpers::instance()->getCurrentAccademicYear())->first();
            $class->class_id = $request->section;
            $class->save();
            DB::commit();
            return redirect()->to(route('admin.students.index', $request->section))->with('success', "Student saved successfully !");
        }catch(\Exception $e){
            DB::rollBack();
            echo $e;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    	$student = Students::find($id);
    	if($student->classes->count() > 1 || $student->result->count() > 0 || $student->payment->count() > 0){
            return redirect()->back()->with('error', "Student cant be deleted !");
        }
        $student->classes->first()->delete();
        $student->delete();
        return redirect()->back()->with('success', "Student deleted successfully !");
    }
}
