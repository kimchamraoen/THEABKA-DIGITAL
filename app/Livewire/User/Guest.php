<?php

namespace App\Livewire\User;

use App\Models\Guest as ModelsGuest;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Exports\GuestsExport;
use Maatwebsite\Excel\Facades\Excel;
use Livewire\WithFileUploads;
use App\Imports\GuestsImport;
use Illuminate\Support\Str;

class Guest extends Component
{
    use WithFileUploads;

    public $guest_name, $phone, $Greeting, $gift_money, $gift, $guest_id, $file, $note;
    public $statue = '';
    public $group = '';
    public $isModalOpen = false, $uuid;
    public $guests;
    public $invite_link;
    public $showDeleteModal = false, $deleteGuestId = null, $deleteGuestName = '';

    protected $rules = [
        'guest_name' => 'required|string|max:255',
        'group' => 'required',
        'statue' => 'required',
        'gift' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'gift_money' => 'nullable|string|max:255',

    ];

    public function mount()
    {
        // Load all guests from DB
        $this->guests = ModelsGuest::where('user_id', Auth::id())->get();
    }

    public function confirmDelete($id)
    {
        $guest = ModelsGuest::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $this->deleteGuestId = $id;
        $this->deleteGuestName = $guest->guest_name;
        $this->showDeleteModal = true;
    }

    public function deleteGuest()
    {
        ModelsGuest::where('id', $this->deleteGuestId)->where('user_id', Auth::id())->delete();
        session()->flash('Success', 'Guest deleted successfully');
        $this->reset(['showDeleteModal', 'deleteGuestId', 'deleteGuestName']);
        return redirect(request()->header('Referer'));
    }

    // edit modal
    public function openModal()
    {
        $this->resetForm();
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
    }

    // export file
    public function exportExcel()
    {
        return Excel::download(new GuestsExport, 'my-guests.xlsx');
    }

    public function updatedFile()
    {
        $this->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            Excel::import(new GuestsImport, $this->file->getRealPath());
            
            // Clear the file and show success
            $this->reset('file');
            session()->flash('message', 'Import successful!');
        } catch (\Exception $e) {
            session()->flash('error', 'Error during import: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $guest = ModelsGuest::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        // $guest = ModelsGuest::findOrFail($id);
        $this->guest_id = $id;
        $this->guest_name = $guest->guest_name;
        $this->phone = $guest->phone;
        $this->group = $guest->group;
        $this->statue = $guest->statue;
        $this->Greeting = $guest->Greeting;
        $this->gift = $guest->gift;
        $this->gift_money = $guest->gift_money;
        $this->note = $guest->note;

        $this->isModalOpen = true;
    }

    public function update()
    {
        $this->validate([
            'guest_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'group' => 'required|string|max:100',
            'statue' => 'required|string|max:50',
            'Greeting' => 'nullable|string',
            'gift' => 'nullable|string|max:255',
            'gift_money' => 'nullable|string|min:0',
            'note' => 'nullable|string|max:255',
        ]);

        $guest = Guest::findOrFail($this->guest_id);

        if ($guest->payment_confirmed ?? false) {
            session()->flash('error', 'Cannot edit confirmed payment');
            return;
        }

        $guest->update([
            'guest_name' => $this->guest_name,
            'phone' => $this->phone,
            'group' => $this->group,
            'statue' => $this->statue,
            'Greeting' => $this->Greeting,
            'gift' => $this->gift,
            'gift_money' => $this->gift_money,
            'note' => $this->note,
        ]);

        session()->flash('success', 'Guest updated successfully');
    }

    public function save()
    {
        $this->validate();

        // Find the guest if it exists, or start a new one
        $guest = \App\Models\Guest::find($this->guest_id) ?? new \App\Models\Guest();

        $guest->user_id = Auth::id();
        $guest->guest_name = $this->guest_name;
        $guest->phone = $this->phone;
        $guest->group = $this->group;
        $guest->statue = $this->statue;
        $guest->Greeting = $this->Greeting;
        $guest->gift = $this->gift;
        $guest->gift_money = $this->gift_money;
        $guest->note = $this->note;
        // $guest->gift = $this->gift ? $this->gift->store('gifts') : null;

        // IMPORTANT: Only create a UUID if the guest doesn't have one yet
        if (empty($guest->uuid)) {
            $guest->uuid = (string) \Illuminate\Support\Str::uuid();
        }

        $guest->save();
        session()->flash('success', 'Guest saved successfully');
        return redirect(request()->header('Referer'));

        $this->closeModal();
        $this->resetForm();
    }

    public function delete($id)
    {
        ModelsGuest::where('id', $id)->where('user_id', Auth::id())->delete();
    }

    private function resetForm()
    {
        $this->reset(['guest_name', 'phone', 'group', 'Greeting', 'gift_money', 'gift', 'statue', 'guest_id', 'note']);
    }

    public function render()
    {
        $guests = ModelsGuest::query()
            // Ensure only the logged-in user's data is fetched
            ->where('user_id', Auth::id()) 
            ->when($this->group, fn ($q) =>
                $q->where('group', $this->group)
            )
            ->when($this->statue, fn ($q) =>
                $q->where('statue', $this->statue)
            )
            ->latest()
            ->get();


        return view('livewire.user.guest', [
            'guests' => ModelsGuest::latest()
                ->where('user_id', Auth::id())
                ->get()
        ])->layout('layouts.app');
    }

    public function showLinks()
    {
        $guests = auth()->user()->guests; // Fetches all guests for this user
        return view('pages.guest-links', compact('guests'));
    }
}
