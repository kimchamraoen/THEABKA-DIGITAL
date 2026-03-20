<?php

namespace App\Livewire\Admin;

use App\Models\SocialSetting;
use Livewire\Component;

class SocialSettings extends Component
{
    public string $google_client_id = '';
    public string $google_client_secret = '';
    public string $google_redirect_url = '';
    public bool $google_enabled = true;
    public string $facebook_client_id = '';
    public string $facebook_client_secret = '';
    public string $facebook_redirect_url = '';
    public bool $facebook_enabled = true;
    public string $telegram_bot_token = '';
    public string $telegram_bot_name = '';
    public bool $telegram_enabled = true;
    public string $twitter_client_id = '';
    public string $twitter_client_secret = '';
    public string $twitter_redirect_url = '';
    public bool $twitter_enabled = true;

    public function mount(): void
    {
        $this->google_client_id = SocialSetting::get('GOOGLE_CLIENT_ID', '');
        $this->google_client_secret = SocialSetting::get('GOOGLE_CLIENT_SECRET', '');
        $this->google_redirect_url = SocialSetting::get('GOOGLE_REDIRECT_URL', '');
        $this->google_enabled = SocialSetting::get('GOOGLE_ENABLED', 'true') === 'true';
        $this->facebook_client_id = SocialSetting::get('FACEBOOK_CLIENT_ID', '');
        $this->facebook_client_secret = SocialSetting::get('FACEBOOK_CLIENT_SECRET', '');
        $this->facebook_redirect_url = SocialSetting::get('FACEBOOK_REDIRECT_URL', '');
        $this->facebook_enabled = SocialSetting::get('FACEBOOK_ENABLED', 'true') === 'true';
        $this->telegram_bot_token = SocialSetting::get('TELEGRAM_BOT_TOKEN', '');
        $this->telegram_bot_name = SocialSetting::get('TELEGRAM_BOT_NAME', '');
        $this->telegram_enabled = SocialSetting::get('TELEGRAM_ENABLED', 'true') === 'true';
        $this->twitter_client_id = SocialSetting::get('TWITTER_CLIENT_ID', '');
        $this->twitter_client_secret = SocialSetting::get('TWITTER_CLIENT_SECRET', '');
        $this->twitter_redirect_url = SocialSetting::get('TWITTER_REDIRECT_URL', '');
        $this->twitter_enabled = SocialSetting::get('TWITTER_ENABLED', 'true') === 'true';
    }

    public function save(): void
    {
        $this->validate([
            'google_client_id' => 'nullable|string|max:500',
            'google_client_secret' => 'nullable|string|max:500',
            'google_redirect_url' => 'nullable|url|max:500',
            'facebook_client_id' => 'nullable|string|max:500',
            'facebook_client_secret' => 'nullable|string|max:500',
            'facebook_redirect_url' => 'nullable|url|max:500',
            'telegram_bot_token' => 'nullable|string|max:500',
            'telegram_bot_name' => 'nullable|string|max:255',
            'twitter_client_id' => 'nullable|string|max:500',
            'twitter_client_secret' => 'nullable|string|max:500',
            'twitter_redirect_url' => 'nullable|url|max:500',
        ]);

        // Save all settings to database
        SocialSetting::set('GOOGLE_CLIENT_ID', $this->google_client_id ?: null);
        SocialSetting::set('GOOGLE_CLIENT_SECRET', $this->google_client_secret ?: null);
        SocialSetting::set('GOOGLE_REDIRECT_URL', $this->google_redirect_url ?: null);
        SocialSetting::set('GOOGLE_ENABLED', $this->google_enabled ? 'true' : 'false');
        SocialSetting::set('FACEBOOK_CLIENT_ID', $this->facebook_client_id ?: null);
        SocialSetting::set('FACEBOOK_CLIENT_SECRET', $this->facebook_client_secret ?: null);
        SocialSetting::set('FACEBOOK_REDIRECT_URL', $this->facebook_redirect_url ?: null);
        SocialSetting::set('FACEBOOK_ENABLED', $this->facebook_enabled ? 'true' : 'false');
        SocialSetting::set('TELEGRAM_BOT_TOKEN', $this->telegram_bot_token ?: null);
        SocialSetting::set('TELEGRAM_BOT_NAME', $this->telegram_bot_name ?: null);
        SocialSetting::set('TELEGRAM_ENABLED', $this->telegram_enabled ? 'true' : 'false');
        SocialSetting::set('TWITTER_CLIENT_ID', $this->twitter_client_id ?: null);
        SocialSetting::set('TWITTER_CLIENT_SECRET', $this->twitter_client_secret ?: null);
        SocialSetting::set('TWITTER_REDIRECT_URL', $this->twitter_redirect_url ?: null);
        SocialSetting::set('TWITTER_ENABLED', $this->twitter_enabled ? 'true' : 'false');

        session()->flash('social-message', 'Social login settings saved successfully!');
    }

    /**
     * Auto-generate redirect URLs based on current app URL.
     */
    public function generateRedirectUrls(): void
    {
        $baseUrl = config('app.url');
        
        if (empty($this->google_redirect_url)) {
            $this->google_redirect_url = $baseUrl . '/auth/google/callback';
        }
        
        if (empty($this->facebook_redirect_url)) {
            $this->facebook_redirect_url = $baseUrl . '/auth/facebook/callback';
        }

        if (empty($this->twitter_redirect_url)) {
            $this->twitter_redirect_url = $baseUrl . '/auth/twitter/callback';
        }

        session()->flash('social-message', 'Redirect URLs generated. Remember to update them if your domain changes.');
    }

    public function render()
    {
        return view('livewire.admin.social-settings');
    }
}
