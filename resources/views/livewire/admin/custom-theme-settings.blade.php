<div class="glass-card p-6 rounded-2xl">
    <h3 class="text-lg font-semibold mb-2 flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.098 19.902a3.75 3.75 0 0 0 5.304 0l6.401-6.402M6.75 21A3.75 3.75 0 0 1 3 17.25V4.125C3 3.504 3.504 3 4.125 3h5.25c.621 0 1.125.504 1.125 1.125v4.072M6.75 21a3.75 3.75 0 0 0 3.75-3.75V8.197M6.75 21h13.125c.621 0 1.125-.504 1.125-1.125v-5.25c0-.621-.504-1.125-1.125-1.125h-4.072M10.5 8.197l2.88-2.88c.438-.439 1.15-.439 1.59 0l3.712 3.713c.44.44.44 1.152 0 1.59l-2.879 2.88M6.75 17.25h.008v.008H6.75v-.008Z" />
        </svg>
        {{ __('Custom Theme Designer') }}
    </h3>
    <p class="text-sm opacity-50 mb-6">{{ __('Create your own color scheme, configure glass effects, and inject custom CSS.') }}</p>

    @if (session()->has('custom-theme-message'))
        <div class="mb-6 p-3 rounded-xl bg-emerald-500/20 border border-emerald-500/30 text-emerald-300 text-sm flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            {{ session('custom-theme-message') }}
        </div>
    @endif

    {{-- Unsaved Changes Indicator --}}
    @if ($hasUnsavedChanges)
        <div class="mb-6 p-3 rounded-xl bg-amber-500/15 border border-amber-500/25 text-amber-300 text-sm flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0 animate-pulse" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" /></svg>
                <span>{{ __('You have unsaved changes') }} &mdash; colors and gradients preview live</span>
            </div>
            <button wire:click="revert"
                    class="px-3 py-1.5 rounded-lg text-xs font-medium bg-amber-500/20 border border-amber-500/30 hover:bg-amber-500/30 transition-colors">
                {{ __('Revert All') }}
            </button>
        </div>
    @endif

    {{-- Theme Presets --}}
    <div class="mb-8">
        <label class="block text-sm font-semibold mb-3 opacity-80 uppercase tracking-wider">{{ __('Quick Presets') }}</label>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            @foreach ($presets as $key => $preset)
                <button wire:click="applyPreset('{{ $key }}')"
                    class="group relative p-3 rounded-xl border border-white/10 bg-white/5 hover:bg-white/10 transition-all duration-200 text-center">
                    <div class="flex gap-1 justify-center mb-2">
                        <span class="w-4 h-4 rounded-full border border-white/20" style="background-color: {{ $preset['primary'] }}"></span>
                        <span class="w-4 h-4 rounded-full border border-white/20" style="background-color: {{ $preset['secondary'] }}"></span>
                        <span class="w-4 h-4 rounded-full border border-white/20" style="background-color: {{ $preset['accent'] }}"></span>
                    </div>
                    <span class="text-xs font-medium opacity-70 group-hover:opacity-100">{{ $preset['label'] }}</span>
                </button>
            @endforeach
        </div>
    </div>

    {{-- {{ __('Color Palette') }} --}}
    <div class="mb-8">
        <label class="block text-sm font-semibold mb-3 opacity-80 uppercase tracking-wider">Color Palette</label>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
            @php
                $colorFields = [
                    ['color_primary', 'Primary', $color_primary, '--color-primary'],
                    ['color_secondary', 'Secondary', $color_secondary, '--color-secondary'],
                    ['color_accent', 'Accent', $color_accent, '--color-accent'],
                    ['color_success', 'Success', $color_success, '--color-success'],
                    ['color_warning', 'Warning', $color_warning, '--color-warning'],
                    ['color_danger', 'Danger', $color_danger, '--color-danger'],
                ];
            @endphp
            @foreach ($colorFields as [$field, $label, $value, $cssVar])
                <div class="text-center">
                    <label class="block text-xs font-medium mb-2 opacity-60">{{ $label }}</label>
                    <div class="relative inline-block">
                        <input type="color" wire:model.live="{{ $field }}"
                            x-on:input="$dispatch('glass-preview-updated', { vars: { '{{ $cssVar }}': $el.value } })"
                            class="w-14 h-14 rounded-xl cursor-pointer border-2 border-white/20 bg-transparent [&::-webkit-color-swatch-wrapper]:p-1 [&::-webkit-color-swatch]:rounded-lg" />
                    </div>
                    <input type="text" wire:model.live="{{ $field }}"
                        x-on:input="(() => { const h = ($el.value || '').replace('#',''); if (/^[0-9a-fA-F]{6}$/.test(h)) $dispatch('glass-preview-updated', { vars: { '{{ $cssVar }}': '#' + h } }); })()"
                        class="mt-2 w-full px-2 py-1 text-xs text-center font-mono rounded-lg bg-white/5 border border-white/10 outline-none focus:border-blue-400/50" />
                </div>
            @endforeach
        </div>
    </div>

    {{-- Background Gradients --}}
    <div class="mb-8 grid grid-cols-1 lg:grid-cols-2 gap-6"
         x-data="{
            dispatchGradient() {
                const bg = document.querySelector('[data-gradient-bg]');
                if (!bg) return;
                const isDark = document.documentElement.classList.contains('dark') || !document.documentElement.classList.contains('light');
                // Read current wire values
                const df = $wire.dark_bg_from, dv = $wire.dark_bg_via, dt = $wire.dark_bg_to;
                const lf = $wire.light_bg_from, lv = $wire.light_bg_via, lt = $wire.light_bg_to;
                bg.style.background = isDark
                    ? `linear-gradient(135deg, ${df}, ${dv}, ${dt})`
                    : `linear-gradient(135deg, ${lf}, ${lv}, ${lt})`;
            }
         }">
        {{-- {{ __('Dark Mode Gradient') }} --}}
        <div class="p-4 rounded-xl bg-white/5 border border-white/10">
            <label class="block text-sm font-semibold mb-3 opacity-80">Dark Mode Gradient</label>
            <div class="h-16 rounded-xl mb-4 border border-white/10 shadow-inner transition-all duration-300"
                 style="background: linear-gradient(135deg, {{ $dark_bg_from }}, {{ $dark_bg_via }}, {{ $dark_bg_to }})"></div>
            <div class="grid grid-cols-3 gap-3">
                @foreach (['dark_bg_from' => 'From', 'dark_bg_via' => 'Via', 'dark_bg_to' => 'To'] as $field => $label)
                    <div class="text-center">
                        <label class="block text-xs opacity-50 mb-1">{{ $label }}</label>
                        <input type="color" wire:model.live="{{ $field }}"
                            x-on:input="$nextTick(() => dispatchGradient())"
                            class="w-10 h-10 rounded-lg cursor-pointer border border-white/20 bg-transparent [&::-webkit-color-swatch-wrapper]:p-0.5 [&::-webkit-color-swatch]:rounded-md" />
                        <input type="text" wire:model.live="{{ $field }}"
                            x-on:input="$nextTick(() => dispatchGradient())"
                            class="mt-1 w-full px-1 py-0.5 text-[10px] text-center font-mono rounded bg-white/5 border border-white/10 outline-none" />
                    </div>
                @endforeach
            </div>
        </div>

        {{-- {{ __('Light Mode Gradient') }} --}}
        <div class="p-4 rounded-xl bg-white/5 border border-white/10">
            <label class="block text-sm font-semibold mb-3 opacity-80">Light Mode Gradient</label>
            <div class="h-16 rounded-xl mb-4 border border-white/10 shadow-inner transition-all duration-300"
                 style="background: linear-gradient(135deg, {{ $light_bg_from }}, {{ $light_bg_via }}, {{ $light_bg_to }})"></div>
            <div class="grid grid-cols-3 gap-3">
                @foreach (['light_bg_from' => 'From', 'light_bg_via' => 'Via', 'light_bg_to' => 'To'] as $field => $label)
                    <div class="text-center">
                        <label class="block text-xs opacity-50 mb-1">{{ $label }}</label>
                        <input type="color" wire:model.live="{{ $field }}"
                            x-on:input="$nextTick(() => dispatchGradient())"
                            class="w-10 h-10 rounded-lg cursor-pointer border border-white/20 bg-transparent [&::-webkit-color-swatch-wrapper]:p-0.5 [&::-webkit-color-swatch]:rounded-md" />
                        <input type="text" wire:model.live="{{ $field }}"
                            x-on:input="$nextTick(() => dispatchGradient())"
                            class="mt-1 w-full px-1 py-0.5 text-[10px] text-center font-mono rounded bg-white/5 border border-white/10 outline-none" />
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- {{ __('Glass Morphism') }} note --}}
    <div class="mb-8 p-4 rounded-xl bg-white/5 border border-white/10">
        <div class="flex items-center gap-2 text-sm font-semibold opacity-80 uppercase tracking-wider mb-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" /></svg>
            Glass Morphism
        </div>
        <p class="text-xs opacity-50">Detailed glass rendering controls (blur, brightness, opacity, tint, texture, font colors) are available in the <strong>{{ __('Default Theme') }}</strong> settings panel.</p>
    </div>

    {{-- {{ __('Custom CSS Plugin') }} --}}
    <div class="mb-8" x-data="{ cssTab: 'global' }">
        <label class="flex items-center gap-2 text-sm font-semibold mb-1 opacity-80 uppercase tracking-wider">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75 22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3-4.5 16.5" />
            </svg>
            Custom CSS Plugin
        </label>
        <p class="text-xs opacity-40 mb-4">Add CSS rules per page. Enable/disable each section independently. Use {{ __('Global') }} for site-wide styles.</p>

        {{-- Tab Navigation --}}
        <div class="flex gap-1 mb-4 p-1 rounded-xl bg-white/5 border border-white/10 w-fit">
            <button @click="cssTab = 'global'"
                    :class="cssTab === 'global' ? 'bg-white/15 text-white shadow-sm' : 'text-white/50 hover:text-white/80'"
                    class="px-4 py-2 rounded-lg text-xs font-semibold transition-all duration-200 flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5a17.92 17.92 0 0 1-8.716-2.247m0 0A8.966 8.966 0 0 1 3 12c0-1.97.633-3.792 1.708-5.273" /></svg>
                Global
            </button>
            <button @click="cssTab = 'landing'"
                    :class="cssTab === 'landing' ? 'bg-white/15 text-white shadow-sm' : 'text-white/50 hover:text-white/80'"
                    class="px-4 py-2 rounded-lg text-xs font-semibold transition-all duration-200 flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" /></svg>
                {{ __('Landing Page') }}
                @if ($custom_css_landing_enabled)
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                @endif
            </button>
            <button @click="cssTab = 'dashboard'"
                    :class="cssTab === 'dashboard' ? 'bg-white/15 text-white shadow-sm' : 'text-white/50 hover:text-white/80'"
                    class="px-4 py-2 rounded-lg text-xs font-semibold transition-all duration-200 flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25a2.25 2.25 0 0 1-2.25-2.25v-2.25Z" /></svg>
                {{ __('Dashboard') }}
                @if ($custom_css_dashboard_enabled)
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                @endif
            </button>
        </div>

        {{-- Global CSS Tab --}}
        <div x-show="cssTab === 'global'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
            <div class="p-4 rounded-xl bg-white/5 border border-white/10">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold opacity-60 uppercase tracking-wider">Global CSS (all pages)</span>
                </div>
                <div class="relative">
                    <textarea
                        wire:model="custom_css"
                        rows="10"
                        placeholder="/* Your custom CSS here */&#10;&#10;.glass-card {&#10;    border-radius: 24px;&#10;}&#10;&#10;.my-custom-class {&#10;    color: var(--color-primary);&#10;}"
                        class="w-full px-4 py-3 rounded-xl bg-white/5 backdrop-blur-xl border border-white/10
                               focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20
                               placeholder-white/20 transition-all duration-200 outline-none
                               font-mono text-sm leading-relaxed resize-y min-h-[200px]"
                        spellcheck="false"
                    ></textarea>
                    <div class="absolute top-3 right-3 text-xs opacity-30 font-mono pointer-events-none">CSS</div>
                </div>
                @error('custom_css') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
            </div>
        </div>

        {{-- {{ __('Landing Page CSS') }} Tab --}}
        <div x-show="cssTab === 'landing'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
            <div class="p-4 rounded-xl bg-white/5 border border-white/10">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold opacity-60 uppercase tracking-wider">Landing Page CSS</span>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <span class="text-xs" :class="$wire.custom_css_landing_enabled ? 'text-emerald-400' : 'opacity-40'">
                            <span x-text="$wire.custom_css_landing_enabled ? 'Enabled' : 'Disabled'"></span>
                        </span>
                        <div class="relative">
                            <input type="checkbox" wire:model.live="custom_css_landing_enabled" class="sr-only peer" />
                            <div class="w-9 h-5 rounded-full bg-white/10 border border-white/20 peer-checked:bg-emerald-500/30 peer-checked:border-emerald-500/50 transition-all duration-200"></div>
                            <div class="absolute top-0.5 left-0.5 w-4 h-4 rounded-full bg-white/50 peer-checked:bg-emerald-400 peer-checked:translate-x-4 transition-all duration-200"></div>
                        </div>
                    </label>
                </div>
                <p class="text-xs opacity-30 mb-3">CSS applied only on the landing/home page. Great for seasonal effects like snow, confetti, custom animations.</p>
                <div class="relative" :class="!$wire.custom_css_landing_enabled && 'opacity-40 pointer-events-none'">
                    <textarea
                        wire:model="custom_css_landing"
                        rows="10"
                        placeholder="/* Landing page CSS — e.g. snow effect */&#10;&#10;.snowflake {&#10;    position: fixed;&#10;    color: white;&#10;    font-size: 1.5em;&#10;    animation: fall linear infinite;&#10;    pointer-events: none;&#10;    z-index: 9999;&#10;}&#10;&#10;@keyframes fall {&#10;    to { transform: translateY(100vh); opacity: 0; }&#10;}"
                        class="w-full px-4 py-3 rounded-xl bg-white/5 backdrop-blur-xl border border-white/10
                               focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20
                               placeholder-white/20 transition-all duration-200 outline-none
                               font-mono text-sm leading-relaxed resize-y min-h-[200px]"
                        spellcheck="false"
                    ></textarea>
                    <div class="absolute top-3 right-3 text-xs opacity-30 font-mono pointer-events-none">CSS</div>
                </div>
                @error('custom_css_landing') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
            </div>
        </div>

        {{-- {{ __('Dashboard CSS') }} Tab --}}
        <div x-show="cssTab === 'dashboard'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
            <div class="p-4 rounded-xl bg-white/5 border border-white/10">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold opacity-60 uppercase tracking-wider">Dashboard CSS</span>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <span class="text-xs" :class="$wire.custom_css_dashboard_enabled ? 'text-emerald-400' : 'opacity-40'">
                            <span x-text="$wire.custom_css_dashboard_enabled ? 'Enabled' : 'Disabled'"></span>
                        </span>
                        <div class="relative">
                            <input type="checkbox" wire:model.live="custom_css_dashboard_enabled" class="sr-only peer" />
                            <div class="w-9 h-5 rounded-full bg-white/10 border border-white/20 peer-checked:bg-emerald-500/30 peer-checked:border-emerald-500/50 transition-all duration-200"></div>
                            <div class="absolute top-0.5 left-0.5 w-4 h-4 rounded-full bg-white/50 peer-checked:bg-emerald-400 peer-checked:translate-x-4 transition-all duration-200"></div>
                        </div>
                    </label>
                </div>
                <p class="text-xs opacity-30 mb-3">{{ __('CSS applied only on the authenticated dashboard pages. Add custom styling for your app\'s main interface.') }}</p>
                <div class="relative" :class="!$wire.custom_css_dashboard_enabled && 'opacity-40 pointer-events-none'">
                    <textarea
                        wire:model="custom_css_dashboard"
                        rows="10"
                        placeholder="/* Dashboard CSS — e.g. custom card styles */&#10;&#10;.glass-card {&#10;    border-radius: 20px;&#10;    box-shadow: 0 8px 32px rgba(0,0,0,0.3);&#10;}&#10;&#10;.sidebar-nav a:hover {&#10;    background: rgba(255,255,255,0.1);&#10;}"
                        class="w-full px-4 py-3 rounded-xl bg-white/5 backdrop-blur-xl border border-white/10
                               focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20
                               placeholder-white/20 transition-all duration-200 outline-none
                               font-mono text-sm leading-relaxed resize-y min-h-[200px]"
                        spellcheck="false"
                    ></textarea>
                    <div class="absolute top-3 right-3 text-xs opacity-30 font-mono pointer-events-none">CSS</div>
                </div>
                @error('custom_css_dashboard') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
            </div>
        </div>

        <p class="text-xs opacity-30 mt-3">
            {{ __('Available CSS variables') }}: <code class="bg-white/10 px-1 rounded">--color-primary</code>,
            <code class="bg-white/10 px-1 rounded">--color-secondary</code>,
            <code class="bg-white/10 px-1 rounded">--color-accent</code>,
            <code class="bg-white/10 px-1 rounded">--glass-blur</code>,
            <code class="bg-white/10 px-1 rounded">--glass-opacity</code>
        </p>
    </div>

    {{-- Actions --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            @if ($hasUnsavedChanges)
                <button
                    wire:click="revert"
                    class="px-4 py-2.5 rounded-xl bg-white/10 border border-white/20 text-sm font-medium hover:bg-white/20
                           transition-all duration-200 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" /></svg>
                    {{ __('Revert Changes') }}
                </button>
            @endif

            <button
                wire:click="resetToDefaults"
                wire:confirm="Reset all theme customizations to defaults?"
                class="px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20
                       transition-all duration-200 text-sm"
            >
                {{ __('Reset to Defaults') }}
            </button>
        </div>

        <button
            wire:click="save"
            class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-medium
                   transition-all duration-200 shadow-lg shadow-blue-600/25 hover:shadow-blue-500/40 flex items-center gap-2"
        >
            <span wire:loading.remove wire:target="save">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                </svg>
            </span>
            <span wire:loading wire:target="save">
                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </span>
            {{ __('Save Theme') }}
        </button>
    </div>
</div>
