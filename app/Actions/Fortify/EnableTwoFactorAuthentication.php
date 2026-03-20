<?php

namespace App\Actions\Fortify;

use Illuminate\Validation\ValidationException;

class EnableTwoFactorAuthentication extends \Laravel\Fortify\Actions\EnableTwoFactorAuthentication
{
    /**
     * Ensure account recovery prerequisites before allowing 2FA enrollment.
     *
     * @param  mixed  $user
     */
    public function __invoke($user, $force = false): void
    {
        if (blank($user->email)) {
            throw ValidationException::withMessages([
                'two_factor' => __('You must add an email address to your account before enabling 2FA.'),
            ]);
        }

        if (is_null($user->email_verified_at)) {
            throw ValidationException::withMessages([
                'two_factor' => __('You must verify your email address before enabling 2FA.'),
            ]);
        }

        parent::__invoke($user, $force);
    }
}
