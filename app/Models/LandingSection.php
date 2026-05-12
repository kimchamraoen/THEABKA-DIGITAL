<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingSection extends Model
{
    protected $fillable = [
        'section_key', 'title', 'subtitle', 'body',
        'image', 'video_url', 'button_text', 'button_url',
        'sort_order', 'is_visible',
    ];

    protected function casts(): array
    {
        return [
            'is_visible' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Get all visible sections ordered.
     */
    public static function visible()
    {
        return cache()->remember('landing_sections', 3600, function () {
            return self::where('is_visible', true)->orderBy('sort_order')->get();
        });
    }

    protected static function booted(): void
    {
        static::saved(fn() => cache()->forget('landing_sections'));
        static::deleted(fn() => cache()->forget('landing_sections'));
    }
}
