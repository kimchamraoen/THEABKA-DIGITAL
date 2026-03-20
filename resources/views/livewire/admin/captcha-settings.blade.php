<div class="glass-card p-6 rounded-2xl">
    <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
        </svg>
        CAPTCHA Settings
    </h3>

    @if (session()->has('captcha-message'))
        <div class="mb-4 p-3 rounded-xl bg-emerald-500/20 border border-emerald-500/30 text-emerald-300 text-sm">
            {{ session('captcha-message') }}
        </div>
    @endif

    {{-- Provider Selection --}}
    <div class="mb-6">
        <label class="block text-sm font-medium mb-2 opacity-80">CAPTCHA Provider</label>
        <div class="flex flex-wrap gap-3">
            {{-- None --}}
            <label class="flex items-center gap-2 px-4 py-2.5 rounded-xl border cursor-pointer transition-all
                {{ !$captcha_provider ? 'bg-blue-500/20 border-blue-400/50' : 'bg-white/5 border-white/10 hover:bg-white/10' }}">
                <input type="radio" wire:model.live="captcha_provider" value="" class="hidden" />
                <svg class="w-4 h-4 {{ !$captcha_provider ? 'text-blue-400' : 'opacity-40' }}" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636" />
                </svg>
                <span class="text-sm font-medium">{{ __('Disabled') }}</span>
            </label>

            {{-- Google reCAPTCHA --}}
            <label class="flex items-center gap-2 px-4 py-2.5 rounded-xl border cursor-pointer transition-all
                {{ $captcha_provider === 'recaptcha' ? 'bg-blue-500/20 border-blue-400/50' : 'bg-white/5 border-white/10 hover:bg-white/10' }}">
                <input type="radio" wire:model.live="captcha_provider" value="recaptcha" class="hidden" />
                <svg class="w-4 h-4 {{ $captcha_provider === 'recaptcha' ? 'text-blue-400' : 'opacity-40' }}" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.94-.49-7-3.85-7-7.93 0-.62.08-1.22.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                </svg>
                <span class="text-sm font-medium">{{ __('Google reCAPTCHA v2') }}</span>
            </label>

            {{-- {{ __('Cloudflare Turnstile') }} --}}
            <label class="flex items-center gap-2 px-4 py-2.5 rounded-xl border cursor-pointer transition-all
                {{ $captcha_provider === 'turnstile' ? 'bg-blue-500/20 border-blue-400/50' : 'bg-white/5 border-white/10 hover:bg-white/10' }}">
                <input type="radio" wire:model.live="captcha_provider" value="turnstile" class="hidden" />
                <svg class="w-4 h-4 {{ $captcha_provider === 'turnstile' ? 'text-blue-400' : 'opacity-40' }}" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                </svg>
                <span class="text-sm font-medium">Cloudflare Turnstile</span>
            </label>
        </div>
    </div>

    {{-- Page Toggles --}}
    @if ($captcha_provider)
        <div class="mb-6 p-4 rounded-xl bg-white/5 border border-white/10">
            <label class="block text-sm font-medium mb-3 opacity-80">{{ __('Enable CAPTCHA on') }}</label>
            <div class="flex flex-wrap gap-4">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" wire:model="captcha_on_login"
                           class="w-4 h-4 rounded border-white/30 bg-white/10 text-blue-500 focus:ring-blue-400/50" />
                    <span class="text-sm">{{ __('Login Page') }}</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" wire:model="captcha_on_register"
                           class="w-4 h-4 rounded border-white/30 bg-white/10 text-blue-500 focus:ring-blue-400/50" />
                    <span class="text-sm">{{ __('Register Page') }}</span>
                </label>
            </div>
        </div>
    @endif

    {{-- Google reCAPTCHA Keys --}}
    @if ($captcha_provider === 'recaptcha')
        <div class="mb-6 p-4 rounded-xl bg-white/5 border border-white/10">
            <h4 class="text-sm font-semibold mb-3 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-blue-400"></span>
                {{ __('Google reCAPTCHA v2 Keys') }}
            </h4>
            <p class="text-xs opacity-60 mb-3">
                {{ __('Get your keys from') }} <a href="https://www.google.com/recaptcha/admin" target="_blank" class="text-blue-400 underline">Google reCAPTCHA Admin Console</a>. Select reCAPTCHA v2 "I'm not a robot" checkbox.
            </p>
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1 opacity-80">{{ __('Site Key') }}</label>
                    <input type="text" wire:model="recaptcha_site_key" placeholder="6Le..."
                           class="w-full px-4 py-2.5 rounded-xl bg-white/10 backdrop-blur-xl border border-white/20
                                  focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 outline-none transition-all font-mono text-sm" />
                    @error('recaptcha_site_key') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1 opacity-80">{{ __('Secret Key') }}</label>
                    <input type="password" wire:model="recaptcha_secret_key" placeholder="6Le..."
                           class="w-full px-4 py-2.5 rounded-xl bg-white/10 backdrop-blur-xl border border-white/20
                                  focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 outline-none transition-all font-mono text-sm" />
                    @error('recaptcha_secret_key') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>
    @endif

    {{-- {{ __('Cloudflare Turnstile Keys') }} --}}
    @if ($captcha_provider === 'turnstile')
        <div class="mb-6 p-4 rounded-xl bg-white/5 border border-white/10">
            <h4 class="text-sm font-semibold mb-3 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-orange-400"></span>
                Cloudflare Turnstile Keys
            </h4>
            <p class="text-xs opacity-60 mb-3">
                Get your keys from <a href="https://dash.cloudflare.com/?to=/:account/turnstile" target="_blank" class="text-blue-400 underline">Cloudflare Dashboard → Turnstile</a>. Create a widget for your domain.
            </p>
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1 opacity-80">Site Key</label>
                    <input type="text" wire:model="turnstile_site_key" placeholder="0x4..."
                           class="w-full px-4 py-2.5 rounded-xl bg-white/10 backdrop-blur-xl border border-white/20
                                  focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 outline-none transition-all font-mono text-sm" />
                    @error('turnstile_site_key') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1 opacity-80">Secret Key</label>
                    <input type="password" wire:model="turnstile_secret_key" placeholder="0x4..."
                           class="w-full px-4 py-2.5 rounded-xl bg-white/10 backdrop-blur-xl border border-white/20
                                  focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 outline-none transition-all font-mono text-sm" />
                    @error('turnstile_secret_key') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>
    @endif

    {{-- Info Box --}}
    @if ($captcha_provider)
        <div class="mb-6 p-4 rounded-xl bg-amber-500/10 border border-amber-500/20">
            <p class="text-xs opacity-80 leading-relaxed">
                <strong class="text-amber-300">{{ __('Important') }}:</strong><br>
                @if ($captcha_provider === 'recaptcha')
                    • Make sure to add your domain (including <code class="bg-white/10 px-1 rounded">localhost</code> for testing) in the reCAPTCHA admin console<br>
                    • {{ __('Use reCAPTCHA v2') }} "I'm not a robot" checkbox type — v3 is not supported
                @else
                    • {{ __('Add your domain in the Cloudflare Turnstile widget settings') }}<br>
                    • {{ __('Turnstile is privacy-friendly and often invisible — great for UX') }}<br>
                    • For testing, use Cloudflare's test keys: Site <code class="bg-white/10 px-1 rounded">1x00000000000000000000AA</code> / Secret <code class="bg-white/10 px-1 rounded">1x0000000000000000000000000000000AA</code>
                @endif
            </p>
        </div>
    @endif

    {{-- Save Button --}}
    <div class="flex justify-end">
        <button wire:click="save" wire:loading.attr="disabled"
                class="px-6 py-2.5 rounded-xl bg-blue-500/80 hover:bg-blue-500 text-white font-medium text-sm
                       transition-all duration-200 disabled:opacity-50 flex items-center gap-2">
            <svg wire:loading wire:target="save" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            {{ __('Save CAPTCHA Settings') }}
        </button>
    </div>
</div>
