<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Sequence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
class HomeController extends Controller
{

    public function index(){
        return view('student.dashboard');
    }

    public function fee(){
        $data['title'] = "My fee Report";
        return view('student.fee')->with($data);
    }

    public function result(){
        $data['title'] = "My Result";
        $data['seqs'] = Sequence::orderBy('name')->get();
        $data['subjects'] = Auth('student')->user()->class(\App\Helpers\Helpers::instance()->getYear())->subjects;
        return view('student.result')->with($data);
    }

    public function subject(){
        $data['title'] = "My Subjects";
        return view('student.subject')->with($data);
    }

    public function profile(){
        return view('student.edit_profile');
    }

    public function update(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|min:8',
            'phone' => 'required|min:9|max:15',
        ]);


        if ($validator->fails()) {
            return redirect()->back()->with(['e'=> $validator->errors()->first()]);
        }

        $data['success'] = 200;
        $user = \Auth::user();
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->save();
        $data['user'] = \Auth::user();
        return redirect()->back()->with(['s'=>'Phone Number and Email Updated Successfully']);
    }

    public function __construct(){
        $this->middleware('auth:student');
    }
}
