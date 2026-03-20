<?php

namespace App\Http\Middleware;

use App\Models\Language;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = session('locale', config('app.locale'));
        $activeLocales = Language::getActiveLocales();

        if (in_array($locale, $activeLocales, true)) {
            App::setLocale($locale);
        } elseif (! empty($activeLocales)) {
            App::setLocale($activeLocales[0]);
            session(['locale' => $activeLocales[0]]);
        }

        return $next($request);
    }
}
