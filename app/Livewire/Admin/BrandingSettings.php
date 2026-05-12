<?php

namespace App\Livewire\Admin;

use App\Models\Setting;
use Livewire\Component;
use Livewire\WithFileUploads;

class BrandingSettings extends Component
{
    use WithFileUploads;

    public $app_name = '';
    public $logo_upload;
    public $favicon_upload;
    public $current_logo;
    public $current_favicon;

    // Email template texts
    public $verify_email_text = '';
    public $forgot_password_text = '';
    public $welcome_email_text = '';

    // Auth background
    public $auth_bg_type = 'gradient';
    public $auth_bg_image_upload;
    public $auth_bg_video_upload;
    public $current_auth_bg_image;
    public $current_auth_bg_video;

    // App (dashboard/settings) background
    public $app_bg_type = 'gradient';
    public $app_bg_image_upload;
    public $app_bg_video_upload;
    public $current_app_bg_image;
    public $current_app_bg_video;

    // Landing page background
    public $landing_bg_type = 'gradient';
    public $landing_bg_image_upload;
    public $landing_bg_video_upload;
    public $current_landing_bg_image;
    public $current_landing_bg_video;

    // Landing page hero
    public $landing_hero_badge = '';
    public $landing_hero_line1 = '';
    public $landing_hero_line2 = '';
    public $landing_hero_line3 = '';
    public $landing_hero_subtitle = '';
    public $landing_cta_primary_text = '';
    public $landing_cta_primary_url = '';
    public $landing_cta_secondary_text = '';
    public $landing_cta_secondary_url = '';

    // Landing sections toggles
    public $landing_features_title = '';
    public $landing_features_subtitle = '';
    public $landing_features_visible = true;
    public $landing_cta_title = '';
    public $landing_cta_subtitle = '';
    public $landing_cta_visible = true;
    public $landing_floating_cards = true;
    public $landing_particles = true;

    // Terms & Privacy
    public $terms_content = '';
    public $privacy_content = '';
    public $footer_text = '';

    // UI tab
    public $activeTab = 'branding';

    // Dirty state tracking for revert
    public array $originalValues = [];
    public bool $hasUnsavedChanges = false;

    protected function getTrackableProperties(): array
    {
        return [
            'app_name', 'verify_email_text', 'forgot_password_text', 'welcome_email_text',
            'auth_bg_type', 'app_bg_type', 'landing_bg_type',
            'landing_hero_badge', 'landing_hero_line1', 'landing_hero_line2', 'landing_hero_line3',
            'landing_hero_subtitle', 'landing_cta_primary_text', 'landing_cta_primary_url',
            'landing_cta_secondary_text', 'landing_cta_secondary_url',
            'landing_features_title', 'landing_features_subtitle', 'landing_features_visible',
            'landing_cta_title', 'landing_cta_subtitle', 'landing_cta_visible',
            'landing_floating_cards', 'landing_particles',
            'terms_content', 'privacy_content', 'footer_text',
        ];
    }

    protected function storeOriginals(): void
    {
        $this->originalValues = [];
        foreach ($this->getTrackableProperties() as $prop) {
            $this->originalValues[$prop] = $this->$prop;
        }
    }

    public function updated($property): void
    {
        if ($property === 'activeTab') return;
        $this->hasUnsavedChanges = true;

        // Live preview: when switching app bg to gradient, clear preview overlay
        if ($property === 'app_bg_type' && $this->app_bg_type === 'gradient') {
            $this->dispatch('app-bg-preview-clear');
        }
    }

    public function revert(): void
    {
        foreach ($this->originalValues as $prop => $value) {
            $this->$prop = $value;
        }

        // Clear all pending file uploads
        $this->logo_upload = null;
        $this->favicon_upload = null;
        $this->auth_bg_image_upload = null;
        $this->auth_bg_video_upload = null;
        $this->app_bg_image_upload = null;
        $this->app_bg_video_upload = null;
        $this->landing_bg_image_upload = null;
        $this->landing_bg_video_upload = null;

        $this->hasUnsavedChanges = false;

        // Clear any live background preview overlay
        $this->dispatch('app-bg-preview-clear');
    }

    public function mount(): void
    {
        $settings = Setting::instance();
        $this->app_name = $settings->app_name;
        $this->current_logo = $settings->app_logo;
        $this->current_favicon = $settings->app_favicon;
        $this->verify_email_text = $settings->verify_email_text ?? "Before continuing, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.";
        $this->forgot_password_text = $settings->forgot_password_text ?? 'Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.';
        $this->welcome_email_text = $settings->welcome_email_text ?? '';

        // Auth background
        $this->auth_bg_type = $settings->auth_bg_type ?? 'gradient';
        $this->current_auth_bg_image = $settings->auth_bg_image;
        $this->current_auth_bg_video = $settings->auth_bg_video_file;

        // App background
        $this->app_bg_type = $settings->app_bg_type ?? 'gradient';
        $this->current_app_bg_image = $settings->app_bg_image;
        $this->current_app_bg_video = $settings->app_bg_video;

        // Landing background
        $this->landing_bg_type = $settings->landing_bg_type ?? 'gradient';
        $this->current_landing_bg_image = $settings->landing_bg_image;
        $this->current_landing_bg_video = $settings->landing_bg_video;

        // Landing hero
        $this->landing_hero_badge = $settings->landing_hero_badge ?? '';
        $this->landing_hero_line1 = $settings->landing_hero_line1 ?? '';
        $this->landing_hero_line2 = $settings->landing_hero_line2 ?? '';
        $this->landing_hero_line3 = $settings->landing_hero_line3 ?? '';
        $this->landing_hero_subtitle = $settings->landing_hero_subtitle ?? '';
        $this->landing_cta_primary_text = $settings->landing_cta_primary_text ?? '';
        $this->landing_cta_primary_url = $settings->landing_cta_primary_url ?? '';
        $this->landing_cta_secondary_text = $settings->landing_cta_secondary_text ?? '';
        $this->landing_cta_secondary_url = $settings->landing_cta_secondary_url ?? '';

        // Landing sections
        $this->landing_features_title = $settings->landing_features_title ?? '';
        $this->landing_features_subtitle = $settings->landing_features_subtitle ?? '';
        $this->landing_features_visible = $settings->landing_features_visible ?? true;
        $this->landing_cta_title = $settings->landing_cta_title ?? '';
        $this->landing_cta_subtitle = $settings->landing_cta_subtitle ?? '';
        $this->landing_cta_visible = $settings->landing_cta_visible ?? true;
        $this->landing_floating_cards = $settings->landing_floating_cards ?? true;
        $this->landing_particles = $settings->landing_particles ?? true;

        // Legal
        $this->terms_content = $settings->terms_content ?? '';
        $this->privacy_content = $settings->privacy_content ?? '';
        $this->footer_text = $settings->footer_text ?? '';

        $this->storeOriginals();
    }

    public function save(): void
    {
        $this->validate([
            'app_name' => 'required|string|max:100',
            'logo_upload' => 'nullable|image|max:2048',
            'favicon_upload' => 'nullable|image|max:512',
            'verify_email_text' => 'nullable|string|max:2000',
            'forgot_password_text' => 'nullable|string|max:2000',
            'welcome_email_text' => 'nullable|string|max:2000',
            'auth_bg_type' => 'required|in:gradient,image,video',
            'auth_bg_image_upload' => 'nullable|image|max:5120',
            'auth_bg_video_upload' => 'nullable|mimes:mp4,webm|max:20480',
            'app_bg_type' => 'required|in:gradient,image,video',
            'app_bg_image_upload' => 'nullable|image|max:5120',
            'app_bg_video_upload' => 'nullable|mimes:mp4,webm|max:20480',
            'landing_bg_type' => 'required|in:gradient,image,video',
            'landing_bg_image_upload' => 'nullable|image|max:5120',
            'landing_bg_video_upload' => 'nullable|mimes:mp4,webm|max:20480',
            'terms_content' => 'nullable|string|max:100000',
            'privacy_content' => 'nullable|string|max:100000',
            'footer_text' => 'nullable|string|max:500',
        ]);

        $settings = Setting::instance();
        $data = [
            'app_name' => $this->app_name,
            'verify_email_text' => $this->verify_email_text,
            'forgot_password_text' => $this->forgot_password_text,
            'welcome_email_text' => $this->welcome_email_text,
            'auth_bg_type' => $this->auth_bg_type,
            'app_bg_type' => $this->app_bg_type,
            'landing_bg_type' => $this->landing_bg_type,
            'terms_content' => $this->terms_content ?: null,
            'privacy_content' => $this->privacy_content ?: null,
            'footer_text' => $this->footer_text ?: null,
            // Landing hero
            'landing_hero_badge' => $this->landing_hero_badge ?: null,
            'landing_hero_line1' => $this->landing_hero_line1 ?: null,
            'landing_hero_line2' => $this->landing_hero_line2 ?: null,
            'landing_hero_line3' => $this->landing_hero_line3 ?: null,
            'landing_hero_subtitle' => $this->landing_hero_subtitle ?: null,
            'landing_cta_primary_text' => $this->landing_cta_primary_text ?: null,
            'landing_cta_primary_url' => $this->landing_cta_primary_url ?: null,
            'landing_cta_secondary_text' => $this->landing_cta_secondary_text ?: null,
            'landing_cta_secondary_url' => $this->landing_cta_secondary_url ?: null,
            // Landing sections
            'landing_features_title' => $this->landing_features_title ?: null,
            'landing_features_subtitle' => $this->landing_features_subtitle ?: null,
            'landing_features_visible' => $this->landing_features_visible,
            'landing_cta_title' => $this->landing_cta_title ?: null,
            'landing_cta_subtitle' => $this->landing_cta_subtitle ?: null,
            'landing_cta_visible' => $this->landing_cta_visible,
            'landing_floating_cards' => $this->landing_floating_cards,
            'landing_particles' => $this->landing_particles,
        ];

        // File uploads: Logo & Favicon
        if ($this->logo_upload) {
            $data['app_logo'] = $this->logo_upload->store('branding', 'public');
            $this->current_logo = $data['app_logo'];
        }
        if ($this->favicon_upload) {
            $data['app_favicon'] = $this->favicon_upload->store('branding', 'public');
            $this->current_favicon = $data['app_favicon'];
        }

        // Auth background files
        if ($this->auth_bg_image_upload) {
            $data['auth_bg_image'] = $this->auth_bg_image_upload->store('backgrounds', 'public');
            $this->current_auth_bg_image = $data['auth_bg_image'];
        }
        if ($this->auth_bg_video_upload) {
            $data['auth_bg_video_file'] = $this->auth_bg_video_upload->store('backgrounds', 'public');
            $data['auth_bg_video'] = null; // Clear legacy URL
            $this->current_auth_bg_video = $data['auth_bg_video_file'];
        }

        // App background files
        if ($this->app_bg_image_upload) {
            $data['app_bg_image'] = $this->app_bg_image_upload->store('backgrounds', 'public');
            $this->current_app_bg_image = $data['app_bg_image'];
        }
        if ($this->app_bg_video_upload) {
            $data['app_bg_video'] = $this->app_bg_video_upload->store('backgrounds', 'public');
            $this->current_app_bg_video = $data['app_bg_video'];
        }

        // Landing background files
        if ($this->landing_bg_image_upload) {
            $data['landing_bg_image'] = $this->landing_bg_image_upload->store('backgrounds', 'public');
            $this->current_landing_bg_image = $data['landing_bg_image'];
        }
        if ($this->landing_bg_video_upload) {
            $data['landing_bg_video'] = $this->landing_bg_video_upload->store('backgrounds', 'public');
            $this->current_landing_bg_video = $data['landing_bg_video'];
        }

        // Safety: if bg_type is image/video but no media exists, fall back to gradient
        if ($data['auth_bg_type'] === 'image' && !($data['auth_bg_image'] ?? $this->current_auth_bg_image)) {
            $data['auth_bg_type'] = 'gradient';
            $this->auth_bg_type = 'gradient';
        }
        if ($data['auth_bg_type'] === 'video' && !($data['auth_bg_video_file'] ?? $this->current_auth_bg_video)) {
            $data['auth_bg_type'] = 'gradient';
            $this->auth_bg_type = 'gradient';
        }
        if ($data['app_bg_type'] === 'image' && !($data['app_bg_image'] ?? $this->current_app_bg_image)) {
            $data['app_bg_type'] = 'gradient';
            $this->app_bg_type = 'gradient';
        }
        if ($data['app_bg_type'] === 'video' && !($data['app_bg_video'] ?? $this->current_app_bg_video)) {
            $data['app_bg_type'] = 'gradient';
            $this->app_bg_type = 'gradient';
        }
        if ($data['landing_bg_type'] === 'image' && !($data['landing_bg_image'] ?? $this->current_landing_bg_image)) {
            $data['landing_bg_type'] = 'gradient';
            $this->landing_bg_type = 'gradient';
        }
        if ($data['landing_bg_type'] === 'video' && !($data['landing_bg_video'] ?? $this->current_landing_bg_video)) {
            $data['landing_bg_type'] = 'gradient';
            $this->landing_bg_type = 'gradient';
        }

        $settings->update($data);
        cache()->forget('app_settings');

        $this->logo_upload = null;
        $this->favicon_upload = null;
        $this->auth_bg_image_upload = null;
        $this->auth_bg_video_upload = null;
        $this->app_bg_image_upload = null;
        $this->app_bg_video_upload = null;
        $this->landing_bg_image_upload = null;
        $this->landing_bg_video_upload = null;

        // Update originals and clear dirty state
        $this->storeOriginals();
        $this->hasUnsavedChanges = false;

        // Update the live page background to reflect saved state.
        // The layout only renders once on initial load, so we need JS to update the background.
        if ($this->app_bg_type === 'image' && $this->current_app_bg_image) {
            $this->dispatch('app-bg-preview', type: 'image', url: asset('storage/' . $this->current_app_bg_image));
        } elseif ($this->app_bg_type === 'video' && $this->current_app_bg_video) {
            $this->dispatch('app-bg-preview', type: 'video', url: asset('storage/' . $this->current_app_bg_video));
        } else {
            // Gradient mode or no media — clear the preview overlay so the theme gradient shows
            $this->dispatch('app-bg-preview-clear');
        }

        session()->flash('branding-saved', 'Settings saved successfully!');
    }

    public function removeLogo(): void
    {
        $settings = Setting::instance();
        $settings->update(['app_logo' => null]);
        $this->current_logo = null;
        cache()->forget('app_settings');
    }

    public function removeFavicon(): void
    {
        $settings = Setting::instance();
        $settings->update(['app_favicon' => null]);
        $this->current_favicon = null;
        cache()->forget('app_settings');
    }

    public function removeAuthBgImage(): void
    {
        $settings = Setting::instance();
        $settings->update(['auth_bg_image' => null]);
        $this->current_auth_bg_image = null;
        cache()->forget('app_settings');
    }

    public function removeAuthBgVideo(): void
    {
        $settings = Setting::instance();
        $settings->update(['auth_bg_video_file' => null, 'auth_bg_video' => null]);
        $this->current_auth_bg_video = null;
        cache()->forget('app_settings');
    }

    public function removeAppBgImage(): void
    {
        $settings = Setting::instance();
        $settings->update(['app_bg_image' => null]);
        $this->current_app_bg_image = null;
        cache()->forget('app_settings');
    }

    public function removeAppBgVideo(): void
    {
        $settings = Setting::instance();
        $settings->update(['app_bg_video' => null]);
        $this->current_app_bg_video = null;
        cache()->forget('app_settings');
    }

    public function removeLandingBgImage(): void
    {
        $settings = Setting::instance();
        $settings->update(['landing_bg_image' => null]);
        $this->current_landing_bg_image = null;
        cache()->forget('app_settings');
    }

    public function removeLandingBgVideo(): void
    {
        $settings = Setting::instance();
        $settings->update(['landing_bg_video' => null]);
        $this->current_landing_bg_video = null;
        cache()->forget('app_settings');
    }

    public function render()
    {
        return view('livewire.admin.branding-settings');
    }
}