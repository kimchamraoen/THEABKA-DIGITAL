<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class NavLabel extends Model
{
    use HasFactory;

    public const CACHE_KEY = 'nav_labels';

    protected $fillable = [
        'key',
        'label',
        'is_visible',
        'sort_order',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
        'sort_order' => 'integer',
    ];

    public static function allCached()
    {
        return Cache::remember(self::CACHE_KEY, 3600, fn() => self::query()->orderBy('sort_order')->orderBy('id')->get());
    }

    public static function defaults(): array
    {
        return [
            ['key' => 'sidebar.dashboard', 'label' => 'Dashboard', 'sort_order' => 10],
            ['key' => 'sidebar.chatbot', 'label' => 'Chatbot', 'sort_order' => 20],
            ['key' => 'sidebar.profile', 'label' => 'Profile', 'sort_order' => 30],
            ['key' => 'sidebar.docs', 'label' => 'Docs', 'sort_order' => 40],
            ['key' => 'sidebar.analytics', 'label' => 'Analytics', 'sort_order' => 50],
            ['key' => 'sidebar.broadcasts', 'label' => 'Broadcasts', 'sort_order' => 60],
            ['key' => 'sidebar.users', 'label' => 'Users', 'sort_order' => 70],
            ['key' => 'sidebar.translations', 'label' => 'Translations', 'sort_order' => 80],
            ['key' => 'sidebar.settings', 'label' => 'Settings', 'sort_order' => 90],
        ];
    }

    public static function defaultLabelForKey(string $key): ?string
    {
        foreach (self::defaults() as $row) {
            if (($row['key'] ?? null) === $key) {
                return $row['label'] ?? null;
            }
        }

        return null;
    }

    public static function findByKey(string $key): ?self
    {
        return self::allCached()->firstWhere('key', $key);
    }

    public static function upsertDefaults(array $defaults): void
    {
        foreach ($defaults as $row) {
            self::query()->firstOrCreate(
                ['key' => $row['key']],
                [
                    'label' => $row['label'],
                    'is_visible' => $row['is_visible'] ?? true,
                    'sort_order' => $row['sort_order'] ?? 0,
                ]
            );
        }

        Cache::forget(self::CACHE_KEY);
    }
}
