<div class="glass-card p-6 rounded-2xl">
    <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
        </svg>
        {{ __('Social Login Settings') }}
    </h3>

    @if (session()->has('social-message'))
        <div class="mb-4 p-3 rounded-xl bg-emerald-500/20 border border-emerald-500/30 text-emerald-300 text-sm">
            {{ session('social-message') }}
        </div>
    @endif

    @if (session()->has('social-error'))
        <div class="mb-4 p-3 rounded-xl bg-red-500/20 border border-red-500/30 text-red-300 text-sm">
            {{ session('social-error') }}
        </div>
    @endif

    {{-- Important Notice --}}
    {{-- Update this URL in Admin → Social Settings when tunnel URL changes --}}
    <div class="mb-6 p-4 rounded-xl bg-amber-500/10 border border-amber-500/20">
        <p class="text-xs opacity-80 leading-relaxed">
            <strong class="text-amber-300">{{ __('Important') }}:</strong><br>
            • {{ __('Redirect URLs must match exactly what you configure in each provider\'s developer console') }}<br>
            • {{ __('When your tunnel URL or domain changes, update the redirect URLs here and in the provider consoles') }}<br>
            • {{ __('For development, you can use the "Generate Redirect URLs" button to auto-fill based on your current APP_URL') }}
        </p>
    </div>

    {{-- Generate Redirect URLs Button --}}
    <div class="mb-6">
        <button type="button" wire:click="generateRedirectUrls"
                class="px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white text-sm font-medium transition-all">
            {{ __('Generate Redirect URLs') }}
        </button>
        <p class="text-xs opacity-40 mt-1">{{ __('Auto-fills redirect URLs based on current APP_URL') }}: {{ config('app.url') }}</p>
    </div>

    {{-- Toggle Switch Styles --}}
    <style>
        .provider-toggle {
            position: relative;
            display: inline-block;
            width: 44px;
            height: 24px;
            flex-shrink: 0;
        }
        .provider-toggle input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .provider-toggle .slider {
            position: absolute;
            cursor: pointer;
            top: 0; left: 0; right: 0; bottom: 0;
            background-color: rgba(255,255,255,0.15);
            border-radius: 24px;
            transition: background-color 0.25s;
        }
        .provider-toggle .slider::before {
            content: "";
            position: absolute;
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: #fff;
            border-radius: 50%;
            transition: transform 0.25s;
        }
        .provider-toggle input:checked + .slider {
            background-color: #3b82f6;
        }
        .provider-toggle input:checked + .slider::before {
            transform: translateX(20px);
        }
        .provider-section-disabled {
            opacity: 0.45;
            pointer-events: auto;
        }
    </style>

    {{-- Google OAuth Section --}}
    <div class="mb-8 p-4 rounded-xl bg-white/5 border border-white/10">
        <h4 class="text-md font-semibold mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-red-400" viewBox="0 0 24 24" fill="currentColor">
                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
            </svg>
            Google OAuth
            <label class="provider-toggle" style="margin-left:auto">
                <input type="checkbox" wire:model.live="google_enabled">
                <span class="slider"></span>
            </label>
        </h4>
        <p class="text-xs opacity-60 mb-4">
            {{ __('Get credentials from') }} <a href="https://console.cloud.google.com/apis/credentials" target="_blank" class="text-blue-400 underline">Google Cloud Console</a>
        </p>

        <div class="{{ !$google_enabled ? 'provider-section-disabled' : '' }}">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Google Client ID --}}
            <div>
                <label class="block text-sm font-medium mb-1 opacity-80">Client ID</label>
                <input type="text" wire:model="google_client_id" placeholder="xxx.apps.googleusercontent.com"
                       class="w-full px-4 py-2.5 rounded-xl bg-white/10 backdrop-blur-xl border border-white/20
                              focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 outline-none transition-all" />
                @error('google_client_id') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
            </div>

            {{-- Google Client Secret --}}
            <div x-data="{ show: false }">
                <label class="block text-sm font-medium mb-1 opacity-80">Client Secret</label>
                <div class="relative">
                    <input :type="show ? 'text' : 'password'" wire:model="google_client_secret" placeholder="••••••••"
                           class="w-full px-4 py-2.5 pr-12 rounded-xl bg-white/10 backdrop-blur-xl border border-white/20
                                  focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 outline-none transition-all" />
                    <button type="button" @click="show = !show"
                            class="absolute right-3 top-1/2 -translate-y-1/2 opacity-60 hover:opacity-100">
                        <svg x-show="!show" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                        <svg x-show="show" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                        </svg>
                    </button>
                </div>
                @error('google_client_secret') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
            </div>

            {{-- Google Redirect URL --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-medium mb-1 opacity-80">Redirect URL</label>
                <input type="url" wire:model="google_redirect_url" placeholder="https://yourdomain.com/auth/google/callback"
                       class="w-full px-4 py-2.5 rounded-xl bg-white/10 backdrop-blur-xl border border-white/20
                              focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 outline-none transition-all" />
                <p class="text-xs opacity-40 mt-1">{{ __('Must match the authorized redirect URI in Google Cloud Console') }}</p>
                @error('google_redirect_url') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>
        </div>
    </div>

    {{-- Facebook OAuth Section --}}
    <div class="mb-8 p-4 rounded-xl bg-white/5 border border-white/10">
        <h4 class="text-md font-semibold mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-500" viewBox="0 0 24 24" fill="currentColor">
                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
            </svg>
            Facebook OAuth
            <label class="provider-toggle" style="margin-left:auto">
                <input type="checkbox" wire:model.live="facebook_enabled">
                <span class="slider"></span>
            </label>
        </h4>
        <p class="text-xs opacity-60 mb-4">
            {{ __('Get credentials from') }} <a href="https://developers.facebook.com/apps/" target="_blank" class="text-blue-400 underline">Facebook Developers</a>
        </p>

        <div class="{{ !$facebook_enabled ? 'provider-section-disabled' : '' }}">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Facebook App ID --}}
            <div>
                <label class="block text-sm font-medium mb-1 opacity-80">App ID</label>
                <input type="text" wire:model="facebook_client_id" placeholder="123456789012345"
                       class="w-full px-4 py-2.5 rounded-xl bg-white/10 backdrop-blur-xl border border-white/20
                              focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 outline-none transition-all" />
                @error('facebook_client_id') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
            </div>

            {{-- Facebook App Secret --}}
            <div x-data="{ show: false }">
                <label class="block text-sm font-medium mb-1 opacity-80">App Secret</label>
                <div class="relative">
                    <input :type="show ? 'text' : 'password'" wire:model="facebook_client_secret" placeholder="••••••••"
                           class="w-full px-4 py-2.5 pr-12 rounded-xl bg-white/10 backdrop-blur-xl border border-white/20
                                  focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 outline-none transition-all" />
                    <button type="button" @click="show = !show"
                            class="absolute right-3 top-1/2 -translate-y-1/2 opacity-60 hover:opacity-100">
                        <svg x-show="!show" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                        <svg x-show="show" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                        </svg>
                    </button>
                </div>
                @error('facebook_client_secret') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
            </div>

            {{-- Facebook Redirect URL --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-medium mb-1 opacity-80">Redirect URL</label>
                <input type="url" wire:model="facebook_redirect_url" placeholder="https://yourdomain.com/auth/facebook/callback"
                       class="w-full px-4 py-2.5 rounded-xl bg-white/10 backdrop-blur-xl border border-white/20
                              focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 outline-none transition-all" />
                <p class="text-xs opacity-40 mt-1">{{ __('Must match the Valid OAuth Redirect URI in Facebook App Settings') }}</p>
                @error('facebook_redirect_url') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>
        </div>
    </div>

    {{-- Telegram OAuth Section --}}
    <div class="mb-8 p-4 rounded-xl bg-white/5 border border-white/10">
        <h4 class="text-md font-semibold mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-sky-400" viewBox="0 0 24 24" fill="currentColor">
                <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>
            </svg>
            Telegram Login
            <label class="provider-toggle" style="margin-left:auto">
                <input type="checkbox" wire:model.live="telegram_enabled">
                <span class="slider"></span>
            </label>
        </h4>
        <p class="text-xs opacity-60 mb-4">
            {{ __('Create a bot via') }} <a href="https://t.me/BotFather" target="_blank" class="text-blue-400 underline">@BotFather</a> {{ __('and configure the domain') }}
        </p>

        <div class="{{ !$telegram_enabled ? 'provider-section-disabled' : '' }}">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Telegram Bot Token --}}
            <div x-data="{ show: false }">
                <label class="block text-sm font-medium mb-1 opacity-80">Bot Token</label>
                <div class="relative">
                    <input :type="show ? 'text' : 'password'" wire:model="telegram_bot_token" placeholder="123456789:ABCdefGHIjklMNOpqrsTUVwxyz"
                           class="w-full px-4 py-2.5 pr-12 rounded-xl bg-white/10 backdrop-blur-xl border border-white/20
                                  focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 outline-none transition-all" />
                    <button type="button" @click="show = !show"
                            class="absolute right-3 top-1/2 -translate-y-1/2 opacity-60 hover:opacity-100">
                        <svg x-show="!show" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                        <svg x-show="show" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                        </svg>
                    </button>
                </div>
                @error('telegram_bot_token') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
            </div>

            {{-- Telegram Bot Name --}}
            <div>
                <label class="block text-sm font-medium mb-1 opacity-80">Bot Username</label>
                <input type="text" wire:model="telegram_bot_name" placeholder="YourBotName"
                       class="w-full px-4 py-2.5 rounded-xl bg-white/10 backdrop-blur-xl border border-white/20
                              focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 outline-none transition-all" />
                <p class="text-xs opacity-40 mt-1">{{ __('Without the @ symbol') }}</p>
                @error('telegram_bot_name') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="mt-4 p-3 rounded-xl bg-sky-500/10 border border-sky-500/20">
            <p class="text-xs opacity-80">
                <strong class="text-sky-300">{{ __('Telegram Setup') }}:</strong><br>
                1. {{ __('Message') }} <a href="https://t.me/BotFather" target="_blank" class="text-blue-400 underline">@BotFather</a> {{ __('and create a new bot') }}<br>
                2. {{ __('Copy the bot token and username') }}<br>
                3. {{ __('Send') }} <code class="bg-white/10 px-1 rounded">/setdomain</code> {{ __('to BotFather and set your domain') }}
            </p>
        </div>
        </div>
    </div>

    {{-- X (Twitter) OAuth Section --}}
    <div class="mb-8 p-4 rounded-xl bg-white/5 border border-white/10">
        <h4 class="text-md font-semibold mb-4 flex items-center gap-2">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
            </svg>
            X (Twitter) OAuth
            <label class="provider-toggle" style="margin-left:auto">
                <input type="checkbox" wire:model.live="twitter_enabled">
                <span class="slider"></span>
            </label>
        </h4>
        <p class="text-xs opacity-60 mb-4">
            {{ __('Get credentials from') }} <a href="https://developer.x.com/en/portal/dashboard" target="_blank" class="text-blue-400 underline">X Developer Portal</a>
        </p>

        <div class="{{ !$twitter_enabled ? 'provider-section-disabled' : '' }}">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Twitter Client ID --}}
            <div>
                <label class="block text-sm font-medium mb-1 opacity-80">Client ID</label>
                <input type="text" wire:model="twitter_client_id" placeholder="Your Twitter Client ID"
                       class="w-full px-4 py-2.5 rounded-xl bg-white/10 backdrop-blur-xl border border-white/20
                              focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 outline-none transition-all" />
                @error('twitter_client_id') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
            </div>

            {{-- Twitter Client Secret --}}
            <div x-data="{ show: false }">
                <label class="block text-sm font-medium mb-1 opacity-80">Client Secret</label>
                <div class="relative">
                    <input :type="show ? 'text' : 'password'" wire:model="twitter_client_secret" placeholder="••••••••"
                           class="w-full px-4 py-2.5 pr-12 rounded-xl bg-white/10 backdrop-blur-xl border border-white/20
                                  focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 outline-none transition-all" />
                    <button type="button" @click="show = !show"
                            class="absolute right-3 top-1/2 -translate-y-1/2 opacity-60 hover:opacity-100">
                        <svg x-show="!show" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                        <svg x-show="show" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                        </svg>
                    </button>
                </div>
                @error('twitter_client_secret') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
            </div>

            {{-- Twitter Redirect URL --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-medium mb-1 opacity-80">Redirect URL</label>
                <input type="url" wire:model="twitter_redirect_url" placeholder="https://yourdomain.com/auth/twitter/callback"
                       class="w-full px-4 py-2.5 rounded-xl bg-white/10 backdrop-blur-xl border border-white/20
                              focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 outline-none transition-all" />
                <p class="text-xs opacity-40 mt-1">{{ __('Must match the callback URL in your X Developer App settings') }}</p>
                @error('twitter_redirect_url') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>
        </div>
    </div>

    <button
        wire:click="save"
        class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-medium
               transition-all duration-200 shadow-lg shadow-blue-600/25 hover:shadow-blue-500/40"
    >
        {{ __('Save Settings') }}
    </button>
</div>
