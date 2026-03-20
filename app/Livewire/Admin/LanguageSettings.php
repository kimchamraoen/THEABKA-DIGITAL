<?php

namespace App\Livewire\Admin;

use App\Models\Language;
use Illuminate\Support\Facades\File;
use Livewire\Component;

class LanguageSettings extends Component
{
    public array $languages = [];

    public bool $showModal = false;
    public bool $isEditing = false;
    public ?int $editingId = null;

    public string $name = '';
    public string $locale = '';
    public string $flag = '';
    public string $font_type = 'system';
    public ?string $font_value = null;
    public bool $is_active = true;

    public function mount(): void
    {
        Language::ensureDefaults();
        $this->refreshLanguages();
    }

    public function refreshLanguages(): void
    {
        $this->languages = Language::query()
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get()
            ->toArray();
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showModal = true;
        $this->isEditing = false;
    }

    public function openEditModal(int $id): void
    {
        $language = Language::findOrFail($id);

        $this->editingId = $language->id;
        $this->name = $language->name;
        $this->locale = $language->locale;
        $this->flag = $language->flag ?? '';
        $this->font_type = $language->font_type ?? 'system';
        $this->font_value = $language->font_value;
        $this->is_active = (bool) $language->is_active;

        $this->isEditing = true;
        $this->showModal = true;
    }

    public function save(): void
    {
        $localeRules = $this->isEditing
            ? 'required|string|max:20|regex:/^[a-z]{2,10}(-[A-Z]{2,10})?$/|unique:languages,locale,' . $this->editingId
            : 'required|string|max:20|regex:/^[a-z]{2,10}(-[A-Z]{2,10})?$/|unique:languages,locale';

        $this->validate([
            'name' => 'required|string|max:100',
            'locale' => $localeRules,
            'flag' => 'nullable|string|max:12',
            'font_type' => 'required|in:system,google,custom',
            'font_value' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $locale = strtolower(trim($this->locale));

        if ($this->font_type === 'system') {
            $this->font_value = null;
        }

        $payload = [
            'name' => trim($this->name),
            'locale' => $locale,
            'flag' => trim($this->flag) ?: null,
            'font_type' => $this->font_type,
            'font_value' => $this->font_value ? trim($this->font_value) : null,
            'is_active' => $this->is_active,
        ];

        if ($this->isEditing && $this->editingId) {
            $language = Language::findOrFail($this->editingId);
            $oldLocale = $language->locale;
            $language->update($payload);

            if ($oldLocale !== $locale) {
                $this->ensureLocaleFiles($locale);
            }

            session()->flash('message', 'Language updated successfully.');
        } else {
            Language::create(array_merge($payload, ['is_default' => false]));
            $this->ensureLocaleFiles($locale);
            session()->flash('message', 'Language created successfully.');
        }

        $this->showModal = false;
        $this->resetForm();
        $this->refreshLanguages();
    }

    public function delete(int $id): void
    {
        $language = Language::findOrFail($id);

        if ($language->is_default) {
            $this->addError('delete', 'Default language cannot be deleted.');
            return;
        }

        $language->delete();
        $this->refreshLanguages();

        session()->flash('message', 'Language deleted successfully.');
    }

    protected function ensureLocaleFiles(string $locale): void
    {
        $dir = lang_path($locale);
        $phpFile = $dir . '/app.php';
        $jsonFile = lang_path($locale . '.json');

        if (! File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        if (! File::exists($phpFile)) {
            File::put($phpFile, "<?php\n\nreturn [];\n");
        }

        if (! File::exists($jsonFile)) {
            File::put($jsonFile, json_encode(new \stdClass(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }
    }

    protected function resetForm(): void
    {
        $this->resetErrorBag();
        $this->editingId = null;
        $this->name = '';
        $this->locale = '';
        $this->flag = '';
        $this->font_type = 'system';
        $this->font_value = null;
        $this->is_active = true;
    }

    public function render()
    {
        return view('livewire.admin.language-settings');
    }
}
