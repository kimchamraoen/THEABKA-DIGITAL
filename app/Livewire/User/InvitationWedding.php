<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\InvitationWedding as modelInvitationWedding;
use Illuminate\Support\Facades\Auth;

class InvitationWedding extends Component
{
    use WithFileUploads;

    public modelInvitationWedding $invitation;
    public $cover_image_upload;
    public $background_image_upload;
    public $guest_name, $group, $Greeting, $gift_money, $statue;

    protected $rules = [
        'invitation.title' => 'nullable|string',
        'invitation.bride_name' => 'nullable|string',
        'invitation.groom_name' => 'nullable|string',
        'invitation.title_color' => 'nullable|string',
        'invitation.text_color' => 'nullable|string',
        'guest_name' => 'nullable|string',
        'group' => 'string',
        'Greeting' => 'nullable|string',
        'statue' => 'string',
        'gift_money' => 'nullable|string',
    ];

    public function mount()
    {
        $this->invitation = modelInvitationWedding::firstOrNew(['user_id' => Auth::id()]);
        
        // Set defaults if new
        if (!$this->invitation->exists) {
            $this->invitation->title = "សិរីមង្គលអាពាហ៍ពិពាហ៍";
            $this->invitation->title_color = "#f9af59";
            $this->invitation->text_color = "#030303";
        }
    }

    public function updatedCoverImageUpload()
    {
        $path = $this->cover_image_upload->store('invitations', 'public');
        $this->invitation->cover_image = $path;
    }

    public function updatedBackgroundImageUpload()
    {
        $path = $this->background_image_upload->store('invitations', 'public');
        $this->invitation->background_image = $path;
    }

    public function save()
    {
        $this->invitation->user_id = Auth::id();
        $this->invitation->save();
        session()->flash('success', 'រក្សាទុកបានជោគជ័យ!');
    }

    public function render()
    {
        return view('livewire.user.invitation-wedding');
    }
}