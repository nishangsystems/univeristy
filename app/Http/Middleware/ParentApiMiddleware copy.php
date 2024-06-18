<?php

namespace App\Http\Middleware;

use App\Helpers\Helpers;
use App\Models\PlatformCharge;
use Closure;
use Illuminate\Http\Request;

class ParentApiMiddleware
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
        if(auth('parent_api')->user() == null){
            return response()->json(['message'=>"Invalid Credentials", 'error_type'=>'session-expired-error'], 400);
        }

        $parent = $request->user('parent_api');
        $year_id = Helpers::instance()->getCurrentAccademicYear();
        $plcharge = PlatformCharge::where(['year_id'=>$year_id])->first();
        $charge = $parent->platformCharges->where('year_id', $year_id)->first();
        if(Helpers::instance()->payCharges() and ($plcharge != null and $plcharge->parent_amount??0 > 0) ){
            if($charge == null){
                return response()->json(['message'=>'you have not paid platform charges for the current accademic year. Login to '.url('/').' and pay platform charges to continue.']);
            }
        }
        return $next($request);
    }
}
