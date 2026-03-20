<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    protected $fillable = [
        'name', 'slug', 'is_default', 'is_active',
        'bg_type', 'bg_image', 'bg_video', 'bg_overlay_opacity', 'bg_overlay_color',
        'dark_bg_from', 'dark_bg_via', 'dark_bg_to', 'dark_gradient_direction',
        'light_bg_from', 'light_bg_via', 'light_bg_to', 'light_gradient_direction',
        'color_primary', 'color_secondary', 'color_accent',
        'color_success', 'color_warning', 'color_danger',
        'glass_blur', 'glass_bg_opacity', 'glass_border_opacity', 'glass_shadow_opacity',
        'glass_tint_color', 'glass_saturation', 'glass_noise_opacity',
        'glass_brightness', 'glass_noise_texture',
        'glass_style_settings',
        'font_color_dark', 'font_color_light',
        'blob_color_1', 'blob_color_2', 'blob_color_3', 'blob_color_4', 'blobs_enabled',
        'custom_css',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'is_active' => 'boolean',
            'blobs_enabled' => 'boolean',
            'glass_style_settings' => 'array',
        ];
    }

    /**
     * Built-in defaults for each glass style.
     * These values are used when no per-style customization has been saved.
     */
    public const GLASS_STYLE_DEFAULTS = [
        'liquid' => [
            'blur' => '16px',
            'brightness' => '0.97',
            'bg_opacity' => '0.12',
            'border_opacity' => '0.12',
            'shadow_opacity' => '0.2',
            'saturation' => '1.8',
            'tint_color' => '#ffffff',
            'noise_texture' => 'egg-shell',
        ],
        'card' => [
            'blur' => '12px',
            'brightness' => '0.9',
            'bg_opacity' => '0.15',
            'border_opacity' => '0.24',
            'shadow_opacity' => '0.16',
            'saturation' => '1.5',
            'tint_color' => '#ffffff',
            'noise_texture' => 'egg-shell',
        ],
        'crystal' => [
            'blur' => '6px',
            'brightness' => '1.0',
            'bg_opacity' => '0.08',
            'border_opacity' => '0.18',
            'shadow_opacity' => '0.1',
            'saturation' => '1.1',
            'tint_color' => '#ffffff',
            'noise_texture' => 'egg-shell',
        ],
        'frosted' => [
            'blur' => '28px',
            'brightness' => '0.85',
            'bg_opacity' => '0.27',
            'border_opacity' => '0.3',
            'shadow_opacity' => '0.2',
            'saturation' => '1.4',
            'tint_color' => '#c8d2e6',
            'noise_texture' => 'egg-shell',
        ],
        'glass3d' => [
            'blur' => '32px',
            'brightness' => '0.85',
            'bg_opacity' => '0.19',
            'border_opacity' => '0.12',
            'shadow_opacity' => '0.25',
            'saturation' => '2.5',
            'tint_color' => '#ffffff',
            'noise_texture' => 'egg-shell',
        ],
    ];

    /**
     * Get glass settings for a specific style, merging saved values over defaults.
     */
    public function getGlassSettingsForStyle(string $style): array
    {
        $defaults = self::GLASS_STYLE_DEFAULTS[$style] ?? self::GLASS_STYLE_DEFAULTS['liquid'];
        $saved = ($this->glass_style_settings ?? [])[$style] ?? [];

        return array_merge($defaults, $saved);
    }

    /**
     * Save glass settings for a specific style.
     */
    public function setGlassSettingsForStyle(string $style, array $values): void
    {
        $all = $this->glass_style_settings ?? [];
        $all[$style] = $values;
        $this->glass_style_settings = $all;
        $this->save();
    }

    /**
     * Reset glass settings for a specific style back to built-in defaults.
     */
    public function resetGlassStyleToDefaults(string $style): void
    {
        $all = $this->glass_style_settings ?? [];
        unset($all[$style]);
        $this->glass_style_settings = $all;
        $this->save();
    }

    /**
     * Get the currently active theme.
     */
    public static function active(): self
    {
        $settings = Setting::instance();
        if ($settings->active_theme_id) {
            $theme = cache()->remember("theme_{$settings->active_theme_id}", 3600, function () use ($settings) {
                return self::find($settings->active_theme_id);
            });
            if ($theme) return $theme;
        }

        return cache()->remember('theme_default', 3600, function () {
            return self::where('is_default', true)->first() ?? self::createDefault();
        });
    }

    /**
     * Create the default theme.
     */
    public static function createDefault(): self
    {
        return self::create([
            'name' => 'Default Dark',
            'slug' => 'default-dark',
            'is_default' => true,
        ]);
    }

    /**
     * Generate CSS custom properties string.
     * Outputs the active glass style's settings as the primary --glass-* vars.
     */
    public function getCssVariables(?string $activeGlassStyle = null): string
    {
        $style = $activeGlassStyle ?? (Setting::instance()->default_glass_style ?? 'liquid');
        $gs = $this->getGlassSettingsForStyle($style);

        // Convert hex tint color to RGB components for CSS rgba() usage
        $tintHex = ltrim($gs['tint_color'] ?? '#ffffff', '#');
        $tintR = hexdec(substr($tintHex, 0, 2));
        $tintG = hexdec(substr($tintHex, 2, 2));
        $tintB = hexdec(substr($tintHex, 4, 2));
        $noiseUrl = $this->glassNoiseTextureUrl($gs['noise_texture'] ?? 'egg-shell');

        return "
            --color-primary: {$this->color_primary};
            --color-secondary: {$this->color_secondary};
            --color-accent: {$this->color_accent};
            --color-success: {$this->color_success};
            --color-warning: {$this->color_warning};
            --color-danger: {$this->color_danger};
            --glass-blur: {$gs['blur']};
            --glass-bg-opacity: {$gs['bg_opacity']};
            --glass-border-opacity: {$gs['border_opacity']};
            --glass-shadow-opacity: {$gs['shadow_opacity']};
            --glass-brightness: {$gs['brightness']};
            --glass-tint-color: {$gs['tint_color']};
            --glass-tint-r: {$tintR};
            --glass-tint-g: {$tintG};
            --glass-tint-b: {$tintB};
            --glass-saturation: {$gs['saturation']};
            --glass-noise-url: url('{$noiseUrl}');
            --glass-noise-opacity: {$this->glass_noise_opacity};
            --font-color-dark: {$this->font_color_dark};
            --font-color-light: {$this->font_color_light};
            --blob-color-1: {$this->blob_color_1};
            --blob-color-2: {$this->blob_color_2};
            --blob-color-3: {$this->blob_color_3};
            --blob-color-4: {$this->blob_color_4};
            --dark-bg-from: {$this->dark_bg_from};
            --dark-bg-via: {$this->dark_bg_via};
            --dark-bg-to: {$this->dark_bg_to};
            --light-bg-from: {$this->light_bg_from};
            --light-bg-via: {$this->light_bg_via};
            --light-bg-to: {$this->light_bg_to};
            --bg-overlay-opacity: {$this->bg_overlay_opacity};
        ";
    }

    protected function glassNoiseTextureUrl(string $texture): string
    {
        return match ($texture) {
            'rice-paper' => 'https://www.transparenttextures.com/patterns/rice-paper.png',
            'ink-jet' => 'https://www.transparenttextures.com/patterns/ink.png',
            'coarse' => 'https://www.transparenttextures.com/patterns/asfalt-light.png',
            'topology' => 'https://www.transparenttextures.com/patterns/topography.png',
            default => 'https://www.transparenttextures.com/patterns/egg-shell.png',
        };
    }

    /**
     * Get the gradient CSS for dark mode.
     */
    public function getDarkGradient(): string
    {
        return "linear-gradient({$this->dark_gradient_direction}, {$this->dark_bg_from}, {$this->dark_bg_via}, {$this->dark_bg_to})";
    }

    /**
     * Get the gradient CSS for light mode.
     */
    public function getLightGradient(): string
    {
        return "linear-gradient({$this->light_gradient_direction}, {$this->light_bg_from}, {$this->light_bg_via}, {$this->light_bg_to})";
    }

    /**
     * Get the background image URL.
     */
    public function getBackgroundImageUrl(): ?string
    {
        if (!$this->bg_image) {
            return null;
        }
        return asset('storage/' . $this->bg_image);
    }

    /**
     * Get the background video URL.
     */
    public function getBackgroundVideoUrl(): ?string
    {
        if (!$this->bg_video) {
            return null;
        }
        return asset('storage/' . $this->bg_video);
    }

    /**
     * Clear cache when saved.
     */
    protected static function booted(): void
    {
        static::saved(function ($theme) {
            cache()->forget("theme_{$theme->id}");
            cache()->forget('theme_default');
        });
        static::deleted(function ($theme) {
            cache()->forget("theme_{$theme->id}");
            cache()->forget('theme_default');
        });
    }
}
