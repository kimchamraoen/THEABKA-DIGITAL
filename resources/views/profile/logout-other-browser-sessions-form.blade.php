<x-action-section>
    <x-slot name="title">
        {{ __('app.profile.browser_sessions') }}
    </x-slot>

    <x-slot name="description">
        {{ __('app.profile.browser_sessions_desc') }}
    </x-slot>

    <x-slot name="content">
        <div class="max-w-xl text-sm opacity-70">
            {{ __('app.profile.browser_sessions_text') }}
        </div>

        @if (count($this->sessions) > 0)
            <div class="mt-5 space-y-6">
                <!-- Other Browser Sessions -->
                @foreach ($this->sessions as $session)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div>
                                @if ($session->agent->isDesktop())
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-8 opacity-50">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25" />
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-8 opacity-50">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                                    </svg>
                                @endif
                            </div>

                            <div class="ms-3">
                                <div class="text-sm opacity-70">
                                    {{ $session->agent->platform() ? $session->agent->platform() : __('app.profile.unknown') }} - {{ $session->agent->browser() ? $session->agent->browser() : __('app.profile.unknown') }}
                                </div>

                                <div>
                                    <div class="text-xs opacity-50">
                                        {{ $session->ip_address }},

                                        @if ($session->is_current_device)
                                            <span class="text-green-500 font-semibold">{{ __('app.profile.this_device') }}</span>
                                        @else
                                            {{ __('app.profile.last_active') }} {{ $session->last_active }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if (! $session->is_current_device)
                            <button wire:click="confirmSingleLogout('{{ $session->id }}')"
                                class="text-xs px-3 py-1.5 rounded-lg bg-red-600/20 hover:bg-red-600/40 text-red-400 border border-red-500/30 transition-all duration-200 flex items-center gap-1.5 shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                                </svg>
                                {{ __('app.nav.log_out') }}
                            </button>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        <div class="flex items-center mt-5">
            <x-button wire:click="confirmLogout" wire:loading.attr="disabled">
                {{ __('app.profile.log_out_sessions') }}
            </x-button>

            <x-action-message class="ms-3" on="loggedOut">
                {{ __('app.profile.done') }}
            </x-action-message>
        </div>

        <!-- Log Out Other Devices Confirmation Modal -->
        <x-dialog-modal wire:model.live="confirmingLogout">
            <x-slot name="title">
                {{ __('app.profile.log_out_sessions') }}
            </x-slot>

            <x-slot name="content">
                {{ __('app.profile.log_out_sessions_confirm') }}

                <div class="mt-4" x-data="{}" x-on:confirming-logout-other-browser-sessions.window="setTimeout(() => $refs.password.focus(), 250)">
                    <x-input type="password" class="mt-1 block w-3/4"
                                autocomplete="current-password"
                                placeholder="{{ __('app.auth.password') }}"
                                x-ref="password"
                                wire:model="password"
                                wire:keydown.enter="logoutOtherBrowserSessions" />

                    <x-input-error for="password" class="mt-2" />
                </div>
            </x-slot>

            <x-slot name="footer">
                <x-secondary-button wire:click="$toggle('confirmingLogout')" wire:loading.attr="disabled">
                    {{ __('app.profile.cancel') }}
                </x-secondary-button>

                <x-button class="ms-3"
                            wire:click="logoutOtherBrowserSessions"
                            wire:loading.attr="disabled">
                    {{ __('app.profile.log_out_sessions') }}
                </x-button>
            </x-slot>
        </x-dialog-modal>

        <!-- Log Out Single Device Confirmation Modal -->
        <x-dialog-modal wire:model.live="confirmingSingleLogout">
            <x-slot name="title">
                {{ __('app.profile.log_out_device') }}
            </x-slot>

            <x-slot name="content">
                {{ __('app.profile.log_out_device_confirm') }}

                <div class="mt-4" x-data="{}" x-on:confirming-single-session-logout.window="setTimeout(() => $refs.single_password.focus(), 250)">
                    <x-input type="password" class="mt-1 block w-3/4"
                                autocomplete="current-password"
                                placeholder="{{ __('app.auth.password') }}"
                                x-ref="single_password"
                                wire:model="singleSessionPassword"
                                wire:keydown.enter="logoutSingleSession" />

                    <x-input-error for="single_session_password" class="mt-2" />
                </div>
            </x-slot>

            <x-slot name="footer">
                <x-secondary-button wire:click="$toggle('confirmingSingleLogout')" wire:loading.attr="disabled">
                    {{ __('app.profile.cancel') }}
                </x-secondary-button>

                <x-danger-button class="ms-3"
                            wire:click="logoutSingleSession"
                            wire:loading.attr="disabled">
                    {{ __('app.profile.log_out_device') }}
                </x-danger-button>
            </x-slot>
        </x-dialog-modal>
    </x-slot>
</x-action-section>
