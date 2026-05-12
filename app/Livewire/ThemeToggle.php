<?php

namespace App\Livewire;

use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ThemeToggle extends Component
{
    public string $theme = 'dark';

    /** @var array<string, string> */
    public array $themes = [
        'light' => 'Light',
        'dark' => 'Dark',
        'dim' => 'Dim',
    ];

    public function mount(): void
    {
        $user = Auth::user();

        if ($user) {
            $this->theme = $user->getEffectiveTheme();
        } else {
            $this->theme = Setting::instance()->default_theme;
        }
    }

    public function toggleTheme(): void
    {
        $this->setTheme($this->theme === 'dark' ? 'light' : 'dark');
    }

    public function setTheme(string $theme): void
    {
        if (! array_key_exists($theme, $this->themes)) {
            return;
        }

        $this->theme = $theme;

        if (Auth::check()) {
            Auth::user()->update(['theme_preference' => $this->theme]);
        }

        $this->dispatch('theme-changed', theme: $this->theme);
    }

    public function render()
    {
        return view('livewire.theme-toggle');
    }
}
