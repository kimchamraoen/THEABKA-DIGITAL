<?php

namespace App\Livewire\Admin;

use App\Models\Setting;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class SmtpSettings extends Component
{
    public string $smtp_host = '';
    public ?int $smtp_port = 587;
    public string $smtp_username = '';
    public string $smtp_password = '';
    public string $smtp_encryption = 'tls';
    public string $smtp_from_address = '';
    public string $smtp_from_name = '';
    public string $test_email = '';

    public function mount(): void
    {
        $settings = Setting::instance();
        $this->smtp_host = $settings->smtp_host ?? '';
        $this->smtp_port = $settings->smtp_port ?? 587;
        $this->smtp_username = $settings->smtp_username ?? '';
        $this->smtp_password = $settings->smtp_password ?? '';
        $this->smtp_encryption = $settings->smtp_encryption ?? 'tls';
        $this->smtp_from_address = $settings->smtp_from_address ?? '';
        $this->smtp_from_name = $settings->smtp_from_name ?? '';
        $this->test_email = auth()->user()->email ?? '';
    }

    public function save(): void
    {
        $this->validate([
            'smtp_host' => 'nullable|string|max:255',
            'smtp_port' => 'nullable|integer|min:1|max:65535',
            'smtp_username' => 'nullable|string|max:255',
            'smtp_password' => 'nullable|string|max:500',
            'smtp_encryption' => 'nullable|in:tls,ssl,null',
            'smtp_from_address' => 'nullable|email|max:255',
            'smtp_from_name' => 'nullable|string|max:255',
        ]);

        $settings = Setting::instance();
        $settings->update([
            'smtp_host' => $this->smtp_host ?: null,
            'smtp_port' => $this->smtp_port ?: null,
            'smtp_username' => $this->smtp_username ?: null,
            'smtp_password' => $this->smtp_password ?: null,
            'smtp_encryption' => $this->smtp_encryption !== 'null' ? $this->smtp_encryption : null,
            'smtp_from_address' => $this->smtp_from_address ?: null,
            'smtp_from_name' => $this->smtp_from_name ?: null,
        ]);

        // Dynamically update mail config
        $this->updateMailConfig($settings->fresh());

        session()->flash('smtp-message', 'SMTP settings saved successfully!');
    }

    public function sendTestEmail(): void
    {
        $this->validate(['test_email' => 'required|email']);

        try {
            // Apply current settings
            $settings = Setting::instance();
            $this->updateMailConfig($settings);

            $appName = $settings->app_name;

            Mail::raw("This is a test email from {$appName}.\n\nIf you received this, your SMTP settings are configured correctly.\n\nSent at: " . now()->toDateTimeString(), function ($message) use ($appName) {
                $message->to($this->test_email)
                        ->subject("{$appName} - SMTP Test Email");
            });

            session()->flash('smtp-message', "Test email sent to {$this->test_email}! Check your inbox (and spam folder).");
        } catch (\Exception $e) {
            session()->flash('smtp-error', 'Failed to send test email: ' . $e->getMessage());
        }
    }

    protected function updateMailConfig(Setting $settings): void
    {
        if ($settings->smtp_host) {
            config([
                'mail.default' => 'smtp',
                'mail.mailers.smtp.host' => $settings->smtp_host,
                'mail.mailers.smtp.port' => $settings->smtp_port,
                'mail.mailers.smtp.username' => $settings->smtp_username,
                'mail.mailers.smtp.password' => $settings->smtp_password,
                'mail.mailers.smtp.encryption' => $settings->smtp_encryption,
                'mail.from.address' => $settings->smtp_from_address,
                'mail.from.name' => $settings->smtp_from_name,
            ]);
        }
    }

    public function render()
    {
        return view('livewire.admin.smtp-settings');
    }
}
