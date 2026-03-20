@php
    $authSettings = \App\Models\Setting::instance();
    $cardMaxWidth = ($authSettings->auth_card_max_width ?? 448);
    $policyMaxWidth = max($cardMaxWidth, 640);
@endphp
<x-guest-layout>
    <div class="mb-4 pt-4">
        <x-authentication-card-logo />
    </div>

    <div class="w-full mt-6 px-4 sm:px-0 p-8 glass-card rounded-2xl" style="max-width: {{ $policyMaxWidth }}px;">
        <h1 class="text-2xl font-bold mb-6 gradient-text">{{ __('app.legal.privacy_policy') }}</h1>
        <div class="prose prose-invert max-w-none opacity-70 leading-relaxed
                    prose-headings:opacity-100 prose-headings:font-bold
                    prose-a:text-blue-400 prose-a:no-underline hover:prose-a:underline
                    prose-strong:opacity-90">
            {!! $policy !!}
        </div>
    </div>
</x-guest-layout>
