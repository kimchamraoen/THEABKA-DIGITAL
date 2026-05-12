<?php

namespace App\Livewire\User;

use App\Models\Guest;
use Livewire\Component;

class Profit extends Component
{
    public $chartData;

    public function mount()
    {
        
    }

    public function render()
    {
        return view('livewire.user.profit')->layout('layouts.guest');
    }
}