<div class="glass-card timezone-settings-card p-6 rounded-2xl">
    <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
        </svg>
        {{ __('Timezone') }} {{ __('Settings') }}
    </h3>

    @if (session()->has('timezone-message'))
        <div class="mb-4 p-3 rounded-xl bg-emerald-500/20 border border-emerald-500/30 text-emerald-300 text-sm">
            {{ session('timezone-message') }}
        </div>
    @endif

    {{-- {{ __('Current Time Preview') }} --}}
    <div class="mb-6 p-4 rounded-xl timezone-preview-card" x-data="{
        currentTime: '',
        currentDate: '',
        tick() {
            const now = new Date();
            this.currentTime = now.toLocaleTimeString('en-US', { timeZone: $wire.timezone, hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true });
            this.currentDate = now.toLocaleDateString('en-US', { timeZone: $wire.timezone, weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' });
            setTimeout(() => this.tick(), 1000);
        }
    }" x-init="tick()">
        <label class="block text-sm font-medium mb-2 opacity-80">Current Time Preview</label>
        <div class="flex items-center gap-3">
            <svg class="w-5 h-5 text-blue-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            <div>
                <span class="text-lg font-semibold" x-text="currentTime"></span>
                <span class="text-sm opacity-60 ml-2" x-text="currentDate"></span>
            </div>
        </div>
    </div>

    {{-- Timezone Search --}}
    <div class="mb-4">
        <label class="block text-sm font-medium mb-1 opacity-80">{{ __('Search Timezone') }}</label>
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search... (e.g. America, Tokyo, UTC)"
               class="w-full px-4 py-2.5 rounded-xl bg-white/10 backdrop-blur-xl border border-white/20
                      focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 outline-none transition-all text-sm" />
    </div>

    {{-- Timezone Select --}}
    <div class="mb-6">
        <label class="block text-sm font-medium mb-1 opacity-80">Timezone</label>
        <select wire:model.live="timezone"
                class="w-full px-4 py-2.5 rounded-xl bg-white/10 backdrop-blur-xl border border-white/20
                       focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 outline-none transition-all text-sm">
            @foreach ($this->filteredTimezones as $tz)
                <option value="{{ $tz }}">{{ str_replace('_', ' ', $tz) }} (UTC{{ now()->setTimezone($tz)->format('P') }})</option>
            @endforeach
        </select>
        @error('timezone') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
        <p class="text-xs opacity-40 mt-1">This timezone is used for the clock in the header and for all date/time displays across the app.</p>
    </div>

    {{-- Common Timezones Quick Pick --}}
    <div class="mb-6">
        <label class="block text-sm font-medium mb-2 opacity-80">{{ __('Quick Select') }}</label>
        <div class="flex flex-wrap gap-2">
            @foreach ([
                'UTC' => 'UTC',
                'America/New_York' => 'New York',
                'America/Los_Angeles' => 'Los Angeles',
                'Europe/London' => 'London',
                'Europe/Paris' => 'Paris',
                'Asia/Tokyo' => 'Tokyo',
                'Asia/Shanghai' => 'Shanghai',
                'Asia/Phnom_Penh' => 'Phnom Penh',
                'Asia/Bangkok' => 'Bangkok',
                'Australia/Sydney' => 'Sydney',
            ] as $tz => $label)
                <button type="button" wire:click="$set('timezone', '{{ $tz }}')"
                    class="timezone-quick-btn {{ $timezone === $tz ? 'is-active' : '' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- Save Button --}}
    <div class="flex justify-end">
        <button wire:click="save" wire:loading.attr="disabled"
                class="px-6 py-2.5 rounded-xl bg-blue-500/80 hover:bg-blue-500 text-white font-medium text-sm
                       transition-all duration-200 disabled:opacity-50 flex items-center gap-2">
            <svg wire:loading wire:target="save" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            {{ __('Save Timezone Settings') }}
        </button>
    </div>
</div>
