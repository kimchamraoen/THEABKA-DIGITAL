<?php

namespace App\Livewire\Admin;

use App\Models\LandingSection;
use Livewire\Component;
use Livewire\WithFileUploads;

class LandingPageEditor extends Component
{
    use WithFileUploads;

    public $sections = [];
    public $editingSection = null;
    public $showEditor = false;

    public $section_key = '';
    public $title = '';
    public $subtitle = '';
    public $body = '';
    public $image_upload;
    public $video_url = '';
    public $button_text = '';
    public $button_url = '';
    public $sort_order = 0;
    public $is_visible = true;

    public function mount(): void
    {
        $this->loadSections();
    }

    public function loadSections(): void
    {
        $this->sections = LandingSection::orderBy('sort_order')->get()->toArray();
    }

    public function newSection(): void
    {
        $this->resetForm();
        $this->showEditor = true;
        $this->editingSection = null;
        $this->sort_order = count($this->sections);
    }

    public function editSection($id): void
    {
        $section = LandingSection::findOrFail($id);
        $this->editingSection = $id;
        $this->section_key = $section->section_key;
        $this->title = $section->title ?? '';
        $this->subtitle = $section->subtitle ?? '';
        $this->body = $section->body ?? '';
        $this->video_url = $section->video_url ?? '';
        $this->button_text = $section->button_text ?? '';
        $this->button_url = $section->button_url ?? '';
        $this->sort_order = $section->sort_order;
        $this->is_visible = $section->is_visible;
        $this->showEditor = true;
    }

    public function save(): void
    {
        $this->validate([
            'section_key' => 'required|string|max:50',
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'body' => 'nullable|string|max:10000',
            'video_url' => 'nullable|url|max:500',
            'button_text' => 'nullable|string|max:100',
            'button_url' => 'nullable|string|max:500',
            'sort_order' => 'integer|min:0',
        ]);

        $data = [
            'section_key' => $this->section_key,
            'title' => $this->title ?: null,
            'subtitle' => $this->subtitle ?: null,
            'body' => $this->body ?: null,
            'video_url' => $this->video_url ?: null,
            'button_text' => $this->button_text ?: null,
            'button_url' => $this->button_url ?: null,
            'sort_order' => $this->sort_order,
            'is_visible' => $this->is_visible,
        ];

        if ($this->image_upload) {
            $data['image'] = $this->image_upload->store('landing', 'public');
        }

        if ($this->editingSection) {
            LandingSection::find($this->editingSection)->update($data);
            session()->flash('message', 'Section updated!');
        } else {
            LandingSection::create($data);
            session()->flash('message', 'Section created!');
        }

        $this->showEditor = false;
        $this->loadSections();
    }

    public function deleteSection($id): void
    {
        LandingSection::destroy($id);
        $this->loadSections();
        session()->flash('message', 'Section deleted.');
    }

    public function toggleVisibility($id): void
    {
        $section = LandingSection::findOrFail($id);
        $section->update(['is_visible' => !$section->is_visible]);
        $this->loadSections();
    }

    public function moveUp($id): void
    {
        $section = LandingSection::findOrFail($id);
        $prev = LandingSection::where('sort_order', '<', $section->sort_order)->orderBy('sort_order', 'desc')->first();
        if ($prev) {
            $temp = $section->sort_order;
            $section->update(['sort_order' => $prev->sort_order]);
            $prev->update(['sort_order' => $temp]);
            $this->loadSections();
        }
    }

    public function moveDown($id): void
    {
        $section = LandingSection::findOrFail($id);
        $next = LandingSection::where('sort_order', '>', $section->sort_order)->orderBy('sort_order')->first();
        if ($next) {
            $temp = $section->sort_order;
            $section->update(['sort_order' => $next->sort_order]);
            $next->update(['sort_order' => $temp]);
            $this->loadSections();
        }
    }

    public function cancelEditor(): void
    {
        $this->showEditor = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->section_key = '';
        $this->title = '';
        $this->subtitle = '';
        $this->body = '';
        $this->image_upload = null;
        $this->video_url = '';
        $this->button_text = '';
        $this->button_url = '';
        $this->sort_order = 0;
        $this->is_visible = true;
    }

    public function render()
    {
        return view('livewire.admin.landing-page-editor');
    }
}
