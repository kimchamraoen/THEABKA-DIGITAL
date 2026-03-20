<?php

namespace App\Livewire\Profile;

use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Actions\EnableTwoFactorAuthentication;
use Laravel\Fortify\Features;

class TwoFactorAuthenticationForm extends \Laravel\Jetstream\Http\Livewire\TwoFactorAuthenticationForm
{
    /**
     * Enable two factor authentication only when email prerequisites are met.
     */
    public function enableTwoFactorAuthentication(EnableTwoFactorAuthentication $enable): void
    {
        if (blank(Auth::user()->email)) {
            session()->flash('error', __('You must add an email address to your account before enabling 2FA.'));
            return;
        }

        if (is_null(Auth::user()->email_verified_at)) {
            session()->flash('error', __('You must verify your email address before enabling 2FA.'));
            return;
        }

        if (Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword')) {
            $this->ensurePasswordIsConfirmed();
        }

        $enable(Auth::user());

        $this->showingQrCode = true;

        if (Features::optionEnabled(Features::twoFactorAuthentication(), 'confirm')) {
            $this->showingConfirmation = true;
        } else {
            $this->showingRecoveryCodes = true;
        }
    }
}
