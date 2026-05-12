<?php

namespace App\Livewire\User;

use Livewire\Component;

class TemplateBirthday extends Component
{
    public function render()
    {
        return view('livewire.user.template-birthday')->layout('layouts.guest');
    }
}
