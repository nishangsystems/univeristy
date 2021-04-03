<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;


class WelcomeController extends Controller
{
    public function home()
    {
        if(!Auth::guard('student')->check()){
            if(Auth::user()->type == 'teacher'){
                return redirect()->route('teacher.home');
             }elseif(Auth::user()->type == 'admin'){
               return redirect()->route('admin.home');
            }
        }elseif(Auth::guard('student')->check()){
            return redirect()->route('student.home');
        }

        return redirect()->to(route('login'));

    }



}
