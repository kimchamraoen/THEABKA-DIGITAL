<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Get a social setting value by key.
     * Falls back to env() if not set in database.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();

        if ($setting && $setting->value !== null && $setting->value !== '') {
            return $setting->value;
        }

        // Fallback to environment variable if no database value
        return env($key, $default);
    }

    /**
     * Set a social setting value by key.
     */
    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    /**
     * Get all social setting keys used in the application.
     */
    public static function allKeys(): array
    {
        return [
            'GOOGLE_CLIENT_ID',
            'GOOGLE_CLIENT_SECRET',
            'GOOGLE_REDIRECT_URL',
            'GOOGLE_ENABLED',
            'FACEBOOK_CLIENT_ID',
            'FACEBOOK_CLIENT_SECRET',
            'FACEBOOK_REDIRECT_URL',
            'FACEBOOK_ENABLED',
            'TELEGRAM_BOT_TOKEN',
            'TELEGRAM_BOT_NAME',
            'TELEGRAM_ENABLED',
            'TWITTER_CLIENT_ID',
            'TWITTER_CLIENT_SECRET',
            'TWITTER_REDIRECT_URL',
            'TWITTER_ENABLED',
        ];
    }

    /**
     * Check if a provider is configured (has all required credentials).
     */
    public static function isProviderConfigured(string $provider): bool
    {
        return match ($provider) {
            'google' => !empty(self::get('GOOGLE_CLIENT_ID')) && !empty(self::get('GOOGLE_CLIENT_SECRET')),
            'facebook' => !empty(self::get('FACEBOOK_CLIENT_ID')) && !empty(self::get('FACEBOOK_CLIENT_SECRET')),
            'telegram' => !empty(self::get('TELEGRAM_BOT_TOKEN')) && !empty(self::get('TELEGRAM_BOT_NAME')),
            'twitter' => !empty(self::get('TWITTER_CLIENT_ID')) && !empty(self::get('TWITTER_CLIENT_SECRET')),
            default => false,
        };
    }
}
