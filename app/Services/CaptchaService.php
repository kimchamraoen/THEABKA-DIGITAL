<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;

class CaptchaService
{
    /**
     * Get the active captcha provider (null, 'recaptcha', 'turnstile').
     */
    public static function provider(): ?string
    {
        $settings = Setting::instance();
        $provider = $settings->captcha_provider;

        if (!$provider) {
            return null;
        }

        // Only return provider if keys are fully configured
        if (!static::hasValidKeys($settings, $provider)) {
            return null;
        }

        return $provider;
    }

    /**
     * Check if both site key and secret key are configured for a provider.
     */
    protected static function hasValidKeys(Setting $settings, string $provider): bool
    {
        return match ($provider) {
            'recaptcha' => !empty($settings->recaptcha_site_key) && !empty($settings->recaptcha_secret_key),
            'turnstile' => !empty($settings->turnstile_site_key) && !empty($settings->turnstile_secret_key),
            default => false,
        };
    }

    /**
     * Check if captcha is enabled for a given page.
     */
    public static function enabledFor(string $page): bool
    {
        $settings = Setting::instance();
        $provider = $settings->captcha_provider;

        if (!$provider || !static::hasValidKeys($settings, $provider)) {
            return false;
        }

        return match ($page) {
            'login' => (bool) $settings->captcha_on_login,
            'register' => (bool) $settings->captcha_on_register,
            default => false,
        };
    }

    /**
     * Get the site key for the active provider.
     */
    public static function siteKey(): ?string
    {
        $settings = Setting::instance();

        return match ($settings->captcha_provider) {
            'recaptcha' => $settings->recaptcha_site_key,
            'turnstile' => $settings->turnstile_site_key,
            default => null,
        };
    }

    /**
     * Get the token from the request (handles both provider field names).
     */
    public static function getTokenFromRequest(\Illuminate\Http\Request $request): ?string
    {
        return $request->input('cf-turnstile-response')
            ?? $request->input('g-recaptcha-response');
    }

    /**
     * Get the token from an input array (handles both provider field names).
     */
    public static function getTokenFromArray(array $input): ?string
    {
        return $input['cf-turnstile-response'] ?? $input['g-recaptcha-response'] ?? null;
    }

    /**
     * Verify captcha response token server-side.
     */
    public static function verify(?string $token): bool
    {
        if (!$token) {
            return false;
        }

        $settings = Setting::instance();

        return match ($settings->captcha_provider) {
            'recaptcha' => static::verifyRecaptcha($token, $settings->recaptcha_secret_key),
            'turnstile' => static::verifyTurnstile($token, $settings->turnstile_secret_key),
            default => true,
        };
    }

    protected static function verifyRecaptcha(string $token, ?string $secret): bool
    {
        if (!$secret) {
            return false;
        }

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => $secret,
            'response' => $token,
            'remoteip' => request()->ip(),
        ]);

        return $response->successful() && $response->json('success') === true;
    }

    protected static function verifyTurnstile(string $token, ?string $secret): bool
    {
        if (!$secret) {
            return false;
        }

        $response = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
            'secret' => $secret,
            'response' => $token,
            'remoteip' => request()->ip(),
        ]);

        return $response->successful() && $response->json('success') === true;
    }
}
