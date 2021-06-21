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

    /**
     * show subject details
     * @param integer 
     * @return json
     */
    public function show($id)
    {
        $data['subject'] = $this->showSubject($id);
        $data['title'] = 'Subject details';
        return view('teacher.subject_detail')->with($data);
    }

    /**
     * show subject notes
     * @param integer subject id
     * @parame Illuminate\Http\Request
     */
    public function index(Request $request, $id)
    {
        $data['notes'] = $this->getSubjectNotes($id);
        $data['id'] = $id;
        $data['title'] = 'Subject Notes';
        return  view('teacher.subject_notes')->with($data);
    }

    /**
     * upload subject notes
     * @param integer subject id
     * @parame Illuminate\Http\Request
     * @return json
     */
    public function store(Request $request, $id)
    {
        //validate file
        $uploaded_file = $request->validate([
            'file' => 'required|mimes:pdf,docx,odt,txt|max:2048',
        ]);
        $extension = $request->file("file")->getClientOriginalExtension();
        $name = $request->file('file')->getClientOriginalName();
        $path = time() . '.' . $extension;
        //store the file
        $request->file('file')->move('storage/SubjectNotes/', $path);
        //save the file in the database
        $notes = new SubjectNotes();
        $notes->subject_id = $id;
        $notes->note_path = $path;
        $notes->note_name = $name;
        $notes->batch_id = Batch::find(\App\Helpers\Helpers::instance()->getCurrentAccademicYear());
        $notes->save();
        return  back()->with(['success' => 'File has been successfully uploaded']);
    }

    /**
     *  get subject details
     * 
     * @parame integer
     * @return json
     */
    public function showSubject($id)
    {
        $subject = Subjects::findOrFail($id);
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
            ->join('subjects', 'subjects.id', '=', 'subject_notes.subject_id')
            ->where('subjects.id', $id)
            ->whereYear('subject_notes.batch_id', $batch_id)
            ->select(
                'subjects.id',
                'subjects.name',
                'subject_notes.note_name',
                'subject_notes.note_path',
                'subject_notes.created_at'
            )
            ->get()->toArray();
        return $notes;
    }
}
