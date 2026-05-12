<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Page Title --}}
            <div class="mb-6 gsap-fade-up">
                <h1 class="text-3xl font-bold">{{ __('app.profile.title') }}</h1>
                <p class="text-sm opacity-60 mt-1">{{ __('app.profile.manage_account') }}</p>
            </div>
            @if (Laravel\Fortify\Features::canUpdateProfileInformation())
                @livewire('profile.update-profile-information-form')

                <x-section-border />
            @endif

            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
                <div class="mt-10 sm:mt-0">
                    @livewire('profile.update-password-form')
                </div>

                <x-section-border />
            @endif

            @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                <div class="mt-10 sm:mt-0">
                    @livewire('profile.two-factor-authentication-form')
                </div>

                <x-section-border />
            @endif

            <div class="mt-10 sm:mt-0">
                @livewire('profile.logout-other-browser-sessions-form')
            </div>

            <x-section-border />

            {{-- Connected Accounts --}}
            <div class="mt-10 sm:mt-0">
                @livewire('connected-accounts')
            </div>

            @if (auth()->user()?->terms_accepted)
                <x-section-border />

                <div class="mt-10 sm:mt-0">
                    <div class="glass-card rounded-2xl p-6">
                        <h2 class="text-lg font-semibold">Legal Agreement</h2>
                        <p class="mt-2 text-sm opacity-80">
                            You agreed to our Terms &amp; Conditions on
                            {{ auth()->user()?->terms_accepted_at ? auth()->user()->terms_accepted_at->format('F d, Y H:i:s') : 'an unknown date' }}
                            from IP {{ auth()->user()?->agreement_ip ?: 'unknown' }}.
                        </p>
                    </div>
                </div>
            @endif

            @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
                <x-section-border />

                <div class="mt-10 sm:mt-0">
                    @livewire('profile.delete-user-form')
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
