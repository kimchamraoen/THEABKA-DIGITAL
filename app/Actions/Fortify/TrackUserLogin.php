<?php

namespace App\Actions\Fortify;

use App\Services\UserTrackingService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrackUserLogin
{
    public function __construct(private UserTrackingService $trackingService)
    {
    }

    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $user = Auth::user();
        if ($user) {
            $user->forceFill([
                'login_provider' => 'email',
            ])->save();

            $this->trackingService->track($user, $request);
        }

        return $response;
    }
}
