<?php

namespace App\Livewire\Admin;

use App\Models\Setting;
use App\Models\Theme;
use Livewire\Component;

class ThemeSettings extends Component
{
    public string $defaultTheme = 'dark';
    public string $defaultGlassStyle = 'liquid';

    // Per-style glass controls (loaded/saved per active glass style)
    public string $glassBlur = '16';
    public string $glassBrightness = '0.97';
    public string $glassBgOpacity = '0.12';
    public string $glassBorderOpacity = '0.12';
    public string $glassShadowOpacity = '0.2';
    public string $glassSaturation = '1.8';
    public string $glassTintColor = '#ffffff';
    public string $glassNoiseTexture = 'egg-shell';

    // Global font colors (shared across all styles)
    public string $fontColorDark = '#bfdbfe';
    public string $fontColorLight = '#334155';

    // Whether current style has customised (non-default) values
    public bool $styleIsCustomised = false;

    // Dirty state tracking for revert
    public array $originalValues = [];
    public bool $hasUnsavedChanges = false;

    public function mount(): void
    {
        $settings = Setting::instance();
        $theme = Theme::active();

        $this->defaultTheme = $settings->default_theme;
        $this->defaultGlassStyle = $settings->default_glass_style ?? 'liquid';
        $this->fontColorDark = $theme->font_color_dark ?? '#bfdbfe';
        $this->fontColorLight = $theme->font_color_light ?? '#334155';

        $this->loadStyleSettings($this->defaultGlassStyle);

        $this->storeOriginals();
    }

    protected function storeOriginals(): void
    {
        $this->originalValues = [
            'defaultTheme' => $this->defaultTheme,
            'defaultGlassStyle' => $this->defaultGlassStyle,
            'glassBlur' => $this->glassBlur,
            'glassBrightness' => $this->glassBrightness,
            'glassBgOpacity' => $this->glassBgOpacity,
            'glassBorderOpacity' => $this->glassBorderOpacity,
            'glassShadowOpacity' => $this->glassShadowOpacity,
            'glassSaturation' => $this->glassSaturation,
            'glassTintColor' => $this->glassTintColor,
            'glassNoiseTexture' => $this->glassNoiseTexture,
            'fontColorDark' => $this->fontColorDark,
            'fontColorLight' => $this->fontColorLight,
        ];
    }

    /**
     * Load per-style settings into the component properties.
     */
    protected function loadStyleSettings(string $style): void
    {
        $theme = Theme::active();
        $gs = $theme->getGlassSettingsForStyle($style);

        $this->glassBlur = rtrim((string) ($gs['blur'] ?? '16px'), 'px');
        $this->glassBrightness = (string) ($gs['brightness'] ?? '0.97');
        $this->glassBgOpacity = (string) ($gs['bg_opacity'] ?? '0.12');
        $this->glassBorderOpacity = (string) ($gs['border_opacity'] ?? '0.12');
        $this->glassShadowOpacity = (string) ($gs['shadow_opacity'] ?? '0.2');
        $this->glassSaturation = (string) ($gs['saturation'] ?? '1.8');
        $this->glassTintColor = $gs['tint_color'] ?? '#ffffff';
        $this->glassNoiseTexture = $gs['noise_texture'] ?? 'egg-shell';

        // Check if this style has saved customisations
        $saved = ($theme->glass_style_settings ?? [])[$style] ?? null;
        $this->styleIsCustomised = ! empty($saved);
    }

    public function updated(string $property): void
    {
        if ($property === 'defaultTheme') {
            $this->dispatch('theme-changed', theme: $this->defaultTheme);
        }

        if ($property === 'defaultGlassStyle') {
            // Switching glass style → load that style's settings
            $this->loadStyleSettings($this->defaultGlassStyle);
            $this->dispatch('glass-style-changed', glassStyle: $this->defaultGlassStyle);
            $this->dispatch('glass-preview-updated', vars: $this->previewVars());
        }

        if (in_array($property, [
            'glassBlur',
            'glassBrightness',
            'glassBgOpacity',
            'glassBorderOpacity',
            'glassShadowOpacity',
            'glassSaturation',
            'glassTintColor',
            'glassNoiseTexture',
            'fontColorDark',
            'fontColorLight',
        ], true)) {
            $this->dispatch('glass-preview-updated', vars: $this->previewVars());
        }

        $this->hasUnsavedChanges = true;
    }

    protected function previewVars(): array
    {
        $hex = ltrim($this->glassTintColor ?: '#ffffff', '#');

        if (strlen($hex) !== 6 || ! ctype_xdigit($hex)) {
            $hex = 'ffffff';
        }

        return [
            '--glass-blur' => $this->glassBlur . 'px',
            '--glass-brightness' => $this->glassBrightness,
            '--glass-bg-opacity' => $this->glassBgOpacity,
            '--glass-border-opacity' => $this->glassBorderOpacity,
            '--glass-shadow-opacity' => $this->glassShadowOpacity,
            '--glass-saturation' => $this->glassSaturation,
            '--glass-tint-color' => '#' . $hex,
            '--glass-tint-r' => (string) hexdec(substr($hex, 0, 2)),
            '--glass-tint-g' => (string) hexdec(substr($hex, 2, 2)),
            '--glass-tint-b' => (string) hexdec(substr($hex, 4, 2)),
            '--glass-noise-url' => sprintf('url("%s")', $this->noiseTextureUrl($this->glassNoiseTexture)),
            '--font-color-dark' => $this->fontColorDark,
            '--font-color-light' => $this->fontColorLight,
        ];
    }

    protected function noiseTextureUrl(string $texture): string
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
     * Revert all unsaved changes back to last saved state.
     */
    public function revert(): void
    {
        foreach ($this->originalValues as $prop => $value) {
            $this->$prop = $value;
        }

        $this->hasUnsavedChanges = false;

        // Restore visual preview
        $this->dispatch('theme-changed', theme: $this->defaultTheme);
        $this->dispatch('glass-style-changed', glassStyle: $this->defaultGlassStyle);
        $this->dispatch('glass-preview-updated', vars: $this->previewVars());
    }

    /**
     * Reset current glass style back to built-in defaults.
     */
    public function resetStyleToDefaults(): void
    {
        $theme = Theme::active();
        $theme->resetGlassStyleToDefaults($this->defaultGlassStyle);

        $this->loadStyleSettings($this->defaultGlassStyle);

        session()->flash('theme-message', ucfirst($this->defaultGlassStyle) . ' glass style reset to defaults.');
        $this->dispatch('glass-preview-updated', vars: $this->previewVars());
    }

    /**
     * Save everything: default theme/style setting + per-style glass values + font colors.
     */
    public function save(): void
    {
        $this->validate([
            'defaultTheme' => 'required|in:dark,light',
            'defaultGlassStyle' => 'required|in:liquid,card,crystal,frosted,glass3d',
            'glassBlur' => 'required|numeric|min:0|max:60',
            'glassBrightness' => 'required|numeric|min:0.5|max:1.4',
            'glassBgOpacity' => 'required|numeric|min:0|max:0.8',
            'glassBorderOpacity' => 'required|numeric|min:0|max:0.8',
            'glassShadowOpacity' => 'required|numeric|min:0|max:0.8',
            'glassSaturation' => 'required|numeric|min:0.5|max:3.5',
            'glassTintColor' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'glassNoiseTexture' => 'required|in:rice-paper,egg-shell,ink-jet,coarse,topology',
            'fontColorDark' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'fontColorLight' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);

        // Save default theme & glass style
        $settings = Setting::instance();
        $settings->update([
            'default_theme' => $this->defaultTheme,
            'default_glass_style' => $this->defaultGlassStyle,
        ]);

        // Save per-style glass settings
        $theme = Theme::active();
        $theme->setGlassSettingsForStyle($this->defaultGlassStyle, [
            'blur' => $this->glassBlur . 'px',
            'brightness' => $this->glassBrightness,
            'bg_opacity' => $this->glassBgOpacity,
            'border_opacity' => $this->glassBorderOpacity,
            'shadow_opacity' => $this->glassShadowOpacity,
            'saturation' => $this->glassSaturation,
            'tint_color' => $this->glassTintColor,
            'noise_texture' => $this->glassNoiseTexture,
        ]);

        // Save global font colors
        $theme->update([
            'font_color_dark' => $this->fontColorDark,
            'font_color_light' => $this->fontColorLight,
        ]);

        $this->styleIsCustomised = true;

        // Update originals and clear dirty state
        $this->storeOriginals();
        $this->hasUnsavedChanges = false;

        session()->flash('theme-message', ucfirst($this->defaultGlassStyle) . ' glass settings saved successfully!');
        $this->dispatch('theme-changed', theme: $this->defaultTheme);
        $this->dispatch('glass-style-changed', glassStyle: $this->defaultGlassStyle);
        $this->dispatch('glass-preview-updated', vars: $this->previewVars());
    }

    public function render()
    {
        return view('livewire.admin.theme-settings');
    }
}
