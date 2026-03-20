<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class Language extends Model
{
    protected $fillable = [
        'name',
        'locale',
        'flag',
        'font_type',
        'font_value',
        'is_active',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public static function ensureDefaults(): void
    {
        if (! Schema::hasTable('languages')) {
            return;
        }

        if (self::query()->count() > 0) {
            return;
        }

        self::query()->create([
            'name' => 'English',
            'locale' => 'en',
            'flag' => '🇬🇧',
            'font_type' => 'system',
            'font_value' => null,
            'is_active' => true,
            'is_default' => true,
        ]);

        self::query()->create([
            'name' => 'Khmer',
            'locale' => 'km',
            'flag' => '🇰🇭',
            'font_type' => 'system',
            'font_value' => null,
            'is_active' => true,
            'is_default' => false,
        ]);
    }

    public static function getActiveLocales(): array
    {
        return Cache::remember('active_locales', 3600, function () {
            if (! Schema::hasTable('languages')) {
                return ['en', 'km'];
            }

            self::ensureDefaults();

            return self::active()
                ->orderByDesc('is_default')
                ->orderBy('name')
                ->pluck('locale')
                ->values()
                ->all();
        });
    }

    protected static function booted(): void
    {
        static::saved(fn () => self::clearLanguageCache());
        static::deleted(fn () => self::clearLanguageCache());
    }

    public static function clearLanguageCache(): void
    {
        Cache::forget('active_locales');
        Cache::forget('active_languages');
    }
}
