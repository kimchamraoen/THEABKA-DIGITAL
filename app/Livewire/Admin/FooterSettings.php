<?php

namespace App\Livewire\Admin;

use App\Models\Setting;
use Livewire\Component;

class FooterSettings extends Component
{
    public bool $footer_sticky = true;
    public bool $footer_glass = true;
    public bool $footer_show_copyright = true;
    public bool $footer_show_terms = true;
    public bool $footer_show_privacy = true;
    public bool $footer_show_docs = true;
    public string $footer_text = '';

    public array $footer_links = [];
    public array $footer_social_links = [];

    // Temp fields for adding new items
    public string $new_link_label = '';
    public string $new_link_url = '';
    public string $new_social_platform = 'facebook';
    public string $new_social_url = '';

    public function mount(): void
    {
        $settings = Setting::instance();
        $this->footer_sticky = (bool) ($settings->footer_sticky ?? true);
        $this->footer_glass = (bool) ($settings->footer_glass ?? true);
        $this->footer_show_copyright = (bool) ($settings->footer_show_copyright ?? true);
        $this->footer_show_terms = (bool) ($settings->footer_show_terms ?? true);
        $this->footer_show_privacy = (bool) ($settings->footer_show_privacy ?? true);
        $this->footer_show_docs = (bool) ($settings->footer_show_docs ?? true);
        $this->footer_text = $settings->footer_text ?? '';
        $this->footer_links = $settings->footer_links ?? [];
        $this->footer_social_links = $settings->footer_social_links ?? [];
    }

    public function addLink(): void
    {
        $this->validate([
            'new_link_label' => 'required|string|max:100',
            'new_link_url' => 'required|url|max:500',
        ]);

        $this->footer_links[] = [
            'label' => $this->new_link_label,
            'url' => $this->new_link_url,
        ];

        $this->new_link_label = '';
        $this->new_link_url = '';
    }

    public function removeLink(int $index): void
    {
        unset($this->footer_links[$index]);
        $this->footer_links = array_values($this->footer_links);
    }

    public function addSocialLink(): void
    {
        $this->validate([
            'new_social_platform' => 'required|string|in:' . implode(',', array_keys($this->getSocialPlatforms())),
            'new_social_url' => 'required|url|max:500',
        ]);

        $this->footer_social_links[] = [
            'platform' => $this->new_social_platform,
            'url' => $this->new_social_url,
        ];

        $this->new_social_platform = 'facebook';
        $this->new_social_url = '';
    }

    public function removeSocialLink(int $index): void
    {
        unset($this->footer_social_links[$index]);
        $this->footer_social_links = array_values($this->footer_social_links);
    }

    public function save(): void
    {
        $this->validate([
            'footer_text' => 'nullable|string|max:500',
        ]);

        $settings = Setting::instance();
        $settings->update([
            'footer_sticky' => $this->footer_sticky,
            'footer_glass' => $this->footer_glass,
            'footer_show_copyright' => $this->footer_show_copyright,
            'footer_show_terms' => $this->footer_show_terms,
            'footer_show_privacy' => $this->footer_show_privacy,
            'footer_show_docs' => $this->footer_show_docs,
            'footer_text' => $this->footer_text,
            'footer_links' => $this->footer_links,
            'footer_social_links' => $this->footer_social_links,
        ]);

        session()->flash('footer-saved', 'Footer settings saved successfully!');
        $this->dispatch('toast', message: 'Footer settings saved!', type: 'success');
    }

    public function getSocialPlatforms(): array
    {
        return [
            'facebook' => 'Facebook',
            'twitter' => 'X (Twitter)',
            'instagram' => 'Instagram',
            'youtube' => 'YouTube',
            'tiktok' => 'TikTok',
            'linkedin' => 'LinkedIn',
            'github' => 'GitHub',
            'discord' => 'Discord',
            'telegram' => 'Telegram',
            'reddit' => 'Reddit',
            'whatsapp' => 'WhatsApp',
            'threads' => 'Threads',
            'mastodon' => 'Mastodon',
            'twitch' => 'Twitch',
            'pinterest' => 'Pinterest',
        ];
    }

    public function render()
    {
        return view('livewire.admin.footer-settings', [
            'socialPlatforms' => $this->getSocialPlatforms(),
        ]);
    }
}
