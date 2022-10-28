<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use function Ramsey\Uuid\v1;

class SubjectController extends Controller
{

    public function next(Request $request)
    {
        
        # code...
        // $lid=$request->input('lid');
        // $bid=$request->input('background');
        // $sid=$request->input('semester');
        return redirect(route('admin.courses._create', [$request->lid, $request->semester,$request->background]));
    }

    public function _create(Request $request)
    {
        $data['title'] = 'Create '.\App\Models\Semester::find($request->semester)->name.' Course';
        return view('admin.subject._create', $data);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'coef' => 'required',
            'code'=>'required',
            'level'=>'required',
            'semester'=>'required',
            'status'=>'required',
        ]);
        if(\App\Models\Subjects::where('code', $request->input('code'))->count()>0){
            return back()->with('error', "Course code ".$request->input('code').' already exist');
        }
        $subject = new \App\Models\Subjects();
        $subject->name = $request->input('name');
        $subject->coef = $request->input('coef');
        $subject->code = $request->input('code');
        $subject->status = $request->input('status');
        $subject->level_id = $request->input('level');
        $subject->semester_id = $request->input('semester');
        $subject->save();
        return back()->with('success', "Subject Created!");
    }

    public function edit(Request $request, $id)
    {
        $data['subject'] = \App\Models\Subjects::find($id);
        $data['title'] = "Edit " . $data['subject']->name;
        return view('admin.subject.edit')->with($data);
    }

    public function show(Request $request, $id)
    {
        return redirect(route(
            'admin.subjects.index'
        ));
    }

    public function create()
    {
        //$data['title'] = "Create Subject";
        return view('admin.subject.create');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'coef' => 'required',
            'code'=>'required',
            'level'=>'required',
            'semester'=>'required',
            'status'=>'required',
        ]);
        if(\App\Models\Subjects::where('code', $request->input('code'))->count()>0 && \App\Models\Subjects::find($id)->code != $request->input('code')){
            return back()->with('error', "Course code ".$request->input('code').' already exist');
        }
        $subject = \App\Models\Subjects::find($id);
        $subject->name = $request->input('name');
        $subject->coef = $request->input('coef');
        $subject->code = $request->input('code');
        $subject->status = $request->input('status');
        $subject->level_id = $request->input('level');
        $subject->semester_id = $request->input('semester');
        $subject->save();
        return back()->with('success', "Subject Updated Successfully!");
    }

    /**
     * Delete the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $subject = \App\Models\Subjects::find($id);
        if ($subject->units->count() > 0) {
            return redirect()->to(route('admin.subjects.index'))->with('error', "Subject cant be deleted");
        }
        $subject->delete();
        return redirect()->to(route('admin.subjects.index'))->with('success', "subject deleted");
    }

    public function index(Request $request)
    {

        $data['title'] = "List of all Subjects";
        $data['subjects'] = \App\Models\Subjects::orderBy('name')->paginate(15);
        return view('admin.subject.index')->with($data);
    }
}
