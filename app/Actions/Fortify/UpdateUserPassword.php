<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\UpdatesUserPasswords;

class UpdateUserPassword implements UpdatesUserPasswords
{
    use PasswordValidationRules;

    /**
     * Validate and update the user's password.
     *
     * @param  array<string, string>  $input
     *
     * @throws ValidationException
     */
    public function update(User $user, array $input): void
    {
        // Social login users (Google, Facebook, Telegram, X) don't know their random password
        // so we skip current password validation for them
        $isSocialUser = $user->socialAccounts()->exists();

        $rules = $isSocialUser
            ? ['password' => $this->passwordRules()]
            : [
                'current_password' => ['required', 'string', 'current_password:web'],
                'password' => $this->passwordRules(),
            ];

        Validator::make($input, $rules, [
            'current_password.current_password' => __('The provided password does not match your current password.'),
        ])->validateWithBag('updatePassword');

        $user->forceFill([
            'password' => Hash::make($input['password']),
        ])->save();
    }
}
