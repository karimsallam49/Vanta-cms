<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        // The preference lives on the user record; the session is only
        // a fallback for guests hitting the login screen.
        $locale = $request->user()?->locale
            ?? $request->session()->get('locale', 'en');

        app()->setLocale(in_array($locale, ['en', 'ar'], true) ? $locale : 'en');

        return $next($request);
    }
}
