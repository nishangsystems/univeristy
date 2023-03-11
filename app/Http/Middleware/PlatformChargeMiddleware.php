<?php

namespace App\Http\Middleware;

use App\Helpers\Helpers;
use App\Models\Charge;
use App\Models\PlatformCharge;
use Closure;
use Illuminate\Support\Facades\Auth;
use App\User;

class PlatformChargeMiddleware
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
        $charge = PlatformCharge::first();
        if ($charge != null && $charge->yearly_amount > 0){
            if(Helpers::instance()->has_paid_platform_charges(auth()->user())){
                return redirect(route('student.platform_charge.pay'))->with('error', 'Pay PLATFORM CHARGES to continue.');
            }
        }

        return $next($request);
    }
}
