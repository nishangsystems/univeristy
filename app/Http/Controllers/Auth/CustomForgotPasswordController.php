<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use App\Mail\ResetEmail;
use App\Models\Students;
use App\Models\User;
use Carbon\Carbon;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CustomForgotPasswordController extends Controller
{

    private function sendResetEmail($email, $token)
    {
        $link = route('reset', [$token, urlencode($email)]);

        try {
            $data['email'] = $email;
            $data['link'] = $link;
            Mail::to($email)->send(new ResetEmail($data));
        } catch (\Exception $e) {
            die ($e);
        }
        return true;
    }

    public function validatePasswordRequest(Request $request)
    {
        $type = $request->type;
        $email = $request->email;
        if ($type) {
            $user = Students::where('email', $email)->get();
            if ($user->count() == 0) {
                return redirect()->back()->with('e', 'Student does not exist with this email');
            }
        } else {
            $user = User::where('email', $email)->get();
            if ($user->count() == 0) {
                return redirect()->back()->with('e', 'User does not exist with this email');
            }
        }


        //Get the token just created above
        $tokenData = \DB::table('password_resets')
            ->where('email', $email)->delete();

        //Create Password Reset Token
        \DB::table('password_resets')->insert([
            'email' => $email,
            'token' => Str::random(64),
            'created_at' => Carbon::now(),
            'type' => ($type) ? '1' : '0'
        ]);

        //Get the token just created above
        $tokenData = \DB::table('password_resets')
            ->where('email', $email)->first();


        if ($this->sendResetEmail($email, $tokenData->token)) {
            return redirect()->back()->with('s', 'A reset link has been sent to your email address.');
        } else {
            return redirect()->back()->with('e', 'A Network Error occurred. Please try again.');
        }
    }

    public function resetPassword(Request $request)
{
    
    //Validate input
    $validator = Validator::make($request->all(), [
        'email' => 'required',
        'token' => 'required',
        'password' => 'required|confirmed',
    ]);

    //check if input is valid before moving on
    if ($validator->fails()) {
        return redirect()->back()->withErrors(['email' => 'Please complete the form']);
    }
    

    $password = $request->password;// Validate the token
    $tokenData = \DB::table('password_resets')
    ->where('token', $request->token)->first();// Redirect the user back to the password reset request form if the token is invalid
    if (!$tokenData){
        $request->session()->flash('error', 'Invalid Password Reset Link');
        return view('auth.login');
    } 

    if($tokenData->type == 0){
        $user = User::where('email', $tokenData->email)->first();
        if (!$user) return redirect()->back()->withErrors(['email' => 'Email not found']);//Hash and update the new password
        $user->password = \Hash::make($password);
        $user->save();
        //login the user immediately they change password successfully
        \Auth::login($user);
    }else{
        $user = Students::where('email', $tokenData->email)->first();
        if (!$user) return redirect()->back()->withErrors(['email' => 'Email not found']);//Hash and update the new password
        $user->password = \Hash::make($password);
        $user->save();
        //login the user immediately they change password successfully
        \Auth::guard('student')->login($user);
    }
   
    //Delete the token
    \DB::table('password_resets')->where('email', $user->email)
    ->delete();
return redirect()->route('login')->with('s','Password Changed Successfully');
        // return redirect()->to(route('login'));

}

    public function resetForm($token, $email){
        //dd($email);
        $data['token'] = $token;
        $data['email'] = $email;
        return view('auth.passwords.reset')->with($data);
    }

    public function recover_username(Request $request)
    {
        $validity = Validator::make($request->all(), ['matric'=>'required']);
        if($validity->fails()){
            return back()->with('error', $validity->errors()->first());
        }
        $student = Students::where('matric', $request->matric)->first();
        if($student != null){
            if($student->username == null){
                return back()->with('error', __('text.no_username_registered_for_this_account'));
            }
            return back()->with('success', __('text.word_done'));
        }
        return back()->with('error', __('text.could_not_find_any_account_with_specified_matricule'));
    }
}
