<?php

namespace App\Livewire\User;

use App\Models\Guest;
use App\Models\Template;
use Livewire\Component;
use Illuminate\Support\Facades\File;

class LandingWedding extends Component
{
    public $invitation,$guest_name,$isGuestView, $guest_id, $statue, $Greeting,$userTemplate, $guest;
    public $imageFiles = [];
    public $background_images,$uuid;

    public function mount($uuid)
    {
        if($uuid){
            $this->isGuestView = true;
        }

        $guest = Guest::where('uuid', $uuid)->firstOrFail();
        $this->guest_id = $guest->id;
        $this->guest_name = $guest->guest_name;
        $this->userTemplate = $guest->template ?? null;
        $this->statue = $this->guest->statue ?? '';
        $this->Greeting = $this->guest->Greeting ?? '';

        $guest = Guest::where('uuid', $uuid)->firstOrFail();

        $this->guest_id = $guest->id;
        $this->guest_name = $guest->guest_name;
        $this->uuid = $guest->uuid;

        $this->invitation = Template::where('user_id', $guest->user_id)->first();

        $this->background_images = $this->invitation->background_images ?? [];

        $bgFiles = File::files(public_path('storage/background_images'));
        foreach ($bgFiles as $file) {
            $this->imageFiles[] = 'background_images/' . $file->getFilename();
        }
    }

    public function render()
    {
        // $backgroundFiles = $this->invitation->background_images ?: 'default_bg.jpg';
        // $files = is_array($backgroundFiles) ? $backgroundFiles : [$backgroundFiles];

        return view('livewire.user.landing-wedding', [
            // 'files' => $files
        ])->layout('layouts.guest');
    }
}