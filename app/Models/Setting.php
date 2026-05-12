<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Setting extends Model
{
    protected $fillable = [
        'app_name',
        'app_logo',
        'app_favicon',
        'active_theme_id',
        'default_theme',
        'default_glass_style',
        'app_font',
        'font_type_en',
        'font_value_en',
        'font_type_km',
        'font_value_km',
        'sidebar_font_size',
        'sidebar_icon_size',
        'sidebar_width',
        'sidebar_collapsed_width',
        'sidebar_active_bg_color',
        'sidebar_active_border_color',
        'sidebar_active_border_radius',
        'google_fonts_api_key',
        'timezone',
        'smtp_host',
        'smtp_port',
        'smtp_username',
        'smtp_password',
        'smtp_encryption',
        'smtp_from_address',
        'smtp_from_name',
        'captcha_provider',
        'recaptcha_site_key',
        'recaptcha_secret_key',
        'turnstile_site_key',
        'turnstile_secret_key',
        'captcha_on_login',
        'captcha_on_register',
        'allow_unverified_login',
        'custom_css',
        'custom_css_landing',
        'custom_css_landing_enabled',
        'custom_css_dashboard',
        'custom_css_dashboard_enabled',
        'color_primary',
        'color_secondary',
        'color_accent',
        'color_success',
        'color_warning',
        'color_danger',
        'dark_bg_from',
        'dark_bg_via',
        'dark_bg_to',
        'light_bg_from',
        'light_bg_via',
        'light_bg_to',
        'glass_blur',
        'glass_opacity',
        'glass_border_opacity',
        'auth_bg_type',
        'auth_bg_image',
        'auth_bg_video',
        'auth_bg_video_file',
        'auth_card_max_width',
        'auth_card_padding_x',
        'auth_card_padding_y',
        'auth_card_border_radius',
        'auth_card_font_size',
        'auth_card_font_color',
        'auth_label_color',
        'auth_heading_color',
        'auth_link_color',
        'auth_btn_bg_color',
        'auth_btn_text_color',
        'auth_logo_size',
        'verify_email_text',
        'forgot_password_text',
        'welcome_email_text',
        'terms_content',
        'privacy_content',
        'footer_text',
        // Landing page hero
        'landing_hero_badge',
        'landing_hero_line1',
        'landing_hero_line2',
        'landing_hero_line3',
        'landing_hero_subtitle',
        'landing_cta_primary_text',
        'landing_cta_primary_url',
        'landing_cta_secondary_text',
        'landing_cta_secondary_url',
        // Landing features/CTA sections
        'landing_features_title',
        'landing_features_subtitle',
        'landing_features_visible',
        'landing_cta_title',
        'landing_cta_subtitle',
        'landing_cta_visible',
        'landing_floating_cards',
        'landing_particles',
        // Background customization
        'app_bg_type',
        'app_bg_image',
        'app_bg_video',
        'landing_bg_type',
        'landing_bg_image',
        'landing_bg_video',
        // Footer settings
        'footer_sticky',
        'footer_glass',
        'footer_links',
        'footer_social_links',
        'footer_show_copyright',
        'footer_show_terms',
        'footer_show_privacy',
        'footer_show_docs',
        'gemini_api_key',
        'gemini_api_keys',
        'gemini_model',
        'translation_source_language',
    ];

    protected function casts(): array
    {
        return [
            'allow_unverified_login' => 'boolean',
            'captcha_on_login' => 'boolean',
            'captcha_on_register' => 'boolean',
            'custom_css_landing_enabled' => 'boolean',
            'custom_css_dashboard_enabled' => 'boolean',
            'smtp_port' => 'integer',
            'sidebar_font_size' => 'integer',
            'sidebar_icon_size' => 'integer',
            'sidebar_width' => 'integer',
            'sidebar_collapsed_width' => 'integer',
            'auth_card_max_width' => 'integer',
            'auth_card_padding_x' => 'integer',
            'auth_card_padding_y' => 'integer',
            'auth_card_border_radius' => 'integer',
            'auth_card_font_size' => 'integer',
            'auth_logo_size' => 'integer',
            'landing_features_visible' => 'boolean',
            'landing_cta_visible' => 'boolean',
            'landing_floating_cards' => 'boolean',
            'landing_particles' => 'boolean',
            'footer_sticky' => 'boolean',
            'footer_glass' => 'boolean',
            'footer_links' => 'array',
            'footer_social_links' => 'array',
            'footer_show_copyright' => 'boolean',
            'footer_show_terms' => 'boolean',
            'footer_show_privacy' => 'boolean',
            'footer_show_docs' => 'boolean',
            'gemini_api_keys' => 'array',
        ];
    }

    /**
     * Get the singleton settings instance (cached).
     */
    public static function instance(): self
    {
        return cache()->remember('app_settings', 3600, function () {
            return self::firstOrCreate([], [
                'default_theme' => 'dark',
                'default_glass_style' => 'liquid',
                'app_font' => 'Inter',
            ]);
        });
    }

    /**
     * Clear the settings cache after save.
     */
    protected static function booted(): void
    {
        static::saved(function () {
            cache()->forget('app_settings');
        });
    }

    /**
     * Get sanitized font name for CSS.
     */
    public function getSanitizedFontAttribute(): string
    {
        return preg_replace('/[^a-zA-Z0-9\s]/', '', $this->app_font);
    }

    /**
     * Get Google Fonts URL.
     */
    public function getFontUrlAttribute(): string
    {
        $font = urlencode($this->sanitized_font);
        return "https://fonts.googleapis.com/css2?family={$font}&display=swap";
    }

    /**
     * Resolve the effective font configuration for a locale.
     *
     * @return array{bodyFontFamily:string,fontUrl:string,customFontName:?string,customFontUrl:?string}
     */
    public function resolveLocaleFontConfig(?string $locale = null): array
    {
        $locale = $locale ?: app()->getLocale();

        $fallbackFont = preg_replace('/[^a-zA-Z0-9\s\-_]/', '', (string) $this->app_font);
        $bodyFontFamily = "'{$fallbackFont}', ui-sans-serif, system-ui, sans-serif";
        $fontUrl = $this->font_url;
        $customFontName = null;
        $customFontUrl = null;

        if (! Schema::hasTable('languages')) {
            return [
                'bodyFontFamily' => $bodyFontFamily,
                'fontUrl' => $fontUrl,
                'customFontName' => $customFontName,
                'customFontUrl' => $customFontUrl,
            ];
        }

        $activeLanguages = cache()->remember('active_languages', 3600, function () {
            return Language::active()
                ->orderByDesc('is_default')
                ->orderBy('name')
                ->get(['locale', 'font_type', 'font_value']);
        });

        $currentLanguage = $activeLanguages->firstWhere('locale', $locale);
        $localeFontType = $currentLanguage->font_type ?? null;
        $localeFontValue = $currentLanguage->font_value ?? null;

        if (in_array($locale, ['en', 'km'], true)) {
            $localeFontType = $this->{'font_type_' . $locale} ?: $localeFontType;
            $localeFontValue = $this->{'font_value_' . $locale} ?: $localeFontValue;
        }

        if ($localeFontType === 'google' && ! empty($localeFontValue)) {
            $fontName = preg_replace('/[^a-zA-Z0-9\s\-_]/', '', trim((string) $localeFontValue));
            if ($fontName !== '') {
                $bodyFontFamily = "'{$fontName}', ui-sans-serif, system-ui, sans-serif";
                $fontUrl = 'https://fonts.googleapis.com/css2?family=' . urlencode($fontName) . '&display=swap';
            }
        }

        if ($localeFontType === 'custom' && ! empty($localeFontValue)) {
            $customFontName = 'locale_font_' . preg_replace('/[^a-z0-9_]/i', '_', $locale);
            $customFontUrl = asset('storage/fonts/' . ltrim((string) $localeFontValue, '/'));
            $bodyFontFamily = "'{$customFontName}', ui-sans-serif, system-ui, sans-serif";
        }

        return [
            'bodyFontFamily' => $bodyFontFamily,
            'fontUrl' => $fontUrl,
            'customFontName' => $customFontName,
            'customFontUrl' => $customFontUrl,
        ];
    }

    /**
     * Get the effective Google Fonts API key (DB setting takes priority over .env).
     */
    public function getGoogleFontsApiKey(): string
    {
        return $this->google_fonts_api_key ?: config('services.google_fonts.api_key', '');
    }

    /**
     * Get the active theme.
     */
    public function theme(): Theme
    {
        return Theme::active();
    }

    /**
     * Get app name with fallback.
     */
    public function getAppNameAttribute(): string
    {
        return $this->attributes['app_name'] ?? config('app.name', 'G2FA');
    }

    /**
     * Get logo URL.
     */
    public function getLogoUrlAttribute(): ?string
    {
        return $this->app_logo ? asset('storage/' . $this->app_logo) : null;
    }

    /**
     * Get favicon URL.
     */
    public function getFaviconUrlAttribute(): ?string
    {
        return $this->app_favicon ? asset('storage/' . $this->app_favicon) : null;
    }

    /**
     * Generate CSS custom properties from theme colors.
     */
    public function getThemeCssVariables(): string
    {
        return "
            --color-primary: {$this->color_primary};
            --color-secondary: {$this->color_secondary};
            --color-accent: {$this->color_accent};
            --color-success: {$this->color_success};
            --color-warning: {$this->color_warning};
            --color-danger: {$this->color_danger};
            --dark-bg-from: {$this->dark_bg_from};
            --dark-bg-via: {$this->dark_bg_via};
            --dark-bg-to: {$this->dark_bg_to};
            --light-bg-from: {$this->light_bg_from};
            --light-bg-via: {$this->light_bg_via};
            --light-bg-to: {$this->light_bg_to};
            --glass-blur: {$this->glass_blur};
            --glass-opacity: {$this->glass_opacity};
            --glass-border-opacity: {$this->glass_border_opacity};
        ";
    }
}
