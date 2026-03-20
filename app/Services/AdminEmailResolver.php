<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Schema;

class AdminEmailResolver
{
    public function resolve(): ?string
    {
        try {
            if (Schema::hasTable('settings')) {
                $settings = Setting::instance();
                $adminEmail = trim((string) ($settings->admin_email ?? ''));

                if ($adminEmail !== '') {
                    return $adminEmail;
                }

                $smtpFrom = trim((string) ($settings->smtp_from_address ?? ''));

                if ($smtpFrom !== '') {
                    return $smtpFrom;
                }
            }
        } catch (\Throwable $e) {
            // Fall back to config value when DB is unavailable.
        }

        $configAdmin = trim((string) config('app.admin_email'));

        return $configAdmin !== '' ? $configAdmin : null;
    }
}
