<?php

namespace App\Listeners;

use App\Jobs\SendUserLoginNotificationJob;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Route;

class SendUserLoginNotification
{
    public function handle(Login $event): void
    {
        if (! $event->user->email) {
            return;
        }

        $request = app()->bound('request') ? request() : null;
        $provider = $this->resolveProvider(
            $request?->route('provider'),
            $request?->path(),
            $event->user->login_provider,
        );

        $profileUrl = Route::has('profile.show')
            ? route('profile.show')
            : url('/user/profile');

        dispatch(new SendUserLoginNotificationJob(
            recipientEmail: $event->user->email,
            payload: [
                'name' => $event->user->name,
                'login_time' => now()->timezone('Asia/Phnom_Penh')->format('Y-m-d H:i:s') . ' (Asia/Phnom_Penh)',
                'profile_url' => $profileUrl,
            ],
            ipAddress: $request?->ip(),
            userAgent: $request?->userAgent(),
            provider: $provider,
        ));
    }

    private function resolveProvider(?string $routeProvider, ?string $path, ?string $userProvider): string
    {
        if ($routeProvider) {
            return $routeProvider;
        }

        if ($path && str_contains($path, 'auth/telegram/callback')) {
            return 'telegram';
        }

        if ($userProvider) {
            return $userProvider;
        }

        return 'email';
    }
}
