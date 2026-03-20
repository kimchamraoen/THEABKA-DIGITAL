<div x-data="{ showDirty: $wire.entangle('hasUnsavedChanges'), tab: $wire.entangle('activeTab') }" @input="showDirty = true" @change="showDirty = true">
    <div class="glass-card rounded-2xl p-6">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-cyan-600 flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.53 16.122a3 3 0 0 0-5.78 1.128 2.25 2.25 0 0 1-2.4 2.245 4.5 4.5 0 0 0 8.4-2.245c0-.399-.078-.78-.22-1.128Zm0 0a15.998 15.998 0 0 0 3.388-1.62m-5.043-.025a15.994 15.994 0 0 1 1.622-3.395m3.42 3.42a15.995 15.995 0 0 0 4.764-4.648l3.876-5.814a1.151 1.151 0 0 0-1.597-1.597L14.146 6.32a15.996 15.996 0 0 0-4.649 4.763m3.42 3.42a6.776 6.776 0 0 0-3.42-3.42" />
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-bold">{{ __('Branding & Customization') }}</h3>
                <p class="text-sm opacity-60">{{ __('Complete site identity, backgrounds, landing page, and legal') }}</p>
            </div>
        </div>

        @if (session()->has('branding-saved'))
            <div class="mb-4 p-3 rounded-xl bg-emerald-500/20 border border-emerald-500/30 text-emerald-300 text-sm flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                {{ session('branding-saved') }}
            </div>
        @endif

        {{-- Unsaved Changes Indicator --}}
        <div x-show="showDirty" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="mb-4 p-3 rounded-xl bg-amber-500/15 border border-amber-500/25 text-amber-300 text-sm flex items-center justify-between" style="display:none;">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0 animate-pulse" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" /></svg>
                <span>{{ __('You have unsaved changes') }}</span>
            </div>
            <button type="button" wire:click="revert" @click="showDirty = false"
                    class="px-3 py-1.5 rounded-lg text-xs font-medium bg-amber-500/20 border border-amber-500/30 hover:bg-amber-500/30 transition-colors">
                {{ __('Revert All') }}
            </button>
        </div>

        {{-- Tabs --}}
        <div class="flex flex-wrap gap-2 mb-6 border-b border-white/10 pb-4">
            @foreach ([
                'branding' => 'Branding',
                'landing' => 'Landing Page',
                'bg-auth' => 'Auth BG',
                'bg-app' => 'App BG',
                'bg-landing' => 'Landing BG',
                'emails' => 'Emails',
                'legal' => 'Legal'
            ] as $tabKey => $label)
                <button type="button" @click="tab = '{{ $tabKey }}'"
                        :class="tab === '{{ $tabKey }}' ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/25' : 'bg-white/10 border border-white/10 hover:bg-white/20'"
                        class="px-4 py-2 rounded-xl text-sm font-medium transition-all">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        <form wire:submit="save" class="space-y-6">

            {{-- ===== TAB: Branding ===== --}}
            <div x-show="tab === 'branding'" class="space-y-6">
                <div>
                    <label class="block text-sm font-medium mb-2 opacity-80">{{ __('App Name') }}</label>
                    <input wire:model="app_name" type="text"
                           class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 outline-none transition text-inherit" />
                    <p class="text-xs opacity-40 mt-1">Shows everywhere: navigation, auth pages, landing page, footer, emails</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium mb-2 opacity-80">{{ __('Logo') }}</label>
                        <div class="flex items-center gap-4">
                            @if ($current_logo)
                                <img src="{{ asset('storage/' . $current_logo) }}" class="w-12 h-12 rounded-xl object-contain bg-white/10 p-1" />
                                <button type="button" wire:click="removeLogo" class="text-xs text-red-400 hover:text-red-300">{{ __('Remove') }}</button>
                            @endif
                            <input type="file" wire:model="logo_upload" accept="image/*"
                                   class="text-sm file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-blue-600 file:text-white file:font-medium file:cursor-pointer" />
                        </div>
                        <p class="text-xs opacity-40 mt-1">200x200px PNG transparent recommended</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2 opacity-80">{{ __('Favicon') }}</label>
                        <div class="flex items-center gap-4">
                            @if ($current_favicon)
                                <img src="{{ asset('storage/' . $current_favicon) }}" class="w-8 h-8 rounded object-contain bg-white/10 p-0.5" />
                                <button type="button" wire:click="removeFavicon" class="text-xs text-red-400 hover:text-red-300">Remove</button>
                            @endif
                            <input type="file" wire:model="favicon_upload" accept="image/*"
                                   class="text-sm file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-blue-600 file:text-white file:font-medium file:cursor-pointer" />
                        </div>
                        <p class="text-xs opacity-40 mt-1">32x32 or 64x64px PNG</p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2 opacity-80">{{ __('Footer Text') }}</label>
                    <input wire:model="footer_text" type="text" placeholder="&copy; 2026 MyApp. All rights reserved."
                           class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 outline-none transition text-inherit" />
                    <p class="text-xs opacity-40 mt-1">{{ __('Landing page footer. Leave empty for auto-generated.') }}</p>
                </div>
            </div>

            {{-- ===== TAB: Landing Page ===== --}}
            <div x-show="tab === 'landing'" x-cloak class="space-y-6">
                <div class="p-3 rounded-xl bg-blue-500/10 border border-blue-500/20">
                    <p class="text-xs opacity-70"><strong class="text-blue-300">Tip:</strong> {{ __('Leave fields empty to use the defaults. All text here overrides the landing page hero, features section, and CTA section.') }}</p>
                </div>

                {{-- {{ __('Hero Section') }} --}}
                <div class="p-4 rounded-xl bg-white/5 border border-white/10 space-y-4">
                    <h4 class="text-sm font-bold opacity-80 flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" /></svg>
                        Hero Section
                    </h4>

                    <div>
                        <label class="block text-xs font-medium mb-1 opacity-60">{{ __('Badge Text') }}</label>
                        <input wire:model="landing_hero_badge" type="text" placeholder="Secure by Default"
                               class="w-full px-3 py-2 rounded-lg bg-white/10 border border-white/20 text-sm text-inherit outline-none" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-medium mb-1 opacity-60">{{ __('Heading Line 1') }}</label>
                            <input wire:model="landing_hero_line1" type="text" placeholder="Next-Gen"
                                   class="w-full px-3 py-2 rounded-lg bg-white/10 border border-white/20 text-sm text-inherit outline-none" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium mb-1 opacity-60">Heading Line 2 (gradient)</label>
                            <input wire:model="landing_hero_line2" type="text" placeholder="Authentication"
                                   class="w-full px-3 py-2 rounded-lg bg-white/10 border border-white/20 text-sm text-inherit outline-none" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium mb-1 opacity-60">{{ __('Heading Line 3') }}</label>
                            <input wire:model="landing_hero_line3" type="text" placeholder="Platform"
                                   class="w-full px-3 py-2 rounded-lg bg-white/10 border border-white/20 text-sm text-inherit outline-none" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium mb-1 opacity-60">{{ __('Subtitle') }}</label>
                        <textarea wire:model="landing_hero_subtitle" rows="2" placeholder="Enterprise-grade two-factor authentication..."
                                  class="w-full px-3 py-2 rounded-lg bg-white/10 border border-white/20 text-sm text-inherit outline-none"></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium mb-1 opacity-60">{{ __('Primary Button Text') }}</label>
                            <input wire:model="landing_cta_primary_text" type="text" placeholder="Start Free"
                                   class="w-full px-3 py-2 rounded-lg bg-white/10 border border-white/20 text-sm text-inherit outline-none" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium mb-1 opacity-60">{{ __('Primary Button URL') }}</label>
                            <input wire:model="landing_cta_primary_url" type="text" placeholder="/register"
                                   class="w-full px-3 py-2 rounded-lg bg-white/10 border border-white/20 text-sm text-inherit outline-none" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium mb-1 opacity-60">{{ __('Secondary Button Text') }}</label>
                            <input wire:model="landing_cta_secondary_text" type="text" placeholder="Learn More"
                                   class="w-full px-3 py-2 rounded-lg bg-white/10 border border-white/20 text-sm text-inherit outline-none" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium mb-1 opacity-60">{{ __('Secondary Button URL') }}</label>
                            <input wire:model="landing_cta_secondary_url" type="text" placeholder="#features"
                                   class="w-full px-3 py-2 rounded-lg bg-white/10 border border-white/20 text-sm text-inherit outline-none" />
                        </div>
                    </div>
                </div>

                {{-- {{ __('Features Section') }} --}}
                <div class="p-4 rounded-xl bg-white/5 border border-white/10 space-y-4">
                    <div class="flex items-center justify-between">
                        <h4 class="text-sm font-bold opacity-80">Features Section</h4>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model="landing_features_visible" class="sr-only peer">
                            <div class="w-9 h-5 bg-white/20 rounded-full peer peer-checked:bg-emerald-600 after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:after:translate-x-full"></div>
                        </label>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium mb-1 opacity-60">{{ __('Section Title') }}</label>
                            <input wire:model="landing_features_title" type="text" placeholder="Enterprise Security Features"
                                   class="w-full px-3 py-2 rounded-lg bg-white/10 border border-white/20 text-sm text-inherit outline-none" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium mb-1 opacity-60">{{ __('Section Subtitle') }}</label>
                            <input wire:model="landing_features_subtitle" type="text" placeholder="Everything you need..."
                                   class="w-full px-3 py-2 rounded-lg bg-white/10 border border-white/20 text-sm text-inherit outline-none" />
                        </div>
                    </div>
                </div>

                {{-- CTA Section --}}
                <div class="p-4 rounded-xl bg-white/5 border border-white/10 space-y-4">
                    <div class="flex items-center justify-between">
                        <h4 class="text-sm font-bold opacity-80">{{ __('Bottom CTA Section') }}</h4>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model="landing_cta_visible" class="sr-only peer">
                            <div class="w-9 h-5 bg-white/20 rounded-full peer peer-checked:bg-emerald-600 after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:after:translate-x-full"></div>
                        </label>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium mb-1 opacity-60">CTA Title</label>
                            <input wire:model="landing_cta_title" type="text" placeholder="Ready to Secure Your App?"
                                   class="w-full px-3 py-2 rounded-lg bg-white/10 border border-white/20 text-sm text-inherit outline-none" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium mb-1 opacity-60">CTA Subtitle</label>
                            <input wire:model="landing_cta_subtitle" type="text" placeholder="Set up in minutes, not days."
                                   class="w-full px-3 py-2 rounded-lg bg-white/10 border border-white/20 text-sm text-inherit outline-none" />
                        </div>
                    </div>
                </div>

                {{-- Visual Toggles --}}
                <div class="p-4 rounded-xl bg-white/5 border border-white/10">
                    <h4 class="text-sm font-bold opacity-80 mb-3">{{ __('Visual Effects') }}</h4>
                    <div class="flex flex-wrap gap-6">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" wire:model="landing_floating_cards" class="sr-only peer">
                            <div class="w-9 h-5 bg-white/20 rounded-full peer peer-checked:bg-emerald-600 relative after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:after:translate-x-full"></div>
                            <span class="text-sm">{{ __('Floating Cards') }}</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" wire:model="landing_particles" class="sr-only peer">
                            <div class="w-9 h-5 bg-white/20 rounded-full peer peer-checked:bg-emerald-600 relative after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:after:translate-x-full"></div>
                            <span class="text-sm">{{ __('Particles Effect') }}</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- ===== TAB: Auth Background ===== --}}
            <div x-show="tab === 'bg-auth'" x-cloak class="space-y-4">
                <p class="text-sm opacity-60">{{ __('Background for login, register, forgot password, and other auth pages.') }}</p>
                @include('livewire.admin.partials.bg-picker', ['prefix' => 'auth_bg', 'current_image' => $current_auth_bg_image, 'current_video' => $current_auth_bg_video, 'removeImageMethod' => 'removeAuthBgImage', 'removeVideoMethod' => 'removeAuthBgVideo'])
            </div>

            {{-- ===== TAB: App Background ===== --}}
            <div x-show="tab === 'bg-app'" x-cloak class="space-y-4">
                <p class="text-sm opacity-60">{{ __('Background for dashboard, settings, and all authenticated pages.') }}</p>
                @include('livewire.admin.partials.bg-picker', ['prefix' => 'app_bg', 'current_image' => $current_app_bg_image, 'current_video' => $current_app_bg_video, 'removeImageMethod' => 'removeAppBgImage', 'removeVideoMethod' => 'removeAppBgVideo'])
            </div>

            {{-- ===== TAB: Landing Background ===== --}}
            <div x-show="tab === 'bg-landing'" x-cloak class="space-y-4">
                <p class="text-sm opacity-60">{{ __('Background for the public landing page. Overrides the theme gradient when set.') }}</p>
                @include('livewire.admin.partials.bg-picker', ['prefix' => 'landing_bg', 'current_image' => $current_landing_bg_image, 'current_video' => $current_landing_bg_video, 'removeImageMethod' => 'removeLandingBgImage', 'removeVideoMethod' => 'removeLandingBgVideo'])
            </div>

            {{-- ===== TAB: Email Templates ===== --}}
            <div x-show="tab === 'emails'" x-cloak class="space-y-4">
                <p class="text-sm opacity-60">{{ __('Customize the text shown on verification and password reset pages.') }}</p>
                <div>
                    <label class="block text-sm font-medium mb-2 opacity-80">{{ __('Verify Email Message') }}</label>
                    <textarea wire:model="verify_email_text" rows="3"
                              class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 text-sm text-inherit outline-none focus:border-blue-400/50"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2 opacity-80">{{ __('Forgot Password Message') }}</label>
                    <textarea wire:model="forgot_password_text" rows="3"
                              class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 text-sm text-inherit outline-none focus:border-blue-400/50"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2 opacity-80">Welcome Email (optional)</label>
                    <textarea wire:model="welcome_email_text" rows="3" placeholder="Welcome to our platform..."
                              class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 text-sm text-inherit outline-none focus:border-blue-400/50"></textarea>
                </div>
            </div>

            {{-- ===== TAB: Terms & Privacy ===== --}}
            <div x-show="tab === 'legal'" x-cloak class="space-y-6">
                <div class="p-3 rounded-xl bg-amber-500/10 border border-amber-500/20">
                    <p class="text-xs opacity-70">
                        <strong class="text-amber-300">{{ __('Important') }}:</strong>
                        {{ __('Users must agree at registration. Pages at') }} <code class="bg-white/10 px-1 rounded">/terms-of-service</code> and <code class="bg-white/10 px-1 rounded">/privacy-policy</code>. HTML supported.
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2 opacity-80">{{ __('Terms of Service') }}</label>
                    <textarea wire:model="terms_content" rows="12" placeholder="<h2>Terms of Service</h2><p>...</p>"
                              class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 text-sm text-inherit outline-none focus:border-blue-400/50 font-mono"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2 opacity-80">{{ __('Privacy Policy') }}</label>
                    <textarea wire:model="privacy_content" rows="12" placeholder="<h2>Privacy Policy</h2><p>...</p>"
                              class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 text-sm text-inherit outline-none focus:border-blue-400/50 font-mono"></textarea>
                </div>
            </div>

            {{-- Save / Revert Bar --}}
            <div class="flex items-center justify-between pt-2 border-t border-white/10">
                <div class="flex items-center gap-3">
                    <button type="button" wire:click="revert" @click="showDirty = false" x-show="showDirty"
                            class="px-5 py-2.5 rounded-xl bg-white/10 border border-white/20 text-sm font-medium hover:bg-white/20 transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" /></svg>
                        {{ __('Revert Changes') }}
                    </button>
                </div>
                <button type="submit" wire:loading.attr="disabled"
                        class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-cyan-600 text-white font-medium text-sm hover:from-blue-500 hover:to-cyan-500 transition-all shadow-lg shadow-blue-600/25 flex items-center gap-2">
                    <span wire:loading.remove wire:target="save">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                    </span>
                    <span wire:loading wire:target="save">
                        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    </span>
                    {{ __('Save Settings') }}
                </button>
            </div>
        </form>
    </div>
</div>
