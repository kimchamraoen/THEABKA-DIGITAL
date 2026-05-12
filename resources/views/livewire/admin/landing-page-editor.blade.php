<div>
    <div class="glass-card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold">{{ __('Landing Page Editor') }}</h3>
                    <p class="text-sm opacity-60">{{ __('Add custom sections, text, images & videos') }}</p>
                </div>
            </div>
            <button wire:click="newSection"
                    class="px-4 py-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 text-white text-sm font-medium hover:from-emerald-500 hover:to-teal-500 transition-all shadow-lg shadow-emerald-600/25 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                {{ __('Add Section') }}
            </button>
        </div>

        @if (session()->has('message'))
            <div class="mb-4 p-3 rounded-xl bg-emerald-500/20 border border-emerald-500/30 text-emerald-300 text-sm">{{ session('message') }}</div>
        @endif

        {{-- Sections List --}}
        @if (!$showEditor)
        <div class="space-y-3">
            @forelse ($sections as $section)
                <div class="flex items-center gap-4 p-4 rounded-xl bg-white/5 border border-white/10 group" wire:key="section-{{ $section['id'] }}">
                    <div class="flex flex-col gap-1">
                        <button wire:click="moveUp({{ $section['id'] }})" class="opacity-30 hover:opacity-100 transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 15.75 7.5-7.5 7.5 7.5" /></svg>
                        </button>
                        <button wire:click="moveDown({{ $section['id'] }})" class="opacity-30 hover:opacity-100 transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg>
                        </button>
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="text-xs px-2 py-0.5 rounded-full bg-white/10 font-mono">{{ $section['section_key'] }}</span>
                            @if (!$section['is_visible'])
                                <span class="text-xs px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-300">{{ __('Hidden') }}</span>
                            @endif
                        </div>
                        <p class="text-sm font-medium mt-1 truncate">{{ $section['title'] ?: '(No title)' }}</p>
                    </div>

                    @if ($section['image'])
                        <img src="{{ asset('storage/' . $section['image']) }}" class="w-12 h-8 rounded object-cover" />
                    @endif
                    @if ($section['video_url'])
                        <span class="text-xs px-2 py-0.5 rounded-full bg-red-500/20 text-red-300">{{ __('Video') }}</span>
                    @endif

                    <div class="flex gap-2">
                        <button wire:click="toggleVisibility({{ $section['id'] }})" class="p-1.5 rounded-lg bg-white/10 hover:bg-white/20 transition">
                            <svg class="w-4 h-4 {{ $section['is_visible'] ? 'text-emerald-400' : 'text-amber-400' }}" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                @if ($section['is_visible'])
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                @endif
                            </svg>
                        </button>
                        <button wire:click="editSection({{ $section['id'] }})" class="p-1.5 rounded-lg bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" /></svg>
                        </button>
                        <button wire:click="deleteSection({{ $section['id'] }})" wire:confirm="Delete this section?" class="p-1.5 rounded-lg bg-red-500/20 hover:bg-red-500/30 text-red-300 transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                        </button>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center rounded-xl bg-white/5 border border-white/10">
                    <p class="opacity-40 text-sm mb-3">{{ __('No custom sections added yet') }}</p>
                    <button wire:click="newSection" class="text-emerald-400 text-sm hover:text-emerald-300">{{ __('Add your first section') }}</button>
                </div>
            @endforelse
        </div>
        @endif

        {{-- Section Editor --}}
        @if ($showEditor)
        <div class="space-y-4 p-4 rounded-xl bg-white/5 border border-white/10">
            <h4 class="font-bold text-sm">{{ $editingSection ? 'Edit Section' : 'New Section' }}</h4>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium mb-1 opacity-60">{{ __('Section Key') }}</label>
                    <input wire:model="section_key" type="text" placeholder="e.g. features, about, cta"
                           class="w-full px-3 py-2 rounded-lg bg-white/10 border border-white/20 text-sm text-inherit outline-none" />
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1 opacity-60">{{ __('Sort Order') }}</label>
                    <input wire:model="sort_order" type="number" min="0"
                           class="w-full px-3 py-2 rounded-lg bg-white/10 border border-white/20 text-sm text-inherit outline-none" />
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium mb-1 opacity-60">{{ __('Title') }}</label>
                <input wire:model="title" type="text" placeholder="Section heading"
                       class="w-full px-3 py-2 rounded-lg bg-white/10 border border-white/20 text-sm text-inherit outline-none" />
            </div>

            <div>
                <label class="block text-xs font-medium mb-1 opacity-60">{{ __('Subtitle') }}</label>
                <input wire:model="subtitle" type="text" placeholder="Section subtitle"
                       class="w-full px-3 py-2 rounded-lg bg-white/10 border border-white/20 text-sm text-inherit outline-none" />
            </div>

            <div>
                <label class="block text-xs font-medium mb-1 opacity-60">{{ __('Body Content') }}</label>
                <textarea wire:model="body" rows="5" placeholder="Rich text content, HTML supported"
                          class="w-full px-3 py-2 rounded-lg bg-white/10 border border-white/20 text-sm text-inherit outline-none font-mono"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium mb-1 opacity-60">{{ __('Image') }}</label>
                    <input type="file" wire:model="image_upload" accept="image/*"
                           class="text-xs file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-emerald-600 file:text-white file:text-xs file:cursor-pointer" />
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1 opacity-60">{{ __('YouTube / Video URL') }}</label>
                    <input wire:model="video_url" type="url" placeholder="https://youtube.com/embed/..."
                           class="w-full px-3 py-2 rounded-lg bg-white/10 border border-white/20 text-sm text-inherit outline-none" />
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium mb-1 opacity-60">{{ __('Button Text') }}</label>
                    <input wire:model="button_text" type="text" placeholder="Learn More"
                           class="w-full px-3 py-2 rounded-lg bg-white/10 border border-white/20 text-sm text-inherit outline-none" />
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1 opacity-60">{{ __('Button URL') }}</label>
                    <input wire:model="button_url" type="text" placeholder="/register or #section"
                           class="w-full px-3 py-2 rounded-lg bg-white/10 border border-white/20 text-sm text-inherit outline-none" />
                </div>
            </div>

            <div class="flex items-center gap-3">
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" wire:model="is_visible" class="sr-only peer">
                    <div class="w-9 h-5 bg-white/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-white after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-600"></div>
                </label>
                <span class="text-sm">{{ __('Visible') }}</span>
            </div>

            @error('section_key') <p class="text-red-400 text-xs">{{ $message }}</p> @enderror

            <div class="flex gap-3 pt-2">
                <button wire:click="save" class="px-5 py-2 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-500 transition">{{ __('Save Section') }}</button>
                <button wire:click="cancelEditor" class="px-5 py-2 rounded-xl bg-white/10 border border-white/20 text-sm font-medium hover:bg-white/20 transition">{{ __('Cancel') }}</button>
            </div>
        </div>
        @endif
    </div>
</div>
