<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserLoginLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class UserTrackingService
{
    public function track(User $user, Request $request): void
    {
        $context = $this->buildFromRequest($request, $user->login_provider ?: 'email');

        UserLoginLog::create([
            'user_id' => $user->id,
            'provider' => $context['provider'],
            'ip_address' => $context['ip_address'],
            'device_type' => $context['device_type'],
            'browser' => $context['browser'],
            'os' => $context['os'],
            'country' => $context['country'],
            'region' => $context['region'],
            'city' => $context['city'],
            'latitude' => $context['latitude'],
            'longitude' => $context['longitude'],
            'logged_in_at' => now(),
        ]);

        $user->forceFill([
            'last_login_at' => now(),
            'last_login_ip' => $context['ip_address'],
            'last_login_device' => $context['device_type'],
            'last_login_browser' => $context['browser'],
            'last_login_os' => $context['os'],
            'login_provider' => $context['provider'],
        ])->save();
    }

    public function buildFromRequest(Request $request, ?string $provider = null): array
    {
        return $this->buildFromIpAndUserAgent(
            (string) $request->ip(),
            (string) $request->userAgent(),
            $provider
        );
    }

    public function buildFromIpAndUserAgent(?string $ip, ?string $userAgent, ?string $provider = null): array
    {
        $normalizedIp = (string) $ip;
        $normalizedUserAgent = (string) $userAgent;

        $geo = [
            'country' => null,
            'region' => null,
            'city' => null,
            'latitude' => null,
            'longitude' => null,
        ];

        if ($normalizedIp !== '') {
            try {
                $response = Http::timeout(5)->get("http://ip-api.com/json/{$normalizedIp}");

                if ($response->successful()) {
                    $data = $response->json();

                    $geo['country'] = $data['country'] ?? null;
                    $geo['region'] = $data['regionName'] ?? null;
                    $geo['city'] = $data['city'] ?? null;
                    $geo['latitude'] = $data['lat'] ?? null;
                    $geo['longitude'] = $data['lon'] ?? null;
                }
            } catch (\Throwable $e) {
                // Best-effort tracking: keep auth flow resilient if geo lookup fails.
            }
        }

        return [
            'provider' => $provider ?: 'email',
            'ip_address' => $normalizedIp !== '' ? $normalizedIp : null,
            'device_type' => $this->detectDeviceType($normalizedUserAgent),
            'browser' => $this->detectBrowser($normalizedUserAgent),
            'os' => $this->detectOs($normalizedUserAgent),
            'country' => $geo['country'],
            'region' => $geo['region'],
            'city' => $geo['city'],
            'latitude' => $geo['latitude'],
            'longitude' => $geo['longitude'],
        ];
    }

    private function detectDeviceType(string $userAgent): string
    {
        if (preg_match('/mobile|iphone|android.+mobile|windows phone|blackberry|opera mini/i', $userAgent)) {
            return 'Mobile';
        }

        if (preg_match('/tablet|ipad|android(?!.*mobile)|kindle|playbook|silk/i', $userAgent)) {
            return 'Tablet';
        }

        return 'Desktop';
    }

    private function detectBrowser(string $userAgent): string
    {
        if (preg_match('/Edg\//i', $userAgent)) {
            return 'Edge';
        }

        if (preg_match('/OPR\/|Opera/i', $userAgent)) {
            return 'Opera';
        }

        if (preg_match('/Chrome\//i', $userAgent)) {
            return 'Chrome';
        }

        if (preg_match('/Firefox\//i', $userAgent)) {
            return 'Firefox';
        }

        if (preg_match('/Safari\//i', $userAgent)) {
            return 'Safari';
        }

        return 'Unknown';
    }

    private function detectOs(string $userAgent): string
    {
        if (preg_match('/Android/i', $userAgent)) {
            return 'Android';
        }

        if (preg_match('/iPhone|iPad|iPod/i', $userAgent)) {
            return 'iOS';
        }

        if (preg_match('/Windows/i', $userAgent)) {
            return 'Windows';
        }

        if (preg_match('/Macintosh|Mac OS X/i', $userAgent)) {
            return 'Mac';
        }

        if (preg_match('/Linux/i', $userAgent)) {
            return 'Linux';
        }

        return 'Unknown';
    }
}
