<?php

namespace App\Providers;

use App\Actions\Fortify\EnableTwoFactorAuthentication;
use App\Actions\Jetstream\DeleteUser;
use App\Livewire\LogoutBrowserSessionsForm;
use App\Livewire\Profile\TwoFactorAuthenticationForm;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Laravel\Jetstream\Jetstream;
use Livewire\Livewire;

class JetstreamServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \Laravel\Fortify\Actions\EnableTwoFactorAuthentication::class,
            EnableTwoFactorAuthentication::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configurePermissions();

        Jetstream::deleteUsersUsing(DeleteUser::class);

        Vite::prefetch(concurrency: 3);

        Livewire::component('profile.logout-other-browser-sessions-form', LogoutBrowserSessionsForm::class);
        Livewire::component('profile.two-factor-authentication-form', TwoFactorAuthenticationForm::class);
    }

    /**
     * Configure the permissions that are available within the application.
     */
    protected function configurePermissions(): void
    {
        Jetstream::defaultApiTokenPermissions(['read']);

        Jetstream::permissions([
            'create',
            'read',
            'update',
            'delete',
        ]);
    }
}
