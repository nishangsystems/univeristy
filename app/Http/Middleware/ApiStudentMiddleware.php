<?php

namespace App\Http\Middleware;

use App\Helpers\Helpers;
use App\Models\PlatformCharge;
use Closure;
use Illuminate\Http\Request;

class ApiStudentMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if(auth('student_api')->user() == null){
            return response()->json(['message'=>'User session not found'], 400);
        }

        $user = auth('student_api')->user();
        if(!($user->parent_phone_number != null && strlen($user->parent_phone_number) > 7)){
            return redirect(route('student.edit_profile'))->with('message', 'Complete your profile to continue');
        }
        if($user->parent_phone_number == $user->phone ){
            return redirect(route('student.edit_profile'))->with('message', 'Update your profile to continue. Parent and student phone number must not be the same.');
        }

        $charge = PlatformCharge::where(['year_id'=>Helpers::instance()->getCurrentAccademicYear()])->first();
        if (Helpers::instance()->payCharges() && ($charge != null && $charge->yearly_amount > 0)){
            if(!(Helpers::instance()->has_paid_platform_charges())){
                return redirect(route('platform_charge.pay'))->with('error', 'Pay PLATFORM CHARGES to continue.');
            }
        }

        return $next($request);
    }
}
