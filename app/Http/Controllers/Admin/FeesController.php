<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Redirect;
use DB;
use Auth;

class FeesController extends Controller{

    public function setfees(){
        return view('finance.fees.setfees');
    }
    public function seefeesprog(Request $request){
        $data['program']=\App\Options::find($request->pid);
        return view('finance.fees.seefeesprog')->with($data);
    }
    public function settingfees(Request $request){
        $data['pid']=\App\Options::find($request->pid);
        return view('finance.fees.settingfees')->with($data);
    }
    public function setmyfees($id){
        $data['pid']=\App\Options::find($id);

        return view('finance.fees.settingfees')->with($data);;

    }
    public function savefees(Request $request,$id){
        $res=\App\Options::find($id);
        if(\App\Fee::WHERE('program_id',$res->id)
        ->WHERE('level_id',$request->lid)->count() >0 ){
            Session::flash('error','Records Already Exists');
            return Redirect::route('finance.fees.setfees');

        }
        else {
        echo $res->name;
        $save=new \App\Fee;
        $save->program_id=$res->id;
        $save->level_id=$request->lid;
        $save->fee_amt=$request->fees;
        $save->reg_fee=$request->reg;
        $save->save();
        return redirect()->back();
        }
        //return view('finance.fees.settingfees')->with($data);
    }
    public function deletefees($id){
        DB::table('fees')->where('id', '=', $id)->delete();

        return redirect()->back();
    }
    public function seestudents(){
        return view('finance.fees.seestudents');

    }
    public function saveurfees(Request $request,$id){

        $data['data']=\App\StudentInfo::find($id);
        $data['fees']=\App\Fee::where('program_id',$data['data']->program_id)
        ->WHERE('level_id',$data['data']->level_id)->first();
        return view('finance.fees.savefees')->with($data);
    }
    public function savemyfees(Request $request,$id){
        $amtfees=$request->amt;
        $paidsofar=$request->sofar;
        $paid=$request->paid;
       $balance=$amtfees-($paidsofar+$paid);
        if($balance<0){
            return redirect()->back()->with('e','Negative balance of '.$balance .'  ');
        }
        else {
        $data=\App\StudentInfo::find($id);
        $saved=new \App\FeePaymt;
        $saved->student_id=$id;
        $saved->program_id=$data->program_id;
        $saved->level_id=$data->level_id;
        $saved->fee_amt=$request->paid;
        $saved->yearid=$request->yearid;
        $saved->date=$request->date;
        $saved->saveby=\Auth::user()->id;
        $saved->save();


        return redirect()->back();
        }
        }

       // return view('finance.fees.savefees')->with($data);




}
