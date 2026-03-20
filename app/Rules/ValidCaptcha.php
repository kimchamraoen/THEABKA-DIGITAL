<?php

namespace App\Rules;

use App\Services\CaptchaService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidCaptcha implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!CaptchaService::verify($value)) {
            $fail('CAPTCHA verification failed. Please try again.');
        }
    }
}
