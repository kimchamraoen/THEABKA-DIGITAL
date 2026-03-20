<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailIsVerifiedOrBypassed
{
    /**
     * Handle an incoming request.
     *
     * Allows the user through if:
     * - User has verified their email
     * - User has the bypass_email_verification flag set
     * - Global setting allow_unverified_login is enabled
     */
    public function handle(Request $request, Closure $next, ?string $redirectToRoute = null): Response
    {
        if (! $request->user()) {
            return $next($request);
        }

        // Use the canBypassEmailVerification method on the User model
        if ($request->user()->canBypassEmailVerification()) {
            return $next($request);
        }

        return $request->expectsJson()
            ? abort(403, 'Your email address is not verified.')
            : Redirect::guest(URL::route($redirectToRoute ?: 'verification.notice'));
    }
}
