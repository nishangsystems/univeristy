<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Config;
use App\Models\PaymentItem;
use App\Models\Payments;
use App\Models\SchoolUnits;
use App\Models\Students;
use Illuminate\Http\Request;
use Session;
use Redirect;
use DB;
use Auth;

class ResultController extends Controller{

    public function index(Request $request){
        $data['releases'] = \App\Models\Config::orderBy('id', 'desc')->get();
        $data['title'] = "All result releases";
        return view('admin.setting.result.index')->with($data);
    }

    public function create(Request $request){
        $data['title'] = "Add Release";
        return view('admin.setting.result.create')->with($data);
    }

    public function edit(Request $request, $id){
        $data['title'] = "Edit result releases";
        $data['release'] = \App\Models\Config::find($id);
        return view('admin.setting.result.edit')->with($data);
    }

    public function store(Request $request){
        $request->validate([
            'year_id' => 'required',
            'seq_id' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
        ]);
        Config::create($request->all());
        return redirect()->to(route('admin.result_release.index'))->with('success', "Release created successfully");
    }

    public function update(Request $request, $id){
        $request->validate([
            'year_id' => 'required',
            'seq_id' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
        ]);
        $release = \App\Models\Config::find($id);
        $release->update($request->all());
        return redirect()->to(route('admin.result_release.index'))->with('success', "Release updated successfully");
    }

    public function destroy(Request $request, $id){
        $config = Config::find($id);
       if(\App\Models\Config::all()->count() > 0){
           $config->delete();
           return redirect()->back()->with('success', "Release deleted successfully");
       }else{
           return redirect()->back()->with('error', "Change current academic year");
       }
    }
}
