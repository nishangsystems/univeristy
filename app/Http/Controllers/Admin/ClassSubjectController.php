<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassSubject;
use App\Models\SchoolUnits;
use App\Models\Subjects;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClassSubjectController extends Controller
{


    public function  edit($section_id, $id)
    {
        $data['parent'] = SchoolUnits::find($section_id);
        $data['subject'] = DB::table('class_subjects')
            ->join('school_units', ['school_units.id' => 'class_subjects.class_id'])
            ->join('subjects', ['subjects.id' => 'class_subjects.subject_id'])
            ->where('class_subjects.class_id', $section_id)
            ->where('class_subjects.subject_id', $id)
            ->select('class_subjects.subject_id', 'subjects.name', 'class_subjects.coef')
            ->first();
        $data['title'] = __('text.word_edit').' ' . $data['subject']->name . ' '.__('text.word_for').' ' . $data['parent']->name;
        return view('admin.class_subjects.edit')->with($data);
    }

    /**
     * update class subject
     */
    public function update(Request $request, $section_id, $id)
    {

        $class_subject = ClassSubject::where('class_id', $section_id)->where('subject_id', $id)->first();
        $class_subject->update([
            'coef' => $request->coef
        ]);
        return redirect()->route('admin.units.subjects', $section_id)->with('success', __('text.word_done'));
    }

    public function delete(Request $request, $program_level_id, $id)
    {
        # code...
        // return \App\Models\Result::where(['subject_id'=>$id])->count() + \App\Models\StudentSubject::where(['course_id'=>$id])->count();
        if((\App\Models\Result::where(['subject_id'=>$id])->count() == 0) && (\App\Models\StudentSubject::where(['course_id'=>$id])->count() == 0)){
            ClassSubject::where(['class_id'=>$program_level_id, 'subject_id'=>$id])->delete();
            return back()->with('success', __('text.word_done'));
        }
        return back()->with('error', __('text.x_phrase_1'));
    }
}
