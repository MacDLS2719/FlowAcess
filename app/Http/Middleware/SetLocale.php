<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        if (Session::has('locale')) {

            app()->setLocale(Session::get('locale'));

        } else {

            $locale = substr(
                $request->server('HTTP_ACCEPT_LANGUAGE'),
                0,
                2
            );

            if (! in_array($locale, ['es', 'en'])) {
                $locale = 'es';
            }

            app()->setLocale($locale);
        }

        return $next($request);
    }
}