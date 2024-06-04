<?php

namespace App\Http\Middleware;

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
        return $next($request);
    }
}
