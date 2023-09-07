<?php

namespace App\Http\Middleware;

use App\Helpers\Helpers;
use App\Models\Guardian;
use App\Models\PlatformCharge;
use Closure;
use Illuminate\Http\Request;

class ParentPlatformCharge
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
        $parent = auth('parents')->user();
        $year_id = Helpers::instance()->getCurrentAccademicYear();
        $plcharge = PlatformCharge::where(['year_id'=>$year_id])->first();
        $charge = $parent->platformCharges->where('year_id', $year_id)->first();
        if(Helpers::instance()->payCharges() and ($plcharge != null and $plcharge->parent_amount??0 > 0) ){
            if($charge == null){
                return redirect(route('parents.tranzak.platform_charge.pay'));
            }
        }
        return $next($request);
    }
}
