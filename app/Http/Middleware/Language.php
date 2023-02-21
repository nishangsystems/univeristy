<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Lang;

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
        $userLang = substr($request->server('HTTP_ACCEPT_LANGUAGE'), 0, 2);

        if (Session::has('appLocale'))
        {
            App::setlocale(Session::get('appLocale'));
            Lang::setLocale(Session::get('appLocale'));
        }
        else if (in_array($userLang, $availableLangs))
        {
            App::setLocale($userLang);
            Session::put('appLocale', $userLang);
            Lang::setLocale($userLang);
        }
        else {
            App::setLocale('en');
            Lang::setLocale('en');
        }
        return $next($request);
    }
}