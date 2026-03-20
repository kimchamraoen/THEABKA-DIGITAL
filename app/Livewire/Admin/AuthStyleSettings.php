<?php

namespace App\Livewire\Admin;

use App\Models\Setting;
use Livewire\Component;

class AuthStyleSettings extends Component
{
    public int $auth_card_max_width = 448;
    public int $auth_card_padding_x = 32;
    public int $auth_card_padding_y = 24;
    public int $auth_card_border_radius = 16;
    public int $auth_card_font_size = 14;
    public ?string $auth_card_font_color = null;
    public ?string $auth_label_color = null;
    public ?string $auth_heading_color = null;
    public ?string $auth_link_color = null;
    public ?string $auth_btn_bg_color = null;
    public ?string $auth_btn_text_color = null;
    public int $auth_logo_size = 48;

    public function mount(): void
    {
        $settings = Setting::instance();
        $this->auth_card_max_width = $settings->auth_card_max_width ?? 448;
        $this->auth_card_padding_x = $settings->auth_card_padding_x ?? 32;
        $this->auth_card_padding_y = $settings->auth_card_padding_y ?? 24;
        $this->auth_card_border_radius = $settings->auth_card_border_radius ?? 16;
        $this->auth_card_font_size = $settings->auth_card_font_size ?? 14;
        $this->auth_card_font_color = $settings->auth_card_font_color;
        $this->auth_label_color = $settings->auth_label_color;
        $this->auth_heading_color = $settings->auth_heading_color;
        $this->auth_link_color = $settings->auth_link_color;
        $this->auth_btn_bg_color = $settings->auth_btn_bg_color;
        $this->auth_btn_text_color = $settings->auth_btn_text_color;
        $this->auth_logo_size = $settings->auth_logo_size ?? 48;
    }

    public function save(
        int $maxWidth,
        int $paddingX,
        int $paddingY,
        int $borderRadius,
        int $fontSize,
        ?string $fontColor,
        ?string $labelColor,
        ?string $headingColor,
        ?string $linkColor,
        ?string $btnBgColor,
        ?string $btnTextColor,
        int $logoSize
    ): void {
        $this->auth_card_max_width = $maxWidth;
        $this->auth_card_padding_x = $paddingX;
        $this->auth_card_padding_y = $paddingY;
        $this->auth_card_border_radius = $borderRadius;
        $this->auth_card_font_size = $fontSize;
        $this->auth_card_font_color = $fontColor ?: null;
        $this->auth_label_color = $labelColor ?: null;
        $this->auth_heading_color = $headingColor ?: null;
        $this->auth_link_color = $linkColor ?: null;
        $this->auth_btn_bg_color = $btnBgColor ?: null;
        $this->auth_btn_text_color = $btnTextColor ?: null;
        $this->auth_logo_size = $logoSize;

        $this->validate([
            'auth_card_max_width' => 'required|integer|min:320|max:800',
            'auth_card_padding_x' => 'required|integer|min:12|max:64',
            'auth_card_padding_y' => 'required|integer|min:12|max:64',
            'auth_card_border_radius' => 'required|integer|min:0|max:32',
            'auth_card_font_size' => 'required|integer|min:11|max:20',
            'auth_logo_size' => 'required|integer|min:24|max:96',
        ]);

        $settings = Setting::instance();
        $settings->update([
            'auth_card_max_width' => $this->auth_card_max_width,
            'auth_card_padding_x' => $this->auth_card_padding_x,
            'auth_card_padding_y' => $this->auth_card_padding_y,
            'auth_card_border_radius' => $this->auth_card_border_radius,
            'auth_card_font_size' => $this->auth_card_font_size,
            'auth_card_font_color' => $this->auth_card_font_color,
            'auth_label_color' => $this->auth_label_color,
            'auth_heading_color' => $this->auth_heading_color,
            'auth_link_color' => $this->auth_link_color,
            'auth_btn_bg_color' => $this->auth_btn_bg_color,
            'auth_btn_text_color' => $this->auth_btn_text_color,
            'auth_logo_size' => $this->auth_logo_size,
        ]);
    }

    public function resetDefaults(): void
    {
        $this->auth_card_max_width = 448;
        $this->auth_card_padding_x = 32;
        $this->auth_card_padding_y = 24;
        $this->auth_card_border_radius = 16;
        $this->auth_card_font_size = 14;
        $this->auth_card_font_color = null;
        $this->auth_label_color = null;
        $this->auth_heading_color = null;
        $this->auth_link_color = null;
        $this->auth_btn_bg_color = null;
        $this->auth_btn_text_color = null;
        $this->auth_logo_size = 48;

        Setting::instance()->update([
            'auth_card_max_width' => 448,
            'auth_card_padding_x' => 32,
            'auth_card_padding_y' => 24,
            'auth_card_border_radius' => 16,
            'auth_card_font_size' => 14,
            'auth_card_font_color' => null,
            'auth_label_color' => null,
            'auth_heading_color' => null,
            'auth_link_color' => null,
            'auth_btn_bg_color' => null,
            'auth_btn_text_color' => null,
            'auth_logo_size' => 48,
        ]);
    }

    public function render()
    {
        return view('livewire.admin.auth-style-settings');
    }
}
