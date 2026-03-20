<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div>
                <x-label for="name" value="{{ __('app.auth.name') }}" />
                <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            </div>

            <div class="mt-4">
                <x-label for="email" value="{{ __('app.auth.email') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('app.auth.password') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            </div>

            <div class="mt-4">
                <x-label for="password_confirmation" value="{{ __('app.auth.confirm_password') }}" />
                <x-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            </div>

            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div class="mt-4" x-data="{ showTerms: false, showPrivacy: false }">
                    <x-label for="terms">
                        <div class="flex items-center">
                            <x-checkbox name="terms" id="terms" required />

                            <div class="ms-2">
                                {!! __('app.auth.agree_terms', [
                                        'terms_of_service' => '<button type="button" @click="showTerms = true" class="underline text-sm opacity-70 hover:opacity-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">'.__('app.auth.terms_of_service').'</button>',
                                        'privacy_policy' => '<button type="button" @click="showPrivacy = true" class="underline text-sm opacity-70 hover:opacity-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">'.__('app.auth.privacy_policy').'</button>',
                                ]) !!}
                            </div>
                        </div>
                    </x-label>

                    {{-- Terms of Service Modal --}}
                    <div x-show="showTerms" x-cloak
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="fixed inset-0 z-50 flex items-center justify-center p-4"
                         @keydown.escape.window="showTerms = false">
                        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="showTerms = false"></div>
                        <div class="relative w-full max-w-2xl max-h-[80vh] glass-card rounded-2xl overflow-hidden flex flex-col"
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-200"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95">
                            <div class="flex items-center justify-between p-6 border-b border-white/10">
                                <h2 class="text-xl font-bold gradient-text">{{ __('app.auth.terms_of_service') }}</h2>
                                <button @click="showTerms = false" class="p-2 rounded-xl hover:bg-white/10 transition">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                                </button>
                            </div>
                            <div class="p-6 overflow-y-auto prose prose-invert max-w-none opacity-80 leading-relaxed prose-headings:opacity-100 prose-headings:font-bold prose-a:text-blue-400 prose-a:no-underline hover:prose-a:underline prose-strong:opacity-90">
                                @php
                                    $termsContent = \App\Models\Setting::instance()->terms_content
                                        ?: \Illuminate\Support\Str::markdown(file_get_contents(resource_path('markdown/terms.md')));
                                @endphp
                                {!! $termsContent !!}
                            </div>
                            <div class="p-4 border-t border-white/10 flex justify-end">
                                <button type="button" @click="showTerms = false"
                                        class="px-5 py-2.5 rounded-xl bg-blue-500/80 hover:bg-blue-500 text-white font-medium text-sm transition-all duration-200">
                                    {{ __('app.auth.close') }}
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Privacy Policy Modal --}}
                    <div x-show="showPrivacy" x-cloak
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="fixed inset-0 z-50 flex items-center justify-center p-4"
                         @keydown.escape.window="showPrivacy = false">
                        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="showPrivacy = false"></div>
                        <div class="relative w-full max-w-2xl max-h-[80vh] glass-card rounded-2xl overflow-hidden flex flex-col"
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-200"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95">
                            <div class="flex items-center justify-between p-6 border-b border-white/10">
                                <h2 class="text-xl font-bold gradient-text">{{ __('app.auth.privacy_policy') }}</h2>
                                <button @click="showPrivacy = false" class="p-2 rounded-xl hover:bg-white/10 transition">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                                </button>
                            </div>
                            <div class="p-6 overflow-y-auto prose prose-invert max-w-none opacity-80 leading-relaxed prose-headings:opacity-100 prose-headings:font-bold prose-a:text-blue-400 prose-a:no-underline hover:prose-a:underline prose-strong:opacity-90">
                                @php
                                    $privacyContent = \App\Models\Setting::instance()->privacy_content
                                        ?: \Illuminate\Support\Str::markdown(file_get_contents(resource_path('markdown/policy.md')));
                                @endphp
                                {!! $privacyContent !!}
                            </div>
                            <div class="p-4 border-t border-white/10 flex justify-end">
                                <button type="button" @click="showPrivacy = false"
                                        class="px-5 py-2.5 rounded-xl bg-blue-500/80 hover:bg-blue-500 text-white font-medium text-sm transition-all duration-200">
                                    {{ __('app.auth.close') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <x-captcha-widget page="register" />

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm opacity-70 hover:opacity-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                    {{ __('app.auth.already_registered') }}
                </a>

                <x-button class="ms-4">
                    {{ __('app.auth.register') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
