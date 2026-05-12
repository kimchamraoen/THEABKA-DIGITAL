<?php

namespace App\Livewire\Admin;

use App\Models\Setting;
use Livewire\Component;

class SidebarSettings extends Component
{
    public int $sidebar_font_size = 15;
    public int $sidebar_icon_size = 20;
    public int $sidebar_width = 360;
    public int $sidebar_collapsed_width = 72;
    public string $sidebar_active_bg_color = 'rgba(255,255,255,0.15)';
    public string $sidebar_active_border_color = 'rgba(255,255,255,0.2)';
    public int $sidebar_active_border_radius = 12;

    public function mount(): void
    {
        $settings = Setting::instance();
        $this->sidebar_font_size = $settings->sidebar_font_size ?? 15;
        $this->sidebar_icon_size = $settings->sidebar_icon_size ?? 20;
        $this->sidebar_width = $settings->sidebar_width ?? 360;
        $this->sidebar_collapsed_width = $settings->sidebar_collapsed_width ?? 72;
        $this->sidebar_active_bg_color = $settings->sidebar_active_bg_color ?? 'rgba(255,255,255,0.15)';
        $this->sidebar_active_border_color = $settings->sidebar_active_border_color ?? 'rgba(255,255,255,0.2)';
        $this->sidebar_active_border_radius = $settings->sidebar_active_border_radius ?? 12;
    }

    public function save(int $fontSize, int $iconSize, int $width, int $collapsedWidth, string $activeBgColor, string $activeBorderColor, int $activeBorderRadius): void
    {
        $this->sidebar_font_size = $fontSize;
        $this->sidebar_icon_size = $iconSize;
        $this->sidebar_width = $width;
        $this->sidebar_collapsed_width = $collapsedWidth;
        $this->sidebar_active_bg_color = $activeBgColor;
        $this->sidebar_active_border_color = $activeBorderColor;
        $this->sidebar_active_border_radius = $activeBorderRadius;

        $this->validate([
            'sidebar_font_size' => 'required|integer|min:11|max:22',
            'sidebar_icon_size' => 'required|integer|min:14|max:32',
            'sidebar_width' => 'required|integer|min:280|max:480',
            'sidebar_collapsed_width' => 'required|integer|min:56|max:120',
            'sidebar_active_bg_color' => 'required|string|max:50',
            'sidebar_active_border_color' => 'required|string|max:50',
            'sidebar_active_border_radius' => 'required|integer|min:0|max:24',
        ]);

        $settings = Setting::instance();
        $settings->update([
            'sidebar_font_size' => $this->sidebar_font_size,
            'sidebar_icon_size' => $this->sidebar_icon_size,
            'sidebar_width' => $this->sidebar_width,
            'sidebar_collapsed_width' => $this->sidebar_collapsed_width,
            'sidebar_active_bg_color' => $this->sidebar_active_bg_color,
            'sidebar_active_border_color' => $this->sidebar_active_border_color,
            'sidebar_active_border_radius' => $this->sidebar_active_border_radius,
        ]);
    }

    public function resetDefaults(): void
    {
        $this->sidebar_font_size = 15;
        $this->sidebar_icon_size = 20;
        $this->sidebar_width = 360;
        $this->sidebar_collapsed_width = 72;
        $this->sidebar_active_bg_color = 'rgba(255,255,255,0.15)';
        $this->sidebar_active_border_color = 'rgba(255,255,255,0.2)';
        $this->sidebar_active_border_radius = 12;

        $settings = Setting::instance();
        $settings->update([
            'sidebar_font_size' => 15,
            'sidebar_icon_size' => 20,
            'sidebar_width' => 360,
            'sidebar_collapsed_width' => 72,
            'sidebar_active_bg_color' => 'rgba(255,255,255,0.15)',
            'sidebar_active_border_color' => 'rgba(255,255,255,0.2)',
            'sidebar_active_border_radius' => 12,
        ]);
    }

    public function render()
    {
        return view('livewire.admin.sidebar-settings');
    }
}
