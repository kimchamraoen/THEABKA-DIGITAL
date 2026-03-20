<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Rules\ValidCaptcha;
use App\Services\CaptchaService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        if (!array_key_exists('terms_accepted', $input) && array_key_exists('terms', $input)) {
            $input['terms_accepted'] = $input['terms'];
        }

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'terms_accepted' => ['required', 'accepted'],
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ];

        if (CaptchaService::enabledFor('register')) {
            $rules['captcha_token'] = ['required', new ValidCaptcha];
        }

        // Map provider-specific field to our unified field name
        $input['captcha_token'] = CaptchaService::getTokenFromArray($input);

        Validator::make($input, $rules)->validate();

        $cookieConsent = request()->cookie('cookie_consent', 'pending');
        if (!in_array($cookieConsent, ['accepted', 'declined', 'pending'], true)) {
            $cookieConsent = 'pending';
        }

        $cookieConsentAt = $cookieConsent === 'pending' ? null : now();
        $agreementTimestamp = now();

        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'terms_accepted' => true,
            'terms_accepted_at' => $agreementTimestamp,
            'privacy_accepted' => true,
            'privacy_accepted_at' => $agreementTimestamp,
            'cookie_consent' => $cookieConsent,
            'cookie_consent_at' => $cookieConsentAt,
            'agreement_ip' => request()->ip(),
        ]);
    }
}
