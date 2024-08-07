<?php

namespace App\Http\Controllers\Admin;

use App\Events\StudentResultChangedEvent;
use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Models\PaymentItem;
use App\Models\ProgramLevel;
use App\Models\Result;
use App\Models\SchoolUnits;
use App\Models\Students;
use App\Models\Batch;
use App\Models\Transcript;
use App\Models\TranscriptRating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Session;
use Redirect;
use Auth;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ResultsAndTranscriptsController extends Controller{


    public function spread_sheet(Request $request)
    {
        # code...
        $data['title'] = __('text.spread_sheet');
        if($request->class_id != null){
            $class = \App\Models\ProgramLevel::find(request('class_id'));
            $semester = \App\Models\Semester::find(request('semester_id'));
            $year = \App\Models\Batch::find(request('year_id'));
            $data['year'] = request('year_id');
            $data['students'] = $class->_students($year->id)->get();
            $data['grades'] = \Cache::remember('grading_scale', 60, function () use ($class) {
                return $class->program->gradingType->grading->sortBy('grade') ?? [];
            });
            $courses = [];
            foreach (($class->class_subjects_by_semester(request('semester_id')) ?? [] ) as $course){
               if(Result::where([
                       'batch_id'=>request('year_id'),
                       'subject_id'=>$course->subject->id
                   ])->count() > 0 ){

                   array_push($courses ,  $course);
               }
            }

            $data['courses'] = $courses;
            $data['base_pass'] = ($class->program->ca_total ?? 0 + $class->program->exam_total ?? 0)*0.5;
            $data['_title'] = $class->name().' '.$semester->name.' '.$data['title'].' FOR '.$year->name.' '.__('text.academic_year');
        }
        
        return view('admin.res_and_trans.spr_sheet', $data);
    }

    public function grade_sheet(Request $request)
    {
        # code...
        $data['title'] = __('text.grades_sheet');
        if($request->has('class_id'))
            return view('admin.res_and_trans.grd_sheet', $data);
        else
            return view('admin.res_and_trans.index', $data);
    }

    public function ca_only(Request $request)
    {
        # code...
        $data['title'] = __('text.CA');
        if($request->has('class_id'))
            return view('admin.res_and_trans.ca_only', $data);
        else
            return view('admin.res_and_trans.index', $data);
    }

    public function semester_results_report(Request $request)
    {
        # code...
        $data['title'] = __('text.semester_results_report');
        if($request->has('class_id'))
            return view('admin.res_and_trans.sem_res_report', $data);
        else
            return view('admin.res_and_trans.index', $data);
    }

    public function passfail_report(Request $request)
    {
        # code...
        $data['title'] = __('text.passfail_report');
        if($request->has('class_id'))
            return view('admin.res_and_trans.passfail_report', $data);
        else
            return view('admin.res_and_trans.index', $data);
    }

    public function configure_transcript()
    {
        # code...
        $data['title'] = __('text.configure_transcripts');
        return view('admin.res_and_trans.transcripts.create', $data);
    }

    public function configure_edit_transcript($id)
    {
        # code...
        $data['title'] = __('text.configure_transcripts');
        $data['instance'] = TranscriptRating::find($id);
        return view('admin.res_and_trans.transcripts.create', $data);
    }

    public function configure_save_transcript(Request $request)
    {
        # code...
        $validator = Validator::make($request->all(),[
            'mode'=>'required|in:NORMAL MODE,FAST MODE,SUPER FAST MODE',
            'duration'=>'required|numeric',
            'current_price'=>'required|numeric',
            'former_price'=>'required|numeric'
        ]);
        if ($validator->fails()) {
            # code...
            return back()->with('error', $validator->errors()->first());
        }
        if (TranscriptRating::where(['mode'=>$request->mode])->count() > 0) {
            # code...
            return back()->with('error', __('text.record_already_exist', ['item'=>$request->mode]));
        }
        $rating = new TranscriptRating($request->all());
        $rating->save();
        return back()->with('success', __('text.word_done'));
    }

    public function configure_update_transcript(Request $request, $id)
    {
        # code...
        $validator = Validator::make($request->all(),[
            'mode'=>'required|in:NORMAL MODE,FAST MODE,SUPER FAST MODE',
            'duration'=>'required|numeric',
            'current_price'=>'required|numeric',
            'former_price'=>'required|numeric'
        ]);
        if ($validator->fails()) {
            # code...
            return back()->with('error', $validator->errors()->first());
        }
        $rating = TranscriptRating::find($id);
        if (TranscriptRating::where(['mode'=>$request->mode])->whereNot('mode', $rating->mode)->count() > 0) {
            # code...
            return back()->with('error', __('text.record_already_exist', ['item'=>$request->mode]));
        }
        $rating->fill($request->all());
        $rating->save();
        return back()->with('success', __('text.word_done'));
    }

    public function completed_transcripts()
    {
        # code...
        $data['title'] = __('text.completed_transcripts');
        // filter completed transcripts if the duration has not yet expired, then the rest of the done transcripts follow; order by Mode, created_at(date of application)
        $transcripts = Transcript::whereNotNull('transcripts.done')
                    ->join('transcript_ratings', ['transcript_ratings.id'=>'transcripts.config_id'])
                    ->orderBy('id', 'DESC')->orderBy('config_id')->get(['transcripts.*', 'transcript_ratings.duration', 'transcript_ratings.mode',]);

        $data['data'] = $transcripts->filter(function ($row) {
            return Carbon::now()->subDays((int) $row->duration) <= $row->created_at;
        });

        $data['_data'] = $transcripts->filter(function ($row) {
            return Carbon::now()->subDays((int) $row->duration) > $row->created_at;
        });

        return view('admin.res_and_trans.transcripts.completed', $data);
    }

    public function pending_transcripts()
    {
        # code...
        $data['title'] = __('text.pending_transcripts');
        // filter completed transcripts if the duration has not yet expired, then the rest of the done transcripts follow; order by Mode, created_at(date of application)
        $transcripts = Transcript::whereNull('transcripts.done')
                    ->join('transcript_ratings', ['transcript_ratings.id'=>'transcripts.config_id'])
                    ->orderBy('id', 'DESC')->orderBy('config_id')->get(['transcripts.*', 'transcript_ratings.duration', 'transcript_ratings.mode',]);

        $data['data'] = $transcripts->filter(function ($row) {
            return Carbon::now()->subDays((int) $row->duration) <= $row->created_at;
        });

        return view('admin.res_and_trans.transcripts.pending', $data);
    }

    public function undone_transcripts()
    {
        # code...
        $data['title'] = __('text.undone_transcripts');
                // filter completed transcripts if the duration has not yet expired, then the rest of the done transcripts follow; order by Mode, created_at(date of application)
                $transcripts = Transcript::whereNull('transcripts.done')
                ->join('transcript_ratings', ['transcript_ratings.id'=>'transcripts.config_id'])
                ->orderBy('id', 'DESC')->orderBy('config_id')->get(['transcripts.*', 'transcript_ratings.duration', 'transcript_ratings.mode',]);

        $data['data'] = $transcripts->filter(function ($row) {
            return Carbon::now()->subDays((int) $row->duration) > $row->created_at;
        });

        return view('admin.res_and_trans.transcripts.undone', $data);
    }

    public function set_done_transcripts(Request $request, $id)
    {
        # code...
        $trans = Transcript::find($id);
        if($trans != null){
            $trans->fill(['done'=>now()->toDateTimeString(), 'user_id'=>auth()->id()]);
            $trans->save();
            $student = $trans->student;
            $message = "Hello ".$student->name.", Your transcript applied on ".Carbon::parse($trans->created_at)->format('d-m-Y')." has been completely processed and is available for collection";
            $contact = $student->phone;
            $this->sendSmsNotificaition($message, [$contact]);
            return back()->with('success', __('text.word_done'));
        }
        return back()->with('error', __('text.not_found'));
    }


    public function alter_student_results(Request $request, $student_id = null, $year_id = null, $semester_id = null){
        $data = ['title'=>'Add Student Exam Course', 'student_id'=>$student_id, 'year_id'=>$year_id, 'semester_id'=>$semester_id];
        $data['years'] = Batch::all();
        if($student_id != null){
            $data['student'] = Students::find($student_id);
            $data['semester'] = \App\Models\Semester::find($semester_id);
            $data['title'] = "Add Student Exam Course For {$data['student']->name}";
        }
        return view('admin.res_and_trans.alter_results', $data);
    }


    public function alter_save_student_results(Request $request, $student_id = null, $year_id = null, $semester_id = null){
        
        $validity = Validator::make($request->all(), ['course_id'=>'required', 'exam_score'=>'required']);

        if($validity->fails()){
            session()->flash('error', $validity->errors()->first());
            return back()->withInput();
        }

        $student = Students::find($student_id);
        $class = $student->_class();
        $update = ['ca_score'=>$request->ca_score, 'exam_score'=>$request->exam_score, 'class_id'=>$class->id??0, 'reference'=>'__MISSING__'];
        $base = ['batch_id'=>$year_id, 'student_id'=>$student_id, 'subject_id'=>$request->course_id, 'semester_id'=>$semester_id];
        Result::updateOrInsert($base, $update);

        event(new StudentResultChangedEvent($student_id, $year_id, $semester_id, $request->course_id, $action="EXAM_COURSE_ADDED", $actor=auth()->id(), $data=null));

        $rec = Result::where($base)->first();
        $_message = "EXAM COURSE ADDED; Student: [{$rec->student->matric}, Course code: {$rec->subject->code}, Year: {}";

        return back()->with('success', 'Done');
    }

    public function alter_delete_student_results(Request $request=null, $student_id=null, $year_id=null, $semester_id=null){
        $data = ['title'=>'Delete Student Exam Course', 'student_id'=>$student_id, 'year_id'=>$year_id, 'semester_id'=>$semester_id];
        $data['years'] = Batch::all();
        if($student_id != null){
            $data['student'] = Students::find($student_id);
            $data['semester'] = \App\Models\Semester::find($semester_id);
            $data['title'] = "Delete Student Exam Course For {$data['student']->name}";
        }
        return view('admin.res_and_trans.alter_results_delete', $data);
    }

    public function alter_save_delete_student_results(Request $request, $student_id, $year_id, $semester_id){
        $validity = Validator::make($request->all(), ['course_id'=>'required']);

        if($validity->fails()){
            session()->flash('error', $validity->errors()->first());
            return back()->withInput();
        }

        $base = ['batch_id'=>$year_id, 'student_id'=>$student_id, 'subject_id'=>$request->course_id, 'semester_id'=>$semester_id];
        $result = Result::where($base)->first();
        $result->delete();

        // trigger monitoring event
        event(new StudentResultChangedEvent($student_id, $year_id, $semester_id, $request->course_id, $action="EXAM_COURSE_DELETED", $actor=auth()->id(), $data=null));

        return back()->with('success', 'Done');
    }

    public function alter_change_student_results(Request $request, $student_id=null, $year_id=null, $semester_id=null){
        $data = ['title'=>'Change Student CA/Exam Mark', 'student_id'=>$student_id, 'year_id'=>$year_id, 'semester_id'=>$semester_id];
        $data['years'] = Batch::all();
        if($student_id != null){
            $data['student'] = Students::find($student_id);
            $data['semester'] = \App\Models\Semester::find($semester_id);
            $data['title'] = "Change Student CA/Exam Mark For {$data['student']->name}";
        }
        return view('admin.res_and_trans.alter_results_change_mark', $data);
    }

    public function alter_save_student_ca_results(Request $request, $student_id, $year_id, $semester_id){
        $validity = Validator::make($request->all(), ['ca_score'=>'required', 'course_id'=>'required']);
        
        if($validity->fails()){
            session()->flash('error', $validity->errors()->first());
            return back()->withInput();
        }

        $base = ['batch_id'=>$year_id, 'student_id'=>$student_id, 'subject_id'=>$request->course_id, 'semester_id'=>$semester_id];
        $rec = Result::where($base)->first();
        if($rec == null){
            session()->flash('error', "This course does not have a result record at the moment. Consider adding this course to the student result");
            return back()->withInput();
        }
        $old_score = $rec->ca_score??null;
        try {

            DB::beginTransaction();
            // update mark
            $rec->ca_score = $request->ca_score;
            $rec->save();
            // trigger monitoring event
            event(new StudentResultChangedEvent($student_id, $year_id, $semester_id, $request->course_id, $action="CA_MARK_CHANGED", $actor=auth()->id(), $data=['from'=>$old_score, 'to'=>$rec->ca_score]));
            DB::commit();
            return back()->with('success', 'Done');
        } catch (\Throwable $th) {
            DB::rollBack();
            session()->flash('error', "F::{$th->getFile()}, L::{$th->getLine()}, M::{$th->getMessage()}");
            return back()->withInput();
        }
    }

    public function alter_save_student_exam_results(Request $request, $student_id, $year_id, $semester_id){
        $validity = Validator::make($request->all(), ['exam_score'=>'required', 'course_id'=>'required']);
        
        if($validity->fails()){
            session()->flash('error', $validity->errors()->first());
            return back()->withInput();
        }

        $base = ['batch_id'=>$year_id, 'student_id'=>$student_id, 'subject_id'=>$request->course_id, 'semester_id'=>$semester_id];
        $rec = Result::where($base)->first();
        if($rec == null){
            session()->flash('error', "This course does not have a result record at the moment. Consider adding this course to the student result");
            return back()->withInput();
        }
        $old_score = $rec->exam_score??null;
        try {

            DB::beginTransaction();
            // update mark
            $rec->exam_score = $request->exam_score;
            $rec->save();
            // trigger monitoring event
            event(new StudentResultChangedEvent($student_id, $year_id, $semester_id, $request->course_id, $action="EXAM_MARK_CHANGED", $actor=auth()->id(), $data=['from'=>$old_score, 'to'=>$rec->exam_score]));
            DB::commit();
            return back()->with('success', 'Done');
        } catch (\Throwable $th) {
            DB::rollBack();
            session()->flash('error', "F::{$th->getFile()}, L::{$th->getLine()}, M::{$th->getMessage()}");
            return back()->withInput();
        }
    }
    
    public function transcripts_summary_stats(Request $request, $year_id = null){
        $data['title'] = "Summary Transcript statistics";
        
        $data['data'] = \App\Models\TranscriptRating::join('transcripts', ['transcripts.config_id'=>'transcript_ratings.id'])
            ->whereNotNull('transcripts.transaction_id')
            ->select([
                'transcript_ratings.mode', 'transcript_ratings.duration', 'transcripts.status', 'transcripts.done', 'transcripts.collected', 
                DB::raw("SUM(CASE WHEN transcripts.status = 'CURRENT' THEN transcript_ratings.current_price ELSE transcript_ratings.former_price END) as amount")
            ])->groupBy('transcript_ratings.mode', 'transcripts.status', 'transcripts.done', 'transcripts.collected')->distinct()->get();

        return view('admin.res_and_trans.transcripts.summary', $data);
    }
}
