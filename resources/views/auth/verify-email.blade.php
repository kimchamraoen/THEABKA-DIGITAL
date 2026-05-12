<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        {{-- Success message for new registration --}}
        @if (session('registered'))
            <div x-data="{ show: true }" 
                 x-show="show" 
                 x-init="setTimeout(() => show = false, 5000)"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform -translate-y-2"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="mb-6 p-4 rounded-xl bg-green-500/20 border border-green-500/30 backdrop-blur-sm">
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-green-500/30 flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-green-400">{{ __('app.auth.registration_success_title') }}</p>
                        <p class="text-sm text-green-300/80">{{ __('app.auth.registration_success_message') }}</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Email Icon with Animation --}}
        <div class="flex justify-center mb-6">
            <div class="relative">
                <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-blue-500/30 to-purple-500/30 flex items-center justify-center backdrop-blur-sm border border-white/10">
                    <svg class="w-10 h-10 text-blue-400 animate-pulse" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                    </svg>
                </div>
                {{-- Decorative ring --}}
                <div class="absolute -inset-2 rounded-3xl border border-blue-500/20 animate-ping opacity-30"></div>
            </div>
        </div>

        {{-- Title --}}
        <h2 class="text-xl font-bold text-center mb-2">{{ __('app.auth.verify_email_title') }}</h2>

        {{-- Description --}}
        <p class="text-sm text-center opacity-70 mb-6">
            {{ __('app.auth.verify_email_text') }}
        </p>

        {{-- User Email Display --}}
        <div class="mb-6 p-3 rounded-xl bg-white/5 border border-white/10 text-center">
            <p class="text-xs opacity-50 mb-1">{{ __('app.auth.verification_sent_to') }}</p>
            <p class="font-medium text-blue-400">{{ auth()->user()->email }}</p>
        </div>

        {{-- Status Message --}}
        @if (session('status') == 'verification-link-sent')
            <div class="mb-6 p-4 rounded-xl bg-green-500/20 border border-green-500/30">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <p class="text-sm text-green-400">{{ __('app.auth.verification_link_sent') }}</p>
                </div>
            </div>
        @endif

        {{-- Resend Button --}}
        <form method="POST" action="{{ route('verification.send') }}" class="mb-6">
            @csrf
            <button type="submit" 
                    class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-blue-500 to-purple-500 hover:from-blue-600 hover:to-purple-600 text-white font-semibold text-sm transition-all duration-200 flex items-center justify-center gap-2 shadow-lg shadow-blue-500/25">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                </svg>
                {{ __('app.auth.resend_verification') }}
            </button>
        </form>

        {{-- Divider --}}
        <div class="relative mb-6">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-white/10"></div>
            </div>
            <div class="relative flex justify-center text-xs">
                <span class="px-3 bg-transparent opacity-50">{{ __('app.auth.or') }}</span>
            </div>
        </div>

        {{-- Action Links --}}
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
            <a href="{{ route('profile.show') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 text-sm transition-all duration-200">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                </svg>
                {{ __('app.auth.edit_profile') }}
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" 
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/5 hover:bg-red-500/20 border border-white/10 hover:border-red-500/30 text-sm transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15m-3 0-3-3m0 0 3-3m-3 3H15" />
                    </svg>
                    {{ __('app.nav.log_out') }}
                </button>
            </form>
        </div>

        {{-- Help Text --}}
        <div class="mt-6 p-3 rounded-xl bg-white/5 border border-white/10">
            <p class="text-xs opacity-50 text-center">
                {{ __('app.auth.check_spam_folder') }}
            </p>
        </div>
    </x-authentication-card>

    {{-- Auto-poll: detect verification from another device and redirect --}}
    <script>
    (function() {
        var poll = setInterval(function() {
            fetch('{{ route("email.check-verified") }}', {
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json' }
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.verified) {
                    clearInterval(poll);
                    window.location.href = '/dashboard?verified=1';
                }
            })
            .catch(function() {});
        }, 3000); // Check every 3 seconds
    })();
    </script>
</x-guest-layout>
