@php
    $authSettings = \App\Models\Setting::instance();
    $cardMaxWidth = ($authSettings->auth_card_max_width ?? 448) . 'px';
    $cardPx = ($authSettings->auth_card_padding_x ?? 32) . 'px';
    $cardPy = ($authSettings->auth_card_padding_y ?? 24) . 'px';
    $cardRadius = ($authSettings->auth_card_border_radius ?? 16) . 'px';
    $cardFontSize = ($authSettings->auth_card_font_size ?? 14) . 'px';
    $cardFontColor = $authSettings->auth_card_font_color ?? '';
    $cardLabelColor = $authSettings->auth_label_color ?? '';
    $cardLinkColor = $authSettings->auth_link_color ?? '';
    $cardBtnBg = $authSettings->auth_btn_bg_color ?? '';
    $cardBtnText = $authSettings->auth_btn_text_color ?? '';
@endphp
{{-- No extra flex wrapper here — the guest layout already provides
     min-h-screen flex-col items-center centering. Keeping the logo
     and card as direct flex children lets w-full resolve against the
     viewport-width parent so max-width actually takes effect. --}}
<div class="mb-4">
    {{ $logo }}
</div>

<div class="w-full mt-6 px-3 sm:px-4 md:px-0 glass-card auth-card auth-card-shell"
     style="max-width: {{ $cardMaxWidth }}; padding: {{ $cardPy }} {{ $cardPx }}; border-radius: {{ $cardRadius }}; font-size: {{ $cardFontSize }};
            {{ $cardFontColor ? 'color: ' . $cardFontColor . ';' : '' }}
            {{ $cardLabelColor ? '--auth-label-color: ' . $cardLabelColor . ';' : '' }}
            {{ $cardLinkColor ? '--auth-link-color: ' . $cardLinkColor . ';' : '' }}
            {{ $cardBtnBg ? '--auth-btn-bg: ' . $cardBtnBg . ';' : '' }}
            {{ $cardBtnText ? '--auth-btn-text: ' . $cardBtnText . ';' : '' }}">
    {{ $slot }}
</div>
