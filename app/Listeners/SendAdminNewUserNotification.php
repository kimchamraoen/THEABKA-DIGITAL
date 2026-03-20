<?php

namespace App\Listeners;

use App\Jobs\SendAdminNewUserNotificationJob;
use App\Services\AdminEmailResolver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Route;

class SendAdminNewUserNotification
{
    public function __construct(private AdminEmailResolver $adminEmailResolver)
    {
    }

    public function handle(Registered $event): void
    {
        $adminEmail = $this->adminEmailResolver->resolve();

        if (! $adminEmail) {
            return;
        }

        $request = request();
        $provider = $this->resolveProvider($request?->route('provider'), $request?->path());

        $viewUserUrl = Route::has('admin.users.show')
            ? route('admin.users.show', $event->user->id)
            : route('admin.users', ['user' => $event->user->id]);

        dispatch(new SendAdminNewUserNotificationJob(
            recipientEmail: $adminEmail,
            payload: [
                'name' => $event->user->name,
                'email' => $event->user->email ?: 'N/A',
                'view_user_url' => $viewUserUrl,
                'registered_at' => now()->timezone('Asia/Phnom_Penh')->format('Y-m-d H:i:s') . ' (Asia/Phnom_Penh)',
            ],
            ipAddress: $request?->ip(),
            userAgent: $request?->userAgent(),
            provider: $provider,
        ));
    }

    private function resolveProvider(?string $routeProvider, ?string $path): string
    {
        if ($routeProvider) {
            return $routeProvider;
        }

        if ($path && str_contains($path, 'auth/telegram/callback')) {
            return 'telegram';
        }

        return 'email';
    }
}
