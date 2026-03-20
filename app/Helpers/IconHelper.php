<?php

namespace App\Helpers;

use App\Models\SiteIcon;
use Illuminate\Support\Facades\Storage;

class IconHelper
{
    public static function getIcon(string $key, string $fallback = ''): string
    {
        $icon = SiteIcon::findByKey($key);

        if (!$icon || !$icon->icon_value) {
            return $fallback;
        }

        $rendered = match ($icon->icon_type) {
            'css_class' => '<i class="' . e($icon->icon_value) . '"></i>',
            'emoji' => '<span>' . e($icon->icon_value) . '</span>',
            'image' => self::renderImage($icon->icon_value),
            default => $icon->icon_value,
        };

        return trim((string) $rendered) !== '' ? $rendered : $fallback;
    }

    protected static function renderImage(string $path): string
    {
        if (!Storage::disk('public')->exists($path)) {
            return '';
        }

        return '<img src="' . e(asset('storage/' . ltrim($path, '/'))) . '" alt="icon" style="width:100%;height:100%;object-fit:contain;display:block;" />';
    }
}
