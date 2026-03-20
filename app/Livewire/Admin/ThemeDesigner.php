<?php

namespace App\Livewire\Admin;

use App\Models\Theme;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class ThemeDesigner extends Component
{
    use WithFileUploads;

    public $themes = [];
    public $editingTheme = null;
    public $showEditor = false;

    // Theme fields
    public $theme_name = '';
    public $bg_type = 'gradient';
    public $bg_image_upload;
    public $bg_video_url = '';
    public $bg_overlay_opacity = '0.5';
    public $bg_overlay_color = '#000000';

    public $dark_bg_from = '#0f0c29';
    public $dark_bg_via = '#302b63';
    public $dark_bg_to = '#24243e';
    public $dark_gradient_direction = '135deg';

    public $light_bg_from = '#e0eafc';
    public $light_bg_via = '#cfdef3';
    public $light_bg_to = '#e2d1c3';
    public $light_gradient_direction = '135deg';

    public $color_primary = '#3b82f6';
    public $color_secondary = '#6366f1';
    public $color_accent = '#8b5cf6';
    public $color_success = '#22c55e';
    public $color_warning = '#eab308';
    public $color_danger = '#ef4444';

    public $glass_blur = '40px';
    public $glass_bg_opacity = '0.03';
    public $glass_border_opacity = '0.08';
    public $glass_shadow_opacity = '0.08';
    public $glass_tint_color = '#ffffff';
    public $glass_saturation = '2.2';
    public $glass_noise_opacity = '0.02';

    public $blob_color_1 = '#8b5cf6';
    public $blob_color_2 = '#ec4899';
    public $blob_color_3 = '#3b82f6';
    public $blob_color_4 = '#f97316';
    public $blobs_enabled = true;

    public $custom_css = '';

    // Presets
    public array $presets = [
        'midnight-aurora' => [
            'name' => 'Midnight Aurora',
            'dark_bg_from' => '#0f0c29', 'dark_bg_via' => '#302b63', 'dark_bg_to' => '#24243e',
            'light_bg_from' => '#e0eafc', 'light_bg_via' => '#cfdef3', 'light_bg_to' => '#e2d1c3',
            'color_primary' => '#818cf8', 'color_secondary' => '#6366f1', 'color_accent' => '#a78bfa',
            'blob_color_1' => '#8b5cf6', 'blob_color_2' => '#ec4899', 'blob_color_3' => '#3b82f6', 'blob_color_4' => '#f97316',
        ],
        'ocean-breeze' => [
            'name' => 'Ocean Breeze',
            'dark_bg_from' => '#0a192f', 'dark_bg_via' => '#112240', 'dark_bg_to' => '#0a192f',
            'light_bg_from' => '#e0f2fe', 'light_bg_via' => '#bae6fd', 'light_bg_to' => '#e0f7fa',
            'color_primary' => '#06b6d4', 'color_secondary' => '#0ea5e9', 'color_accent' => '#22d3ee',
            'blob_color_1' => '#06b6d4', 'blob_color_2' => '#0ea5e9', 'blob_color_3' => '#2dd4bf', 'blob_color_4' => '#38bdf8',
        ],
        'sunset-glow' => [
            'name' => 'Sunset Glow',
            'dark_bg_from' => '#1a0a2e', 'dark_bg_via' => '#2d1b69', 'dark_bg_to' => '#1a0533',
            'light_bg_from' => '#fff7ed', 'light_bg_via' => '#fef3c7', 'light_bg_to' => '#fce7f3',
            'color_primary' => '#f97316', 'color_secondary' => '#ec4899', 'color_accent' => '#eab308',
            'blob_color_1' => '#f97316', 'blob_color_2' => '#ec4899', 'blob_color_3' => '#eab308', 'blob_color_4' => '#ef4444',
        ],
        'emerald-forest' => [
            'name' => 'Emerald Forest',
            'dark_bg_from' => '#022c22', 'dark_bg_via' => '#064e3b', 'dark_bg_to' => '#0f172a',
            'light_bg_from' => '#ecfdf5', 'light_bg_via' => '#d1fae5', 'light_bg_to' => '#f0fdf4',
            'color_primary' => '#10b981', 'color_secondary' => '#059669', 'color_accent' => '#34d399',
            'blob_color_1' => '#10b981', 'blob_color_2' => '#06b6d4', 'blob_color_3' => '#22c55e', 'blob_color_4' => '#14b8a6',
        ],
        'rose-gold' => [
            'name' => 'Rose Gold',
            'dark_bg_from' => '#1c1917', 'dark_bg_via' => '#292524', 'dark_bg_to' => '#1c1917',
            'light_bg_from' => '#fdf2f8', 'light_bg_via' => '#fce7f3', 'light_bg_to' => '#fff1f2',
            'color_primary' => '#f43f5e', 'color_secondary' => '#ec4899', 'color_accent' => '#fb923c',
            'blob_color_1' => '#f43f5e', 'blob_color_2' => '#ec4899', 'blob_color_3' => '#fb923c', 'blob_color_4' => '#f472b6',
        ],
        'cyber-neon' => [
            'name' => 'Cyber Neon',
            'dark_bg_from' => '#000000', 'dark_bg_via' => '#0a0a0a', 'dark_bg_to' => '#0f0f23',
            'light_bg_from' => '#f5f3ff', 'light_bg_via' => '#ede9fe', 'light_bg_to' => '#fdf4ff',
            'color_primary' => '#a855f7', 'color_secondary' => '#06b6d4', 'color_accent' => '#f0abfc',
            'blob_color_1' => '#a855f7', 'blob_color_2' => '#06b6d4', 'blob_color_3' => '#ec4899', 'blob_color_4' => '#22d3ee',
        ],
    ];

    public function mount(): void
    {
        $this->loadThemes();
    }

    public function loadThemes(): void
    {
        $this->themes = Theme::orderBy('created_at', 'desc')->get()->toArray();
    }

    public function newTheme(): void
    {
        $this->resetForm();
        $this->showEditor = true;
        $this->editingTheme = null;
    }

    public function editTheme($id): void
    {
        $theme = Theme::findOrFail($id);
        $this->editingTheme = $id;
        $this->theme_name = $theme->name;
        $this->bg_type = $theme->bg_type;
        $this->bg_video_url = $theme->bg_video ?? '';
        $this->bg_overlay_opacity = $theme->bg_overlay_opacity;
        $this->bg_overlay_color = $theme->bg_overlay_color;
        $this->dark_bg_from = $theme->dark_bg_from;
        $this->dark_bg_via = $theme->dark_bg_via;
        $this->dark_bg_to = $theme->dark_bg_to;
        $this->dark_gradient_direction = $theme->dark_gradient_direction;
        $this->light_bg_from = $theme->light_bg_from;
        $this->light_bg_via = $theme->light_bg_via;
        $this->light_bg_to = $theme->light_bg_to;
        $this->light_gradient_direction = $theme->light_gradient_direction;
        $this->color_primary = $theme->color_primary;
        $this->color_secondary = $theme->color_secondary;
        $this->color_accent = $theme->color_accent;
        $this->color_success = $theme->color_success;
        $this->color_warning = $theme->color_warning;
        $this->color_danger = $theme->color_danger;
        $this->glass_blur = $theme->glass_blur;
        $this->glass_bg_opacity = $theme->glass_bg_opacity;
        $this->glass_border_opacity = $theme->glass_border_opacity;
        $this->glass_shadow_opacity = $theme->glass_shadow_opacity;
        $this->glass_tint_color = $theme->glass_tint_color ?? '#ffffff';
        $this->glass_saturation = $theme->glass_saturation ?? '1.8';
        $this->glass_noise_opacity = $theme->glass_noise_opacity ?? '0.03';
        $this->blob_color_1 = $theme->blob_color_1;
        $this->blob_color_2 = $theme->blob_color_2;
        $this->blob_color_3 = $theme->blob_color_3;
        $this->blob_color_4 = $theme->blob_color_4;
        $this->blobs_enabled = $theme->blobs_enabled;
        $this->custom_css = $theme->custom_css ?? '';
        $this->showEditor = true;
    }

    public function applyPreset($key): void
    {
        if (!isset($this->presets[$key])) return;
        $p = $this->presets[$key];
        $this->theme_name = $this->theme_name ?: $p['name'];
        foreach (['dark_bg_from','dark_bg_via','dark_bg_to','light_bg_from','light_bg_via','light_bg_to',
                   'color_primary','color_secondary','color_accent',
                   'blob_color_1','blob_color_2','blob_color_3','blob_color_4'] as $field) {
            if (isset($p[$field])) $this->{$field} = $p[$field];
        }
    }

    public function save(): void
    {
        $this->validate([
            'theme_name' => 'required|string|max:100',
            'bg_type' => 'required|in:gradient,image,video',
            'bg_overlay_opacity' => 'required|numeric|min:0|max:1',
        ]);

        $data = [
            'name' => $this->theme_name,
            'slug' => Str::slug($this->theme_name) . ($this->editingTheme ? '' : '-' . Str::random(4)),
            'bg_type' => $this->bg_type,
            'bg_video' => $this->bg_video_url ?: null,
            'bg_overlay_opacity' => $this->bg_overlay_opacity,
            'bg_overlay_color' => $this->bg_overlay_color,
            'dark_bg_from' => $this->dark_bg_from,
            'dark_bg_via' => $this->dark_bg_via,
            'dark_bg_to' => $this->dark_bg_to,
            'dark_gradient_direction' => $this->dark_gradient_direction,
            'light_bg_from' => $this->light_bg_from,
            'light_bg_via' => $this->light_bg_via,
            'light_bg_to' => $this->light_bg_to,
            'light_gradient_direction' => $this->light_gradient_direction,
            'color_primary' => $this->color_primary,
            'color_secondary' => $this->color_secondary,
            'color_accent' => $this->color_accent,
            'color_success' => $this->color_success,
            'color_warning' => $this->color_warning,
            'color_danger' => $this->color_danger,
            'glass_blur' => $this->glass_blur,
            'glass_bg_opacity' => $this->glass_bg_opacity,
            'glass_border_opacity' => $this->glass_border_opacity,
            'glass_shadow_opacity' => $this->glass_shadow_opacity,
            'glass_tint_color' => $this->glass_tint_color,
            'glass_saturation' => $this->glass_saturation,
            'glass_noise_opacity' => $this->glass_noise_opacity,
            'blob_color_1' => $this->blob_color_1,
            'blob_color_2' => $this->blob_color_2,
            'blob_color_3' => $this->blob_color_3,
            'blob_color_4' => $this->blob_color_4,
            'blobs_enabled' => $this->blobs_enabled,
            'custom_css' => $this->sanitizeCss($this->custom_css),
        ];

        // Handle image upload
        if ($this->bg_image_upload) {
            $data['bg_image'] = $this->bg_image_upload->store('themes', 'public');
        }

        if ($this->editingTheme) {
            Theme::find($this->editingTheme)->update($data);
            session()->flash('message', 'Theme updated successfully!');
        } else {
            Theme::create($data);
            session()->flash('message', 'Theme created successfully!');
        }

        $this->showEditor = false;
        $this->loadThemes();
    }

    public function activateTheme($id): void
    {
        $settings = \App\Models\Setting::instance();
        $settings->update(['active_theme_id' => $id]);
        cache()->forget('app_settings');
        cache()->forget("theme_{$id}");
        cache()->forget('theme_default');
        $this->dispatch('theme-updated');
        session()->flash('message', 'Theme activated!');
    }

    public function deleteTheme($id): void
    {
        $theme = Theme::findOrFail($id);
        if ($theme->is_default) {
            session()->flash('error', 'Cannot delete the default theme.');
            return;
        }

        // If this was active, clear the active theme
        $settings = \App\Models\Setting::instance();
        if ($settings->active_theme_id == $id) {
            $settings->update(['active_theme_id' => null]);
            cache()->forget('app_settings');
        }

        $theme->delete();
        $this->loadThemes();
        session()->flash('message', 'Theme deleted.');
    }

    public function duplicateTheme($id): void
    {
        $theme = Theme::findOrFail($id);
        $new = $theme->replicate();
        $new->name = $theme->name . ' (Copy)';
        $new->slug = Str::slug($new->name) . '-' . Str::random(4);
        $new->is_default = false;
        $new->save();
        $this->loadThemes();
        session()->flash('message', 'Theme duplicated!');
    }

    public function cancelEditor(): void
    {
        $this->showEditor = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->theme_name = '';
        $this->bg_type = 'gradient';
        $this->bg_image_upload = null;
        $this->bg_video_url = '';
        $this->bg_overlay_opacity = '0.5';
        $this->bg_overlay_color = '#000000';
        $this->dark_bg_from = '#0f0c29';
        $this->dark_bg_via = '#302b63';
        $this->dark_bg_to = '#24243e';
        $this->dark_gradient_direction = '135deg';
        $this->light_bg_from = '#e0eafc';
        $this->light_bg_via = '#cfdef3';
        $this->light_bg_to = '#e2d1c3';
        $this->light_gradient_direction = '135deg';
        $this->color_primary = '#3b82f6';
        $this->color_secondary = '#6366f1';
        $this->color_accent = '#8b5cf6';
        $this->color_success = '#22c55e';
        $this->color_warning = '#eab308';
        $this->color_danger = '#ef4444';
        $this->glass_blur = '40px';
        $this->glass_bg_opacity = '0.03';
        $this->glass_border_opacity = '0.08';
        $this->glass_shadow_opacity = '0.08';
        $this->glass_tint_color = '#ffffff';
        $this->glass_saturation = '2.2';
        $this->glass_noise_opacity = '0.02';
        $this->blob_color_1 = '#8b5cf6';
        $this->blob_color_2 = '#ec4899';
        $this->blob_color_3 = '#3b82f6';
        $this->blob_color_4 = '#f97316';
        $this->blobs_enabled = true;
        $this->custom_css = '';
    }

    private function sanitizeCss(?string $css): ?string
    {
        if (!$css) return null;
        $css = preg_replace('/<script\b[^>]*>.*?<\/script>/si', '', $css);
        $css = preg_replace('/javascript\s*:/i', '', $css);
        $css = preg_replace('/expression\s*\(/i', '', $css);
        return $css;
    }

    public function render()
    {
        return view('livewire.admin.theme-designer');
    }
}
