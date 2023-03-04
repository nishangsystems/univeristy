<?php

namespace App\Http\Controllers;

use App\Models\CampusProgram;
use App\Models\Students;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function set_local(Request $request, $lang)
    {
        # code...
        // return $lang;
        if (array_key_exists($lang, Config::get('languages'))) {
            Session::put('appLocale', $lang);
            App::setLocale($lang);
        }
        return back();
    }

    public static function sorted_program_levels()
    {
        $pls = [];
        # code...
        foreach (\App\Models\ProgramLevel::all() as $key => $value) {
            # code...
            $pls[] = [
                'id' => $value->id,
                'level_id'=>$value->level_id,
                'program_id'=>$value->program_id,
                'name' => $value->program()->first()->name.': LEVEL '.$value->level()->first()->level,
                'department'=> $value->program()->first()->parent()->first()->id
            ];
        }
        $pls = collect($pls)->sortBy(['name', 'level_id']);
        // $pls->where('id')
        return $pls;
    }
    public static function sorted_campus_program_levels($campus)
    {
        $pls = [];
        # code...
        $program_level_ids = CampusProgram::where(['campus_id'=>$campus])->pluck('program_level_id');
        foreach (\App\Models\ProgramLevel::whereIn('id', $program_level_ids)->get() as $key => $value) {
            # code...
            $pls[] = [
                'id' => $value->id,
                'level_id'=>$value->level_id,
                'program_id'=>$value->program_id,
                'name' => $value->program()->first()->name.': LEVEL '.$value->level()->first()->level
            ];
        }
        $pls = collect($pls)->sortBy('si');
        return $pls;
    }

    public function registration(){
        return view('auth.registration');
    }

    public function check_matricule(Request $request){
        // if (Students::where('matric', $request->reg_no)->exists()) { 
        //        if (User::where('username', $request->reg_no)->exists()) {   
        //           return redirect()->route('registration')->with('error','Matricule Number has being used already. Contact the System Administrator.');   
        //        }else{
        //          $student_d = Students::where('matric', $request->reg_no)->first();   
        //          return view('auth.registration_info',compact('student_d'));
        //        }
             
        //    }
        //    else{
        //      return redirect()->route('registration')->with('error','Invalid Registration Number.');   
        //    }

           if (Students::where('matric', $request->reg_no)->exists()) { 
                if (Students::where('matric', $request->reg_no)->whereNotNull('email')->exists()){
                    return redirect()->route('login')->with('error','Account already exist');   
                }
                else {
                    $student_d = Students::where('matric', $request->reg_no)->first();
                    return view('auth.registration_info',compact('student_d'));
                }
           }
           else{
             return redirect()->route('registration')->with('error','Matricule '.$request->reg_no.' Not Found in the system. Contact the registry for rectification.');   
           }
     }

     public function createAccount(Request $request){
        
        if(User::where('phone', $request->phone)->count() > 0){
            return redirect()->route('registration')->with('error', __('text.validation_phrase1'));
            //  return back()->with('error', 'text.validattion_phrase1');
            }
            // return $request->all();
        if (Students::where('matric', $request->username)->exists()) {  
            $update['phone'] = $request->phone;
            $update['email'] = $request->email;
            $update['password'] = Hash::make($request->password);
            
            $up = Students::where('matric', $request->username)->update($update);
            //  if (User::where('username', $request->username)->exists()) {  
            // $update1['name'] = $request->name;
            // $update1['email'] = $request->email;
            // $update1['username'] = $request->username;
            // $update1['type'] = 'student';
            // $update1['password'] = Hash::make($request->password);
            
            // $up1 = User::where('username', $request->username)->update($update1);
            //  }else{
            //      $insert['name'] = $request->name;
            //     $insert['email'] = $request->email;
            //     $insert['username'] = $request->username;
            //     $insert['type'] = 'student';
            //     $insert['gender'] = '';
            //     $insert['password'] = Hash::make($request->password);
            
            // $up2 = User::create($insert);
            //  }
        //      if( Auth::guard('student')->attempt(['matric'=>$request->username,'password'=>$request->password], $request->remember)){
        //     // return "Spot 1";
        //     return redirect()->intended(route('student.home'));
        // }else{
        //     return redirect()->route('login')->with('s','Account created successfully.');   
        // }
            if(auth('student')->attempt(['matric'=>$request->username, 'password'=>$request->password])){return redirect(route('login'));}
            return redirect()->route('login')->with('s','Account created successfully.');   
            //return redirect()->route('student.home')->with('s','Account created successfully.');   
            
          }

    }

    public function reset_password(Request $request, $id= null)
    {
        # code...
        $data['title'] = "Reset Password";
        if (auth()->guard('student')->check()) {
            return view('student.reset_password', $data);
        }
        else {
            if (auth()->user()->type == 'admin') {
                return view('admin.reset_password', $data);
            }else{
                return view('teacher.reset_password', $data);
            }
        }
    }

    public function reset_password_save(Request $request)
    {
        # code...
        $validator = Validator::make($request->all(), [
            'current_password'=>'required',
            'new_password_confirmation'=>'required_with:new_password|same:new_password|min:6',
            'new_password'=>'required|min:6',
        ]);
        if($validator->fails()){
            return back()->with('error', $validator->errors()->first());
        }
        if (auth()->guard('student')->check()) {
            if(Hash::check($request->current_password, auth('student')->user()->getAuthPassword())){
                $stud = Students::find(auth('student')->id());
                $stud->password = Hash::make($request->new_password);
                $stud->password_reset = true;
                $stud->save();
                return back()->with('success', 'Done');
            }else{
                return back()->with('error', 'Operation failed. Make sure you entered the correct password');
            }
        }
        else{
            if(Hash::check($request->current_password, auth()->user()->getAuthPassword())){
                $user = User::find(auth()->id());
                $user->password = Hash::make($request->new_password);
                $user->password_reset = true;
                $user->save();
                return back()->with('success', 'Done');
            }else{
                return back()->with('error', 'Operation failed. Make sure you entered the correct password');
            }
        }

    }

}
