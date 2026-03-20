<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteIcon extends Model
{
    use HasFactory;

    public const CACHE_KEY = 'site_icons';

    protected $fillable = [
        'key',
        'label',
        'icon_type',
        'icon_value',
        'page',
    ];

    public static function allCached()
    {
        return Cache::remember(self::CACHE_KEY, 3600, fn() => self::query()->orderBy('key')->get());
    }

    public static function findByKey(string $key): ?self
    {
        return self::allCached()->firstWhere('key', $key);
    }

    public static function upsertDefaults(array $defaults): void
    {
        foreach ($defaults as $icon) {
            self::query()->firstOrCreate(
                ['key' => $icon['key']],
                [
                    'label' => $icon['label'],
                    'icon_type' => $icon['icon_type'] ?? 'svg',
                    'icon_value' => $icon['icon_value'] ?? null,
                    'page' => $icon['page'],
                ]
            );
        }

        Cache::forget(self::CACHE_KEY);
    }
}
