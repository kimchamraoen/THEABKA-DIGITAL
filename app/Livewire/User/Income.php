<?php

namespace App\Livewire\User;

use App\Models\Guest;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Income extends Component
{
    public $chartData;

    public function mount()
    {
        $guests = Guest::where('user_id', auth()->id())
            ->select('gift_money', 'note')
            ->get();

       $usdTotal = $guests->filter(function ($g) {
            return strtoupper(trim($g->note)) === 'USD';
        })->sum(fn ($g) => (float) $g->gift_money);

        $khrTotal = $guests->filter(function ($g) {
            return strtoupper(trim($g->note)) === 'KHR';
        })->sum(fn ($g) => (float) $g->gift_money);

        $this->chartData = [
            ['note', 'Total'],
            ['USD', $usdTotal],
            ['KHR', $khrTotal],
        ];
    }

    public function render()
    {
        return view('livewire.user.income')->layout('layouts.guest');
    }
}