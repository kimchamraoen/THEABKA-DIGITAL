<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! in_array($request->user()->role, ['super_admin', 'admin'], true)) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        return $next($request);
    }
}
