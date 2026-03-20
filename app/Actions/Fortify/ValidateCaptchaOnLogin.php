<?php

namespace App\Actions\Fortify;

use App\Rules\ValidCaptcha;
use App\Services\CaptchaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ValidateCaptchaOnLogin
{
    public function handle(Request $request, \Closure $next)
    {
        if (CaptchaService::enabledFor('login')) {
            $token = CaptchaService::getTokenFromRequest($request);

            Validator::make(
                ['captcha_token' => $token],
                ['captcha_token' => ['required', new ValidCaptcha]]
            )->validate();
        }

        return $next($request);
    }
}
