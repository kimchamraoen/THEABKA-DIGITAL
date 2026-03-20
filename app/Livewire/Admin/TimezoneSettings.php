<?php

namespace App\Livewire\Admin;

use App\Models\Setting;
use DateTimeZone;
use Livewire\Component;

class TimezoneSettings extends Component
{
    public string $timezone = 'Asia/Phnom_Penh';
    public string $search = '';

    public function mount(): void
    {
        $this->timezone = Setting::instance()->timezone ?? 'Asia/Phnom_Penh';
    }

    public function save(): void
    {
        $this->validate([
            'timezone' => 'required|timezone',
        ]);

        $settings = Setting::instance();
        $settings->update([
            'timezone' => $this->timezone,
        ]);

        session()->flash('timezone-message', 'Timezone settings saved successfully!');
    }

    public function getFilteredTimezonesProperty(): array
    {
        $timezones = DateTimeZone::listIdentifiers();
        if ($this->search) {
            $search = strtolower($this->search);
            $timezones = array_filter($timezones, fn ($tz) => str_contains(strtolower($tz), $search));
        }
        return array_values($timezones);
    }

    public function render()
    {
        return view('livewire.admin.timezone-settings');
    }
}
