<div>
    {{-- Header --}}
    <div class="glass-card rounded-2xl p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.098 19.902a3.75 3.75 0 0 0 5.304 0l6.401-6.402M6.75 21A3.75 3.75 0 0 1 3 17.25V4.125C3 3.504 3.504 3 4.125 3h5.25c.621 0 1.125.504 1.125 1.125v4.072M6.75 21a3.75 3.75 0 0 0 3.75-3.75V8.197M6.75 21h13.125c.621 0 1.125-.504 1.125-1.125v-5.25c0-.621-.504-1.125-1.125-1.125h-4.072M10.5 8.197l2.88-2.88c.438-.439 1.15-.439 1.59 0l3.712 3.713c.44.44.44 1.152 0 1.59l-2.879 2.88M6.75 17.25h.008v.008H6.75v-.008Z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold">{{ __('Theme') }} {{ __('Designer') }}</h3>
                    <p class="text-sm opacity-60">{{ __('Create and manage custom themes') }}</p>
                </div>
            </div>
            <button wire:click="newTheme"
                    class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-purple-600 to-pink-600 text-white font-medium text-sm hover:from-purple-500 hover:to-pink-500 transition-all shadow-lg shadow-purple-600/25 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                {{ __('New Theme') }}
            </button>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session()->has('message'))
        <div class="mb-4 p-4 rounded-xl bg-emerald-500/20 border border-emerald-500/30 text-emerald-300 text-sm">
            {{ session('message') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="mb-4 p-4 rounded-xl bg-red-500/20 border border-red-500/30 text-red-300 text-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- Theme List --}}
    @if (!$showEditor)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach ($themes as $theme)
            <div class="glass-card rounded-2xl overflow-hidden group" wire:key="theme-{{ $theme['id'] }}">
                {{-- Theme Preview --}}
                <div class="h-32 relative overflow-hidden">
                    <div class="absolute inset-0" style="background: linear-gradient({{ $theme['dark_gradient_direction'] ?? '135deg' }}, {{ $theme['dark_bg_from'] }}, {{ $theme['dark_bg_via'] }}, {{ $theme['dark_bg_to'] }})"></div>
                    @if ($theme['blobs_enabled'] ?? true)
                        <div class="absolute w-16 h-16 rounded-full blur-xl opacity-60 top-2 left-4" style="background: {{ $theme['blob_color_1'] }}"></div>
                        <div class="absolute w-12 h-12 rounded-full blur-xl opacity-60 bottom-2 right-6" style="background: {{ $theme['blob_color_2'] }}"></div>
                        <div class="absolute w-10 h-10 rounded-full blur-lg opacity-40 top-8 right-12" style="background: {{ $theme['blob_color_3'] }}"></div>
                    @endif
                    {{-- Mini glass card preview --}}
                    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-24 h-14 rounded-lg"
                         style="backdrop-filter: blur({{ $theme['glass_blur'] }}); background: rgba(255,255,255,{{ $theme['glass_bg_opacity'] }}); border: 1px solid rgba(255,255,255,{{ $theme['glass_border_opacity'] }});">
                        <div class="p-2">
                            <div class="w-12 h-1.5 rounded-full mb-1.5" style="background: {{ $theme['color_primary'] }}"></div>
                            <div class="w-8 h-1 rounded-full opacity-50" style="background: {{ $theme['color_secondary'] }}"></div>
                        </div>
                    </div>
                    @php
                        $settings = \App\Models\Setting::instance();
                    @endphp
                    @if (($settings->active_theme_id ?? null) == $theme['id'])
                        <div class="absolute top-2 right-2 px-2 py-0.5 rounded-full bg-emerald-500/80 text-white text-xs font-bold">{{ __('Active') }}</div>
                    @endif
                </div>

                {{-- Theme Info --}}
                <div class="p-4">
                    <h4 class="font-bold text-sm mb-1">{{ $theme['name'] }}</h4>
                    <div class="flex gap-1 mb-3">
                        <span class="w-4 h-4 rounded-full border border-white/20" style="background: {{ $theme['color_primary'] }}"></span>
                        <span class="w-4 h-4 rounded-full border border-white/20" style="background: {{ $theme['color_secondary'] }}"></span>
                        <span class="w-4 h-4 rounded-full border border-white/20" style="background: {{ $theme['color_accent'] }}"></span>
                        <span class="w-4 h-4 rounded-full border border-white/20" style="background: {{ $theme['blob_color_1'] }}"></span>
                        <span class="w-4 h-4 rounded-full border border-white/20" style="background: {{ $theme['blob_color_2'] }}"></span>
                    </div>
                    <div class="flex gap-2">
                        <button wire:click="activateTheme({{ $theme['id'] }})" class="flex-1 px-3 py-1.5 rounded-lg bg-emerald-500/20 border border-emerald-500/30 text-emerald-300 text-xs font-medium hover:bg-emerald-500/30 transition">{{ __('Activate') }}</button>
                        <button wire:click="editTheme({{ $theme['id'] }})" class="px-3 py-1.5 rounded-lg bg-blue-500/20 border border-blue-500/30 text-blue-300 text-xs font-medium hover:bg-blue-500/30 transition">Edit</button>
                        <button wire:click="duplicateTheme({{ $theme['id'] }})" class="px-3 py-1.5 rounded-lg bg-amber-500/20 border border-amber-500/30 text-amber-300 text-xs font-medium hover:bg-amber-500/30 transition">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H9.75" /></svg>
                        </button>
                        @unless ($theme['is_default'] ?? false)
                        <button wire:click="deleteTheme({{ $theme['id'] }})" wire:confirm="Delete this theme?" class="px-3 py-1.5 rounded-lg bg-red-500/20 border border-red-500/30 text-red-300 text-xs font-medium hover:bg-red-500/30 transition">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                        </button>
                        @endunless
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if (empty($themes))
        <div class="glass-card rounded-2xl p-12 text-center">
            <p class="opacity-50 mb-4">{{ __('No themes yet. Create your first theme!') }}</p>
            <button wire:click="newTheme" class="px-5 py-2 rounded-xl bg-purple-600 text-white text-sm font-medium">{{ __('Create Theme') }}</button>
        </div>
    @endif
    @endif

    {{-- Theme Editor --}}
    @if ($showEditor)
    <div class="glass-card rounded-2xl p-6" x-data="{ activeTab: 'colors' }">
        <h3 class="text-lg font-bold mb-6">{{ $editingTheme ? 'Edit' : 'Create' }} Theme</h3>

        {{-- {{ __('Theme Name') }} --}}
        <div class="mb-6">
            <label class="block text-sm font-medium mb-2 opacity-80">Theme Name</label>
            <input wire:model="theme_name" type="text" placeholder="My Custom Theme"
                   class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 focus:border-purple-400/50 focus:ring-2 focus:ring-purple-400/20 outline-none transition text-inherit" />
        </div>

        {{-- Presets --}}
        <div class="mb-6">
            <label class="block text-sm font-medium mb-3 opacity-80">{{ __('Quick Presets') }}</label>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                @foreach ($presets as $key => $preset)
                    <button wire:click="applyPreset('{{ $key }}')"
                            class="p-3 rounded-xl bg-white/5 border border-white/10 hover:border-white/30 transition text-left group">
                        <div class="flex gap-1 mb-2">
                            <span class="w-4 h-4 rounded-full" style="background: {{ $preset['color_primary'] }}"></span>
                            <span class="w-4 h-4 rounded-full" style="background: {{ $preset['color_secondary'] }}"></span>
                            <span class="w-4 h-4 rounded-full" style="background: {{ $preset['blob_color_1'] }}"></span>
                            <span class="w-4 h-4 rounded-full" style="background: {{ $preset['blob_color_2'] }}"></span>
                        </div>
                        <span class="text-xs font-medium opacity-70 group-hover:opacity-100">{{ $preset['name'] }}</span>
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Tabs --}}
        <div class="flex gap-1 mb-6 bg-white/5 rounded-xl p-1">
            @foreach (['colors' => 'Colors', 'background' => 'Background', 'glass' => 'Glass', 'blobs' => 'Blobs', 'css' => 'CSS'] as $tab => $label)
                <button @click="activeTab = '{{ $tab }}'"
                        :class="activeTab === '{{ $tab }}' ? 'bg-white/15 text-white' : 'text-white/50 hover:text-white/80'"
                        class="flex-1 px-3 py-2 rounded-lg text-xs font-medium transition">{{ $label }}</button>
            @endforeach
        </div>

        {{-- Colors Tab --}}
        <div x-show="activeTab === 'colors'" class="space-y-4">
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                @foreach ([
                    'color_primary' => 'Primary',
                    'color_secondary' => 'Secondary',
                    'color_accent' => 'Accent',
                    'color_success' => 'Success',
                    'color_warning' => 'Warning',
                    'color_danger' => 'Danger',
                ] as $field => $label)
                    <div class="relative group">
                        <label class="block text-xs font-medium mb-1.5 opacity-60 uppercase tracking-wider">{{ $label }}</label>
                        <div class="flex items-center gap-2 p-2 rounded-xl bg-white/5 border border-white/10">
                            <div class="relative">
                                <input type="color" wire:model.live="{{ $field }}"
                                       class="w-10 h-10 rounded-lg border-0 cursor-pointer bg-transparent p-0 [&::-webkit-color-swatch-wrapper]:p-0 [&::-webkit-color-swatch]:rounded-lg [&::-webkit-color-swatch]:border-2 [&::-webkit-color-swatch]:border-white/20" />
                            </div>
                            <input type="text" wire:model.live="{{ $field }}"
                                   class="flex-1 bg-transparent border-0 text-xs font-mono uppercase focus:outline-none text-inherit" />
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Background Tab --}}
        <div x-show="activeTab === 'background'" x-cloak class="space-y-6">
            {{-- {{ __('Background Type') }} --}}
            <div>
                <label class="block text-sm font-medium mb-2 opacity-80">Background Type</label>
                <div class="flex gap-2">
                    @foreach (['gradient' => 'Gradient', 'image' => 'Image', 'video' => 'Video'] as $type => $label)
                        <button wire:click="$set('bg_type', '{{ $type }}')"
                                class="px-4 py-2 rounded-xl text-sm font-medium transition {{ $bg_type === $type ? 'bg-purple-600 text-white' : 'bg-white/10 border border-white/20 hover:bg-white/20' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>

            @if ($bg_type === 'gradient')
                {{-- {{ __('Dark Mode Gradient') }} --}}
                <div>
                    <label class="block text-sm font-medium mb-3 opacity-80">Dark Mode Gradient</label>
                    <div class="h-16 rounded-xl mb-3" style="background: linear-gradient({{ $dark_gradient_direction }}, {{ $dark_bg_from }}, {{ $dark_bg_via }}, {{ $dark_bg_to }})"></div>
                    <div class="grid grid-cols-3 gap-3">
                        @foreach (['dark_bg_from' => 'From', 'dark_bg_via' => 'Via', 'dark_bg_to' => 'To'] as $field => $label)
                            <div>
                                <label class="block text-xs opacity-50 mb-1">{{ $label }}</label>
                                <div class="flex items-center gap-1.5 p-1.5 rounded-lg bg-white/5 border border-white/10">
                                    <input type="color" wire:model.live="{{ $field }}" class="w-8 h-8 rounded border-0 cursor-pointer bg-transparent p-0 [&::-webkit-color-swatch-wrapper]:p-0 [&::-webkit-color-swatch]:rounded [&::-webkit-color-swatch]:border-2 [&::-webkit-color-swatch]:border-white/20" />
                                    <input type="text" wire:model.live="{{ $field }}" class="flex-1 bg-transparent border-0 text-xs font-mono uppercase focus:outline-none text-inherit w-0" />
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-2">
                        <label class="block text-xs opacity-50 mb-1">{{ __('Direction') }}</label>
                        <select wire:model.live="dark_gradient_direction" class="w-full px-3 py-2 rounded-lg bg-white/10 border border-white/20 text-sm text-inherit">
                            @foreach (['135deg' => 'Diagonal ↘', '180deg' => 'Top → Bottom', '90deg' => 'Left → Right', '45deg' => 'Diagonal ↗', '225deg' => 'Diagonal ↙', '315deg' => 'Diagonal ↖', '0deg' => 'Bottom → Top', '270deg' => 'Right → Left'] as $deg => $label)
                                <option value="{{ $deg }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- {{ __('Light Mode Gradient') }} --}}
                <div>
                    <label class="block text-sm font-medium mb-3 opacity-80">Light Mode Gradient</label>
                    <div class="h-16 rounded-xl mb-3" style="background: linear-gradient({{ $light_gradient_direction }}, {{ $light_bg_from }}, {{ $light_bg_via }}, {{ $light_bg_to }})"></div>
                    <div class="grid grid-cols-3 gap-3">
                        @foreach (['light_bg_from' => 'From', 'light_bg_via' => 'Via', 'light_bg_to' => 'To'] as $field => $label)
                            <div>
                                <label class="block text-xs opacity-50 mb-1">{{ $label }}</label>
                                <div class="flex items-center gap-1.5 p-1.5 rounded-lg bg-white/5 border border-white/10">
                                    <input type="color" wire:model.live="{{ $field }}" class="w-8 h-8 rounded border-0 cursor-pointer bg-transparent p-0 [&::-webkit-color-swatch-wrapper]:p-0 [&::-webkit-color-swatch]:rounded [&::-webkit-color-swatch]:border-2 [&::-webkit-color-swatch]:border-white/20" />
                                    <input type="text" wire:model.live="{{ $field }}" class="flex-1 bg-transparent border-0 text-xs font-mono uppercase focus:outline-none text-inherit w-0" />
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-2">
                        <label class="block text-xs opacity-50 mb-1">Direction</label>
                        <select wire:model.live="light_gradient_direction" class="w-full px-3 py-2 rounded-lg bg-white/10 border border-white/20 text-sm text-inherit">
                            @foreach (['135deg' => 'Diagonal ↘', '180deg' => 'Top → Bottom', '90deg' => 'Left → Right', '45deg' => 'Diagonal ↗'] as $deg => $label)
                                <option value="{{ $deg }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            @elseif ($bg_type === 'image')
                <div>
                    <label class="block text-sm font-medium mb-2 opacity-80">{{ __('Background Image') }}</label>
                    <input type="file" wire:model="bg_image_upload" accept="image/*"
                           class="w-full text-sm file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-purple-600 file:text-white file:font-medium file:cursor-pointer" />
                    <div class="mt-3">
                        <label class="block text-xs opacity-50 mb-1">{{ __('Overlay Opacity') }}</label>
                        <input type="range" wire:model.live="bg_overlay_opacity" min="0" max="1" step="0.05" class="w-full accent-purple-500" />
                        <span class="text-xs opacity-50">{{ $bg_overlay_opacity }}</span>
                    </div>
                    <div class="mt-2 flex items-center gap-2">
                        <label class="block text-xs opacity-50">{{ __('Overlay Color') }}</label>
                        <input type="color" wire:model.live="bg_overlay_color" class="w-6 h-6 rounded border-0 cursor-pointer bg-transparent p-0" />
                    </div>
                </div>
            @else
                <div>
                    <label class="block text-sm font-medium mb-2 opacity-80">{{ __('Video URL') }}</label>
                    <input type="url" wire:model="bg_video_url" placeholder="https://example.com/video.mp4"
                           class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 focus:border-purple-400/50 outline-none transition text-inherit text-sm" />
                    <p class="text-xs opacity-40 mt-1">{{ __('Direct URL to MP4 video file') }}</p>
                    <div class="mt-3">
                        <label class="block text-xs opacity-50 mb-1">Overlay Opacity</label>
                        <input type="range" wire:model.live="bg_overlay_opacity" min="0" max="1" step="0.05" class="w-full accent-purple-500" />
                        <span class="text-xs opacity-50">{{ $bg_overlay_opacity }}</span>
                    </div>
                </div>
            @endif
        </div>

        {{-- Glass Tab --}}
        <div x-show="activeTab === 'glass'" x-cloak class="space-y-6">
            {{-- iOS 26 Glass Live Preview --}}
            <div class="rounded-2xl p-8 relative overflow-hidden" style="background: linear-gradient({{ $dark_gradient_direction }}, {{ $dark_bg_from }}, {{ $dark_bg_via }}, {{ $dark_bg_to }})">
                @if ($blobs_enabled)
                    <div class="absolute w-28 h-28 rounded-full blur-2xl opacity-60 -top-6 -left-6" style="background: {{ $blob_color_1 }}"></div>
                    <div class="absolute w-24 h-24 rounded-full blur-2xl opacity-60 bottom-2 right-6" style="background: {{ $blob_color_2 }}"></div>
                @endif
                @php
                    $tintHex = ltrim($glass_tint_color ?? '#ffffff', '#');
                    $pR = hexdec(substr($tintHex, 0, 2));
                    $pG = hexdec(substr($tintHex, 2, 2));
                    $pB = hexdec(substr($tintHex, 4, 2));
                @endphp
                <div class="relative rounded-2xl p-5 border"
                     style="backdrop-filter: blur({{ $glass_blur }}) saturate({{ $glass_saturation }}); -webkit-backdrop-filter: blur({{ $glass_blur }}) saturate({{ $glass_saturation }}); background: rgba({{ $pR }},{{ $pG }},{{ $pB }},{{ $glass_bg_opacity }}); border-color: rgba({{ $pR }},{{ $pG }},{{ $pB }},{{ $glass_border_opacity }}); box-shadow: 0 2px 16px rgba(0,0,0,{{ $glass_shadow_opacity }}), inset 0 0.5px 0 rgba(255,255,255,0.12);">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: {{ $color_primary }}30">
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.53 16.122a3 3 0 0 0-5.78 1.128 2.25 2.25 0 0 1-2.4 2.245 4.5 4.5 0 0 0 8.4-2.245c0-.399-.078-.78-.22-1.128Zm0 0a15.998 15.998 0 0 0 3.388-1.62m-5.043-.025a15.994 15.994 0 0 1 1.622-3.395m3.42 3.42a15.995 15.995 0 0 0 4.764-4.648l3.876-5.814a1.151 1.151 0 0 0-1.597-1.597L14.146 6.32a15.996 15.996 0 0 0-4.649 4.763m3.42 3.42a6.776 6.776 0 0 0-3.42-3.42" />
                            </svg>
                        </div>
                        <div>
                            <div class="w-24 h-2.5 rounded-full" style="background: {{ $color_primary }}"></div>
                            <div class="w-16 h-1.5 rounded-full mt-1.5 opacity-40 bg-white"></div>
                        </div>
                    </div>
                    <div class="w-full h-1 rounded-full opacity-20 bg-white"></div>
                </div>
                <p class="text-center text-[10px] uppercase tracking-widest opacity-30 mt-4">iOS 26 Glass Preview</p>
            </div>

            {{-- {{ __('Tint') }} Color --}}
            <div>
                <label class="block text-xs font-medium opacity-70 mb-2">{{ __('Glass Tint Color') }}</label>
                <div class="flex items-center gap-3 p-3 rounded-xl bg-white/5 border border-white/10">
                    <input type="color" wire:model.live="glass_tint_color" class="w-10 h-10 rounded-lg border-0 cursor-pointer bg-transparent p-0 [&::-webkit-color-swatch-wrapper]:p-0 [&::-webkit-color-swatch]:rounded-lg [&::-webkit-color-swatch]:border-2 [&::-webkit-color-swatch]:border-white/20" />
                    <input type="text" wire:model.live="glass_tint_color" class="flex-1 bg-transparent border-0 text-sm font-mono uppercase focus:outline-none text-inherit" />
                    <span class="text-[10px] opacity-40">Tint</span>
                </div>
            </div>

            {{-- Glass Sliders --}}
            @foreach ([
                'glass_blur' => ['Blur Amount', '0', '60', '1', 'px', 'Controls how blurry the background appears through the glass'],
                'glass_bg_opacity' => ['Glass Opacity', '0', '0.6', '0.01', '', 'Higher = more opaque tint, lower = more transparent'],
                'glass_border_opacity' => ['Border Opacity', '0', '0.5', '0.01', '', 'Subtle border glow around the glass edge'],
                'glass_shadow_opacity' => ['Drop Shadow', '0', '0.5', '0.01', '', 'Shadow beneath the glass element'],
                'glass_saturation' => ['Saturation Boost', '0.5', '3', '0.1', 'x', 'Colors behind the glass become more vivid'],
                'glass_noise_opacity' => ['Noise Texture', '0', '0.15', '0.005', '', 'Subtle grain overlay for realism'],
            ] as $field => [$label, $min, $max, $step, $unit, $desc])
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-xs font-medium opacity-70">{{ $label }}</span>
                        <span class="font-mono text-xs px-2 py-0.5 rounded-md bg-white/5 opacity-50">{{ $this->$field }}{{ $unit }}</span>
                    </div>
                    <input type="range" wire:model.live="{{ $field }}" min="{{ $min }}" max="{{ $max }}" step="{{ $step }}" class="w-full accent-purple-500" />
                    <p class="text-[10px] opacity-30 mt-0.5">{{ $desc }}</p>
                </div>
            @endforeach
        </div>

        {{-- Blobs Tab --}}
        <div x-show="activeTab === 'blobs'" x-cloak class="space-y-6">
            <div class="flex items-center gap-3 mb-4">
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" wire:model.live="blobs_enabled" class="sr-only peer">
                    <div class="w-11 h-6 bg-white/20 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-white after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                </label>
                <span class="text-sm font-medium">{{ __('Enable Decorative Blobs') }}</span>
            </div>

            @if ($blobs_enabled)
                {{-- Preview --}}
                <div class="rounded-xl p-8 relative overflow-hidden h-40" style="background: linear-gradient({{ $dark_gradient_direction }}, {{ $dark_bg_from }}, {{ $dark_bg_via }}, {{ $dark_bg_to }})">
                    <div class="absolute w-24 h-24 rounded-full blur-2xl opacity-70 top-2 left-6 animate-pulse" style="background: {{ $blob_color_1 }}"></div>
                    <div class="absolute w-20 h-20 rounded-full blur-2xl opacity-70 bottom-2 right-8" style="background: {{ $blob_color_2 }}"></div>
                    <div class="absolute w-16 h-16 rounded-full blur-xl opacity-60 top-10 right-20" style="background: {{ $blob_color_3 }}"></div>
                    <div class="absolute w-14 h-14 rounded-full blur-xl opacity-50 bottom-6 left-1/3" style="background: {{ $blob_color_4 }}"></div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    @foreach (['blob_color_1' => 'Blob 1', 'blob_color_2' => 'Blob 2', 'blob_color_3' => 'Blob 3', 'blob_color_4' => 'Blob 4'] as $field => $label)
                        <div>
                            <label class="block text-xs opacity-50 mb-1">{{ $label }}</label>
                            <div class="flex items-center gap-2 p-2 rounded-lg bg-white/5 border border-white/10">
                                <input type="color" wire:model.live="{{ $field }}" class="w-8 h-8 rounded border-0 cursor-pointer bg-transparent p-0 [&::-webkit-color-swatch-wrapper]:p-0 [&::-webkit-color-swatch]:rounded [&::-webkit-color-swatch]:border-2 [&::-webkit-color-swatch]:border-white/20" />
                                <input type="text" wire:model.live="{{ $field }}" class="flex-1 bg-transparent border-0 text-xs font-mono uppercase focus:outline-none text-inherit" />
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- CSS Tab --}}
        <div x-show="activeTab === 'css'" x-cloak>
            <label class="block text-sm font-medium mb-2 opacity-80">{{ __('Custom CSS') }}</label>
            <textarea wire:model="custom_css" rows="10" placeholder="/* Add custom CSS here */"
                      class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 focus:border-purple-400/50 outline-none transition text-inherit text-sm font-mono"></textarea>
        </div>

        {{-- Actions --}}
        <div class="flex gap-3 mt-6 pt-6 border-t border-white/10">
            <button wire:click="save" wire:loading.attr="disabled"
                    class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-purple-600 to-pink-600 text-white font-medium text-sm hover:from-purple-500 hover:to-pink-500 transition-all shadow-lg">
                <span wire:loading.remove wire:target="save">{{ $editingTheme ? 'Update' : 'Create' }} Theme</span>
                <span wire:loading wire:target="save">Saving...</span>
            </button>
            <button wire:click="cancelEditor" class="px-6 py-2.5 rounded-xl bg-white/10 border border-white/20 text-sm font-medium hover:bg-white/20 transition">{{ __('Cancel') }}</button>
        </div>

        @error('theme_name') <p class="text-red-400 text-xs mt-2">{{ $message }}</p> @enderror
    </div>
    @endif
</div>
