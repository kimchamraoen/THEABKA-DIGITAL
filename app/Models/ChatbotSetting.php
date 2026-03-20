<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatbotSetting extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * Get a chatbot setting value by key.
     * Falls back to env() if no DB value is set.
     */
    public static function get(string $key, mixed $default = null): ?string
    {
        $setting = static::where('key', $key)->first();

        if ($setting && $setting->value !== null && $setting->value !== '') {
            return $setting->value;
        }

        return env($key, $default);
    }

    /**
     * Set a chatbot setting value by key.
     */
    public static function set(string $key, ?string $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
