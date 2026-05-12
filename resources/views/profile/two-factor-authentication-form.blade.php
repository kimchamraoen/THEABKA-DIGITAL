<x-action-section>
    <x-slot name="title">
        {{ __('app.two_factor.title') }}
    </x-slot>

    <x-slot name="description">
        {{ __('app.two_factor.description') }}
    </x-slot>

    <x-slot name="content">
        @php
            $user = $this->user;
            $hasEmail = filled($user->email);
            $isEmailVerified = !is_null($user->email_verified_at);
            $canEnableTwoFactor = $hasEmail && $isEmailVerified;
        @endphp

        @if (session()->has('error'))
            <div class="mb-5 p-3 rounded-xl bg-red-500/10 border border-red-500/20 text-sm text-red-300">
                {{ session('error') }}
            </div>
        @endif

        {{-- Status Badge --}}
        <div class="flex items-center gap-3 mb-5">
            @if ($this->enabled)
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    {{ __('app.two_factor.enabled') }}
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-yellow-500/20 text-yellow-400 border border-yellow-500/30">
                    <span class="w-2 h-2 rounded-full bg-yellow-400"></span>
                    {{ __('app.two_factor.disabled') }}
                </span>
            @endif
        </div>

        <h3 class="text-lg font-semibold opacity-90">
            @if ($this->enabled)
                @if ($showingConfirmation)
                    {{ __('app.two_factor.finish_enabling') }}
                @else
                    {{ __('app.two_factor.have_enabled') }}
                @endif
            @else
                {{ __('app.two_factor.have_not_enabled') }}
            @endif
        </h3>

        <div class="mt-3 max-w-xl text-sm opacity-60">
            <p>
                {{ __('app.two_factor.when_enabled') }}
            </p>
        </div>

        @if (! $this->enabled && ! $canEnableTwoFactor)
            <div class="mt-4 max-w-xl p-4 rounded-xl bg-amber-500/10 border border-amber-500/25 text-sm">
                <p class="font-medium text-amber-300">
                    ⚠️ Please add and verify your email address before enabling Two-Factor Authentication. This ensures you can recover your account if you lose access to your authenticator app.
                </p>
                <div class="mt-3 flex items-center gap-2 flex-wrap">
                    @if (! $hasEmail)
                        <button type="button"
                                onclick="const emailField = document.getElementById('email'); if (emailField) { emailField.scrollIntoView({ behavior: 'smooth', block: 'center' }); emailField.focus(); }"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 border border-white/15 text-xs font-medium transition">
                            Add Email
                        </button>
                    @endif

                    @if ($hasEmail && ! $isEmailVerified)
                        <form method="POST" action="{{ route('verification.send') }}">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-500/20 hover:bg-amber-500/30 border border-amber-500/30 text-amber-300 text-xs font-medium transition">
                                Verify Email
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endif

        @if ($this->enabled)
            @if ($showingQrCode)
                <div class="mt-6 max-w-xl text-sm opacity-70">
                    <p class="font-semibold">
                        @if ($showingConfirmation)
                            {{ __('app.two_factor.to_finish') }}
                        @else
                            {{ __('app.two_factor.now_enabled') }}
                        @endif
                    </p>
                </div>

                {{-- QR Code Card --}}
                <div class="mt-5 inline-block p-5 rounded-2xl bg-white shadow-2xl shadow-black/20 relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-50 to-indigo-50 opacity-50"></div>
                    <div class="relative">
                        {!! $this->user->twoFactorQrCodeSvg() !!}
                    </div>
                </div>

                {{-- Setup Key --}}
                <div class="mt-4 max-w-xl">
                    <div class="p-4 rounded-xl bg-white/5 backdrop-blur-sm border border-white/10">
                        <p class="text-xs opacity-50 uppercase tracking-wider mb-1">{{ __('app.two_factor.setup_key') }}</p>
                        <p class="font-mono text-sm font-semibold break-all select-all">
                            {{ decrypt($this->user->two_factor_secret) }}
                        </p>
                    </div>
                </div>

                @if ($showingConfirmation)
                    <div class="mt-5">
                        <x-label for="code" value="{{ __('app.two_factor.verification_code') }}" />
                        <div class="mt-1 max-w-xs">
                            <x-input id="code" type="text" name="code"
                                class="block w-full text-center text-lg tracking-[0.5em] font-mono"
                                inputmode="numeric" autofocus autocomplete="one-time-code"
                                placeholder="000000"
                                wire:model="code"
                                wire:keydown.enter="confirmTwoFactorAuthentication" />
                        </div>
                        <x-input-error for="code" class="mt-2" />
                    </div>
                @endif
            @endif

            @if ($showingRecoveryCodes)
                <div class="mt-5 max-w-xl text-sm opacity-70">
                    <p class="font-semibold">
                        {{ __('app.two_factor.store_codes') }}
                    </p>
                </div>

                {{-- Recovery Codes Glass Card --}}
                <div class="max-w-xl mt-4 p-5 rounded-2xl bg-white/5 backdrop-blur-sm border border-white/10 relative overflow-hidden"
                     x-data="{
                         getCodesFromDom() {
                             return Array.from(this.$refs.codesGrid.querySelectorAll('[data-code]')).map(el => el.dataset.code.trim());
                         },
                         downloadCodes() {
                             const codes = this.getCodesFromDom();
                             let text = '================================\n';
                             text += '  ' + '{{ __('app.two_factor.recovery_codes_title') }}' + '\n';
                             text += '  ' + '{{ __('app.two_factor.generated') }}' + ' ' + new Date().toLocaleDateString() + '\n';
                             text += '================================\n\n';
                             codes.forEach((code, i) => {
                                 text += '  ' + (i + 1) + '. ' + code + '\n';
                             });
                             text += '\n================================\n';
                             text += '  ' + '{{ __('app.two_factor.keep_codes_safe') }}' + '\n';
                             text += '  ' + '{{ __('app.two_factor.code_use_once') }}' + '\n';
                             text += '================================\n';

                             const a = document.createElement('a');
                             a.href = 'data:text/plain;charset=utf-8,' + encodeURIComponent(text);
                             a.download = 'g2fa-recovery-codes-' + new Date().toISOString().slice(0,10) + '.txt';
                             a.style.display = 'none';
                             document.body.appendChild(a);
                             a.click();
                             document.body.removeChild(a);
                         },
                         copyCodes() {
                             const codes = this.getCodesFromDom();
                             navigator.clipboard.writeText(codes.join('\n'));
                             this.$refs.copyBtn.textContent = '{{ __('app.two_factor.copied') }}';
                             setTimeout(() => this.$refs.copyBtn.textContent = '{{ __('app.two_factor.copy_all') }}', 2000);
                         }
                     }">
                    {{-- Decorative gradient --}}
                    <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-bl from-blue-500/10 to-transparent rounded-bl-3xl pointer-events-none"></div>

                    <div class="relative flex items-center justify-between mb-3">
                        <p class="text-xs opacity-50 uppercase tracking-wider font-semibold flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                            </svg>
                            {{ __('app.two_factor.recovery_codes') }}
                        </p>
                        <div class="flex gap-2">
                            <button @click.prevent.stop="copyCodes()" x-ref="copyBtn" type="button"
                                class="text-xs px-3 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 border border-white/10 transition-all duration-200">
                                {{ __('app.two_factor.copy_all') }}
                            </button>
                            <button @click.prevent.stop="downloadCodes()" type="button"
                                class="text-xs px-3 py-1.5 rounded-lg bg-blue-600/80 hover:bg-blue-500 text-white transition-all duration-200 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                </svg>
                                {{ __('app.two_factor.download_txt') }}
                            </button>
                        </div>
                    </div>

                    <div x-ref="codesGrid" class="grid grid-cols-2 gap-2">
                        @foreach (json_decode(decrypt($this->user->two_factor_recovery_codes), true) as $code)
                            <div data-code="{{ $code }}" class="px-3 py-2.5 rounded-lg bg-white/5 border border-white/10 font-mono text-sm tracking-wider text-center select-all hover:bg-white/10 transition-colors">
                                {{ $code }}
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endif

        {{-- Action Buttons --}}
        <div class="flex flex-wrap items-center gap-3 mt-6">
            @if (! $this->enabled)
                <x-confirms-password wire:then="enableTwoFactorAuthentication">
                    <x-button type="button" wire:loading.attr="disabled" class="gap-2" :disabled="! $canEnableTwoFactor">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                        </svg>
                        {{ __('app.two_factor.enable_2fa') }}
                    </x-button>
                </x-confirms-password>
            @else
                @if ($showingRecoveryCodes)
                    <x-confirms-password wire:then="regenerateRecoveryCodes">
                        <x-secondary-button class="gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182" />
                            </svg>
                            {{ __('app.two_factor.regenerate_codes') }}
                        </x-secondary-button>
                    </x-confirms-password>
                @elseif ($showingConfirmation)
                    <x-confirms-password wire:then="confirmTwoFactorAuthentication">
                        <x-button type="button" wire:loading.attr="disabled" class="gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                            {{ __('app.two_factor.confirm_activate') }}
                        </x-button>
                    </x-confirms-password>
                @else
                    <x-confirms-password wire:then="showRecoveryCodes">
                        <x-secondary-button class="gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                            {{ __('app.two_factor.show_recovery_codes') }}
                        </x-secondary-button>
                    </x-confirms-password>
                @endif

                @if ($showingConfirmation)
                    <x-confirms-password wire:then="disableTwoFactorAuthentication">
                        <x-secondary-button wire:loading.attr="disabled">
                            {{ __('app.two_factor.cancel') }}
                        </x-secondary-button>
                    </x-confirms-password>
                @else
                    <x-confirms-password wire:then="disableTwoFactorAuthentication">
                        <x-danger-button wire:loading.attr="disabled" class="gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636" />
                            </svg>
                            {{ __('app.two_factor.disable_2fa') }}
                        </x-danger-button>
                    </x-confirms-password>
                @endif
            @endif
        </div>
    </x-slot>
</x-action-section>
