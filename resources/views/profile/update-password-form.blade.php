<x-form-section submit="updatePassword">
    <x-slot name="title">
        {{ __('app.profile.update_password') }}
    </x-slot>

    <x-slot name="description">
        {{ __('app.profile.update_password_desc') }}
    </x-slot>

    <x-slot name="form">
        @php
            $isSocialUser = auth()->user()->socialAccounts()->exists();
            $socialProviders = $isSocialUser ? auth()->user()->socialAccounts->pluck('provider')->map(fn($p) => $p === 'twitter' ? 'X' : ucfirst($p))->implode(', ') : '';
            $hasPassword = filled(auth()->user()->password);
        @endphp

        {{-- Show current password field only for non-social users --}}
        @if (!$isSocialUser)
            <div class="col-span-6 sm:col-span-4">
                <x-label for="current_password" value="{{ __('app.profile.current_password') }}" />
                <x-input id="current_password" type="password" class="mt-1 block w-full" wire:model="state.current_password" autocomplete="current-password" />
                <x-input-error for="current_password" class="mt-2" />
            </div>
        @else
            <div class="col-span-6 sm:col-span-4">
                @if (! $hasPassword)
                    <div class="p-3 rounded-xl bg-blue-500/10 border border-blue-500/20 text-sm">
                        <p class="opacity-80">
                            {{ __('You signed up with') }} <strong>{{ $socialProviders }}</strong>.
                            {{ __('Set a password to also login with email/password.') }}
                        </p>
                    </div>
                @else
                    <div class="p-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-sm">
                        <p class="text-emerald-300">
                            ✅ Password is set. You can login with email/password in addition to your {{ $socialProviders }} account.
                        </p>
                    </div>
                @endif
            </div>
        @endif

        <div class="col-span-6 sm:col-span-4">
            <x-label for="password" value="{{ __('app.profile.new_password') }}" />
            <x-input id="password" type="password" class="mt-1 block w-full" wire:model="state.password" autocomplete="new-password" />
            <x-input-error for="password" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-label for="password_confirmation" value="{{ __('app.auth.confirm_password') }}" />
            <x-input id="password_confirmation" type="password" class="mt-1 block w-full" wire:model="state.password_confirmation" autocomplete="new-password" />
            <x-input-error for="password_confirmation" class="mt-2" />
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-action-message class="me-3" on="saved">
            {{ __('app.profile.saved') }}
        </x-action-message>

        <x-button>
            {{ __('app.profile.save') }}
        </x-button>
    </x-slot>
</x-form-section>
