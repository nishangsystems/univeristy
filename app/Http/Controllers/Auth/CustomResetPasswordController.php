<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Password;
use Auth;
use Illuminate\Http\Request;

class CustomResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/student';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:student');
    }
    protected function guard(){
        return Auth::guard('student');
    }
    protected function brokers(){
        return Password::broker('student');
    }

    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.passwords.student-reset')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

}
