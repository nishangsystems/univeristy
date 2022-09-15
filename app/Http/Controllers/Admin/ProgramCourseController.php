<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Program;
use App\Models\ProgramCourse;
use App\Models\SchoolUnits;
use App\Models\Subjects;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProgramCourseController extends Controller
{

    public function create()
    {
        # code...
        // return view();
    }

    // store a program-course
    public function store()
    {
        # code...
        request()->validate([
            'program_id'=>'required',
            'course_id'=>'required',
            'school_level_id'=>'required',
            'degree_semester_id'=>'required'
        ]);

        try {
            // Make sure a course is not assigned to a program twice
            if (count(ProgramCourse::where('program_id', request('program_id'))->where('course_id', request('course_id'))->get())>0) {
                # code...
                return back()->with('error', 'Course '.Course::find(request('course_id'))->code.' already assign to '.Program::find(request('program_id'))->name);
            }
            $program_course = new ProgramCourse(request()->all());
            $program_course->save();
            return redirect(route('courses.index').'?program_id='.request('program_id'));
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('error', $th->getMessage());
        }
    }

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
        $data['title'] = 'Edit ' . $data['subject']->name . ' for ' . $data['parent']->name;
        return view('admin.class_subjects.edit')->with($data);
    }

    /**
     * update class subject
     */
    public function update(Request $request, $section_id, $id)
    {

        $program_course = ProgramCourse::where('program_id', $section_id)->where('course_id', $id)->first();
        $program_course->update([
            'school_level_id' => $request->school_level_id,
            'degree_semester_id' => $request->degree_semester_id,
            'credit_value'=>request('credit_value') ?? null,
        ]);
        return redirect()->route('admin.units.subjects', $section_id)->with('success', 'Updated program course successfully');
    }
    
    // unassign a program course
    public function delete()
    {
        $program_course = ProgramCourse::find('program_course_id');
        // do not unassign a program_course with results
        if (count($program_course->results())>0) {
            # code...
            return back()->with('error', 'Can not unassign a program course having results');
        }
        $program_course->delete();
        return redirect(route('courses.index').'?program_id='.$program_course->program_id);

    }
}
