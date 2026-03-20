@php
    use App\Services\CaptchaService;

    $page = $page ?? 'login';
    $enabled = CaptchaService::enabledFor($page);
    $provider = CaptchaService::provider();
    $siteKey = CaptchaService::siteKey();
@endphp

@if ($enabled && $siteKey)
    <div class="mt-4 captcha-widget-wrap">
        @if ($provider === 'recaptcha')
            <div class="captcha-recaptcha-wrap">
                <div id="g-recaptcha-{{ $page }}" class="g-recaptcha" data-sitekey="{{ $siteKey }}"></div>
            </div>
            @once
                <script src="https://www.google.com/recaptcha/api.js" async defer></script>
            @endonce
        @elseif ($provider === 'turnstile')
            <div class="captcha-turnstile-wrap">
                <div id="cf-turnstile-{{ $page }}" class="cf-turnstile" data-sitekey="{{ $siteKey }}" data-theme="auto" data-size="flexible"></div>
            </div>
            @once
                <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
            @endonce
        @endif

        @error('captcha_token')
            <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span>
        @enderror
    </div>
@endif
