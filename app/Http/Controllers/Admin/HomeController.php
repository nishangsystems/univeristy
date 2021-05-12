<?php


namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Http\Resources\StudentFee;
use App\Models\Batch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use MongoDB\Driver\Session;

class HomeController  extends Controller
{
    public function index()
    {
        return view('admin.dashboard');
    }

    public function setayear(){
        return view('admin.setting.setbatch');
    }

    public function setsem(){
        return view('admin.setting.setsem');
    }

    public function createsem(Request $request){
        $id = $request->input('sem');
        $get_sem=\App\Models\Sequence::find($id);
        return redirect()->back();
    }

    public function deletebatch($id){
        if(DB::table('batches')->count() == 1){
            return redirect()->back()->with('error','Cant delete last batch');
        }
        DB::table('batches')->where('id', '=', $id)->delete();
        return redirect()->back()->with('success','batch deleted');
    }



    public function createBatch(Request $request){
        $school_year = new \App\Models\Batch();
        $start = $request->input('start');
        $end = $request->input('end');
        if($end - $start == 1){
            if(Batch::where('name',$start."/".$end)->count() == 0){
                $current_ayear = $start."/".$end;
                $school_year->name = $current_ayear;
                $school_year->save();
                \session()->flash("success","Batch Create Successfully");
            }else{
                \session()->flash("error","This batch is used already");
            }
        }else{
            \session()->flash("error","End Year must be greater than previous year by 1");
        }

        return redirect()->back();

    }
}
