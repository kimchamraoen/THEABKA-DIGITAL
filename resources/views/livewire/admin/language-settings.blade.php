<div class="glass-card p-6 rounded-2xl" x-data="{ showModal: @entangle('showModal') }">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 21a8.96 8.96 0 0 1-4.526-1.227L3 18l1.773-2.974A8.96 8.96 0 0 1 3.75 10.5c0-4.97 4.03-9 9-9 4.97 0 9 4.03 9 9 0 4.97-4.03 9-9 9h-2.25Z" />
            </svg>
            {{ __('Languages') }}
        </h3>

        <button
            type="button"
            wire:click="openCreateModal"
            class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-sm font-medium transition-all duration-200"
        >
            {{ __('Add Language') }}
        </button>
    </div>

    @if (session()->has('message'))
        <div class="mb-4 p-3 rounded-xl bg-emerald-500/20 border border-emerald-500/30 text-emerald-300 text-sm">
            {{ session('message') }}
        </div>
    @endif

    @error('delete')
        <div class="mb-4 p-3 rounded-xl bg-red-500/20 border border-red-500/30 text-red-300 text-sm">
            {{ $message }}
        </div>
    @enderror

    <div class="overflow-x-auto rounded-xl border border-white/10">
        <table class="w-full text-sm">
            <thead class="bg-white/5 border-b border-white/10">
                <tr class="text-left opacity-70 uppercase text-xs tracking-wide">
                    <th class="px-4 py-3">{{ __('Flag') }}</th>
                    <th class="px-4 py-3">{{ __('Name') }}</th>
                    <th class="px-4 py-3">{{ __('Locale') }}</th>
                    <th class="px-4 py-3">{{ __('Font') }}</th>
                    <th class="px-4 py-3">{{ __('Status') }}</th>
                    <th class="px-4 py-3 text-right">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($languages as $language)
                    <tr class="border-b border-white/5 last:border-0 hover:bg-white/[0.03]">
                        <td class="px-4 py-3 text-lg">{{ $language['flag'] ?: '🏳️' }}</td>
                        <td class="px-4 py-3">
                            {{ $language['name'] }}
                            @if ($language['is_default'])
                                <span class="ml-2 text-[10px] px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30">DEFAULT</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 font-mono text-xs">{{ $language['locale'] }}</td>
                        <td class="px-4 py-3 text-xs opacity-80">{{ ucfirst($language['font_type'] ?? 'system') }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs border {{ $language['is_active'] ? 'bg-emerald-500/20 border-emerald-500/30 text-emerald-300' : 'bg-white/10 border-white/20 text-white/70' }}">
                                {{ $language['is_active'] ? __('Active') : __('Inactive') }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <button type="button" wire:click="openEditModal({{ $language['id'] }})" class="px-3 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 border border-white/15 text-xs">
                                    {{ __('Edit') }}
                                </button>
                                @if (!$language['is_default'])
                                    <button
                                        type="button"
                                        wire:click="delete({{ $language['id'] }})"
                                        wire:confirm="Delete this language?"
                                        class="px-3 py-1.5 rounded-lg bg-red-500/15 hover:bg-red-500/25 border border-red-500/25 text-red-300 text-xs"
                                    >
                                        {{ __('Delete') }}
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-sm opacity-50">{{ __('No languages found.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div x-show="showModal" x-cloak class="fixed inset-0 z-[70] flex items-center justify-center p-4" style="display:none;">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="showModal = false"></div>

        <div class="relative w-full max-w-xl rounded-2xl border border-white/10 bg-slate-900/95 p-6 shadow-2xl">
            <h4 class="text-lg font-semibold mb-4" x-text="$wire.isEditing ? '{{ __('Edit Language') }}' : '{{ __('Add Language') }}'"></h4>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm mb-1 opacity-80">{{ __('Language Name') }}</label>
                    <input wire:model.defer="name" type="text" class="w-full px-4 py-2.5 rounded-xl bg-white/10 border border-white/20 focus:border-blue-400/50 outline-none" placeholder="French">
                    @error('name') <p class="text-xs text-red-300 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm mb-1 opacity-80">{{ __('Locale Code') }}</label>
                    <input wire:model.defer="locale" type="text" class="w-full px-4 py-2.5 rounded-xl bg-white/10 border border-white/20 focus:border-blue-400/50 outline-none" placeholder="fr">
                    @error('locale') <p class="text-xs text-red-300 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm mb-1 opacity-80">{{ __('Flag Emoji') }}</label>
                    <input wire:model.defer="flag" type="text" class="w-full px-4 py-2.5 rounded-xl bg-white/10 border border-white/20 focus:border-blue-400/50 outline-none" placeholder="🇫🇷">
                    @error('flag') <p class="text-xs text-red-300 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm mb-1 opacity-80">{{ __('Font Type') }}</label>
                    <select wire:model.live="font_type" class="w-full px-4 py-2.5 rounded-xl bg-white/10 border border-white/20 focus:border-blue-400/50 outline-none">
                        <option value="system">{{ __('System Default') }}</option>
                        <option value="google">{{ __('Google Fonts') }}</option>
                        <option value="custom">{{ __('Custom Upload') }}</option>
                    </select>
                    @error('font_type') <p class="text-xs text-red-300 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-2" x-show="$wire.font_type !== 'system'" x-cloak>
                    <label class="block text-sm mb-1 opacity-80">{{ __('Font Value') }}</label>
                    <input wire:model.defer="font_value" type="text" class="w-full px-4 py-2.5 rounded-xl bg-white/10 border border-white/20 focus:border-blue-400/50 outline-none" placeholder="Noto Sans Khmer or custom file name">
                    @error('font_value') <p class="text-xs text-red-300 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-2 flex items-center gap-2">
                    <input id="is_active" wire:model="is_active" type="checkbox" class="rounded border-white/20 bg-white/10 text-emerald-500 focus:ring-emerald-400/40">
                    <label for="is_active" class="text-sm opacity-80">{{ __('Active') }}</label>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-2">
                <button type="button" @click="showModal = false" class="px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-sm">
                    {{ __('Cancel') }}
                </button>
                <button type="button" wire:click="save" class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-sm font-medium">
                    {{ __('Save') }}
                </button>
            </div>
        </div>
    </div>
</div>
