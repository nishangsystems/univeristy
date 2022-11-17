<?php


namespace App\Http\Controllers\Admin;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Resources\StudentFee;
use App\Models\Background;
use App\Models\Batch;
use App\Models\CampusSemesterConfig;
use App\Models\Config;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config as FacadesConfig;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use MongoDB\Driver\Session;

class HomeController  extends Controller
{
    public function index()
    {
        return view('admin.dashboard');
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

    public function set_background_image()
    {
        # code...
        $data['title'] = 'Set Background Image';
        return view('admin.setting.bg_image', $data);
    }
    public function save_background_image(Request $request)
    {
        # code...
        $val = Validator::make($request->all(), ['file'=>'required|image|mimes:png,jpg,jpeg,gif,tif']);

        if ($val->fails()) {
            # code...
            // return $val->errors();
            return back()->with('error', $val->errors()->first());
        }

        $file = $request->file('file');
        $full_name = storage_path('bg-image').'/background_image.'.$file->getClientOriginalExtension();
        $file->move(base_path('bg_image'), '/background_image.'.$file->getClientOriginalExtension());
        // config()->set('custom.app_bg', $full_name);
        file_put_contents(base_path('bg_img.text'), $file->getClientOriginalExtension());
        return back()->with('success', 'Done');
    }

    public function setsemester(Request $request)
    {
        # code...
        $data['title'] = "Set Current Semester";
        $data['backgrounds'] = Background::all();
        if ($request->has('background')) {
            # code...
            $data['semesters'] = Semester::where(['background_id'=>$request->background])->get();
        }
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

}
