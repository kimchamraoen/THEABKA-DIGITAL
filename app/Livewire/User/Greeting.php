<?php

namespace App\Livewire\User;

use Livewire\Component;

class Greeting extends Component
{
    public function render()
    {
        return view('livewire.user.greeting')->layout('layouts.app');
    }
}
