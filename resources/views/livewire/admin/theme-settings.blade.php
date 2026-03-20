<div class="glass-card p-6 rounded-2xl">
    <h3 class="text-lg font-semibold mb-2 flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
        </svg>
        {{ __('Default') }} {{ __('Appearance') }}
    </h3>

    <p class="text-sm opacity-60 mb-6">{{ __('Set default theme mode, default glass style, and tune active glass rendering details.') }}</p>

    @if (session()->has('theme-message'))
        <div class="mb-4 p-3 rounded-xl bg-emerald-500/20 border border-emerald-500/30 text-emerald-300 text-sm flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
            {{ session('theme-message') }}
        </div>
    @endif

    {{-- Unsaved Changes Indicator --}}
    @if ($hasUnsavedChanges)
        <div class="mb-4 p-3 rounded-xl bg-amber-500/15 border border-amber-500/25 text-amber-300 text-sm flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0 animate-pulse" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" /></svg>
                <span>{{ __('You have unsaved changes') }} &mdash; preview is live</span>
            </div>
            <button wire:click="revert"
                    class="px-3 py-1.5 rounded-lg text-xs font-medium bg-amber-500/20 border border-amber-500/30 hover:bg-amber-500/30 transition-colors">
                {{ __('Revert All') }}
            </button>
        </div>
    @endif

    {{-- Theme Mode --}}
    <div class="flex gap-4 mb-6">
        <button
            wire:click="$set('defaultTheme', 'dark')"
            class="flex-1 p-4 rounded-xl border-2 transition-all duration-200
                   {{ $defaultTheme === 'dark'
                       ? 'border-blue-500 bg-blue-500/20 shadow-lg shadow-blue-500/20'
                       : 'border-white/10 bg-white/5 hover:border-white/30' }}"
        >
            <div class="w-full h-20 rounded-lg bg-gradient-to-br from-blue-950 via-blue-900 to-slate-900 mb-3 flex items-center justify-center">
                <svg class="w-8 h-8 text-blue-300" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                </svg>
            </div>
            <span class="block text-sm font-medium text-center">{{ __('Dark Mode') }}</span>
        </button>

        <button
            wire:click="$set('defaultTheme', 'light')"
            class="flex-1 p-4 rounded-xl border-2 transition-all duration-200
                   {{ $defaultTheme === 'light'
                       ? 'border-blue-500 bg-blue-500/20 shadow-lg shadow-blue-500/20'
                       : 'border-white/10 bg-white/5 hover:border-white/30' }}"
        >
            <div class="w-full h-20 rounded-lg bg-gradient-to-br from-gray-100 via-blue-50 to-gray-200 mb-3 flex items-center justify-center">
                <svg class="w-8 h-8 text-amber-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                </svg>
            </div>
            <span class="block text-sm font-medium text-center">{{ __('Light Mode') }}</span>
        </button>
    </div>

    {{-- Glass Style Selector --}}
    <h4 class="text-sm font-semibold uppercase tracking-wider opacity-70 mb-3">{{ __('Default Glass Style') }}</h4>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-3 mb-6">
        <button
            wire:click="$set('defaultGlassStyle', 'liquid')"
            x-on:click="$dispatch('glass-style-changed', { glassStyle: 'liquid' })"
            class="p-4 rounded-xl border-2 text-left transition-all duration-200
                   {{ $defaultGlassStyle === 'liquid'
                       ? 'border-cyan-400 bg-cyan-500/15 shadow-lg shadow-cyan-500/20'
                       : 'border-white/10 bg-white/5 hover:border-white/30' }}"
        >
            <div class="text-sm font-semibold mb-1">{{ __('Liquid') }}</div>
            <p class="text-xs opacity-60">{{ __('Distortion + glossy shine') }}</p>
        </button>

        <button
            wire:click="$set('defaultGlassStyle', 'card')"
            x-on:click="$dispatch('glass-style-changed', { glassStyle: 'card' })"
            class="p-4 rounded-xl border-2 text-left transition-all duration-200
                   {{ $defaultGlassStyle === 'card'
                       ? 'border-violet-400 bg-violet-500/15 shadow-lg shadow-violet-500/20'
                       : 'border-white/10 bg-white/5 hover:border-white/30' }}"
        >
            <div class="text-sm font-semibold mb-1">{{ __('Card') }}</div>
            <p class="text-xs opacity-60">{{ __('Layered gradient panel look') }}</p>
        </button>

        <button
            wire:click="$set('defaultGlassStyle', 'crystal')"
            x-on:click="$dispatch('glass-style-changed', { glassStyle: 'crystal' })"
            class="p-4 rounded-xl border-2 text-left transition-all duration-200
                   {{ $defaultGlassStyle === 'crystal'
                       ? 'border-emerald-400 bg-emerald-500/15 shadow-lg shadow-emerald-500/20'
                       : 'border-white/10 bg-white/5 hover:border-white/30' }}"
        >
            <div class="text-sm font-semibold mb-1">{{ __('Crystal') }}</div>
            <p class="text-xs opacity-60">{{ __('Minimal blur, ultra-transparent') }}</p>
        </button>

        <button
            wire:click="$set('defaultGlassStyle', 'frosted')"
            x-on:click="$dispatch('glass-style-changed', { glassStyle: 'frosted' })"
            class="p-4 rounded-xl border-2 text-left transition-all duration-200
                   {{ $defaultGlassStyle === 'frosted'
                       ? 'border-sky-400 bg-sky-500/15 shadow-lg shadow-sky-500/20'
                       : 'border-white/10 bg-white/5 hover:border-white/30' }}"
        >
            <div class="text-sm font-semibold mb-1">{{ __('Frosted') }}</div>
            <p class="text-xs opacity-60">{{ __('Heavy blur, matte opaque panel') }}</p>
        </button>

        <button
            wire:click="$set('defaultGlassStyle', 'glass3d')"
            x-on:click="$dispatch('glass-style-changed', { glassStyle: 'glass3d' })"
            class="p-4 rounded-xl border-2 text-left transition-all duration-200
                   {{ $defaultGlassStyle === 'glass3d'
                       ? 'border-cyan-300 bg-cyan-500/15 shadow-lg shadow-cyan-500/20'
                       : 'border-white/10 bg-white/5 hover:border-white/30' }}"
        >
            <div class="text-sm font-semibold mb-1">3D Glass</div>
            <p class="text-xs opacity-60">{{ __('Depth + texture + richer highlights') }}</p>
        </button>
    </div>

    {{-- {{ __('Live Preview') }} --}}
    <div class="mb-6 rounded-2xl border border-white/10 bg-white/5 p-5">
        <div class="flex items-center justify-between gap-4 mb-4">
            <h4 class="text-sm font-semibold uppercase tracking-wider opacity-70 shrink-0">Live Preview</h4>
            <span class="text-[11px] opacity-40 text-right">{{ __('Updates instantly while you drag controls') }}</span>
        </div>
        <div class="glass-card rounded-2xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs opacity-60">{{ __('Preview Card') }}</p>
                    <p class="text-base font-semibold">{{ __('Glass surface sample') }}</p>
                </div>
                <span class="px-2 py-1 rounded-lg bg-white/10 text-xs font-medium">{{ strtoupper($defaultGlassStyle) }}</span>
            </div>
            <div class="mt-4 grid grid-cols-3 gap-2">
                <div class="h-10 rounded-xl bg-white/10"></div>
                <div class="h-10 rounded-xl bg-white/10"></div>
                <div class="h-10 rounded-xl bg-white/10"></div>
            </div>
        </div>
    </div>

    {{-- Per-Style {{ __('Glass Controls') }} --}}
    <div class="border-t border-white/10 pt-5 mb-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h4 class="text-sm font-semibold uppercase tracking-wider opacity-70">
                    {{ ucfirst($defaultGlassStyle) }} Glass Controls
                </h4>
                <p class="text-[11px] opacity-40 mt-0.5">
                    {{ __('Settings') }} below are saved independently for the <span class="font-semibold">{{ $defaultGlassStyle }}</span> style
                </p>
            </div>
            <div class="flex items-center gap-2">
                @if ($styleIsCustomised)
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30">Customised</span>
                @else
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-white/10 opacity-50">Default</span>
                @endif
                <button
                    wire:click="resetStyleToDefaults"
                    wire:confirm="Reset {{ ucfirst($defaultGlassStyle) }} glass style to built-in defaults?"
                    class="px-3 py-1.5 rounded-lg text-xs font-medium border transition-all duration-200
                           {{ $styleIsCustomised
                               ? 'border-amber-500/40 bg-amber-500/10 text-amber-300 hover:bg-amber-500/20'
                               : 'border-white/10 bg-white/5 text-white/40 cursor-not-allowed' }}"
                    @if (! $styleIsCustomised) disabled @endif
                >
                    <span class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182" />
                        </svg>
                        {{ __('Reset to Defaults') }}
                    </span>
                </button>
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-xs font-medium opacity-70 mb-2">{{ __('Tint Color') }}</label>
            <div class="flex items-center gap-3 p-3 rounded-xl bg-white/5 border border-white/10 max-w-md">
                <input type="color" wire:model.live="glassTintColor"
                       x-on:input="(() => {
                           const hex = ($el.value || '#ffffff').replace('#','');
                           const valid = /^[0-9a-fA-F]{6}$/.test(hex) ? hex : 'ffffff';
                           $dispatch('glass-preview-updated', {
                               vars: {
                                   '--glass-tint-color': '#' + valid,
                                   '--glass-tint-r': parseInt(valid.slice(0, 2), 16),
                                   '--glass-tint-g': parseInt(valid.slice(2, 4), 16),
                                   '--glass-tint-b': parseInt(valid.slice(4, 6), 16)
                               }
                           });
                       })()"
                       class="w-10 h-10 rounded-lg border-0 cursor-pointer bg-transparent p-0 [&::-webkit-color-swatch-wrapper]:p-0 [&::-webkit-color-swatch]:rounded-lg [&::-webkit-color-swatch]:border-2 [&::-webkit-color-swatch]:border-white/20" />
                <input type="text" wire:model.live="glassTintColor"
                       x-on:input="(() => {
                           const hex = ($el.value || '#ffffff').replace('#','');
                           if (!/^[0-9a-fA-F]{6}$/.test(hex)) return;
                           $dispatch('glass-preview-updated', {
                               vars: {
                                   '--glass-tint-color': '#' + hex,
                                   '--glass-tint-r': parseInt(hex.slice(0, 2), 16),
                                   '--glass-tint-g': parseInt(hex.slice(2, 4), 16),
                                   '--glass-tint-b': parseInt(hex.slice(4, 6), 16)
                               }
                           });
                       })()"
                       class="flex-1 bg-transparent border-0 text-sm font-mono uppercase focus:outline-none text-inherit" />
            </div>
            @error('glassTintColor') <p class="text-xs text-red-300 mt-1">{{ $message }}</p> @enderror
        </div>

        @php
            $glassControls = [
                'glassBlur' => ['Blur', 0, 60, 1, 'px', 'More blur = stronger frosted look'],
                'glassBrightness' => ['Brightness', 0.5, 1.4, 0.01, '', 'Lower is moodier, higher is brighter glass'],
                'glassBgOpacity' => ['Glass Opacity', 0, 0.8, 0.01, '', 'Higher value makes glass less transparent'],
                'glassBorderOpacity' => ['Outline Opacity', 0, 0.8, 0.01, '', 'Controls edge visibility and glass outline'],
                'glassShadowOpacity' => ['Shadow Opacity', 0, 0.8, 0.01, '', 'Controls depth under cards'],
                'glassSaturation' => ['Saturation', 0.5, 3.5, 0.1, 'x', 'Boosts colors behind the glass'],
            ];
            $previewVars = [
                'glassBlur' => ['--glass-blur', 'px'],
                'glassBrightness' => ['--glass-brightness', ''],
                'glassBgOpacity' => ['--glass-bg-opacity', ''],
                'glassBorderOpacity' => ['--glass-border-opacity', ''],
                'glassShadowOpacity' => ['--glass-shadow-opacity', ''],
                'glassSaturation' => ['--glass-saturation', ''],
            ];
        @endphp

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            @foreach ($glassControls as $field => [$label, $min, $max, $step, $unit, $help])
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-xs font-medium opacity-70">{{ $label }}</span>
                        <span class="text-xs font-mono px-2 py-0.5 rounded-md bg-white/5 opacity-60">{{ $this->$field }}{{ $unit }}</span>
                    </div>
                    <input type="range" wire:model.live="{{ $field }}" min="{{ $min }}" max="{{ $max }}" step="{{ $step }}" class="w-full accent-indigo-500"
                           x-on:input="$dispatch('glass-preview-updated', { vars: { '{{ $previewVars[$field][0] }}': $el.value + '{{ $previewVars[$field][1] }}' } })" />
                    <p class="text-[10px] opacity-35 mt-1">{{ $help }}</p>
                    @error($field) <p class="text-xs text-red-300 mt-1">{{ $message }}</p> @enderror
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            <label class="block text-xs font-medium opacity-70 mb-2">Noise Texture (for 3D Glass)</label>
            <select wire:model.live="glassNoiseTexture"
                    x-on:change="$dispatch('glass-preview-updated', { vars: { '--glass-noise-url': ({
                        'rice-paper': 'url(\'https://www.transparenttextures.com/patterns/rice-paper.png\')',
                        'egg-shell': 'url(\'https://www.transparenttextures.com/patterns/egg-shell.png\')',
                        'ink-jet': 'url(\'https://www.transparenttextures.com/patterns/ink.png\')',
                        'coarse': 'url(\'https://www.transparenttextures.com/patterns/asfalt-light.png\')',
                        'topology': 'url(\'https://www.transparenttextures.com/patterns/topography.png\')'
                    })[$el.value] || 'url(\'https://www.transparenttextures.com/patterns/egg-shell.png\')' } })"
                    class="w-full max-w-md rounded-xl bg-white/5 border border-white/10 text-sm focus:border-cyan-400/50 focus:ring-0">
                <option value="rice-paper">{{ __('Rice paper') }}</option>
                <option value="egg-shell">{{ __('Egg shell') }}</option>
                <option value="ink-jet">{{ __('Ink jet') }}</option>
                <option value="coarse">{{ __('Coarse') }}</option>
                <option value="topology">{{ __('Topology') }}</option>
            </select>
            @error('glassNoiseTexture') <p class="text-xs text-red-300 mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    {{-- Font Color Settings --}}
    <div class="border-t border-white/10 pt-5 mb-6">
        <h4 class="text-sm font-semibold uppercase tracking-wider opacity-70 mb-4">{{ __('Font Colors') }}</h4>
        <p class="text-xs opacity-40 mb-4">{{ __('Set the global text color for dark and light modes. This affects all body text across the application.') }}</p>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 max-w-lg">
            {{-- Dark Mode Font Color --}}
            <div>
                <label class="block text-xs font-medium opacity-70 mb-2">{{ __('Dark Mode Text') }}</label>
                <div class="flex items-center gap-3 p-3 rounded-xl bg-white/5 border border-white/10">
                    <input type="color" wire:model.live="fontColorDark"
                           x-on:input="$dispatch('glass-preview-updated', { vars: { '--font-color-dark': $el.value } })"
                           class="w-10 h-10 rounded-lg border-0 cursor-pointer bg-transparent p-0 [&::-webkit-color-swatch-wrapper]:p-0 [&::-webkit-color-swatch]:rounded-lg [&::-webkit-color-swatch]:border-2 [&::-webkit-color-swatch]:border-white/20" />
                    <input type="text" wire:model.live="fontColorDark"
                           x-on:input="(() => {
                               const hex = ($el.value || '#bfdbfe').replace('#','');
                               if (!/^[0-9a-fA-F]{6}$/.test(hex)) return;
                               $dispatch('glass-preview-updated', { vars: { '--font-color-dark': '#' + hex } });
                           })()"
                           class="flex-1 bg-transparent border-0 text-sm font-mono uppercase focus:outline-none text-inherit" />
                </div>
                <div class="mt-2 px-3 py-2 rounded-lg border border-white/10" style="background: #0f172a;">
                    <p class="text-sm" :style="'color:' + $wire.fontColorDark">{{ __('Sample dark text') }}</p>
                </div>
                @error('fontColorDark') <p class="text-xs text-red-300 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Light Mode Font Color --}}
            <div>
                <label class="block text-xs font-medium opacity-70 mb-2">{{ __('Light Mode Text') }}</label>
                <div class="flex items-center gap-3 p-3 rounded-xl bg-white/5 border border-white/10">
                    <input type="color" wire:model.live="fontColorLight"
                           x-on:input="$dispatch('glass-preview-updated', { vars: { '--font-color-light': $el.value } })"
                           class="w-10 h-10 rounded-lg border-0 cursor-pointer bg-transparent p-0 [&::-webkit-color-swatch-wrapper]:p-0 [&::-webkit-color-swatch]:rounded-lg [&::-webkit-color-swatch]:border-2 [&::-webkit-color-swatch]:border-white/20" />
                    <input type="text" wire:model.live="fontColorLight"
                           x-on:input="(() => {
                               const hex = ($el.value || '#334155').replace('#','');
                               if (!/^[0-9a-fA-F]{6}$/.test(hex)) return;
                               $dispatch('glass-preview-updated', { vars: { '--font-color-light': '#' + hex } });
                           })()"
                           class="flex-1 bg-transparent border-0 text-sm font-mono uppercase focus:outline-none text-inherit" />
                </div>
                <div class="mt-2 px-3 py-2 rounded-lg border border-white/10" style="background: #f1f5f9;">
                    <p class="text-sm" :style="'color:' + $wire.fontColorLight">{{ __('Sample light text') }}</p>
                </div>
                @error('fontColorLight') <p class="text-xs text-red-300 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>

    {{-- {{ __('Save') }} / Revert Bar --}}
    <div class="flex items-center justify-between">
        <div>
            @if ($hasUnsavedChanges)
                <button wire:click="revert"
                        class="px-4 py-2.5 rounded-xl bg-white/10 border border-white/20 text-sm font-medium hover:bg-white/20 transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" /></svg>
                    {{ __('Revert Changes') }}
                </button>
            @endif
        </div>
        <button
            wire:click="save"
            class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-medium
                   transition-all duration-200 shadow-lg shadow-blue-600/25 hover:shadow-blue-500/40 flex items-center gap-2"
        >
            <span wire:loading.remove wire:target="save">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
            </span>
            <span wire:loading wire:target="save">
                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
            </span>
            Save {{ ucfirst($defaultGlassStyle) }} Settings
        </button>
    </div>
</div>
