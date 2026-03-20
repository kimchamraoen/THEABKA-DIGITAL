@php
    $settings = \App\Models\Setting::instance();
    $appName = $settings->app_name;
    $logoSize = ($settings->auth_logo_size ?? 48) . 'px';
    $logoIconSize = (($settings->auth_logo_size ?? 48) * 0.6) . 'px';
    $headingColor = $settings->auth_heading_color ?? '';
@endphp
<a href="/" class="flex items-center gap-3">
    @if ($settings->logo_url)
        <img src="{{ $settings->logo_url }}" alt="{{ $appName }}" style="height: {{ $logoSize }};" class="w-auto rounded-xl shadow-lg">
    @else
        <div class="rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-blue-500/30"
             style="width: {{ $logoSize }}; height: {{ $logoSize }};">
            <svg style="width: {{ $logoIconSize }}; height: {{ $logoIconSize }};" class="text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
            </svg>
        </div>
    @endif
    <span class="text-2xl font-bold bg-clip-text text-transparent"
          style="background-image: linear-gradient(to right, {{ $headingColor ?: '#60a5fa' }}, {{ $headingColor ?: '#818cf8' }});
                 -webkit-background-clip: text;">
        {{ $appName }}
    </span>
</a>
