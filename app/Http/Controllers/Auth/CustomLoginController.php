<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Guardian;
use App\Models\Students;
use App\Models\User;
// use Auth;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use \Cookie;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CustomLoginController extends Controller
{
    public function __construct(){
        $this->middleware('guest:web', ['except'=>['logout']]);
    }

    public function showLoginForm(){
        return view('auth.login');
    }
    
     public function registration(){
        return view('auth.registration');
    } 

    public function showRegistrationForm()
    {
        # code...
        return view('auth.registration');
    }

    public function check_matricule(Request $request){
       if (Students::where('matric', $request->reg_no)->exists()) { 
              if (User::where('username', $request->reg_no)->exists()) {   
                 return redirect()->route('registration')->with('error','Matricule Number has being used already. Contact the System Administrator.');   
              }else{
                $student_d = Students::where('matric', $request->reg_no)->first();   
                return view('auth.registration_info',compact('student_d'));
              }
            
          }
          else{
            return redirect()->route('registration')->with('error','Invalid Registration Number.');   
          }
    }
    
    
    public function createAccount(Request $request){
        if (Students::where('matric', $request->username)->exists()) {  
            $update['phone'] = $request->phone;
            $update['email'] = $request->email;
            $update['password'] = Hash::make($request->password);
            
            $up = Students::where('matric', $request->username)->update($update);
             if (User::where('username', $request->username)->exists()) {  
            $update1['name'] = $request->name;
            $update1['email'] = $request->email;
            $update1['username'] = $request->username;
            $update1['type'] = 'student';
            $update1['password'] = Hash::make($request->password);
            
            $up1 = User::where('username', $request->username)->update($update1);
             }else{
                 $insert['name'] = $request->name;
                $insert['email'] = $request->email;
                $insert['username'] = $request->username;
                $insert['type'] = 'student';
                $insert['gender'] = '';
                $insert['password'] = Hash::make($request->password);
            
            $up2 = User::create($insert);
             }
        //      if( Auth::guard('student')->attempt(['matric'=>$request->username,'password'=>$request->password], $request->remember)){
        //     // return "Spot 1";
        //     return redirect()->intended(route('student.home'));
        // }else{
        //     return redirect()->route('login')->with('s','Account created successfully.');   
        // }
            return redirect()->route('login')->with('s','Account created successfully.');   
            //return redirect()->route('student.home')->with('s','Account created successfully.');   
            
          }
          
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
         //return $request->all();
        //validate the form data
        $this->validate($request, [
            'username' => 'required',
            'password' => 'required|min:2'
        ]);
        //return $request->all();
        //Attempt to log the user in

        // return $request->all();
        session()->flush();
        if( Auth::guard('student')->attempt(['matric'=>$request->username,'password'=>$request->password], $request->remember)){
            // return "Spot 1";
            return redirect()->intended(route('student.home'));
        }elseif(auth('parents')->attempt(['phone'=>$request->username, 'password'=>$request->password])){
            return redirect()->route('parents.home');
        }
        else{
            if( Auth::attempt(['username'=>$request->username,'password'=>$request->password]) ||  Auth::attempt(['matric'=>$request->username,'password'=>$request->password])){
                // return "Spot 2";
                if(Auth::user()->type == 'teacher'){
                    
                    return redirect()->route('user.home')->with('success','Welcome to Teachers Dashboard '.Auth::user()->name);
                }else{
                    return redirect()->route('admin.home')->with('success','Welcome to Admin Dashboard '.Auth::user()->name);
                }
            }
        }
        // return "Spot 3";
        $request->session()->flash('error', 'Invalid Username or Password');
        return redirect()->route('login')->withInput($request->only('username','remember'));
    }

    public function logout(Request $request){
        Auth::logout();
        Auth::guard('student')->logout();
        Auth::guard('parents')->logout();
        return redirect(route('login'));
    }

    public function create_parent(Request $request)
    {
        # code...
        // return $request->all();
        if($request->has('phone')){
            if(Students::where('parent_phone_number', $request->phone)->count() == 0){
                return back()->with('error', __('text.parent_no_child_phrase'));
            }
            if(Guardian::where('phone', $request->phone)->count() > 0){
                return back()->with('error', __('text.parent_phone_used'));
            }
            return view('auth.create_parent', ['phone'=>$request->phone, 'title'=>'Create Parent Account']);
        }
        return view('auth.create_parent', ['title'=>'Create Parent Account']);
    }

    public function save_parent(Request $request)
    {
        # code...
        $validity = Validator::make($request->all(), ['phone'=>'required|min:9']);
        if($validity->fails()){return redirect(route('create_parent'))->with('error', $validity->errors()->first());}
        // check if parent number exists
        
        if($request->password == null){
            // return 2324;
            $phone = $request->phone;
            if(Students::where('parent_phone_number', 'LIKE', "%{$phone}%")->count() > 0){
                return view('auth.create_parent', ['title'=>'Create Parent Account', 'phone'=>$phone]);
            }
            return back()->with('error', __('text.parent_no_child_phrase'));
        }else{
            $validity = Validator::make($request->all(), ['phone'=>'required', 'confirm_password'=>'required|min:6', 'password'=>'required|same:confirm_password']);
            if($validity->fails()){
                // return $validity->errors()->first();
                return redirect(route('create_parent'))->with('error', $validity->errors()->first());
            }
            if(Guardian::where('phone', $request->phone)->count() > 0){
                return redirect(route('login'))->with('error', __('text.account_already_exist_with_this_phone_number'));
            }
            // save guardian
            $data = ['phone'=>$request->phone, 'password'=>Hash::make($request->password)];
            (new Guardian($data))->save();
            return redirect(route('login'))->with('success', __('text.parent_account_created_successfully'));
        }
    }

}
