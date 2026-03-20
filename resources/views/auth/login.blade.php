<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <x-validation-errors class="mb-4" />

        @session('status')
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ $value }}
            </div>
        @endsession

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div>
                <x-label for="email" value="{{ __('app.auth.email') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('app.auth.password') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            </div>

            <div class="block mt-4">
                <label for="remember_me" class="flex items-center">
                    <x-checkbox id="remember_me" name="remember" />
                    <span class="ms-2 text-sm opacity-70">{{ __('app.auth.remember_me') }}</span>
                </label>
            </div>

            <x-captcha-widget page="login" />

            <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end mt-4 gap-3 sm:gap-0">
                @if (Route::has('password.request'))
                    <a class="underline text-sm opacity-70 hover:opacity-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                        {{ __('app.auth.forgot_password') }}
                    </a>
                @endif

                <x-button class="w-full justify-center sm:w-auto sm:ms-4">
                    {{ __('app.auth.log_in') }}
                </x-button>
            </div>
        </form>

        {{-- Social Login Section --}}
        @php
            $googleConfigured = \App\Models\SocialSetting::isProviderConfigured('google') && \App\Models\SocialSetting::get('GOOGLE_ENABLED', 'true') === 'true';
            $facebookConfigured = \App\Models\SocialSetting::isProviderConfigured('facebook') && \App\Models\SocialSetting::get('FACEBOOK_ENABLED', 'true') === 'true';
            $telegramConfigured = \App\Models\SocialSetting::isProviderConfigured('telegram') && \App\Models\SocialSetting::get('TELEGRAM_ENABLED', 'true') === 'true';
            $twitterConfigured = \App\Models\SocialSetting::isProviderConfigured('twitter') && \App\Models\SocialSetting::get('TWITTER_ENABLED', 'true') === 'true';
            $telegramBotName = \App\Models\SocialSetting::get('TELEGRAM_BOT_NAME');
            $anySocialConfigured = $googleConfigured || $facebookConfigured || $telegramConfigured || $twitterConfigured;
        @endphp

        @if ($anySocialConfigured)
            <div class="mt-6">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-white/20"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-transparent text-white/60">{{ __('app.auth.or') }}</span>
                    </div>
                </div>

                <div class="mt-4 space-y-3">
                    {{-- Google Login Button --}}
                    @if ($googleConfigured)
                        <a href="{{ url('/auth/google/redirect') }}"
                           class="w-full flex items-center justify-center gap-3 px-4 py-2.5 rounded-xl
                                  bg-white/10 hover:bg-white/20 border border-white/20
                                  transition-all duration-200 group">
                            {!! get_icon('auth.social.google', '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>') !!}
                            <span class="text-sm font-medium opacity-80 group-hover:opacity-100">
                                {{ __('Login with Google') }}
                            </span>
                        </a>
                    @endif

                    {{-- Facebook Login Button --}}
                    @if ($facebookConfigured)
                        <a href="{{ url('/auth/facebook/redirect') }}"
                           class="w-full flex items-center justify-center gap-3 px-4 py-2.5 rounded-xl
                                  bg-[#1877F2]/20 hover:bg-[#1877F2]/30 border border-[#1877F2]/30
                                  transition-all duration-200 group">
                            {!! get_icon('auth.social.facebook', '<svg class="w-5 h-5 text-[#1877F2]" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>') !!}
                            <span class="text-sm font-medium opacity-80 group-hover:opacity-100">
                                {{ __('Login with Facebook') }}
                            </span>
                        </a>
                    @endif

                    {{-- X (Twitter) Login Button --}}
                    @if ($twitterConfigured)
                        <a href="{{ url('/auth/twitter/redirect') }}"
                           class="w-full flex items-center justify-center gap-3 px-4 py-2.5 rounded-xl
                                  bg-white/10 hover:bg-white/20 border border-white/20
                                  transition-all duration-200 group">
                            {!! get_icon('auth.social.twitter', '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>') !!}
                            <span class="text-sm font-medium opacity-80 group-hover:opacity-100">
                                {{ __('Login with X') }}
                            </span>
                        </a>
                    @endif

                    {{-- Telegram Login Widget --}}
                    {{-- Update bot name in Admin → Social Settings when needed --}}
                    @if ($telegramConfigured && $telegramBotName)
                        <div class="flex justify-center pt-2">
                            <script async src="https://telegram.org/js/telegram-widget.js?22"
                                    data-telegram-login="{{ $telegramBotName }}"
                                    data-size="large"
                                    data-radius="12"
                                    data-auth-url="{{ url('/auth/telegram/callback') }}"
                                    data-request-access="write"></script>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </x-authentication-card>
</x-guest-layout>
