<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Models\PaymentItem;
use App\Models\SchoolUnits;
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

    public function frequency_distribution()
    {
        # code...
        $data['title'] = __('text.frequency_distribution');
        return view('admin.res_and_trans.fre_dis', $data);
    }

    public function spread_sheet(Request $request)
    {
        # code...
        $data['title'] = __('text.spread_sheet');
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

}
