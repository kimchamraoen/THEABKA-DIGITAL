<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class GlassStyleSelector extends Component
{
    public string $glassStyle = 'liquid';

    /** @var array<string, string> */
    public array $styles = [
        'liquid' => 'Liquid',
        'card' => 'Card',
        'crystal' => 'Crystal',
        'frosted' => 'Frosted',
        'glass3d' => '3D Glass',
    ];

    public function mount(): void
    {
        $user = Auth::user();

        if ($user instanceof User) {
            $this->glassStyle = $user->getEffectiveGlassStyle();
        }
    }

    public function setStyle(string $style): void
    {
        if (! array_key_exists($style, $this->styles)) {
            return;
        }

        $this->glassStyle = $style;

        $user = Auth::user();

        if ($user instanceof User) {
            $user->update(['glass_style_preference' => $this->glassStyle]);
        }

        $this->dispatch('glass-style-changed', glassStyle: $this->glassStyle);
    }

    public function render()
    {
        return view('livewire.glass-style-selector');
    }
}
