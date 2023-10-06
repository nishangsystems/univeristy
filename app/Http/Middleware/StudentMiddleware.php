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
        if(auth('student')->user() == null){
            return redirect(route('login'));
          }

        if(!(auth('student')->user()->parent_phone_number != null && strlen(auth('student')->user()->parent_phone_number) > 7)){
            return redirect(route('student.edit_profile'))->with('message', 'Complete your profile to continue');
        }

        $charge = PlatformCharge::where(['year_id'=>Helpers::instance()->getCurrentAccademicYear()])->first();
        if (Helpers::instance()->payCharges() && ($charge != null && $charge->yearly_amount > 0)){
            if(!Helpers::instance()->has_paid_platform_charges()){
                return redirect(route('platform_charge.pay'))->with('error', 'Pay PLATFORM CHARGES to continue.');
            }
        }
        
        return $next($request);
    }
}
