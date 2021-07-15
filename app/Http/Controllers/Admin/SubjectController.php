<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SubjectController extends Controller
{

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'coef' => 'required',
        ]);
        $subject = new \App\Models\Subjects();
        $subject->name = $request->input('name');
        $subject->coef = $request->input('coef');
        $subject->save();
        return redirect()->to(route("admin.subjects.index",))->with('success', "Subject Created!");
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
        $data['title'] = "Create Subject";
        return view('admin.subject.create')->with($data);
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
        ]);

        $subject = \App\Models\Subjects::find($id);
        $subject->name = $request->input('name');
        $subject->coef = $request->input('coef');
        $subject->save();
        return redirect()->to(route('admin.subjects.index'))->with('success', "Subject Updated Successfully!");
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
