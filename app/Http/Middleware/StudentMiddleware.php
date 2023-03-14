<?php

namespace App\Http\Middleware;

use App\Helpers\Helpers;
use App\Models\Charge;
use App\Models\PlatformCharge;
use Closure;
use Illuminate\Support\Facades\Auth;
use App\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

class StudentMiddleware
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
          }elseif (!auth('student')->check()) //If user does //not have this permission
            {
                return redirect(route('login'));
            }
            
            if(!PHP_SESSION_ACTIVE){
                return redirect(route('login'));
            }

            $charge = PlatformCharge::first();
            if ($charge != null && $charge->yearly_amount > 0){
                if(!Helpers::instance()->has_paid_platform_charges()){
                    return redirect(route('platform_charge.pay'))->with('error', 'Pay PLATFORM CHARGES to continue.');
                }
            }

        return $next($request);
    }
}
