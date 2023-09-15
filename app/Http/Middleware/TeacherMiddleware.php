<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\User;

class TeacherMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(Auth::user() == null){
            return redirect(route('login'));
        }elseif (!Auth::user()->type == 'teacher') //If user does //not have this permission
        {
            auth()->logout();
            return redirect(route('login'))->with('error', __('text.permission_denied'));
        }elseif(auth()->user()->active != 1){
            auth()->logout();
            return redirect(route('login'))->with('error', __('text.user_account_blocked'));
        }

        return $next($request);
    }
}
