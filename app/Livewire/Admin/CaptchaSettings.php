<?php

namespace App\Livewire\Admin;

use App\Models\Setting;
use Livewire\Component;

class CaptchaSettings extends Component
{
    public ?string $captcha_provider = null;
    public string $recaptcha_site_key = '';
    public string $recaptcha_secret_key = '';
    public string $turnstile_site_key = '';
    public string $turnstile_secret_key = '';
    public bool $captcha_on_login = true;
    public bool $captcha_on_register = true;

    public function mount(): void
    {
        $settings = Setting::instance();
        $this->captcha_provider = $settings->captcha_provider;
        $this->recaptcha_site_key = $settings->recaptcha_site_key ?? '';
        $this->recaptcha_secret_key = $settings->recaptcha_secret_key ?? '';
        $this->turnstile_site_key = $settings->turnstile_site_key ?? '';
        $this->turnstile_secret_key = $settings->turnstile_secret_key ?? '';
        $this->captcha_on_login = $settings->captcha_on_login ?? true;
        $this->captcha_on_register = $settings->captcha_on_register ?? true;
    }

    public function save(): void
    {
        $this->validate([
            'captcha_provider' => 'nullable|in:recaptcha,turnstile',
            'recaptcha_site_key' => 'nullable|string|max:255',
            'recaptcha_secret_key' => 'nullable|string|max:255',
            'turnstile_site_key' => 'nullable|string|max:255',
            'turnstile_secret_key' => 'nullable|string|max:255',
            'captcha_on_login' => 'boolean',
            'captcha_on_register' => 'boolean',
        ]);

        // Validate that keys are provided when a provider is selected
        if ($this->captcha_provider === 'recaptcha') {
            if (!$this->recaptcha_site_key || !$this->recaptcha_secret_key) {
                $this->addError('recaptcha_site_key', 'Both reCAPTCHA Site Key and Secret Key are required.');
                return;
            }
        }

        if ($this->captcha_provider === 'turnstile') {
            if (!$this->turnstile_site_key || !$this->turnstile_secret_key) {
                $this->addError('turnstile_site_key', 'Both Turnstile Site Key and Secret Key are required.');
                return;
            }
        }

        $settings = Setting::instance();
        $settings->update([
            'captcha_provider' => $this->captcha_provider ?: null,
            'recaptcha_site_key' => $this->recaptcha_site_key ?: null,
            'recaptcha_secret_key' => $this->recaptcha_secret_key ?: null,
            'turnstile_site_key' => $this->turnstile_site_key ?: null,
            'turnstile_secret_key' => $this->turnstile_secret_key ?: null,
            'captcha_on_login' => $this->captcha_on_login,
            'captcha_on_register' => $this->captcha_on_register,
        ]);

        session()->flash('captcha-message', 'CAPTCHA settings saved successfully!');
    }

    public function render()
    {
        return view('livewire.admin.captcha-settings');
    }
}
