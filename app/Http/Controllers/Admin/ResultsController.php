<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Redirect;

class ResultsController extends Controller
{
    //
    public function CreateGPATypes(){
        $datas=\App\GPATypes::all();
        return view('admin.grading.CreateGPATypes')->withDatas($datas);
    }
    public function savegpatypes(Request $request){
        $saving=new \App\GPATypes;
        $saving->name=strtoupper($request->name);
        $saving->save();
        return redirect()->back();
        

    }
    public function deletegpatype($id){
        DB::table('gpa_types')->where('id', '=', $id)->delete();
        return redirect()->back();

    }
    public function editgpatype(Request $request,$id){
        $row=\App\GPATypes::find($id);
        return view('admin.grading.editgpatype')->withRow($row);

    }
    public function savegpatypeupdates(Request $request,$id){
        $save_edit=\App\GPATypes::find($id);
        $save_edit->name=strtoupper($request->name);            
       $save_edit->save();     
            
      return Redirect::route('admin.setgpatypes');

    }
    public function gradingrange(){
        $datas=\App\Grading::orderBy('gpa_id','ASC')->paginate(10);
        $a=1;
        return view('admin.grading.GradingRange')->withDatas($datas);
    }
    public function deletegrading($id){
        DB::table('grading')->where('id', '=', $id)->delete();
        return redirect()->back();

    }
   public function addgradingrange(Request $request)
    {
         return view('admin.grading.ChooseType');
       
    }
    public function setgradingscale( $id)
    {
        $data['grades']=\App\GPATypes::find($id);
        $data['datas']=\App\Grading::WHERE('gpa_id',$id)->orderBy('b','ASC')->get();
        return view('admin.grading.AddGrading')->with($data);
    }
    public function savegradingscale(Request $request,$id){
      if(  $check=\App\Grading::where('gpa_id',$id)
                ->where('weight',$request->grade)
                ->count()
                >0)
        {
            return redirect()->back()->with('e','Grade Already Exists in the System');

        }
        else {
            $save=new \App\Grading;
            $save->b=$request->starts;
            $save->a=$request->end;
            $save->weight=$request->grade;
            $save->grade=$request->weight;
            $save->status=$request->did;
            $save->remark=strtoupper($request->remark);
            $save->gpa_id=$id;
            $save->save();
            return redirect()->back();


        }
    }
    public function editgrading(Request $request,$id){
        $data['grades']=\App\Grading::find($id);
        $data['datas']=\App\GPATypes::Where('id',$data['grades']->gpa_id)->get();
        return view('admin.grading.editgrading')->with($data);
    }
    public function saveupdatedgrading(Request $request,$id){
        $save=\App\Grading::find($id);
        $save->b=$request->starts;
        $save->a=$request->end;
        $save->weight=$request->grade;
        $save->grade=$request->weight;
        $save->status=$request->did;
        $save->remark=strtoupper($request->remark);        
        $save->save();
        return Redirect::route('admin.setgradingscale',$save->gpa_id);

    }
    public function gradingprog(){
        $data['datas']=\App\Options::orderBy('name','ASC')->get();
        return view('admin.grading.gradingprog')->with($data);
    }
    public function definegrading($id){
        $data['progs']=\App\Options::find($id);
        $data['grades']=\App\GPATypes::get();
        $data['prog']=\App\Options::orderBy('name','ASC')->get();
        $data['exists']=\App\GradingProg::orderBy('id','DESC')->get();

        return view('admin.grading.definegrading')->with($data);
    }
    public function savedefinegrading(Request $request, $id){
        if(\App\GradingProg::where('prog_id',$request->prog)
                            ->count()
                            >0
        )
        {
            return redirect()->back()->with('e','Grading has been set for this Program');
   
        }
        else {
        $save=new \App\GradingProg;
        $save->prog_id=$request->prog;
        $save->grade_id=$request->scale;
        $save->save();
        return redirect()->back();
        }

    }

    
}
