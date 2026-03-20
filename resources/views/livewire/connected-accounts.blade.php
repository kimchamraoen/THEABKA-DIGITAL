<x-action-section>
    <x-slot name="title">
        {{ __('app.profile.connected_accounts') }}
    </x-slot>

    <x-slot name="description">
        {{ __('app.profile.connected_accounts_desc') }}
    </x-slot>

    <x-slot name="content">
        @if (session()->has('connected_accounts_status'))
            <div style="margin-bottom: 16px; padding: 12px 16px; border-radius: 12px; background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.2); font-size: 13px; color: #34d399;">
                {{ session('connected_accounts_status') }}
            </div>
        @endif

        @if (session()->has('connected_accounts_error'))
            <div style="margin-bottom: 16px; padding: 12px 16px; border-radius: 12px; background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.2); font-size: 13px; color: #f87171;">
                {{ session('connected_accounts_error') }}
            </div>
        @endif

        {{-- Avatar Source Info --}}
        <div style="margin-bottom: 16px; padding: 12px 16px; border-radius: 12px; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06);">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <img src="{{ $user->profile_image }}" alt="{{ $user->name }}"
                         style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid rgba(255,255,255,0.1);">
                    <div>
                        <p style="font-size: 13px; margin: 0; opacity: 0.7;">
                            {{ __('Profile picture from:') }}
                            <strong style="opacity: 1;">
                                @if ($user->profile_photo_path)
                                    {{ __('Uploaded') }}
                                @elseif ($user->avatar_provider)
                                    {{ $user->avatar_provider === 'twitter' ? 'X (Twitter)' : ucfirst($user->avatar_provider) }}
                                @elseif ($user->avatar)
                                    {{ __('Social') }}
                                @else
                                    {{ __('Default') }}
                                @endif
                            </strong>
                        </p>
                    </div>
                </div>
                @if (count($connectedProviders) > 0)
                    <button wire:click="openAvatarPicker"
                            style="padding: 4px 12px; font-size: 12px; font-weight: 500; border-radius: 8px; border: 1px solid rgba(59,130,246,0.3); background: rgba(59,130,246,0.1); color: #60a5fa; cursor: pointer; transition: all 0.2s;"
                            onmouseover="this.style.background='rgba(59,130,246,0.2)'"
                            onmouseout="this.style.background='rgba(59,130,246,0.1)'">
                        {{ __('Change') }}
                    </button>
                @endif
            </div>
        </div>

        {{-- New Social Avatar Prompt (shown after connecting a new provider) --}}
        @if (session()->has('new_social_avatar'))
            @php $newAvatar = session('new_social_avatar'); @endphp
            <div style="margin-bottom: 16px; padding: 16px; border-radius: 12px; background: rgba(59,130,246,0.08); border: 1px solid rgba(59,130,246,0.2);">
                <p style="font-size: 14px; font-weight: 600; margin: 0 0 12px 0;">
                    {{ __('Would you like to update your profile picture to the one from :provider?', ['provider' => $newAvatar['provider'] === 'twitter' ? 'X (Twitter)' : ucfirst($newAvatar['provider'])]) }}
                </p>
                <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 12px;">
                    <div style="text-align: center;">
                        <img src="{{ $user->profile_image }}" alt="Current"
                             style="width: 64px; height: 64px; border-radius: 50%; object-fit: cover; border: 2px solid rgba(255,255,255,0.1);">
                        <p style="font-size: 11px; margin: 4px 0 0; opacity: 0.6;">{{ __('Current') }}</p>
                    </div>
                    <span style="font-size: 20px; opacity: 0.4;">→</span>
                    <div style="text-align: center;">
                        <img src="{{ $newAvatar['avatar_url'] }}" alt="New"
                             style="width: 64px; height: 64px; border-radius: 50%; object-fit: cover; border: 2px solid rgba(59,130,246,0.3);">
                        <p style="font-size: 11px; margin: 4px 0 0; opacity: 0.6;">{{ $newAvatar['provider'] === 'twitter' ? 'X' : ucfirst($newAvatar['provider']) }}</p>
                    </div>
                </div>
                <div style="display: flex; gap: 8px;">
                    <button wire:click="acceptNewAvatar('{{ $newAvatar['provider'] }}', '{{ $newAvatar['avatar_url'] }}')"
                            style="padding: 6px 14px; font-size: 12px; font-weight: 500; border-radius: 8px; border: 1px solid rgba(59,130,246,0.3); background: rgba(59,130,246,0.15); color: #60a5fa; cursor: pointer; transition: all 0.2s;"
                            onmouseover="this.style.background='rgba(59,130,246,0.25)'"
                            onmouseout="this.style.background='rgba(59,130,246,0.15)'">
                        {{ __('Use :provider photo', ['provider' => $newAvatar['provider'] === 'twitter' ? 'X' : ucfirst($newAvatar['provider'])]) }}
                    </button>
                    <button onclick="this.closest('[style*=margin-bottom]').remove()"
                            style="padding: 6px 14px; font-size: 12px; font-weight: 500; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.05); color: inherit; cursor: pointer; opacity: 0.7; transition: all 0.2s;"
                            onmouseover="this.style.opacity='1'"
                            onmouseout="this.style.opacity='0.7'">
                        {{ __('Keep current photo') }}
                    </button>
                </div>
            </div>
        @endif

        {{-- Provider List --}}
        <div style="display: flex; flex-direction: column; gap: 12px;">
            @foreach ($providers as $provider)
                @php $isConnected = in_array($provider, $connectedProviders); @endphp
                <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; border-radius: 12px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.08);">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        {{-- Provider Icon --}}
                        <div style="width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center;
                            @if ($provider === 'google') background: rgba(234,67,53,0.15);
                            @elseif ($provider === 'facebook') background: rgba(24,119,242,0.15);
                            @elseif ($provider === 'twitter') background: rgba(29,161,242,0.15);
                            @elseif ($provider === 'telegram') background: rgba(0,136,204,0.15);
                            @endif">
                            @if ($provider === 'google')
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/>
                                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                                </svg>
                            @elseif ($provider === 'facebook')
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="#1877F2">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                            @elseif ($provider === 'twitter')
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="#1DA1F2">
                                    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                                </svg>
                            @elseif ($provider === 'telegram')
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="#0088CC">
                                    <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.479.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>
                                </svg>
                            @endif
                        </div>

                        <div>
                            <p style="font-size: 14px; font-weight: 600; margin: 0; text-transform: capitalize;">
                                {{ $provider === 'twitter' ? 'X (Twitter)' : ucfirst($provider) }}
                            </p>
                            @if ($isConnected)
                                <p style="font-size: 12px; color: #34d399; margin: 2px 0 0 0;">
                                    {{ __('app.profile.connected') }}
                                </p>
                            @else
                                <p style="font-size: 12px; opacity: 0.5; margin: 2px 0 0 0;">
                                    {{ __('app.profile.not_connected') }}
                                </p>
                            @endif
                        </div>
                    </div>

                    <div style="display: flex; align-items: center; gap: 8px;">
                        @if ($isConnected)
                            <button wire:click="confirmDisconnect('{{ $provider }}')"
                                    style="padding: 6px 14px; font-size: 12px; font-weight: 500; border-radius: 8px; border: 1px solid rgba(239,68,68,0.3); background: rgba(239,68,68,0.1); color: #f87171; cursor: pointer; transition: all 0.2s;"
                                    onmouseover="this.style.background='rgba(239,68,68,0.2)'"
                                    onmouseout="this.style.background='rgba(239,68,68,0.1)'">
                                {{ __('app.profile.disconnect') }}
                            </button>
                        @else
                            @if ($provider === 'telegram')
                                {{-- Telegram uses its own widget, so we just show a link to the login page --}}
                                <a href="{{ route('login') }}"
                                   style="padding: 6px 14px; font-size: 12px; font-weight: 500; border-radius: 8px; border: 1px solid rgba(59,130,246,0.3); background: rgba(59,130,246,0.1); color: #60a5fa; cursor: pointer; transition: all 0.2s; text-decoration: none;"
                                   onmouseover="this.style.background='rgba(59,130,246,0.2)'"
                                   onmouseout="this.style.background='rgba(59,130,246,0.1)'">
                                    {{ __('Connect') }}
                                </a>
                            @else
                                <a href="{{ route('social.redirect', $provider) }}"
                                   style="padding: 6px 14px; font-size: 12px; font-weight: 500; border-radius: 8px; border: 1px solid rgba(59,130,246,0.3); background: rgba(59,130,246,0.1); color: #60a5fa; cursor: pointer; transition: all 0.2s; text-decoration: none;"
                                   onmouseover="this.style.background='rgba(59,130,246,0.2)'"
                                   onmouseout="this.style.background='rgba(59,130,246,0.1)'">
                                    {{ __('Connect') }}
                                </a>
                            @endif
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Disconnect Confirmation Modal --}}
        <x-dialog-modal wire:model.live="confirmingDisconnect">
            <x-slot name="title">
                {{ __('app.profile.disconnect_provider') }}
            </x-slot>

            <x-slot name="content">
                <p>{{ __('app.profile.disconnect_confirm', ['provider' => $disconnectingProvider === 'twitter' ? 'X (Twitter)' : ucfirst($disconnectingProvider)]) }}</p>

                @if ($needsPassword)
                    <div style="margin-top: 16px; padding: 12px 16px; border-radius: 12px; background: rgba(251,191,36,0.1); border: 1px solid rgba(251,191,36,0.2);">
                        <p style="font-size: 13px; color: #fbbf24; margin: 0;">
                            {{ __('app.profile.set_password_before_disconnect') }}
                        </p>
                    </div>

                    <div style="margin-top: 12px;">
                        <x-label for="disconnect-password" value="{{ __('app.profile.new_password') }}" />
                        <x-input id="disconnect-password" type="password" style="margin-top: 4px; width: 75%;"
                                 wire:model="password"
                                 wire:keydown.enter="disconnectProvider"
                                 placeholder="{{ __('app.profile.new_password') }}" />
                        <x-input-error for="password" class="mt-2" />
                    </div>
                @endif
            </x-slot>

            <x-slot name="footer">
                <x-secondary-button wire:click="$toggle('confirmingDisconnect')" wire:loading.attr="disabled">
                    {{ __('app.profile.cancel') }}
                </x-secondary-button>

                <x-danger-button class="ms-3" wire:click="disconnectProvider" wire:loading.attr="disabled">
                    {{ __('app.profile.disconnect') }}
                </x-danger-button>
            </x-slot>
        </x-dialog-modal>

        {{-- Avatar Picker Modal --}}
        <x-dialog-modal wire:model.live="showAvatarChoiceModal">
            <x-slot name="title">
                {{ __('Choose Profile Picture') }}
            </x-slot>

            <x-slot name="content">
                <p style="font-size: 13px; opacity: 0.7; margin: 0 0 16px 0;">
                    {{ __('Select which social account photo to use as your profile picture, or use the default avatar.') }}
                </p>

                <div style="display: flex; flex-wrap: wrap; gap: 16px; justify-content: center;">
                    @foreach ($avatarOptions as $option)
                        <div style="text-align: center; cursor: pointer;"
                             wire:click="setAvatarFrom('{{ $option['provider'] }}')"
                             onmouseover="this.querySelector('img').style.borderColor='rgba(59,130,246,0.6)'"
                             onmouseout="this.querySelector('img').style.borderColor='{{ $user->avatar_provider === $option['provider'] ? 'rgba(16,185,129,0.6)' : 'rgba(255,255,255,0.1)' }}'">
                            <img src="{{ $option['avatar'] }}"
                                 alt="{{ $option['provider'] }}"
                                 style="width: 72px; height: 72px; border-radius: 50%; object-fit: cover; border: 3px solid {{ $user->avatar_provider === $option['provider'] ? 'rgba(16,185,129,0.6)' : 'rgba(255,255,255,0.1)' }}; transition: border-color 0.2s;">
                            <p style="font-size: 11px; margin: 6px 0 0; text-transform: capitalize;">
                                {{ $option['provider'] === 'twitter' ? 'X' : ucfirst($option['provider']) }}
                                @if ($user->avatar_provider === $option['provider'])
                                    <span style="color: #34d399;">✓</span>
                                @endif
                            </p>
                        </div>
                    @endforeach

                    {{-- Default avatar option --}}
                    <div style="text-align: center; cursor: pointer;"
                         wire:click="useDefaultAvatar"
                         onmouseover="this.querySelector('img').style.borderColor='rgba(59,130,246,0.6)'"
                         onmouseout="this.querySelector('img').style.borderColor='{{ !$user->avatar_provider && !$user->avatar ? 'rgba(16,185,129,0.6)' : 'rgba(255,255,255,0.1)' }}'">
                        @php $defaultName = urlencode($user->name ?? 'User'); @endphp
                        <img src="https://ui-avatars.com/api/?name={{ $defaultName }}&background=random"
                             alt="Default"
                             style="width: 72px; height: 72px; border-radius: 50%; object-fit: cover; border: 3px solid {{ !$user->avatar_provider && !$user->avatar ? 'rgba(16,185,129,0.6)' : 'rgba(255,255,255,0.1)' }}; transition: border-color 0.2s;">
                        <p style="font-size: 11px; margin: 6px 0 0;">
                            {{ __('Default') }}
                            @if (!$user->avatar_provider && !$user->avatar)
                                <span style="color: #34d399;">✓</span>
                            @endif
                        </p>
                    </div>
                </div>
            </x-slot>

            <x-slot name="footer">
                <x-secondary-button wire:click="$toggle('showAvatarChoiceModal')" wire:loading.attr="disabled">
                    {{ __('app.profile.cancel') }}
                </x-secondary-button>
            </x-slot>
        </x-dialog-modal>
    </x-slot>
</x-action-section>
