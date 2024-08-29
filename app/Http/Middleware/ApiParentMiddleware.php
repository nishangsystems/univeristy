<?php

namespace App\Http\Middleware;

use App\Helpers\Helpers;
use App\Models\PlatformCharge;
use Closure;
use Illuminate\Http\Request;

class ApiParentMiddleware
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
        $parent = $request->user('parent_api');
        if($parent == null){
            return response()->json(['message'=>"Invalid Credentials", 'error_type'=>'session-expired-error'], 400);
        }
        
        return $next($request);
    }
}
