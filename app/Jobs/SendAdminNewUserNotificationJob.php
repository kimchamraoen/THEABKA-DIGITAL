<?php

namespace App\Jobs;

use App\Mail\AdminNewUserNotification;
use App\Models\Setting;
use App\Services\UserTrackingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;

class SendAdminNewUserNotificationJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public string $recipientEmail,
        public array $payload,
        public ?string $ipAddress = null,
        public ?string $userAgent = null,
        public ?string $provider = 'email',
    ) {
    }

    public function handle(UserTrackingService $trackingService): void
    {
        $appName = config('app.name', 'Application');
        $appLogoUrl = null;

        try {
            if (Schema::hasTable('settings')) {
                $settings = Setting::instance();
                $appName = $settings->app_name ?: $appName;
                $appLogoUrl = $settings->logo_url;
            }
        } catch (\Throwable $e) {
            // Keep email delivery resilient if settings lookup fails.
        }

        $context = $trackingService->buildFromIpAndUserAgent(
            $this->ipAddress,
            $this->userAgent,
            $this->provider
        );

        Mail::to($this->recipientEmail)->send(new AdminNewUserNotification([
            ...$this->payload,
            'provider' => $context['provider'] ?? 'email',
            'ip_address' => $context['ip_address'] ?? 'Unknown',
            'country' => $context['country'] ?? 'Unknown',
            'city' => $context['city'] ?? 'Unknown',
            'device_type' => $context['device_type'] ?? 'Unknown',
            'os' => $context['os'] ?? 'Unknown',
            'browser' => $context['browser'] ?? 'Unknown',
            'app_name' => $appName,
            'app_logo_url' => $appLogoUrl,
        ]));
    }
}
