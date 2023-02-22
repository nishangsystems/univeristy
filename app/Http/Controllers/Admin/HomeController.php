<?php


namespace App\Http\Controllers\Admin;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Resources\StudentFee;
use App\Models\Background;
use App\Models\Batch;
use App\Models\CampusSemesterConfig;
use App\Models\Config;
use App\Models\File;
use App\Models\Resit;
use App\Models\SchoolUnits;
use App\Models\Semester;
use App\Models\Students;
use App\Models\Subjects;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config as FacadesConfig;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use MongoDB\Driver\Session;
use Barryvdh\DomPDF\Facade\Pdf;

use function PHPUnit\Framework\returnSelf;

class HomeController  extends Controller
{
    public function index()
    {
        return view('admin.dashboard');
    }

    public function set_letter_head()
    {
        # code...
        $data['title'] = "Upload Letter-head";
        return view('admin.setting.set-letter-head', $data);
    }

    public function save_letter_head(Request $request)
    {

        # code...
        $check = Validator::make($request->all(), ['file'=>'required|file|mimes:png,jpg,jpeg,gif,tif']);
        if ($check->fails()) {
            # code...
            return back()->with('error', $check->errors()->first());
        }
        
        $file = $request->file('file');
        // return $file->getClientOriginalName();
        if(!($file == null)){
            $ext = $file->getClientOriginalExtension();
            $filename = '_'.random_int(100000, 999999).'_'.time().'.'.$ext;
            $path = $filename;
            $file->move(url('storage/app/').'/files', $filename);
            if(File::where(['name'=>'letter-head'])->count() == 0){
                File::create(['name'=>'letter-head', 'path'=>$path]);
            }else {
                File::where(['name'=>'letter-head'])->update(['path'=>$path]);
            }
            return back()->with('success', 'Done');
        }
        return back()->with('error', 'Error reading file');
    }

    public function set_background_image()
    {
        # code...
        $data['title'] = 'Set Background Image';
        return view('admin.setting.bg_image', $data);
    }
    public function save_background_image(Request $request)
    {
        # code...
        # code...
        $check = Validator::make($request->all(), ['file'=>'required|file|mimes:jpeg']);
        if ($check->fails()) {
            # code...
            return back()->with('error', $check->errors()->first());
        }
        
        $file = $request->file('file');
        // return $file->getClientOriginalName();
        if(!($file == null)){
            $ext = $file->getClientOriginalExtension();
            $filename = 'background_image.jpeg';
            // $path = $filename;
            $file->storeAs('/bg_image', $filename);
            return back()->with('success', 'Done');
        }
        return back()->with('error', 'Error reading file');
    }

    public function setayear()
    {
        $data['title'] = 'Set Current Academic Year';
        return view('admin.setting.setbatch')->with($data);
    }

    public function setsem()
    {
        return view('admin.setting.setsem');
    }

    public function courses_date_line(Request $request)
    {
        $data['title'] = "Set Course Registration Date Line".($request->has('semester') ? ' For '.Semester::find($request->semester)->name : '');
        if(request()->has('background')){
            $data['current_semester'] = Semester::where(['background_id'=>$request->background, 'status'=>1])->first()->id ?? null;
        }
        return view('admin.setting.set_course_date', $data);
    }

    public function save_courses_date_line(Request $request)
    {
        # code...
        $val = Validator::make($request->all(), ['semester'=>'required', 'date'=>'required|Date']);
        if ($val->fails()) {
            # code...
            return back()->with('error', $val->errors()->first());
        }

        try {
            //code...
            $conf = \App\Models\CampusSemesterConfig::where(['semester_id'=>$request->semester, 'campus_id'=>auth()->user()->campus_id ?? ''])->first() ?? null;
            if ($conf != null) {
                # code...
                $conf->courses_date_line = $request->date;
                $conf->save();
            }
            else {
                CampusSemesterConfig::create([
                    'semester_id'=>$request->semester, 'campus_id'=>auth()->user()->campus_id ?? null, 'courses_date_line'=>$request->date
                ]);
            }
            return back()->with('success', 'Done');
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('error', $th->getMessage());
        }

    }

    public function course_date_line(Request $request, $campus, $semester)
    {
        # code...
        $conf = CampusSemesterConfig::where([
            'campus_id'=>$campus, 'semester_id'=>$semester
            ])->count();
            if ($conf == 0) {
                # code...
                return ['semester'=>Semester::find($semester)->name, 'date_line'=>"DATE LINE NOT SET"];
            }
            // return __DIR__;
            return ['semester'=>Semester::find($semester)->name, 'date_line'=>date('l d-m-Y', strtotime(CampusSemesterConfig::where(['campus_id'=>$campus, 'semester_id'=>$semester])->first()->courses_date_line)), 'date'=>CampusSemesterConfig::where(['campus_id'=>$campus, 'semester_id'=>$semester])->first()->courses_date_line];
    }

    public function program_settings(Request $request)
    {
        # code...
        $data['title'] = "Program Settings";
        return view('admin.setting.program_settings', $data);
    }

    public function post_program_settings(Request $request)
    {
        # code...
        $program = SchoolUnits::find($request->program);
        // return $program;
        if ($program != null) {
            # code...
            $program->max_credit=$request->max_credit;
            $program->ca_total=$request->ca_total;
            $program->exam_total=$request->exam_total;
            $program->resit_cost=$request->resit_cost;
            $program->save();
            return back()->with('success', 'Done');
        }
        return back()->with('error', 'Program Not Found.');
    }


    public function setsemester(Request $request)
    {
        # code...
        $data['title'] = "Set Current Semester";
        $data['semesters'] = Semester::join('backgrounds', ['backgrounds.id'=>'semesters.background_id'])
                    ->distinct()->select(['semesters.*', 'backgrounds.background_name'])->orderBy('background_name', 'DESC')->orderBy('name', 'ASC')->get();
        // return $data;
        return view('admin.setting.setsemester', $data);
    }

    public function postsemester(Request $request, $id)
    {
        # code...
        try {
            //code...
            $semesters = Semester::where(['background_id'=>$request->background])->get();
            foreach ($semesters as $key => $sem) {
                # code...
                $sem->status = 0;
                $sem->save();
            }
            $semester = Semester::find($id);
            $semester->status = 1;
            $semester->save();
            return back()->with('success', 'Done');
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('error', 'Operation failed. '.$th->getMessage());
        }
    }

    public function createsem(Request $request)
    {
        $id = $request->input('sem');
        $get_sem = \App\Models\Sequence::find($id);
        return redirect()->back();
    }

    public function deletebatch($id)
    {
        if (DB::table('batches')->count() == 1) {
            return redirect()->back()->with('error', 'Cant delete last batch');
        }
        DB::table('batches')->where('id', '=', $id)->delete();
        return redirect()->back()->with('success', 'batch deleted');
    }



    public function setAcademicYear($id)
    {
        // dd($id);
        $year = Config::all()->last();
        $data = [
            'year_id' => $id
        ];
        $year->update($data);

        return redirect()->back()->with('success', 'Set Current Academic Year successfully');
    }

    public function extraFee(Request $request)
    {
        # code...
        $data['title'] = "ADD ADDITIONAL FEE ".($request->student_id == null ? '' : ' FOR '.Students::find($request->student_id)->name ?? '');
        return view('admin.fee.extra-fee', $data);
    }

    public function extraFeeSave(Request $request)
    {
        # code...
        $check = Validator::make($request->all(), ['amount'=>'required', 'year_id'=>'required']);
        if ($check->fails()) {
            # code...
            return back()->with('error', $check->errors()->first());
        }
        // return $request->all();
        \App\Models\ExtraFee::create(['student_id'=>$request->student_id, 'amount'=>$request->amount, 'year_id'=>$request->year_id]);
        return back()->with('success', 'Done');
    }

    public function custom_resit_create()
    {
        # code...
        $data['title'] = "Open Resit";
        return view('admin.setting.custom_resit.create', $data);
    }

    public function custom_resit_edit(Request $request, $id)
    {
        # code...
        $data['title'] = "Open Resit";
        $data['resit'] = Resit::find($id);
        return view('admin.setting.custom_resit.edit', $data);
    }

    public function custom_resit_save(Request $request)
    {
        # code...
        $validator = Validator::make($request->all(), ['year_id'=>'required', 'background_id'=>"required", 'start_date'=>'required|date', 'end_date'=>'required|date']);
        if($validator->fails()){
            return back()->with('error', $validator->errors()->first());
        }

        $resit = new Resit($request->all());
        $resit->save();
        return back()->with('success', 'Done');
    }

    public function custom_resit_update(Request $request, $id)
    {
        # code...
        $validator = Validator::make($request->all(), ['year_id'=>'required', 'background_id'=>"required", 'start_date'=>'required|date', 'end_date'=>'required|date']);
        if($validator->fails()){
            return back()->with('error', $validator->errors()->first());
        }

        $resit = Resit::find($id);
        if($resit != null){
            $resit->fill($request->all());
            $resit->save();
            return back()->with('success', 'Done');
        }

        return back()->with('error', 'Update failed. Resit record not found.');
    }

    public function custom_resit_delete(Request $request, $id)
    {

        $resit = Resit::find($id);
        if($resit != null){
            $resit->delete();
            return back()->with('success', 'Done');
        }

        return back()->with('error', 'Operation failed. Resit record not found.');
    }

    public function resits_index()
    {
        # code...
        $data['title'] = "Resits";
        return view('admin.resit.index', $data);
    }

    public function resit_course_list(Request $request, $resit_id)
    {
        # code...
        $resit =  Resit::find($resit_id);
        $data['title'] = "Course List For " . $resit->name();
        $data['resit'] = $resit;
        return view('admin.resit.course_list', $data);
    }

    public function resit_course_list_download(Request $request)
    {
        # code...
        $subject = Subjects::find($request->subject_id);
        $data['title'] = "Resit Course List For [ ".$subject->code .' ] '. $subject->name.' - '.Resit::find($request->resit_id)->year->name;
        $data['subjects'] = Subjects::find($request->subject_id)->student_subjects()->where(['resit_id' => $request->resit_id])->get();
        if($request->print == 1){

            $pdf = Pdf::loadView('admin.resit._course_list_print', $data);
            return $pdf->download($data['title'] . '.pdf');
        }
        return view('admin.resit.course_list_print', $data);
    }
}
