<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <div x-data="{ recovery: false, code: '' }">
            {{-- Shield Icon & Title --}}
            <div class="text-center mb-6">
                <div class="mx-auto w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-500/20 to-indigo-500/20 border border-blue-500/30 flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                    </svg>
                </div>
                <h2 class="text-xl font-bold mb-1">{{ __('app.auth.two_factor_title') }}</h2>
                <p class="text-sm opacity-50" x-show="! recovery">{{ __('app.auth.two_factor_enter_code') }}</p>
                <p class="text-sm opacity-50" x-cloak x-show="recovery">{{ __('app.auth.two_factor_enter_recovery') }}</p>
            </div>

            <x-validation-errors class="mb-4" />

            <form method="POST" action="{{ route('two-factor.login') }}">
                @csrf

                {{-- Authenticator Code Input --}}
                <div x-show="! recovery">
                    <label for="code" class="block text-sm font-medium mb-2 opacity-70">{{ __('app.auth.authentication_code') }}</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 opacity-30" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                            </svg>
                        </div>
                        <input id="code" type="text" inputmode="numeric" name="code" autofocus x-ref="code"
                               autocomplete="one-time-code" placeholder="0 0 0  0 0 0"
                               class="block w-full pl-12 pr-4 py-3.5 text-center text-2xl tracking-[0.5em] font-bold
                                      rounded-xl bg-white/10 backdrop-blur-xl border border-white/20
                                      focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20
                                      placeholder-white/20 transition-all duration-200 outline-none"
                               style="font-family: 'JetBrains Mono', 'Fira Code', 'SF Mono', 'Cascadia Code', 'Consolas', monospace;" />
                    </div>
                </div>

                {{-- Recovery Code Input --}}
                <div x-cloak x-show="recovery">
                    <label for="recovery_code" class="block text-sm font-medium mb-2 opacity-70">{{ __('app.auth.recovery_code') }}</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 opacity-30" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z" />
                            </svg>
                        </div>
                        <input id="recovery_code" type="text" name="recovery_code" x-ref="recovery_code"
                               autocomplete="one-time-code" placeholder="xxxxx-xxxxx"
                               class="block w-full pl-12 pr-4 py-3.5 text-center text-lg tracking-widest font-mono
                                      rounded-xl bg-white/10 backdrop-blur-xl border border-white/20
                                      focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20
                                      placeholder-white/20 transition-all duration-200 outline-none" />
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="mt-6">
                    <x-button class="w-full justify-center py-3 text-base">
                        {{ __('app.auth.verify_log_in') }}
                    </x-button>
                </div>

                {{-- Toggle Link --}}
                <div class="mt-4 text-center">
                    <button type="button"
                        class="inline-flex items-center gap-1.5 text-sm opacity-60 hover:opacity-100 transition-opacity duration-200"
                        x-show="! recovery"
                        x-on:click="
                            recovery = true;
                            $nextTick(() => { $refs.recovery_code.focus() })
                        ">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z" />
                        </svg>
                        {{ __('app.auth.use_recovery_code') }}
                    </button>

                    <button type="button"
                        class="inline-flex items-center gap-1.5 text-sm opacity-60 hover:opacity-100 transition-opacity duration-200"
                        x-cloak
                        x-show="recovery"
                        x-on:click="
                            recovery = false;
                            $nextTick(() => { $refs.code.focus() })
                        ">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                        </svg>
                        {{ __('app.auth.use_authentication_code') }}
                    </button>
                </div>
            </form>
        </div>
    </x-authentication-card>
</x-guest-layout>
