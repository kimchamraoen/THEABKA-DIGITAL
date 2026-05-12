<?php

namespace App\Livewire\User;

use Livewire\Component;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class DefauldWedding extends Component
{
    // public $guest;
    public $guest_name;
    public $template;
    public $isGuestView = true;
    public $defaultTemplate;

    // Template fields
    public $bride_name = '' ;
    public $groom_name = '';
    public $title = '';
    public $subtitle = '';
    public $event_time = '';
    public $date;
    public $title_font_family = '';
    public $text_font_family = '';
    public $text_font_size = '';
    public $title_color = '';
    public $text_color = '';
    public $address = '';
    public $title_invitation = '';
    public $message_invitation = '';
    public $title_thanks = '';
    public $message_thanks = '';
    public $link_map = '';
    public $background_music = '';

    public $cover_image;
    public $background_images;
    public $map_photo;
    public $dollar_qr;
    public $khmer_qr;
    public $pre_wedding1;
    public $pre_wedding2;
    public $pre_wedding3;
    public $pre_wedding4;

    public $userTemplate;
    public $music;
    public $imageFiles = [];

    public $days = '00';
    public $hours = '00';
    public $minutes = '00';
    public $seconds = '00';

    protected $listeners = [
        'tick' => 'updateCountdown',
        // 'refreshGuests' => '$refresh',
    ];

    public $group, $Greeting, $gift_money, $statue = 'Pending';
    public $guest_id, $guest, $guests = [];

    protected $rules = [
        'guest_name' => 'required|min:3',
        'group' => 'required',
        'Greeting' => 'nullable|max:255',
        'statue' => 'required',
    ];

    public function mount()
    {
        // Get data from database and find if guest don't have greeting not display
        // $this->guests = Guest::whereNotNull('Greeting')->where('Greeting', '!=', '')->get();

        // Default event time (optional)
        // $this->date = now()->addDays(90)->format('Y-m-d\TH:i');
        $this->updateCountdown();

        // if($uuid){
        //     $this->isGuestView = true;
        // }

        // $guest = Guest::where('uuid', $uuid)->firstOrFail();
        // $this->guest_id = $guest->id;
        // $this->guest_name = $guest->guest_name;
        $this->userTemplate = $guest->template ?? null;
        $this->statue = $this->guest->statue ?? '';
        $this->Greeting = $this->guest->Greeting ?? '';

        // load default template
        // $this->defaultTemplate = Template::where('is_default', 1)->first();

        // $template = Template::where('user_id', $guest->user_id)->first();

        // if (!$template) {
        //     abort(404, "Wedding template not found");
        // }else{
        //     $this->music = str_replace('\\', '/', $template->background_music ?? 'music/audio1.mp3');
        // }

        // Assign template fields
        $this->title_font_family   = $template->title_font_family ?? "'Great Vibes', cursive";
        $this->text_font_family    = $template->text_font_family ?? "'Poppins', sans-serif";
        $this->text_font_size      = $template->text_font_size ?? '16';
        $this->title_color         = $template->title_color ?? '#F09F1D';
        $this->text_color          = $template->text_color ?? '#694308';

        $this->bride_name          = $template->bride_name ?? 'Da Niang';
        $this->groom_name          = $template->groom_name ?? 'Rang Narak';
        $this->title               = $template->title ?? 'Wedding Invitation';
        $this->subtitle            = $template->subtitle ?? 'You are invited!';
        $this->event_time          = $template->event_time ?? 'Saturday, February 14, 2026 at 5:00 PM';
        $this->date                = now()->addDays(90)->format('Y-m-d\TH:i');
        $this->address             = $template->address ?? 'The Premier Center Sen Sok, Phnom Penh. Cambodia';
        $this->title_invitation    = $template->title_invitation ?? 'WE ARE HONORED TO INVITE YOU';
        $this->message_invitation  = $template->message_invitation ?? 'Your Excellency, Oknha, Madam, Ladies and Gentlemen, please join us as the presiding officers and guests of honor to bestow blessings on our two weddings.';
        $this->title_thanks        = $template->title_thanks ?? 'OUR GRATITUDE AND APOLOGY';
        $this->message_thanks      = $template->message_thanks ?? 'We are extremely thankful for H.E., L.C.T., Okhna, ladies and gentlemen for your presence in the upcoming marriage of our children.
                                                                    We would like to apologize if this invitation has not been personally delivered by us.';
        $this->link_map            = $template->link_map ?? 'https://maps.app.goo.gl/9Wh42r1JEXaL6PXJ9';
        $this->background_music    = $template->background_music ?? 'music/audio1.mp3';

        $this->cover_image          = $template->cover_image ?? 'images/cover.jpg';
        $this->background_images    = $template->background_images ?? 'storage/background_images/animation5.mp4';
        $this->map_photo            = $template->map_photo ?? 'images/map.jpg';
        $this->dollar_qr            = $template->dollar_qr ?? 'images/dollar_qr.jpg';
        $this->khmer_qr             = $template->khmer_qr ?? 'images/khmer_qr.jpg';
        $this->pre_wedding1         = $template->pre_wedding1 ?? 'images/pre1.jpg';
        $this->pre_wedding2         = $template->pre_wedding2 ?? 'images/pre2.jpg';
        $this->pre_wedding3         = $template->pre_wedding3 ?? 'images/pre3.jpg';
        $this->pre_wedding4         = $template->pre_wedding4 ?? 'images/pre4.jpg';
        $this->guest_name           = 'Dout Daranak';   
        $this->music = asset('storage/' . ($template->background_music ?? 'music/kukrong.mp4'));
        // $this->musicFiles = array_map(fn($file) => 'music/' . basename($file), glob(public_path('storage/music/*.mp3')));
        $bgFiles = File::files(public_path('storage/background_images'));
        foreach ($bgFiles as $file) {
            $this->imageFiles[] = 'storage/background_images/' . $file->getFilename();
        }
    }

    public function updatedDate() 
    {
        $this->dispatch('updatedDate'); 
    }

    public function updateCountdown()
    {
        if (!$this->date) return;

        $now = Carbon::now(60);
        $event = Carbon::parse($this->date);

        if ($now->greaterThan($event)) {
            $this->days = $this->hours = $this->minutes = $this->seconds = '00';
            return;
        }

        $diff = $event->diff($now);

        $this->days = str_pad($diff->d, 2, '0', STR_PAD_LEFT);
        $this->hours = str_pad($diff->h, 2, '0', STR_PAD_LEFT);
        $this->minutes = str_pad($diff->i, 2, '0', STR_PAD_LEFT);
        $this->seconds = str_pad($diff->s, 2, '0', STR_PAD_LEFT);
    }

    // auto update date
    public function tick()
    {
        $this->updateCountdown();
    }

    public function save()
    {
        session()->flash('success', 'Your blessing has been saved! ❤️');
    }

    public function render()
    {
        return view('livewire.user.defauld-wedding')->layout('layouts.guest');
    }
}