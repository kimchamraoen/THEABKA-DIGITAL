<div>
    <div class="glass-card rounded-2xl p-6">
        {{-- Header --}}
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-violet-500 to-purple-600 flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 0h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z" />
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-bold">{{ __('Footer Settings') }}</h3>
                <p class="text-sm opacity-60">{{ __('Customize footer layout, links, and social media icons') }}</p>
            </div>
        </div>

        @if (session()->has('footer-saved'))
            <div class="mb-4 p-3 rounded-xl bg-emerald-500/20 border border-emerald-500/30 text-emerald-300 text-sm flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                {{ session('footer-saved') }}
            </div>
        @endif

        <form wire:submit="save" class="space-y-8">

            {{-- ===== SECTION: {{ __('Display Options') }} ===== --}}
            <div class="space-y-4">
                <h4 class="text-sm font-semibold uppercase tracking-wider opacity-60">Display Options</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    {{-- {{ __('Sticky Footer') }} --}}
                    <label class="flex items-center gap-3 p-3 rounded-xl bg-white/5 border border-white/10 cursor-pointer hover:bg-white/10 transition">
                        <input type="checkbox" wire:model="footer_sticky" class="w-5 h-5 rounded-lg bg-white/10 border-white/20 text-blue-500 focus:ring-blue-400/30" />
                        <div>
                            <span class="text-sm font-medium">Sticky Footer</span>
                            <p class="text-xs opacity-40">{{ __('Always visible at bottom') }}</p>
                        </div>
                    </label>

                    {{-- {{ __('Glass Effect') }} --}}
                    <label class="flex items-center gap-3 p-3 rounded-xl bg-white/5 border border-white/10 cursor-pointer hover:bg-white/10 transition">
                        <input type="checkbox" wire:model="footer_glass" class="w-5 h-5 rounded-lg bg-white/10 border-white/20 text-blue-500 focus:ring-blue-400/30" />
                        <div>
                            <span class="text-sm font-medium">Glass Effect</span>
                            <p class="text-xs opacity-40">{{ __('Glassmorphism card style') }}</p>
                        </div>
                    </label>

                    {{-- {{ __('Show Copyright') }} --}}
                    <label class="flex items-center gap-3 p-3 rounded-xl bg-white/5 border border-white/10 cursor-pointer hover:bg-white/10 transition">
                        <input type="checkbox" wire:model="footer_show_copyright" class="w-5 h-5 rounded-lg bg-white/10 border-white/20 text-blue-500 focus:ring-blue-400/30" />
                        <div>
                            <span class="text-sm font-medium">Show Copyright</span>
                            <p class="text-xs opacity-40">{{ __('Display copyright text') }}</p>
                        </div>
                    </label>

                    {{-- {{ __('Show Terms') }} --}}
                    <label class="flex items-center gap-3 p-3 rounded-xl bg-white/5 border border-white/10 cursor-pointer hover:bg-white/10 transition">
                        <input type="checkbox" wire:model="footer_show_terms" class="w-5 h-5 rounded-lg bg-white/10 border-white/20 text-blue-500 focus:ring-blue-400/30" />
                        <div>
                            <span class="text-sm font-medium">Show Terms</span>
                            <p class="text-xs opacity-40">{{ __('Terms of Service link') }}</p>
                        </div>
                    </label>

                    {{-- {{ __('Show Privacy') }} --}}
                    <label class="flex items-center gap-3 p-3 rounded-xl bg-white/5 border border-white/10 cursor-pointer hover:bg-white/10 transition">
                        <input type="checkbox" wire:model="footer_show_privacy" class="w-5 h-5 rounded-lg bg-white/10 border-white/20 text-blue-500 focus:ring-blue-400/30" />
                        <div>
                            <span class="text-sm font-medium">Show Privacy</span>
                            <p class="text-xs opacity-40">{{ __('Privacy Policy link') }}</p>
                        </div>
                    </label>

                    {{-- {{ __('Show Docs') }} --}}
                    <label class="flex items-center gap-3 p-3 rounded-xl bg-white/5 border border-white/10 cursor-pointer hover:bg-white/10 transition">
                        <input type="checkbox" wire:model="footer_show_docs" class="w-5 h-5 rounded-lg bg-white/10 border-white/20 text-blue-500 focus:ring-blue-400/30" />
                        <div>
                            <span class="text-sm font-medium">Show Docs</span>
                            <p class="text-xs opacity-40">{{ __('Documentation link') }}</p>
                        </div>
                    </label>
                </div>
            </div>

            {{-- ===== SECTION: Footer Text ===== --}}
            <div class="space-y-3">
                <h4 class="text-sm font-semibold uppercase tracking-wider opacity-60">{{ __('Copyright Text') }}</h4>
                <input wire:model="footer_text" type="text" placeholder="e.g. © 2026 My Company. All rights reserved."
                       class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 outline-none transition text-inherit" />
                <p class="text-xs opacity-40">{{ __('Leave empty to use default') }}: &copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('All rights reserved.') }}</p>
            </div>

            {{-- ===== SECTION: Custom Links ===== --}}
            <div class="space-y-4">
                <h4 class="text-sm font-semibold uppercase tracking-wider opacity-60">{{ __('Custom Navigation Links') }}</h4>
                <p class="text-xs opacity-40">{{ __('Add links like Homepage, Blog, Contact, etc.') }}</p>

                {{-- Existing Links --}}
                @if (count($footer_links) > 0)
                    <div class="space-y-2">
                        @foreach ($footer_links as $i => $link)
                            <div class="flex items-center gap-3 p-3 rounded-xl bg-white/5 border border-white/10">
                                <div class="flex-1 min-w-0">
                                    <span class="text-sm font-medium">{{ $link['label'] }}</span>
                                    <span class="text-xs opacity-40 ml-2 truncate">{{ $link['url'] }}</span>
                                </div>
                                <button type="button" wire:click="removeLink({{ $i }})"
                                        class="shrink-0 p-1.5 rounded-lg bg-red-500/20 text-red-400 hover:bg-red-500/30 transition">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                    </svg>
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Add New Link --}}
                <div class="flex flex-col sm:flex-row gap-3 p-4 rounded-xl bg-white/5 border border-white/10">
                    <input wire:model="new_link_label" type="text" placeholder="Label (e.g. Homepage)"
                           class="flex-1 px-4 py-2.5 rounded-xl bg-white/10 border border-white/20 focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 outline-none transition text-sm text-inherit" />
                    <input wire:model="new_link_url" type="url" placeholder="URL (e.g. https://example.com)"
                           class="flex-1 px-4 py-2.5 rounded-xl bg-white/10 border border-white/20 focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 outline-none transition text-sm text-inherit" />
                    <button type="button" wire:click="addLink"
                            class="px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-sm font-medium transition shrink-0">
                        {{ __('Add Link') }}
                    </button>
                </div>
                @error('new_link_label') <p class="text-xs text-red-400">{{ $message }}</p> @enderror
                @error('new_link_url') <p class="text-xs text-red-400">{{ $message }}</p> @enderror
            </div>

            {{-- ===== SECTION: {{ __('Social Media Links') }} ===== --}}
            <div class="space-y-4">
                <h4 class="text-sm font-semibold uppercase tracking-wider opacity-60">Social Media Links</h4>
                <p class="text-xs opacity-40">{{ __('Add social media icons with links to the footer') }}</p>

                {{-- Existing Social Links --}}
                @if (count($footer_social_links) > 0)
                    <div class="flex flex-wrap gap-2">
                        @foreach ($footer_social_links as $i => $social)
                            <div class="flex items-center gap-2 px-3 py-2 rounded-xl bg-white/5 border border-white/10">
                                <span class="text-sm">{{ $socialPlatforms[$social['platform']] ?? $social['platform'] }}</span>
                                <button type="button" wire:click="removeSocialLink({{ $i }})"
                                        class="p-1 rounded-lg bg-red-500/20 text-red-400 hover:bg-red-500/30 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Add New Social Link --}}
                <div class="flex flex-col sm:flex-row gap-3 p-4 rounded-xl bg-white/5 border border-white/10">
                    <select wire:model="new_social_platform"
                            class="px-4 py-2.5 rounded-xl bg-white/10 border border-white/20 focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 outline-none transition text-sm text-inherit">
                        @foreach ($socialPlatforms as $key => $label)
                            <option value="{{ $key }}" class="bg-slate-800">{{ $label }}</option>
                        @endforeach
                    </select>
                    <input wire:model="new_social_url" type="url" placeholder="Profile URL (e.g. https://facebook.com/yourpage)"
                           class="flex-1 px-4 py-2.5 rounded-xl bg-white/10 border border-white/20 focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 outline-none transition text-sm text-inherit" />
                    <button type="button" wire:click="addSocialLink"
                            class="px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-sm font-medium transition shrink-0">
                        {{ __('Add Social') }}
                    </button>
                </div>
                @error('new_social_url') <p class="text-xs text-red-400">{{ $message }}</p> @enderror
            </div>

            {{-- ===== PREVIEW ===== --}}
            <div class="space-y-3">
                <h4 class="text-sm font-semibold uppercase tracking-wider opacity-60">{{ __('Preview') }}</h4>

                @if ($footer_glass)
                {{-- Glass Preview --}}
                <div class="glass-card no-gsap gsap-animated rounded-2xl p-4">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 text-xs">
                        @if ($footer_show_copyright)
                            <p class="opacity-50">{{ $footer_text ?: '© ' . date('Y') . ' ' . config('app.name') . '. All rights reserved.' }}</p>
                        @endif
                        <div class="flex items-center gap-4 flex-wrap">
                            @foreach ($footer_links as $link)
                                <span class="opacity-50 hover:opacity-100 transition cursor-pointer">{{ $link['label'] }}</span>
                            @endforeach
                            @if ($footer_show_terms) <span class="opacity-50">Terms</span> @endif
                            @if ($footer_show_privacy) <span class="opacity-50">Privacy</span> @endif
                            @if ($footer_show_docs) <span class="opacity-50">Docs</span> @endif
                        </div>
                        @if (count($footer_social_links) > 0)
                            <div class="flex items-center gap-3">
                                @foreach ($footer_social_links as $social)
                                    <span class="opacity-50 hover:opacity-100 transition">
                                        @include('components.social-icon', ['platform' => $social['platform'], 'size' => 'w-4 h-4'])
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
                @else
                {{-- Flat Bar Preview --}}
                <div class="border-t border-white/10 bg-black/30 rounded-xl overflow-hidden">
                    <div class="px-6 py-5 flex flex-col items-center gap-4">
                        @if (count($footer_social_links) > 0)
                            <div class="flex items-center gap-4">
                                @foreach ($footer_social_links as $social)
                                    <span class="w-10 h-10 rounded-full border border-white/20 flex items-center justify-center opacity-60 hover:opacity-100 hover:bg-white/10 transition-all cursor-pointer">
                                        @include('components.social-icon', ['platform' => $social['platform'], 'size' => 'w-5 h-5'])
                                    </span>
                                @endforeach
                            </div>
                        @endif
                        <div class="flex items-center gap-6 flex-wrap justify-center text-sm">
                            @foreach ($footer_links as $link)
                                <span class="opacity-60 hover:opacity-100 transition cursor-pointer">{{ $link['label'] }}</span>
                            @endforeach
                            @if ($footer_show_terms) <span class="opacity-60">Terms</span> @endif
                            @if ($footer_show_privacy) <span class="opacity-60">Privacy</span> @endif
                            @if ($footer_show_docs) <span class="opacity-60">Docs</span> @endif
                        </div>
                        @if ($footer_show_copyright)
                            <p class="text-xs opacity-40">{{ $footer_text ?: '© ' . date('Y') . ' ' . config('app.name') . '. All rights reserved.' }}</p>
                        @endif
                    </div>
                </div>
                @endif
            </div>

            {{-- Save Button --}}
            <div class="flex justify-end pt-2">
                <button type="submit"
                        class="px-6 py-3 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-semibold transition-all shadow-lg shadow-blue-600/25 hover:shadow-blue-500/40">
                    {{ __('Save Footer Settings') }}
                </button>
            </div>
        </form>
    </div>
</div>
