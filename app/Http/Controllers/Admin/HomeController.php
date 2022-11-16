<?php


namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Http\Resources\StudentFee;
use App\Models\Background;
use App\Models\Batch;
use App\Models\Config;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
