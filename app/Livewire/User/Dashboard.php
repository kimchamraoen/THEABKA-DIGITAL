<?php

namespace App\Livewire\User;

use App\Models\Guest;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Dashboard extends Component
{
    public $chartData;

    public function mount()
    {
        $guests = Guest::where('user_id', auth()->id())
            ->select('statue', DB::raw('count(*) as count'))
            ->groupBy('statue')
            ->orderBy('statue', 'ASC')
            ->get();

        $guestData = [['Status', 'Count']];

        foreach ($guests as $guest) {
            $guestData[] = [$guest->statue ?? 'Unknown', (int) $guest->count];
        }

        $coming = $guests->where('statue', 'Coming')->sum('count');
        $notComing = $guests->where('statue', 'Not Coming')->sum('count');
        $pending = $guests->where('statue', 'Pending')->sum('count');

        $this->chartData = [
            ['Status', 'Guests'],
            ['Coming', max(0, $coming)],
            ['Not Coming', max(0, $notComing)],
            ['Pending', max(0, $pending)],
        ];
    }

    public function render()
    {
        return view('livewire.user.dashboard')->layout('layouts.guest');
    }
}