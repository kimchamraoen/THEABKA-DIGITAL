<x-form-section submit="updateProfileInformation">
    <x-slot name="title">
        {{ __('app.profile.profile_information') }}
    </x-slot>

    <x-slot name="description">
        {{ __('app.profile.profile_information_desc') }}
    </x-slot>

    <x-slot name="form">
        <!-- Profile Photo / Avatar -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="photo" value="{{ __('app.profile.photo') }}" />

            <div style="display: flex; align-items: center; gap: 16px; margin-top: 8px;">
                <img src="{{ $this->user->profile_image }}"
                     alt="{{ $this->user->name }}"
                     style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 2px solid rgba(255,255,255,0.1);">
                <div>
                    @if ($this->user->profile_photo_path)
                        <p style="font-size: 13px; opacity: 0.7; margin: 0;">
                            {{ __('app.profile.using_uploaded_photo') }}
                        </p>
                    @elseif ($this->user->avatar_provider)
                        <p style="font-size: 13px; opacity: 0.7; margin: 0;">
                            {{ __('app.profile.avatar_synced_from_social') }}
                            ({{ $this->user->avatar_provider === 'twitter' ? 'X' : ucfirst($this->user->avatar_provider) }})
                        </p>
                    @elseif ($this->user->avatar)
                        <p style="font-size: 13px; opacity: 0.7; margin: 0;">
                            {{ __('app.profile.avatar_synced_from_social') }}
                        </p>
                    @else
                        <p style="font-size: 13px; opacity: 0.7; margin: 0;">
                            {{ __('app.profile.avatar_initials_used') }}
                        </p>
                    @endif
                </div>
            </div>

            @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                <div x-data="{photoName: null, photoPreview: null}" style="margin-top: 12px;">
                    <!-- Profile Photo File Input -->
                    <input type="file" id="photo" class="hidden"
                                wire:model.live="photo"
                                x-ref="photo"
                                x-on:change="
                                        photoName = $refs.photo.files[0].name;
                                        const reader = new FileReader();
                                        reader.onload = (e) => {
                                            photoPreview = e.target.result;
                                        };
                                        reader.readAsDataURL($refs.photo.files[0]);
                                " />

                    <!-- New Profile Photo Preview -->
                    <div x-show="photoPreview" style="display: none; margin-bottom: 8px;">
                        <span class="block rounded-xl size-20 bg-cover bg-no-repeat bg-center ring-2 ring-blue-500/30"
                              x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                        </span>
                    </div>

                    <div style="display: flex; align-items: center; gap: 8px;">
                        <button type="button" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white/10 hover:bg-white/15 border border-white/10 text-xs font-medium transition" x-on:click.prevent="$refs.photo.click()">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" /></svg>
                            {{ __('app.profile.upload_photo') }}
                        </button>

                        @if ($this->user->profile_photo_path)
                            <button type="button" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-red-500/10 hover:bg-red-500/20 border border-red-500/15 text-red-400 text-xs font-medium transition" wire:click="deleteProfilePhoto">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                                {{ __('app.profile.remove') }}
                            </button>
                        @endif
                    </div>

                    <x-input-error for="photo" class="mt-2" />
                </div>
            @endif
        </div>

        <!-- Name -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="name" value="{{ __('app.auth.name') }}" />
            <x-input id="name" type="text" class="mt-1 block w-full" wire:model="state.name" required autocomplete="name" />
            <x-input-error for="name" class="mt-2" />
        </div>

        <!-- Email -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="email" value="{{ __('app.auth.email') }}" />
            <x-input id="email" type="email" class="mt-1 block w-full" wire:model="state.email" required autocomplete="username" />
            <x-input-error for="email" class="mt-2" />

            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::emailVerification()) && ! $this->user->hasVerifiedEmail())
                <div class="mt-3 p-3 rounded-xl bg-amber-500/10 border border-amber-500/20">
                    <div class="flex items-start gap-2.5">
                        <svg class="w-5 h-5 text-amber-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                        </svg>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-amber-400">{{ __('app.profile.email_not_verified') }}</p>
                            <p class="text-xs opacity-60 mt-0.5">{{ __('app.profile.email_unverified_text') }}</p>
                            <button type="button" class="mt-2 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-500/15 hover:bg-amber-500/25 text-amber-400 text-xs font-medium transition" wire:click.prevent="sendEmailVerification">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" /></svg>
                                {{ __('app.profile.send_verification') }}
                            </button>
                            @if ($this->verificationLinkSent)
                                <p class="mt-2 text-xs text-emerald-400 font-medium">
                                    {{ __('app.profile.verification_sent') }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            @elseif (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::emailVerification()) && $this->user->hasVerifiedEmail())
                <div class="mt-2 flex items-center gap-1.5 text-emerald-400 text-xs font-medium">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                    {{ __('app.profile.email_verified') }}
                </div>
            @endif
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-action-message class="me-3" on="saved">
            {{ __('app.profile.saved') }}
        </x-action-message>

        <x-button wire:loading.attr="disabled" wire:target="photo">
            {{ __('app.profile.save') }}
        </x-button>
    </x-slot>
</x-form-section>
