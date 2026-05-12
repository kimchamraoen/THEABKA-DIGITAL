<?php

namespace App\Livewire\User;

use App\Models\Event;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Template as TemplateModel;
use App\Models\Guest;
use App\Models\TelegramGroup;
use Carbon\Carbon;

class Template extends Component
{
    use WithFileUploads;

    // link
    public $userTemplate;
    public $defaultTemplate = null;
    public $uuid;
    public $templateId;

    // Text fields
    public $bride_name = '';
    public $groom_name = '';
    public $title = '';
    public $subtitle = '';
    public $event_time = '';
    public $title_font_family = '';
    public $text_font_family = '';
    public $text_font_size = '';
    public $title_color = '';
    public $text_color = '';
    public $date = '';
    public $address = '';
    public $title_invitation = '';
    public $message_invitation = '';
    public $title_thanks = '';
    public $message_thanks = '';
    public $link_map = '';
    public $background_music = null;

    // Images
    public $cover_image;
    public $background_images;
    public $map_photo;
    public $dollar_qr;
    public $khmer_qr;
    public $pre_wedding1;
    public $pre_wedding2;
    public $pre_wedding3;
    public $pre_wedding4;
    public $video_url;
    public $video_public_id;
    public $event;
    public $option, $link_dollar, $link_khmer, $facebook, $instagram, $telegram, $phone;

    public $isGuestView = false;
    public $guests = [];

    public $musicFiles = [];
    public $imageFiles = [];
    public $videoFiles = [];

    // event table
    public $name,$event_date,$title_event, $template, $event_id;
    public $isOpen = false;

    // date
    public $days = '00';
    public $hours = '00';
    public $minutes = '00';
    public $seconds = '00';
    protected $listeners = ['tick' => 'updateCountdown'];

    // Mount the component
    public function mount($uuid = null)
    {
        $this->uuid = $uuid;
        $this->isGuestView = (bool)$uuid;

        if ($uuid) {
            $guest = Guest::where('uuid', $uuid)->first();

            if (!$guest) {
                abort(404, "Invitation not found");
            }

            $userId = $guest->user_id;
        } else {
            $userId = Auth::id();
        }

        $this->userTemplate = TemplateModel::with('events')
            ->where('user_id', $userId)
            ->first();

        if (!$this->userTemplate) {
            $this->userTemplate = TemplateModel::with('events')
                ->whereNull('user_id')
                ->first();
        }

        $this->template = $this->userTemplate;

        $this->date = now()->addDays(90)->format('Y-m-d\TH:i');
        $this->updateCountdown();

        $this->guests = !$this->isGuestView
        ? Guest::where('user_id', Auth::id())->get()
        : [];


        $this->uuid = $uuid;
        $this->isGuestView = (bool)$uuid;

        if ($uuid) {
            $guest = Guest::where('uuid', $uuid)->first();
            
            if ($guest) {
                $this->isGuestView = true;
                $userId = $guest->user_id;
            } else {
                // If UUID is in URL but not in Database
                $this->isGuestView = false;
                abort(404, "Invitation not found");
            }
        } else {
            $this->isGuestView = false;
            $userId = Auth::id();
        }

        // Load user template based on the identified $userId
        $this->userTemplate = TemplateModel::where('user_id', $userId)->first();
        $this->defaultTemplate = TemplateModel::whereNull('user_id')->first();
        
        // Only load the guest list for the Admin view
        if (!$this->isGuestView) {
            $this->guests = Guest::where('user_id', Auth::id())->get();
        }

        $template = $this->userTemplate ?? $this->defaultTemplate;

        // Populate fields
        $this->fillTemplateFields($template);

        // Music & background files
        // $this->musicFiles = array_map(fn($file) => 'music/' . basename($file), glob(public_path('storage/music/*.mp3')));
        // $bgFiles = File::files(public_path('storage/background_images'));
        // foreach ($bgFiles as $file) $this->imageFiles[] = 'background_images/' . $file->getFilename();

        $path = storage_path('app/public/background_images');
        if (File::exists($path)) {
            $files = File::files($path);

            foreach ($files as $file) {
                $this->imageFiles[] = 'background_images/' . $file->getFilename();
            }
        }

        $pathMusic = storage_path('app/public/background_musics');
        if (File::exists($pathMusic)) {
            $files = File::files($pathMusic);

            foreach ($files as $file) {
                $this->musicFiles[] = 'background_musics/' . $file->getFilename();
            }
        }
    }

    public function store()
    {
        if (!$this->template) {
            abort(500, "Template not loaded");
        }

        $this->validate([
            'name' => 'nullable|string|max:255',
            'event_date' => 'nullable|date',
            'title_event' => 'nullable|string|max:255',
        ]);

        $this->template->events()->updateOrCreate(
            ['id' => $this->event_id], // if exists → update
            [
                'name' => $this->name,
                'event_time' => $this->event_date, // DB column
                'title' => $this->title_event,     // DB column
            ]
        );

        session()->flash('success', $this->event_id 
            ? 'Event updated successfully' 
            : 'Event created successfully'
        );

        $this->closeModal();
    }

    public function openModal()
    {
        // $this->resetInputFields();
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->event_id = null;
        $this->name = '';
        $this->event_date = '';
        $this->title_event = '';
    }

        public function edit($id)
    {
        $event = Event::findOrFail($id);

        $this->event_id = $event->id;
        $this->name = $event->name;
        $this->event_date = $event->event_time;
        $this->title_event = $event->title; 

        $this->isOpen = true;
    }

    public function update()
    {
        $this->validate([
            'name' => 'nullable|string|max:255',
            'event_date' => 'nullable|string|max:255',
            'title_event' => 'nullable|string|max:255',
        ]);

        $event = Event::findOrFail($this->event_id);
        $event->update([
            'name' => $this->name,
            'event_date' => $this->event_date,
            'title_event' => $this->title_event,
        ]);

        // $this->resetForm();
        session()->flash('success', 'Event updated successfully!');
    }

    public function delete($id)
    {
        Event::findOrFail($id)->delete();

        session()->flash('success', 'Event deleted successfully');
    }

    protected function fillTemplateFields($template)
    {
        $this->title_font_family   = $template->title_font_family ?? "'Great Vibes', cursive";
        $this->text_font_family    = $template->text_font_family ?? "'Poppins', sans-serif";
        $this->text_font_size      = $template->text_font_size ?? '16';
        $this->title_color         = $template->title_color ?? '#cca300';
        $this->text_color          = $template->text_color ?? '#997a00';
        $this->bride_name          = $template->bride_name ?? 'Bride';
        $this->groom_name          = $template->groom_name ?? 'Groom';
        $this->title               = $template->title ?? 'Wedding Invitation';
        $this->subtitle            = $template->subtitle ?? 'You are invited!';
        $this->event_time          = $template->event_time ?? 'Saturday, February 14, 2026 at 5:00 PM';
        $this->date                = $template->date ?? '';
        $this->address             = $template->address ?? 'The Premier Center Sen Sok, Phnom Penh. Cambodia';
        $this->title_invitation    = $template->title_invitation ?? 'WE ARE HONORED TO INVITE YOU';
        $this->message_invitation  = $template->message_invitation ?? 'Your Excellency, Oknha, Madam, Ladies and Gentlemen, please join us as the presiding officers and guests of honor to bestow blessings on our two weddings.';
        $this->title_thanks        = $template->title_thanks ?? 'OUR GRATITUDE AND APOLOGY';
        $this->message_thanks      = $template->message_thanks ?? 'We are extremely thankful for H.E., L.C.T., Okhna, ladies and gentlemen for your presence in the upcoming marriage of our children.
                                                                    We would like to apologize if this invitation has not been personally delivered by us.';
        $this->link_map            = $template->link_map ?? 'https://maps.app.goo.gl/9Wh42r1JEXaL6PXJ9';
        $this->background_music    = $template->background_music ?? 'music/audio1.mp3';
        $this->cover_image         = $template->cover_image ?? 'images/cover.jpg';
        $this->background_images   = $template->background_images ?? 'images/background_image.mp4';
        $this->map_photo           = $template->map_photo ?? 'images/map.jpg';
        $this->dollar_qr           = $template->dollar_qr ?? 'images/dollar_qr.jpg';
        $this->khmer_qr            = $template->khmer_qr ?? 'images/khmer_qr.jpg';
        $this->pre_wedding1        = $template->pre_wedding1 ?? 'images/pre1.jpg';
        $this->pre_wedding2        = $template->pre_wedding2 ?? 'images/pre2.jpg';
        $this->pre_wedding3        = $template->pre_wedding3 ?? 'images/pre3.jpg';
        $this->pre_wedding4        = $template->pre_wedding4 ?? 'images/pre4.jpg';
        $this->link_dollar         = $template->link_dollar ?? 'https://link.payway.com.kh/ABAPAYk5442776D';
        $this->link_khmer          = $template->link_khmer ?? 'https://link.payway.com.kh/ABAPAYk5442776D';
        $this->event               = $template->event ?? '';
        $this->option              = $template->option ?? '';
        $this->video_url            = $template->video_url ?? '';
        $this->video_public_id      = $template->video_public_id ?? '';
        $this->facebook              = $template->facebook ?? '';
        $this->instagram             = $template->instagram ?? '';
        $this->telegram              = $template->telegram ?? '';
        $this->phone                 = $template->phone ?? '';

    }

    public function updatedDate() 
    {
        $this->dispatch('updatedDate'); 
    }

    public function updateCountdown()
    {
        if (!$this->date) return;

        $now = Carbon::now();
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

    // Handle uploads
    private function handleUpload($file, $oldPath = null, $defaultPath = null)
{
    if ($file instanceof TemporaryUploadedFile) {

        // delete old file (optional but good)
        if ($oldPath) {
            Storage::disk('public')->delete($oldPath);
        }

        // ✅ store directly in public disk
        $path = $file->store('images', 'public');

        return $path; // images/filename.jpg
    }

    return $oldPath ?? $defaultPath;
}

    // Save template & generate new guest link
    public function saveTemplate()
    {
        // if ($this->cover_image instanceof TemporaryUploadedFile) {
        //     $path = $this->cover_image->store('images', 'public');

        //     // Save path to DB or variable
        //     $this->cover_image = $path;
        // }

        $template = TemplateModel::firstOrNew(['user_id' => Auth::id()]);
        $template->fill([
            'title_font_family' => $this->title_font_family,
            'text_font_family' => $this->text_font_family,
            'text_font_size' => $this->text_font_size,
            'title_color' => $this->title_color,
            'text_color' => $this->text_color,
            'bride_name' => $this->bride_name,
            'groom_name' => $this->groom_name,
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'event_time' => $this->event_time,
            'date' => $this->date,
            'address' => $this->address,
            'title_invitation' => $this->title_invitation,
            'message_invitation' => $this->message_invitation,
            'title_thanks' => $this->title_thanks,
            'message_thanks' => $this->message_thanks,
            'link_map' => $this->link_map,
            'background_music' => $this->background_music,
            'background_images' => $this->background_images,
            'cover_image' => $this->handleUpload($this->cover_image, $template?->cover_image, $this->defaultTemplate?->cover_image),
            'map_photo' => $this->handleUpload($this->map_photo, $template?->map_photo, $this->defaultTemplate?->map_photo),
            'dollar_qr' => $this->handleUpload($this->dollar_qr, $template?->dollar_qr, $this->defaultTemplate?->dollar_qr),
            'khmer_qr' => $this->handleUpload($this->khmer_qr, $template?->khmer_qr, $this->defaultTemplate?->khmer_qr),
            'pre_wedding1' => $this->handleUpload($this->pre_wedding1, $template?->pre_wedding1, $this->defaultTemplate?->pre_wedding1),
            'pre_wedding2' => $this->handleUpload($this->pre_wedding2, $template?->pre_wedding2, $this->defaultTemplate?->pre_wedding2),
            'pre_wedding3' => $this->handleUpload($this->pre_wedding3, $template?->pre_wedding3, $this->defaultTemplate?->pre_wedding3),
            'pre_wedding4' => $this->handleUpload($this->pre_wedding4, $template?->pre_wedding4, $this->defaultTemplate?->pre_wedding4),
            'video_url' => $this->video_url,
            'video_public_id' => $this->video_public_id,
            'event' => $this->event,
            'option' => $this->option,
            'link_dollar' => $this->link_dollar,
            'link_khmer' => $this->link_khmer,
            'facebook' => $this->facebook,
            'instagram' => $this->instagram,
            'telegram' => $this->telegram,
            'phone' => $this->phone,
        ]);

        // $template->uuid = $template->uuid ?? Str::uuid();

        $template->save();

        // Inside saveTemplate() after saving the template
        // Generate/update guest links
        foreach ($this->guests as $guest) {
            $guest->uuid = (string) Str::uuid(); // new link every save
            // $guest->template_id = $template->id; //make sure linked
            $guest->save();
        }

        // connect telegram
        $this->validate([
            'name' => 'required',
            'date' => 'required',
        ]);

        $template = \App\Models\Template::updateOrCreate(
            ['id' => $this->templateId],
            [
                'name' => $this->name,
                'date' => $this->date,
                'content' => $this->content,
                'user_id' => auth()->id(),
            ]
        );

        $this->templateId = $template->id;

        session()->flash('success', 'Template saved and guest links updated!');
        return redirect()->route('template');

        // Reset temporary uploads
        // $this->cover_image = $this->map_photo = $this->dollar_qr = $this->khmer_qr =
        // $this->pre_wedding1 = $this->pre_wedding2 = $this->pre_wedding3 = $this->pre_wedding4 = null;
    }

    public function render()
    {
        $layout = $this->isGuestView ? 'layouts.guest' : 'layouts.app';

        return view('livewire.user.template',[
            'events' => $this->template?->events()->latest()->get() ?? collect(),
        ])->layout($layout);
    }


    public function telegramGroups()
    {
        return $this->hasMany(TelegramGroup::class);
    }
}