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
use DB;
use Auth;
use Illuminate\Support\Facades\Validator;

class ResultsAndTranscriptsController extends Controller{

    public function frequency_distribution()
    {
        # code...
        $data['title'] = "Frequency Distribution";
        return view('admin.res_and_trans.fre_dis', $data);
    }

    public function spread_sheet(Request $request)
    {
        # code...
        $data['title'] = "Spread Sheet";
        return view('admin.res_and_trans.spr_sheet', $data);
    }

    public function grade_sheet(Request $request)
    {
        # code...
        $data['title'] = "Grades Sheet";
        if($request->has('class_id'))
            return view('admin.res_and_trans.grd_sheet', $data);
        else
            return view('admin.res_and_trans.index', $data);
    }

    public function ca_only(Request $request)
    {
        # code...
        $data['title'] = "CA";
        if($request->has('class_id'))
            return view('admin.res_and_trans.ca_only', $data);
        else
            return view('admin.res_and_trans.index', $data);
    }

    public function semester_results_report(Request $request)
    {
        # code...
        $data['title'] = "Semester Results Report";
        if($request->has('class_id'))
            return view('admin.res_and_trans.sem_res_report', $data);
        else
            return view('admin.res_and_trans.index', $data);
    }

    public function passfail_report(Request $request)
    {
        # code...
        $data['title'] = "PassFail Report";
        if($request->has('class_id'))
            return view('admin.res_and_trans.passfail_report', $data);
        else
            return view('admin.res_and_trans.index', $data);
    }

    public function configure_transcript()
    {
        # code...
        $data['title'] = "Configure Transcripts";
        return view('admin.res_and_trans.transcripts.create', $data);
    }

    public function configure_edit_transcript($id)
    {
        # code...
        $data['title'] = "Configure Transcripts";
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
            return back()->with('error', 'A transcript configuration already exists for mode :' . $request->mode);
        }
        $rating = new TranscriptRating($request->all());
        $rating->save();
        return back()->with('success', 'Done');
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
            return back()->with('error', 'A transcript configuration already exists for mode :' . $request->mode);
        }
        $rating->fill($request->all());
        $rating->save();
        return back()->with('success', 'Done');
    }

    public function completed_transcripts()
    {
        # code...
        $data['title'] = "Completed Transcripts";
        $data['transcripts'] = Transcript::where('transcripts.done', '=', true)->orderBy('created_at', 'DESC')->orderBy('config_id')->get();
        return view('admin.res_and_trans.transcripts.completed', $data);
    }

    public function pending_transcripts()
    {
        # code...
        $data['title'] = "Pending Transcripts";
        return view('admin.res_and_trans.transcripts.pending', $data);
    }

    public function undone_transcripts()
    {
        # code...
        $data['title'] = "Undone Transcripts";
        return view('admin.res_and_trans.transcripts.undone', $data);
    }

}
