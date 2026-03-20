<?php

namespace App\Providers;

use App\Models\Setting;
use App\Listeners\SendAdminNewUserNotification;
use App\Listeners\SendUserLoginNotification;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $helpers = app_path('Helpers/helpers.php');
        if (file_exists($helpers)) {
            require_once $helpers;
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS when behind a reverse proxy (e.g. Cloudflare Tunnel)
        if (str_starts_with(config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        Event::listen(Registered::class, SendAdminNewUserNotification::class);
        Event::listen(Login::class, SendUserLoginNotification::class);

        // Flash session on new registration for success message
        Event::listen(Registered::class, function () {
            session()->flash('registered', true);
        });

        // Load timezone from settings
        $this->loadTimezone();

        // Dynamically load SMTP settings from database
        $this->loadSmtpSettings();
    }

    /**
     * Load timezone from settings and set it as the app default.
     */
    protected function loadTimezone(): void
    {
        try {
            if (! Schema::hasTable('settings')) {
                return;
            }

            $settings = Setting::instance();

            if ($settings->timezone) {
                config(['app.timezone' => $settings->timezone]);
                date_default_timezone_set($settings->timezone);
            }
        } catch (\Exception $e) {
            // Database may not be available during artisan commands
        }
    }

    /**
     * Load SMTP settings from database and override mail config.
     */
    protected function loadSmtpSettings(): void
    {
        try {
            if (! Schema::hasTable('settings')) {
                return;
            }

            $settings = Setting::instance();

            if ($settings->smtp_host) {
                config([
                    'mail.default' => 'smtp',
                    'mail.mailers.smtp.host' => $settings->smtp_host,
                    'mail.mailers.smtp.port' => $settings->smtp_port ?? 587,
                    'mail.mailers.smtp.username' => $settings->smtp_username,
                    'mail.mailers.smtp.password' => $settings->smtp_password,
                    'mail.mailers.smtp.encryption' => $settings->smtp_encryption,
                    'mail.from.address' => $settings->smtp_from_address ?? config('mail.from.address'),
                    'mail.from.name' => $settings->smtp_from_name ?? config('mail.from.name'),
                ]);
            }
        } catch (\Exception $e) {
            // Database may not be available during artisan commands
        }
    }
}
