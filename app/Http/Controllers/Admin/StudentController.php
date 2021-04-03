<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Option;
use Illuminate\Http\Request;
use Session;

class StudentController extends Controller
{
  /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['departments'] = \App\Department::all();
        return view('admin.programs.department')->with($data);
    }

    public function programs($id)
    {     $data['dep_id'] = $id;
        $data['programs'] = \App\Department::find($id)->options;
        return view('admin.programs.index')->with($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {        $data['dep_id'] = $request->id;
            return view('admin.programs.create')->with($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $program = new \App\Option();
      $program->name = $request->name;
      $program->description = $request->content;
      $program->department_id = $request->department;
      $program->save();
        return redirect()->to(route('admin.department.programs', $program->department_id))->with('s', "Program was saved !");

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {

        $data['program'] = \App\Options::find($id);
	    return view('admin.programs.show')->with($data);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $data['program'] = \App\Options::find($id);
        return view('admin.programs.edit')->with($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id){

        $program = \App\Option::find($id);
        $program->name = $request->name;
        $program->description = $request->content;
        $program->save();
        return redirect()->to(route('admin.department.programs', $program->department_id))->with('s', $program->name." was updates Successfully!");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    	return back();
    }
}
