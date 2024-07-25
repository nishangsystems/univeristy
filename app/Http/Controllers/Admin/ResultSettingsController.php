<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Background;
use App\Models\Semester;
use App\Services\ResultSettingsService;
use Illuminate\Http\Request;

class ResultSettingsController extends Controller
{
    protected $resultSettingsService;
    //
    public function __construct(ResultSettingsService $resultSettingsService)
    {
        # code...
        $this->resultSettingsService = $resultSettingsService;
    }

    public function index(Request $request)
    {
        # code...
        $data['title'] = "Results Settings Index";
        $data['backgrounds'] = Background::all();
        return view('admin.setting.result.deadlines', $data);
    }

    public function setCaUploadLatestDate(Request $request, $background_id)
    {
        # code...
        $background = Background::find($background_id);
        $data['title'] = "Set Ca Upload Deadline For ".$background->background_name??'';
        $data['semesters'] = $background->semesters;
        return view('admin.setting.result.ca_deadline', $data);
    }


    public function saveCaUploadLatestDate(Request $request){
        try {
            $semester_id = $request->semester_id;
            $deadline = $request->ca_upload_latest_date;
            $this->resultSettingsService->setCaUploadDateline($semester_id, $deadline);
            return back()->with('success', __('text.word_Done'));
        } catch (\Throwable $th) {
            //throw $th;
            session()->flash('error', $th->getMessage());
            return back()->withInput();
        }
    }


    public function setExamUploadLatestDate(Request $request, $background_id)
    {
        # code...
        $background = Background::find($background_id);
        $data['title'] = "Set Exam Upload Deadline For ".$background->background_name;
        $data['semesters'] = $background->semesters;
        return view('admin.setting.result.exam_deadline', $data);
    }

    public function saveExamUploadLatestDate(Request $request){
        try {
            $semester_id = $request->semester_id;
            $deadline = $request->exam_upload_latest_date;
            $this->resultSettingsService->setExamUploadDateline($semester_id, $deadline);
            return back()->with('success', __('text.word_Done'));
        } catch (\Throwable $th) {
            //throw $th;
            session()->flash('error', $th->getMessage());
            return back()->withInput();
        }
    }
}
