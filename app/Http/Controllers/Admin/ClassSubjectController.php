<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassSubject;
use App\Models\SchoolUnits;
use App\Models\Subjects;
use Illuminate\Http\Request;

class ClassSubjectController extends Controller
{
    public function  edit($parent_id, $subject_id)
    {
        $data['parent'] = SchoolUnits::find($parent_id);
        $data['subject'] = Subjects::find($subject_id);
        // dd($data['subject']);
        $data['title'] = 'Edit ' . $data['subject']->name . ' for ' . $data['parent']->name;
        return view('admin.units.class_subjects_edit')->with($data);
    }

    /**
     * update class subject
     */
    public function update(Request $request, $parent_id)
    {
        dd($request->all());
        $class_subject = ClassSubject::where('class_id', $parent_id)->where('subject_id', $subject_id);
        $class_subject->coef = $request->coef;
        $class_subject->save();
        return redirect()->route('admin.units.subjects', [$parent_id])->with('success', 'Updated class subject successfully');
    }
}
