<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

class Language
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
        $availableLangs = Config::get('languages');
        $userLangs = substr($request->server('HTTP_ACCEPT_LANGUAGE'), 0, 2);

        if (Session::has('appLocale'))
        {
            App::setlocale(Session::get('appLocale'));
        }
        else if (in_array($userLangs, $availableLangs))
        {
            App::setLocale($userLangs);
            Session::put('appLocale', $userLangs);
        }
        else {
            App::setLocale('en');
            Session::put('appLocale', 'en');
        }
        return $next($request);
    }
}