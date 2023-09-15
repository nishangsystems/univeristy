<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\User;
use Illuminate\Support\Carbon;

class AdminMiddleware
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
        // dd(auth()->user());
        if(Auth::user() == null){
        return redirect(route('login'));
      }elseif(Auth::user()->type != 'admin') //If user does //not have this permission
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
