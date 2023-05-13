<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\NonGPACourse;
use App\Models\OfflineResult;
use App\Models\PaymentItem;
use App\Models\School;
use App\Models\SchoolUnits;
use App\Models\Students;
use App\Models\Subjects;
use App\Models\Transcript;
use App\Models\TranscriptRating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Session;
use Redirect;
use Auth;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;
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
            return back()->with('success', __('text.word_done'));
        }
        return back()->with('error', __('text.not_found'));
    }


    public function print_transcript(Request $request){


        $data['student'] = Students::where('matric', '=', $request->student_matric)->first();
        $data['background'] = $data['student']->_class()->program->background;
        $data['grading'] = $data['student']->_class()->program->gradingType->grading()->orderBy('weight', 'DESC')->get();
        $data['school'] = School::first();
        $data['school_last_attended'] = null;
        $data['non_gpa_courses'] = NonGPACourse::pluck('id')->toArray();
        $years = OfflineResult::where('student_matric', '=', $request->student_matric)->distinct()->pluck('batch_id')->toArray();
        $year_counter = 0;
        $labels = ['FIRST YEAR', 'SECOND YEAR', 'THIRD YEAR', 'FORTH YEAR', 'FIFTH YEAR', 'SIXTH YEAR', 'SEVENTH YEAR', 'EIGHTH YEAR', 'NINTH YEAR', 'TENTH YEAR'];
        $data['years'] = array_map(function($bch)use($year_counter, $request, $labels, $data){
            $year_counter++;
            $_results = [];
            $r_pack = [];
            $resit = [];
            $gpa_data = [];
            $batch = Batch::find($bch);
            $results = OfflineResult::where(['student_matric'=> $request->student_matric, 'batch_id'=>$bch])->distinct()->get();
            $semesters = OfflineResult::where(['student_matric'=> $request->student_matric, 'batch_id'=>$bch])->orderBy('semester_id')->distinct()->pluck('semester_id')->toArray();
            if($data['background']->background_name == 'PUBLIC HEALTH'){
                $r1 = count($semesters) > 0 ? $results->where('semester_id', '=', $semesters[0]) : [];
                $r2 = count($semesters) > 1 ? $results->where('semester_id', '=', $semesters[1]) : [];
                $r3 = count($semesters) > 2 ? $results->where('semester_id', '=', $semesters[2]) : [];
                $r_pack = ['r1'=>$r1, 'r2'=>$r2, 'r3'=>$r3];
                $resit = count($semesters) > 3 ? $results->where('semester_id', '=', $semesters[3]) : [];
                $size = count($r1); $size = $size > count($r2) ? $size : count($r2); $size = $size > count($r3) ? $size : count($r3);
                for ($i=0; $i < $size; $i++) { 
                    # code...
                    $_results[] = ['s1'=>count($r2) > $i ? $r1[$i] : null, 's2'=>count($r2) > $i ? $r2[$i] : null, 's3'=>count($r3) > $i ? $r3[$i] : null];
                }
            }else{
                $r1 = count($semesters) > 0 ? $results->where('semester_id', '=', $semesters[0]) : [];
                $r2 = count($semesters) > 1 ? $results->where('semester_id', '=', $semesters[1]) : [];
                $r_pack = ['r1'=>$r1, 'r2'=>$r2];
                $resit = count($semesters) > 2 ? $results->where('semester_id', '=', $semesters[2]) : [];
                $size = count($r1); $size = $size > count($r2) ? $size : count($r2);
                for ($i=0; $i < $size; $i++) { 
                    # code...
                    $_results[] = ['s1'=>count($r1) > $i ? $r1[$i] : null, 's2'=>count($r2) > $i ? $r2[$i] : null];
                }
            }


            $gpa_data[] = collect($r_pack)->map(function($rec)use($request, $data){
                return count($rec) == 0 ? [] : [
                                        'credits_attempted'=>collect($rec)->sum('coef'),
                                        'gpa_credits_attempted'=>collect($rec)->filter(function($row)use($data){return !in_array($row->id, $data['non_gpa_courses']);})->sum('coef'),
                                        'credits_earned'=>collect($rec)->filter(function($row){return $row->ca_score + $row->exam_score >= 50;})->sum('coef'),
                                        'gpa_credits_earned'=>collect($rec)->filter(function($row)use($data){return !in_array($row->id, $data['non_gpa_courses']) and ($row->ca_score + $row->exam_score >= 50);})->sum('coef'),
                                        'gpa'=>(collect($rec)->filter(function($row)use($data){return !in_array($row->id, $data['non_gpa_courses']);})->sum(function($row){return $row->coef*$row->grade()->weight;}))/(collect($rec)->filter(function($row)use($data){return !in_array($row->id, $data['non_gpa_courses']);})->sum('coef'))
                ];
            });
            $gpa_data[] = count($resit) == 0 ? [] : [
                'credits_attempted'=>collect($resit)->sum('coef'),
                'gpa_credits_attempted'=>collect($resit)->filter(function($row)use($data){return !in_array($row->id, $data['non_gpa_courses']);})->sum('coef'),
                'credits_earned'=>collect($resit)->filter(function($row){return $row->ca_score + $row->exam_score >= 50;})->sum('coef'),
                'gpa_credits_earned'=>collect($resit)->filter(function($row)use($data){return !in_array($row->id, $data['non_gpa_courses']) and ($row->ca_score + $row->exam_score >= 50);})->sum('coef'),
                'gpa'=>(collect($resit)->filter(function($row)use($data){return !in_array($row->id, $data['non_gpa_courses']);})->sum(function($row){return $row->coef*$row->grade()->weight;}))/(collect($resit)->filter(function($row)use($data){return !in_array($row->id, $data['non_gpa_courses']);})->sum('coef'))
            ];

            return [
                'index'=>$year_counter,
                'id'=>$bch, 
                'name'=>$batch->name, 
                'results'=>$_results,
                'results_bag'=>$results,
                'result_pack'=>$r_pack,
                'resit'=>$resit,
                'gpa_data'=>$gpa_data,
                'semesters'=>$semesters,
                'label'=>$labels[$year_counter-1]
            ];
        }, $years);
        $cum_r_pack = collect($data['years'])->map(function($year){
            return $year['results_bag'];
        });
        $all_results = new Collection();
        foreach ($cum_r_pack as $pack) {
            // dd($pack);
            foreach ($pack as $value) {
                # code...
                $all_results->push($value);
            }
        }
        $all_results = collect($all_results);
        // dd($all_results);

        $cum_gpa_data = [
            'credits_attempted' => $all_results->map(function($row){return ['id'=>$row->id, 'coef'=>$row->coef];})->unique()->sum('coef'),
            'gpa_credits_attempted' => $all_results->map(function($row){return ['id'=>$row->id, 'coef'=>$row->coef];})->filter(function($rec)use($data){return !in_array($rec['id'], $data['non_gpa_courses']);})->unique()->sum('coef'),
            'credits_earned' => $all_results->filter(function($rec){return $rec->passed();})->map(function($row){return ['id'=>$row->id, 'coef'=>$row->coef];})->unique()->sum('coef'),
            'gpa_credits_earned' => $all_results->filter(function($rec){return $rec->passed();})->map(function($row){return ['id'=>$row->id, 'coef'=>$row->coef];})->filter(function($rec)use($data){return !in_array($rec['id'], $data['non_gpa_courses']);})->unique()->sum('coef'),
            'gpa'=>($data['background']->background_name == 'PUBLIC_HEALTH') ? 
                function()use($data){
                    $gpa_packs = new Collection();
                    foreach (collect($data['years'])->map(function($_r){return $_r['gpa_data'];}) as $y_data) {foreach ($y_data as $key => $value) {if(count($value) > 0){$gpa_packs->push($value);}}}
                    return $gpa_packs->sum('gpa')/$gpa_packs->count();
                } : function(){
                    
                }
        ];

        // dd($all_results);
        $data['years'] = collect($data['years'] )->sortBy('name')->toArray();
        return view('admin.res_and_trans.transcripts.transcript', $data);
    }

    public function print_index(Request $request)
    {
        # code...
        $data['title'] = __('text.search_student');
        return view('admin.res_and_trans.transcripts.print_index', $data);
    }

    public function fill_matric_and_course_codes()
    {
        # code...
        OfflineResult::all()->each(function($item){
            $item->student_matric = $item->student->matric;
            $item->subject_code = $item->subject->code;
            $item->save();
        });
        return back()->with('success', 'Done');
    }

}
