<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SubjectController extends Controller{

    public function store(Request $request){

        $uploadDirectory = base_path() . "/site/img/subjects/";
        $this->validate($request, [
            'name' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,jpg|max:1024',
        ]);
        \DB::beginTransaction();
        try {
            $subject = new \App\Subject();
            $subject->name = $request->input('name');
            $date = new DateTime();
            $subject->slug = str_replace("/", "", Hash::make($request->input('name') . $date->format('Y-m-d H:i:s')));
            $subject->description = $request->input('description');
            $subject->logged_by = Auth::user()->id;
            $subject->examination_id = $request->input('exam_id');
            $subject->update_flag = '1';
            $image = "image_" . $subject->slug . "." . $request->file('image')->getClientOriginalExtension();
            $subject->image_name = $image;
            $subject->type = $request->input('flag');
            $subject->course_code = $request->input('course_code');

            $subject->save();
            \DB::commit();
            return redirect()->to(route("admin.subjects.index", [$request->input('exam_id'), $subject->type]))->with('s', "Subject Created!");
        } catch (\Exception $e) {
            \DB::rollback();
            echo($e);
            //   return redirect()->to(route('admin.subjects.index'))->with('s', "Subject Created!");
        }
    }

    public function edit(Request $request, $id)
    {

        $data['lang'] = !$request->input('lang') ? "en" : $request->input('lang');
        \App::setLocale($data['lang']);

        $data['subject'] = \App\Subject::find($id);
        $data['title'] = "Edit " . $data['subject']->name;
        if (\App\Subject::find($id)->type == \Config::get('config.exam')) {
            $data['exam_id'] = \App\Subject::find($id)->examination->id;
        } else {
            $data['exam_id'] = \App\Subject::find($id)->unit->id;
        }
        $data['flag'] = \App\Subject::find($id)->type;
        $data['languages'] = \App\Languages::get();
        return view('admin.subject.edit')->with($data);
    }

    public function create($exam_id, $flag)
    {
        $data['exam_id'] = $exam_id;
        $data['flag'] = $flag;
        if ($flag == \Config::get('config.exam')) {
            $data['title'] = "Create Subject for " . \App\Examination::find($exam_id)->name;
        } else {
            $data['title'] = "Create Subject for " . \App\SchoolUnit::find($exam_id)->name;
        }

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
        $uploadDirectory = base_path() . "/site/img/subjects/";
        $this->validate($request, [
            'name' => 'required',
        ]);
        \DB::beginTransaction();
        try {
            $subject = \App\Subject::find($id);
            $subject->name = $request->input('name');
            $subject->description = $request->input('description');
            $subject->logged_by = Auth::user()->id;

            $subject->save();
            \DB::commit();
            return redirect()->to(route('admin.subjects.index', [$subject->examination_id, $subject->type]))->with('s', "Subject Updated Successfully!");
        } catch (\Exception $e) {
            \DB::rollback();
            echo($e);
            //  return redirect()->to(route('admin.subjects.index'))->with('s', "Subject Created!");
        }

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
        $subject = \App\Subject::find($id);
        $subject->delete();
        return redirect()->to(route('admin.subjects.index'))->with('s', "subject deleted");
    }

    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(Request $request, $exam_id, $flag)
    {
        if ($flag == \Config::get('config.exam')) {
            $data['title'] = "List of all Sujects in " . \App\Examination::find($exam_id)->name;
            $data['subjects'] = \App\Examination::find($exam_id)->subject()->paginate(15);
        } else {
            $data['title'] = "List of all Sujects in " . \App\SchoolUnit::find($exam_id)->name;
            $data['subjects'] = \App\SchoolUnit::find($exam_id)->subject()->paginate(15);
        }
        $data['flag'] = $flag;
        $data['exam_id'] = $exam_id;

        return view('admin.subject.index')->with($data);
    }

}
