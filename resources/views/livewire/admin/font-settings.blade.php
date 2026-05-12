<div class="glass-card p-6 rounded-2xl" x-data="fontManager()">
    <h3 class="text-lg font-semibold mb-6 flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12" />
        </svg>
        {{ __('Font Settings') }}
    </h3>

    @if (session()->has('message'))
        <div class="mb-4 p-3 rounded-xl bg-emerald-500/20 border border-emerald-500/30 text-emerald-300 text-sm">
            {{ session('message') }}
        </div>
    @endif

    @if (empty($languages))
        <div class="rounded-xl border border-amber-500/30 bg-amber-500/10 px-4 py-3 text-sm text-amber-200 mb-4">
            No active languages found. Go to <strong>Settings -> Languages</strong> and enable at least one language.
        </div>
    @endif

    <div class="space-y-5">
        @foreach ($languages as $language)
            @php
                $locale = $language['locale'];
                $fontType = $fontTypeByLocale[$locale] ?? 'system';
                $fontValue = $fontValueByLocale[$locale] ?? '';
                $sample = $previewSamples[$locale] ?? 'Hello World 123';
                $customUrl = $this->getCustomFontUrl($locale);
                $suggestions = $this->googleFontSuggestions($locale);
            @endphp
            <div class="rounded-2xl border border-white/10 bg-white/[0.03] p-4"
                 x-data="fontLocaleCard({
                    locale: @js($locale),
                    initialType: @js($fontType),
                    initialValue: @js($fontValue),
                    sample: @js($sample),
                    customUrl: @js($customUrl),
                 })">
                <div class="flex items-center justify-between gap-3 mb-4">
                    <div class="flex items-center gap-2">
                        <span class="text-lg">{{ $language['flag'] ?: '🏳️' }}</span>
                        <div>
                            <p class="font-semibold">{{ $language['name'] }}</p>
                            <p class="text-xs opacity-50 font-mono">{{ $locale }}</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <div class="space-y-3">
                        <label class="block text-sm font-medium opacity-80">{{ __('Font Source') }}</label>

                        <div class="flex flex-wrap gap-2">
                            <label class="px-3 py-2 rounded-xl border border-white/20 bg-white/5 text-xs cursor-pointer" :class="type === 'system' ? 'border-blue-400/60 bg-blue-500/20' : ''">
                                <input type="radio" class="hidden" value="system" x-model="type" wire:model.live="fontTypeByLocale.{{ $locale }}">
                                {{ __('System Default') }}
                            </label>
                            <label class="px-3 py-2 rounded-xl border border-white/20 bg-white/5 text-xs cursor-pointer" :class="type === 'google' ? 'border-blue-400/60 bg-blue-500/20' : ''">
                                <input type="radio" class="hidden" value="google" x-model="type" wire:model.live="fontTypeByLocale.{{ $locale }}">
                                {{ __('Google Fonts') }}
                            </label>
                            <label class="px-3 py-2 rounded-xl border border-white/20 bg-white/5 text-xs cursor-pointer" :class="type === 'custom' ? 'border-blue-400/60 bg-blue-500/20' : ''">
                                <input type="radio" class="hidden" value="custom" x-model="type" wire:model.live="fontTypeByLocale.{{ $locale }}">
                                {{ __('Custom Upload') }}
                            </label>
                        </div>

                        <div x-show="type === 'google'" x-cloak class="relative">
                            <label class="block text-xs opacity-60 mb-1">{{ __('Google Font Name') }}</label>
                            <input
                                type="text"
                                x-model="value"
                                @input="syncGoogle"
                                wire:model.live.debounce.250ms="fontValueByLocale.{{ $locale }}"
                                placeholder="e.g. Noto Sans Khmer"
                                class="w-full px-4 py-2.5 rounded-xl bg-white/10 border border-white/20 focus:border-blue-400/50 outline-none"
                            >

                            @if (!empty($fontValue) && count($suggestions) > 0)
                                <div class="mt-2 rounded-xl border border-white/15 bg-slate-900/95 max-h-48 overflow-y-auto">
                                    @foreach ($suggestions as $font)
                                        <button
                                            type="button"
                                            wire:click="$set('fontValueByLocale.{{ $locale }}', '{{ addslashes($font['family']) }}')"
                                            @click="value = @js($font['family']); syncGoogle()"
                                            class="w-full text-left px-3 py-2 border-b border-white/5 last:border-0 hover:bg-white/10 transition flex items-center justify-between"
                                        >
                                            <span class="text-sm">{{ $font['family'] }}</span>
                                            <span class="text-[11px] opacity-50">{{ $font['category'] ?? 'sans-serif' }}</span>
                                        </button>
                                    @endforeach
                                </div>
                            @elseif (!empty($fontValue))
                                <p class="text-[11px] opacity-50 mt-2">No matching fonts found.</p>
                            @endif
                        </div>

                        <div x-show="type === 'custom'" x-cloak>
                            <label class="block text-xs opacity-60 mb-1">{{ __('Upload Custom Font') }}</label>
                            <input
                                type="file"
                                wire:model="fontUploads.{{ $locale }}"
                                accept=".ttf,.woff,.woff2"
                                class="w-full text-sm file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-white/10 file:text-white hover:file:bg-white/20"
                            >
                            <p class="text-[11px] opacity-50 mt-1">{{ __('Accepted: .ttf, .woff, .woff2') }}</p>
                            @if (!empty($fontValue))
                                <p class="text-xs opacity-60 mt-1">{{ __('Current file:') }} <span class="font-mono">{{ $fontValue }}</span></p>
                            @endif
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium opacity-80 mb-2">{{ __('Live Preview') }}</label>
                        <div class="rounded-xl border border-white/15 bg-white/5 p-4 min-h-[120px] flex items-center"
                             :style="previewStyle()">
                            <p class="text-lg leading-relaxed" x-text="sample"></p>
                        </div>
                    </div>
                </div>

                @error("fontTypeByLocale.$locale") <p class="text-xs text-red-300 mt-2">{{ $message }}</p> @enderror
                @error("fontValueByLocale.$locale") <p class="text-xs text-red-300 mt-2">{{ $message }}</p> @enderror
                @error("fontUploads.$locale") <p class="text-xs text-red-300 mt-2">{{ $message }}</p> @enderror
            </div>
        @endforeach
    </div>

    <div class="mt-5">
        <button
            wire:click="saveFont"
            class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-medium transition-all duration-200 shadow-lg shadow-blue-600/25"
        >
            {{ __('Save Font Settings') }}
        </button>
    </div>

    <script>
        function fontManager() {
            return {};
        }

        function fontLocaleCard(cfg) {
            return {
                locale: cfg.locale,
                type: cfg.initialType,
                value: cfg.initialValue,
                sample: cfg.sample,
                customUrl: cfg.customUrl,
                googleLoaded: false,
                init() {
                    if (this.type === 'google' && this.value) {
                        this.loadGoogleFont(this.value);
                    }
                },
                syncGoogle() {
                    if (this.type === 'google' && this.value) {
                        this.loadGoogleFont(this.value);
                    }
                },
                loadGoogleFont(name) {
                    const family = (name || '').trim();
                    if (!family) return;

                    const id = `gf-${this.locale}-${family.toLowerCase().replace(/[^a-z0-9]+/g, '-')}`;
                    if (document.getElementById(id)) return;

                    const link = document.createElement('link');
                    link.id = id;
                    link.rel = 'stylesheet';
                    link.href = `https://fonts.googleapis.com/css2?family=${encodeURIComponent(family)}&display=swap`;
                    document.head.appendChild(link);
                    this.googleLoaded = true;
                },
                previewStyle() {
                    if (this.type === 'google' && this.value) {
                        return `font-family: '${this.value}', ui-sans-serif, system-ui, sans-serif;`;
                    }

                    if (this.type === 'custom' && this.customUrl) {
                        const face = `font-${this.locale}`;
                        if (!document.getElementById(face)) {
                            const st = document.createElement('style');
                            st.id = face;
                            st.textContent = `@font-face { font-family: '${face}'; src: url('${this.customUrl}') format('woff2'), url('${this.customUrl}') format('woff'), url('${this.customUrl}') format('truetype'); font-weight: normal; font-style: normal; }`;
                            document.head.appendChild(st);
                        }

                        return `font-family: '${face}', ui-sans-serif, system-ui, sans-serif;`;
                    }

                    return 'font-family: ui-sans-serif, system-ui, sans-serif;';
                },
            };
        }
    </script>
</div>
