<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Batch;
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
        'coef',
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
        $data['subject'] = $this->showSubject($class_id, $id);
        $data['notes'] = $this->getSubjectNotes($data['subject']->id);
        $data['title'] = 'Subject Notes';
        return view('teacher.subject_detail')->with($data);
    }

    /**
     * show subject notes
     * @param integer subject $id
     */
    public function publish_notes($note_id)
    {
        dd($note_id);
        // $data = [
        //     'status' => 1
        // ];
        // $subject_note = SubjectNotes::find($note)->update($data);
        return  back()->with('success', 'Publish note successfully');
    }

    /**
     * upload subject notes
     * @param integer id
     * @param integer $class_id
     * @parame Illuminate\Http\Request
     * @return json
     */
    public function store(Request $request, $class_id, $id)
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
        //get the class subject id
        $class_subject_id = $this->showSubject($class_id, $id);

        //save the file in the database
        $notes = new SubjectNotes();
        $notes->class_subject_id = $class_subject_id->id;
        $notes->note_path = $path;
        $notes->note_name = $name;
        $notes->status = 0;
        $notes->batch_id = Batch::find(\App\Helpers\Helpers::instance()->getCurrentAccademicYear());
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
        $subject = DB::table('class_subjects')
            ->join('school_units', 'school_units.id', '=', 'class_subjects.class_id')
            ->join('subjects', 'subjects.id', '=', 'class_subjects.subject_id')
            ->where('school_units.id', $class_id)
            ->where('subjects.id', $subject_id)
            ->select($this->select)
            ->first();

        return $subject;
    }

    /**
     * query all notes for a subject for a year
     * @param integer $id
     * @return array
     */
    public function getSubjectNotes($id)
    {
        // dd($id);
        $batch_id = Batch::find(\App\Helpers\Helpers::instance()->getCurrentAccademicYear());
        $notes = DB::table('subject_notes')
            ->join('class_subjects', 'class_subjects.id', '=', 'subject_notes.class_subject_id')
            ->where('class_subjects.id', $id)
            ->whereYear('subject_notes.batch_id', $batch_id)
            ->select(
                'subject_notes.id as note_id',
                'subject_notes.status',
                'class_subjects.id as class_id',
                'subject_notes.note_name',
                'subject_notes.note_path',
                'subject_notes.created_at'
            )
            ->paginate(5);
        return $notes;
    }
}
