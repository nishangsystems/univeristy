<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\ProgramLevel;
use App\Models\Subjects;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\SubjectNotes;

class SubjectNotesController extends Controller
{

    private $select = [
        'class_subjects.id',
        'class_id',
        'subject_id',
        'subjects.name',
        'class_subjects.coef',
        'code'
    ];

    /**
     * show subject details
     * @param integer $class_id,
     * @param integer $id
     * @return json
     */
    public function show($class_id, $id)
    {
        $class = ProgramLevel::find($class_id);

        $data['subject_info'] = $this->showSubject($class_id, $id);
        $data['notes'] = $this->getSubjectNotes($data['subject_info']->id);
        //   dd($data['notes']);
        $data['title'] = 'Course Material For '.Subjects::find($id)->name.' - '. $class->program()->first()->name.' : Level '.$class->level()->first()->level;
        return view('teacher.subject_detail')->with($data);
    }

    /**
     * show subject notes
     * @param integer  $id
     */
    public function publish_notes($id)
    {
        $data = request()->has('action') ? [
            'status' =>  0
        ] : [
            'status' =>  1
        ];
        $subject_note = SubjectNotes::findOrFail($id)->update($data);
        return  back()->with('success', 'Publish note successfully');
    }

    /**
     * upload subject notes
     * @param integer id
     * @param integer $class_id
     * @parame Illuminate\Http\Request
     * @return json
     */
    public function store(Request $request, $id, $class_id)
    {

        //validate file
        $uploaded_file = $request->validate([
            'file' => 'required|mimes:pdf,docx,odt,txt,ppt|max:2048',
        ]);
        $extension = $request->file("file")->getClientOriginalExtension();
        $name = $request->file('file')->getClientOriginalName();
        $path = time() . '.' . $extension;
        //store the file
        $request->file('file')->move('storage/SubjectNotes/', $path);
        //save the file in the database
        $notes = new SubjectNotes();
        $notes->class_subject_id = $id;
        $notes->note_path = $path;
        $notes->note_name = $name;
        $notes->type = $request->option;
        $notes->status = 0;
        $notes->batch_id = Batch::find(\App\Helpers\Helpers::instance()->getCurrentAccademicYear())->id;
        $notes->save();
        return  back()->with(['success' => 'File has been successfully uploaded']);
    }

    /**
     *  get subject details
     * 
     * @param integer $class_id
     * @param int $subject_id
     * @return json
     */
    public function showSubject($class_id, $subject_id)
    {
        $subject = DB::table('class_subjects')->whereNull('class_subjects.deleted_at')
            ->join('school_units', 'school_units.id', '=', 'class_subjects.class_id')
            ->join('subjects', 'subjects.id', '=', 'class_subjects.subject_id')
            ->where('school_units.id', $class_id)
            ->where('subjects.id', $subject_id)
            ->select($this->select)
            ->first();

        return $subject;
    }


    /**
     * get all notes for a subject offered by a student
     * 
     * @param integer subject_id
     * @return array
     */
    public function getSubjectNotes($id)
    {
        $batch_id = Batch::find(\App\Helpers\Helpers::instance()->getCurrentAccademicYear())->id;
        $notes = DB::table('subject_notes')
            ->join('class_subjects', 'class_subjects.id', '=', 'subject_notes.class_subject_id')
            ->where('class_subjects.id', $id)->whereNull('class_subjects.deleted_at')
            ->where('subject_notes.batch_id', $batch_id)
            ->select(
                'subject_notes.id as id',
                'subject_notes.note_name',
                'subject_notes.note_path',
                'subject_notes.type',
                'subject_notes.status',
                'subject_notes.created_at'
            )
            ->orderBy('id', 'DESC')
            ->paginate(5);
        return $notes;
        // return view('student.subject_notes')->with($data);
    }

    /**
     * delete subject note
     * @param int $id
     * 
     */
    public function destroy($id)
    {
        $deleted = SubjectNotes::findOrFail($id)->delete();
        return back()->with('success', 'Deleted note successfully');
    }
}
