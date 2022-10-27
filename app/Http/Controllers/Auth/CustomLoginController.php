<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
// use Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use \Cookie;
use Illuminate\Support\Facades\Auth;

class CustomLoginController extends Controller
{
    public function __construct(){
        $this->middleware('guest:web', ['except'=>['logout']]);
    }

    public function showLoginForm(){
        return view('auth.login');
    }

    public function detail(Request $request){
        $type = Cookie::get('iam');
        $user = Cookie::get('iamuser');
        $data['type'] = $type;

        if($type != '' && $user != ''){
            if($type == 0){
                $data['user'] = \App\StudentInfo::find($user);
        }else{
                $data['user'] = \App\Teacher::find($user);
        }
            return view('auth.register')->with($data);
        }else{
            return redirect()->route('register');
        }
    }

    public function login(Request $request){
        // return $request->all();
        //validate the form data
        $this->validate($request, [
            'username' => 'required',
            'password' => 'required|min:5'
        ]);
        // return $request->all();
        //Attempt to log the user in

        if( Auth::guard('student')->attempt(['matric'=>$request->username,'password'=>$request->password], $request->remember)){
            // return "Spot 1";
            return redirect()->intended(route('student.home'));
        }else{
            if( Auth::attempt(['username'=>$request->username,'password'=>$request->password])){
                // return "Spot 2";
                if(Auth::user()->type == 'teacher'){
                    return redirect()->route('user.home')->with('success','Welcome to Teachers Dashboard '.Auth::user()->name);
                }else{
                    if (Auth::user()->type == 'campus_admin') {
                        # code...
                        return redirect()->route('campus_admin.home')->with('success','Welcome to Campus Admin Dashboard '.Auth::user()->name);
                    } else {
                        # code...
                        return redirect()->route('admin.home')->with('success','Welcome to Admin Dashboard '.Auth::user()->name);
                    }
                    
                }
            }
        }
        // return "Spot 3";
        $request->session()->flash('error', 'Invalid Username or Password');
        return redirect()->back()->withInput($request->only('username','remember'));
    }

    public function logout(Request $request){
        Auth::logout();
        Auth::guard('student')->logout();
        return redirect(route('login'));
    }

}
