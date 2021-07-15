<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassSubject;
use App\Models\SchoolUnits;
use App\Models\Students;
use App\Models\Subjects;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProgramController extends Controller
{

    public function sections()
    {
        $data['title'] = "Sections";
        $data['parent_id'] = 0;
        $data['units'] = \App\Models\SchoolUnits::where('parent_id', 0)->get();
        return view('admin.units.sections')->with($data);
    }



    public function index($parent_id)
    {
        $data = [];
        $parent = \App\Models\SchoolUnits::find($parent_id);
        if (!$parent) {
            return  redirect(route('admin.sections'));
        }
        $units =  $parent->unit;
        $name = $parent->name;
        $data['title'] = ($units->count() == 0) ? "No Sub Units Available in " . $name : "All " . $units->first()->type->name . " > {$name}";
        $data['units']  = $units;
        $data['parent_id']  = $parent_id;
        return view('admin.units.index')->with($data);
    }

    public function show($parent_id)
    {
        $data = [];
        $parent = \App\Models\SchoolUnits::find($parent_id);
        if (!$parent) {
            return  redirect(route('admin.sections'));
        }
        $units =  $parent->unit();
        $data['title'] = ($units->count() == 0) ? "No Sub Units Available in " . $parent->name : "All " . $units->first()->type->name;
        $data['units']  = $units;
        $data['parent_id']  = $parent_id;
        return view('admin.units.show')->with($data);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'type' => 'required',
        ]);
        \DB::beginTransaction();
        try {
            $unit = new \App\Models\SchoolUnits();
            $unit->name = $request->input('name');
            $unit->unit_id = $request->input('type');
            $unit->parent_id = $request->input('parent_id');
            $unit->prefix = $request->input('prefix');
            $unit->suffix = $request->input('suffix');
            $unit->save();
            \DB::commit();
            return redirect()->to(route('admin.units.index', [$unit->parent_id]))->with('success', $unit->name . " Added to units !");
        } catch (\Exception $e) {
            \DB::rollback();
            echo ($e);
        }
    }

    public function edit(Request $request, $id)
    {
        $lang = !$request->lang ? 'en' : $request->lang;
        \App::setLocale($lang);
        $data['id'] = $id;
        $unit = \App\Models\SchoolUnits::find($id);
        $data['unit'] = $unit;
        $data['parent_id'] = \App\Models\SchoolUnits::find($id)->parent_id;
        $data['title'] = "Edit " . $unit->name;
        return view('admin.units.edit')->with($data);
    }

    public function create(Request $request, $parent_id)
    {
        $data['parent_id'] = $parent_id;
        $parent = \App\Models\SchoolUnits::find($parent_id);
        $data['title'] = $parent ? "New Sub-unit Under " . $parent->name : "New Section";
        return view('admin.units.create')->with($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'type' => 'required',
        ]);

        \DB::beginTransaction();
        try {
            $unit = \App\Models\SchoolUnits::find($id);
            $unit->name = $request->input('name');
            $unit->unit_id = $request->input('type');
            $unit->prefix = $request->input('prefix');
            $unit->suffix = $request->input('suffix');
            $unit->save();
            \DB::commit();

            return redirect()->to(route('admin.units.index', [$unit->parent_id]))->with('success', $unit->name . " Updated !");
        } catch (\Exception $e) {
            \DB::rollback();
            echo ($e);
        }
    }

    /**
     * Delete the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $slug
     * @return \Illuminate\Http\Response
     */
    public function destroy($slug)
    {
        $unit = \App\Models\SchoolUnits::find($slug);
        if ($unit->unit->count() > 0) {
            return redirect()->back()->with('error', "Unit cant be deleted");
        }
        $unit->delete();
        return redirect()->back()->with('success', "units deleted");
    }

    public function subjects($id)
    {
        $parent = \App\Models\SchoolUnits::find($id);
        $data['title'] = "Subjects under " . $parent->name;
        $data['parent'] = $parent;
        $data['subjects'] = $parent->subject()->paginate(15);
        //  dd($data['subjects']);
        return view('admin.units.subjects')->with($data);
    }

    public function manageSubjects($id)
    {
        $parent = \App\Models\SchoolUnits::find($id);
        $data['parent'] = $parent;
        $data['title'] = "Manage subjects under " . $parent->name;
        return view('admin.units.manage_subjects')->with($data);
    }

    public function students($id)
    {
        $parent = \App\Models\SchoolUnits::find($id);
        $data['parent'] = $parent;

        $data['title'] = "Manage student under " . $parent->name;
        return view('admin.units.student')->with($data);
    }

    public function saveSubjects(Request  $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'subjects' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $parent = \App\Models\SchoolUnits::find($id);

        foreach ($parent->subject as $subject) {
            $subject->delete();
        }

        foreach ($request->subjects as $subject) {
            \App\Models\ClassSubject::create([
                'class_id' => $id,
                'subject_id' => $subject
            ]);
        }

        $data['title'] = "Manage subjects under " . $parent->name;
        return redirect()->back()->with('success', "Subjects Saved Successfully");
    }
}
