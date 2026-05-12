<?php

namespace App\Livewire\User;

use Livewire\Component;

class Invitation extends Component
{
    public function render()
    {
        return view('livewire.user.invitation')->layout('layouts.guest');
    }
}
