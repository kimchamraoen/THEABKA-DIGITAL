<?php

namespace App\Livewire\Admin;

use App\Models\Setting;
use Livewire\Component;

class CustomThemeSettings extends Component
{
    // Color palette
    public string $color_primary = '#3b82f6';
    public string $color_secondary = '#6366f1';
    public string $color_accent = '#8b5cf6';
    public string $color_success = '#22c55e';
    public string $color_warning = '#eab308';
    public string $color_danger = '#ef4444';

    // Background gradients
    public string $dark_bg_from = '#172554';
    public string $dark_bg_via = '#1e3a5f';
    public string $dark_bg_to = '#0f172a';
    public string $light_bg_from = '#f3f4f6';
    public string $light_bg_via = '#eff6ff';
    public string $light_bg_to = '#e5e7eb';

    // Glass settings
    public string $glass_blur = '20px';
    public string $glass_opacity = '0.1';
    public string $glass_border_opacity = '0.2';

    // Custom CSS
    public string $custom_css = '';

    // Page-specific CSS
    public string $custom_css_landing = '';
    public bool $custom_css_landing_enabled = false;
    public string $custom_css_dashboard = '';
    public bool $custom_css_dashboard_enabled = false;

    // Dirty state tracking for revert
    public array $originalValues = [];
    public bool $hasUnsavedChanges = false;

    // Presets
    public array $presets = [
        'ocean' => [
            'label' => 'Ocean Blue',
            'primary' => '#0ea5e9', 'secondary' => '#06b6d4', 'accent' => '#14b8a6',
            'dark_from' => '#0c1929', 'dark_via' => '#0e2a47', 'dark_to' => '#0a1628',
            'light_from' => '#ecfeff', 'light_via' => '#e0f2fe', 'light_to' => '#f0f9ff',
        ],
        'purple' => [
            'label' => 'Purple Dream',
            'primary' => '#a855f7', 'secondary' => '#8b5cf6', 'accent' => '#d946ef',
            'dark_from' => '#1a0533', 'dark_via' => '#2d1b69', 'dark_to' => '#0f0720',
            'light_from' => '#faf5ff', 'light_via' => '#f3e8ff', 'light_to' => '#ede9fe',
        ],
        'emerald' => [
            'label' => 'Emerald Forest',
            'primary' => '#10b981', 'secondary' => '#059669', 'accent' => '#34d399',
            'dark_from' => '#022c22', 'dark_via' => '#064e3b', 'dark_to' => '#011d17',
            'light_from' => '#ecfdf5', 'light_via' => '#d1fae5', 'light_to' => '#f0fdf4',
        ],
        'sunset' => [
            'label' => 'Sunset Glow',
            'primary' => '#f97316', 'secondary' => '#ef4444', 'accent' => '#eab308',
            'dark_from' => '#1c0a00', 'dark_via' => '#451a03', 'dark_to' => '#0c0404',
            'light_from' => '#fff7ed', 'light_via' => '#fef2f2', 'light_to' => '#fefce8',
        ],
        'midnight' => [
            'label' => 'Midnight',
            'primary' => '#3b82f6', 'secondary' => '#6366f1', 'accent' => '#8b5cf6',
            'dark_from' => '#172554', 'dark_via' => '#1e3a5f', 'dark_to' => '#0f172a',
            'light_from' => '#f3f4f6', 'light_via' => '#eff6ff', 'light_to' => '#e5e7eb',
        ],
        'rose' => [
            'label' => 'Rose Gold',
            'primary' => '#f43f5e', 'secondary' => '#ec4899', 'accent' => '#fb923c',
            'dark_from' => '#1a0a12', 'dark_via' => '#4c0519', 'dark_to' => '#0f0509',
            'light_from' => '#fff1f2', 'light_via' => '#fce7f3', 'light_to' => '#fff7ed',
        ],
    ];

    public function mount(): void
    {
        $settings = Setting::instance();
        $this->color_primary = $settings->color_primary ?? '#3b82f6';
        $this->color_secondary = $settings->color_secondary ?? '#6366f1';
        $this->color_accent = $settings->color_accent ?? '#8b5cf6';
        $this->color_success = $settings->color_success ?? '#22c55e';
        $this->color_warning = $settings->color_warning ?? '#eab308';
        $this->color_danger = $settings->color_danger ?? '#ef4444';
        $this->dark_bg_from = $settings->dark_bg_from ?? '#172554';
        $this->dark_bg_via = $settings->dark_bg_via ?? '#1e3a5f';
        $this->dark_bg_to = $settings->dark_bg_to ?? '#0f172a';
        $this->light_bg_from = $settings->light_bg_from ?? '#f3f4f6';
        $this->light_bg_via = $settings->light_bg_via ?? '#eff6ff';
        $this->light_bg_to = $settings->light_bg_to ?? '#e5e7eb';
        $this->glass_blur = $settings->glass_blur ?? '20px';
        $this->glass_opacity = $settings->glass_opacity ?? '0.1';
        $this->glass_border_opacity = $settings->glass_border_opacity ?? '0.2';
        $this->custom_css = $settings->custom_css ?? '';
        $this->custom_css_landing = $settings->custom_css_landing ?? '';
        $this->custom_css_landing_enabled = (bool) $settings->custom_css_landing_enabled;
        $this->custom_css_dashboard = $settings->custom_css_dashboard ?? '';
        $this->custom_css_dashboard_enabled = (bool) $settings->custom_css_dashboard_enabled;

        $this->storeOriginals();
    }

    protected function storeOriginals(): void
    {
        $this->originalValues = [
            'color_primary' => $this->color_primary,
            'color_secondary' => $this->color_secondary,
            'color_accent' => $this->color_accent,
            'color_success' => $this->color_success,
            'color_warning' => $this->color_warning,
            'color_danger' => $this->color_danger,
            'dark_bg_from' => $this->dark_bg_from,
            'dark_bg_via' => $this->dark_bg_via,
            'dark_bg_to' => $this->dark_bg_to,
            'light_bg_from' => $this->light_bg_from,
            'light_bg_via' => $this->light_bg_via,
            'light_bg_to' => $this->light_bg_to,
            'glass_blur' => $this->glass_blur,
            'glass_opacity' => $this->glass_opacity,
            'glass_border_opacity' => $this->glass_border_opacity,
            'custom_css' => $this->custom_css,
            'custom_css_landing' => $this->custom_css_landing,
            'custom_css_landing_enabled' => $this->custom_css_landing_enabled,
            'custom_css_dashboard' => $this->custom_css_dashboard,
            'custom_css_dashboard_enabled' => $this->custom_css_dashboard_enabled,
        ];
    }

    public function updated($property): void
    {
        $this->hasUnsavedChanges = true;

        // Live preview: dispatch CSS variable updates for colors and gradients
        $colorVarMap = [
            'color_primary' => '--color-primary',
            'color_secondary' => '--color-secondary',
            'color_accent' => '--color-accent',
            'color_success' => '--color-success',
            'color_warning' => '--color-warning',
            'color_danger' => '--color-danger',
        ];

        if (isset($colorVarMap[$property])) {
            $this->dispatch('glass-preview-updated', vars: [$colorVarMap[$property] => $this->$property]);
        }

        // Live preview: gradient changes update the page background
        $gradientProps = ['dark_bg_from', 'dark_bg_via', 'dark_bg_to', 'light_bg_from', 'light_bg_via', 'light_bg_to'];
        if (in_array($property, $gradientProps, true)) {
            $this->dispatch('gradient-preview-updated', 
                darkGradient: "linear-gradient(135deg, {$this->dark_bg_from}, {$this->dark_bg_via}, {$this->dark_bg_to})",
                lightGradient: "linear-gradient(135deg, {$this->light_bg_from}, {$this->light_bg_via}, {$this->light_bg_to})"
            );
        }
    }

    public function revert(): void
    {
        foreach ($this->originalValues as $prop => $value) {
            $this->$prop = $value;
        }

        $this->hasUnsavedChanges = false;

        // Restore all CSS variables to original values
        $vars = [
            '--color-primary' => $this->color_primary,
            '--color-secondary' => $this->color_secondary,
            '--color-accent' => $this->color_accent,
            '--color-success' => $this->color_success,
            '--color-warning' => $this->color_warning,
            '--color-danger' => $this->color_danger,
        ];
        $this->dispatch('glass-preview-updated', vars: $vars);
        $this->dispatch('gradient-preview-updated',
            darkGradient: "linear-gradient(135deg, {$this->dark_bg_from}, {$this->dark_bg_via}, {$this->dark_bg_to})",
            lightGradient: "linear-gradient(135deg, {$this->light_bg_from}, {$this->light_bg_via}, {$this->light_bg_to})"
        );
    }

    public function applyPreset(string $preset): void
    {
        if (!isset($this->presets[$preset])) return;

        $p = $this->presets[$preset];
        $this->color_primary = $p['primary'];
        $this->color_secondary = $p['secondary'];
        $this->color_accent = $p['accent'];
        $this->dark_bg_from = $p['dark_from'];
        $this->dark_bg_via = $p['dark_via'];
        $this->dark_bg_to = $p['dark_to'];
        $this->light_bg_from = $p['light_from'];
        $this->light_bg_via = $p['light_via'];
        $this->light_bg_to = $p['light_to'];
    }

    public function save(): void
    {
        $this->validate([
            'color_primary' => 'required|string|regex:/^#[0-9a-fA-F]{6}$/',
            'color_secondary' => 'required|string|regex:/^#[0-9a-fA-F]{6}$/',
            'color_accent' => 'required|string|regex:/^#[0-9a-fA-F]{6}$/',
            'color_success' => 'required|string|regex:/^#[0-9a-fA-F]{6}$/',
            'color_warning' => 'required|string|regex:/^#[0-9a-fA-F]{6}$/',
            'color_danger' => 'required|string|regex:/^#[0-9a-fA-F]{6}$/',
            'dark_bg_from' => 'required|string|regex:/^#[0-9a-fA-F]{6}$/',
            'dark_bg_via' => 'required|string|regex:/^#[0-9a-fA-F]{6}$/',
            'dark_bg_to' => 'required|string|regex:/^#[0-9a-fA-F]{6}$/',
            'light_bg_from' => 'required|string|regex:/^#[0-9a-fA-F]{6}$/',
            'light_bg_via' => 'required|string|regex:/^#[0-9a-fA-F]{6}$/',
            'light_bg_to' => 'required|string|regex:/^#[0-9a-fA-F]{6}$/',
            'glass_blur' => 'required|string|max:10',
            'glass_opacity' => 'required|string|max:10',
            'glass_border_opacity' => 'required|string|max:10',
            'custom_css' => 'nullable|string|max:10000',
            'custom_css_landing' => 'nullable|string|max:10000',
            'custom_css_landing_enabled' => 'boolean',
            'custom_css_dashboard' => 'nullable|string|max:10000',
            'custom_css_dashboard_enabled' => 'boolean',
        ]);

        // Sanitize custom CSS - strip script tags and dangerous content
        $css = $this->sanitizeCss($this->custom_css);
        $cssLanding = $this->sanitizeCss($this->custom_css_landing);
        $cssDashboard = $this->sanitizeCss($this->custom_css_dashboard);

        $settings = Setting::instance();
        $settings->update([
            'color_primary' => $this->color_primary,
            'color_secondary' => $this->color_secondary,
            'color_accent' => $this->color_accent,
            'color_success' => $this->color_success,
            'color_warning' => $this->color_warning,
            'color_danger' => $this->color_danger,
            'dark_bg_from' => $this->dark_bg_from,
            'dark_bg_via' => $this->dark_bg_via,
            'dark_bg_to' => $this->dark_bg_to,
            'light_bg_from' => $this->light_bg_from,
            'light_bg_via' => $this->light_bg_via,
            'light_bg_to' => $this->light_bg_to,
            'glass_blur' => $this->glass_blur,
            'glass_opacity' => $this->glass_opacity,
            'glass_border_opacity' => $this->glass_border_opacity,
            'custom_css' => $css,
            'custom_css_landing' => $cssLanding,
            'custom_css_landing_enabled' => $this->custom_css_landing_enabled,
            'custom_css_dashboard' => $cssDashboard,
            'custom_css_dashboard_enabled' => $this->custom_css_dashboard_enabled,
        ]);

        session()->flash('custom-theme-message', 'Custom theme saved successfully! Refresh to see changes.');
        $this->dispatch('theme-updated');

        // Update originals and clear dirty state
        $this->storeOriginals();
        $this->hasUnsavedChanges = false;
    }

    public function resetToDefaults(): void
    {
        $this->applyPreset('midnight');
        $this->color_success = '#22c55e';
        $this->color_warning = '#eab308';
        $this->color_danger = '#ef4444';
        $this->glass_blur = '20px';
        $this->glass_opacity = '0.1';
        $this->glass_border_opacity = '0.2';
        $this->custom_css = '';
        $this->custom_css_landing = '';
        $this->custom_css_landing_enabled = false;
        $this->custom_css_dashboard = '';
        $this->custom_css_dashboard_enabled = false;
        $this->hasUnsavedChanges = true;
    }

    protected function sanitizeCss(string $css): string
    {
        $css = preg_replace('/<\s*script[^>]*>.*?<\s*\/\s*script\s*>/si', '', $css);
        $css = preg_replace('/javascript\s*:/si', '', $css);
        $css = preg_replace('/expression\s*\(/si', '', $css);
        $css = preg_replace('/url\s*\(\s*["\']?\s*data\s*:/si', '', $css);
        return $css;
    }

    public function render()
    {
        return view('livewire.admin.custom-theme-settings');
    }
}
