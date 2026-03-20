<?php

namespace App\Livewire\Admin;

use App\Models\Setting;
use Livewire\Component;

class SecuritySettings extends Component
{
    public bool $allow_unverified_login = false;

    public function mount(): void
    {
        $settings = Setting::instance();
        $this->allow_unverified_login = $settings->allow_unverified_login ?? false;
    }

    public function save(): void
    {
        $settings = Setting::instance();
        $settings->update([
            'allow_unverified_login' => $this->allow_unverified_login,
        ]);

        session()->flash('security-message', __('Security settings saved successfully!'));
    }

    public function render()
    {
        return view('livewire.admin.security-settings');
    }
}
