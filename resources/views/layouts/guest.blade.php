@php
    $settings = \App\Models\Setting::instance();
    $theme = $settings->default_theme;
    $glassStyle = auth()->check() ? auth()->user()->getEffectiveGlassStyle() : ($settings->default_glass_style ?? 'liquid');
    $fontConfig = $settings->resolveLocaleFontConfig(app()->getLocale());
    $bodyFontFamily = $fontConfig['bodyFontFamily'];
    $fontUrl = $fontConfig['fontUrl'];
    $localeCustomFontName = $fontConfig['customFontName'];
    $localeCustomFontUrl = $fontConfig['customFontUrl'];
    $activeTheme = \App\Models\Theme::active();
    $appName = $settings->app_name;

    // Determine background image URL
    $authBgImageUrl = null;
    if ($settings->auth_bg_type === 'image' && $settings->auth_bg_image) {
        $authBgImageUrl = asset('storage/' . $settings->auth_bg_image);
    }
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{ $theme }}" style="background-color: {{ $theme === 'dark' ? '#0f172a' : '#f1f5f9' }}" data-glass-style="{{ $glassStyle }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $appName }}</title>

        @if ($settings->favicon_url)
            <link rel="icon" href="{{ $settings->favicon_url }}" type="image/png">
        @endif

        <!-- NoScript fallback: show content if JS is disabled -->
        <noscript><style>body{opacity:1!important}</style></noscript>

        <!-- Dynamic Google Font -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="{{ $fontUrl }}" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet" />

        <!-- GSAP -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js" defer></script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @if ($authBgImageUrl)
            <link rel="preload" as="image" href="{{ $authBgImageUrl }}">
        @endif

        {{-- Critical inline styles to prevent FOUC --}}
        <style>
            :root {
                {!! $activeTheme->getCssVariables($glassStyle) !!}
                --app-font: {{ $bodyFontFamily }};
                --font-sans: {{ $bodyFontFamily }};
                --default-font-family: {{ $bodyFontFamily }};
            }
            @if ($localeCustomFontName && $localeCustomFontUrl)
            @font-face {
                font-family: '{{ $localeCustomFontName }}';
                src: url('{{ $localeCustomFontUrl }}') format('woff2'),
                     url('{{ $localeCustomFontUrl }}') format('woff'),
                     url('{{ $localeCustomFontUrl }}') format('truetype');
                font-weight: normal;
                font-style: normal;
            }
            @endif
            body, html { font-family: {{ $bodyFontFamily }} !important; }
            body {
                opacity: 0;
                transition: opacity 0.15s ease-in;
            }
            .glass-card {
                position: relative;
                isolation: isolate;
                overflow: clip;
            }
            /* Background: start with color, image set via JS after decode */
            @if ($authBgImageUrl)
                html {
                    background-color: #0f172a;
                    background-size: cover;
                    background-position: center;
                    background-repeat: no-repeat;
                    background-attachment: fixed;
                }
            @else
                html { background: {{ $theme === 'dark' ? $activeTheme->getDarkGradient() : $activeTheme->getLightGradient() }}; }
            @endif
            [x-cloak] { display: none !important; }
            {!! $activeTheme->custom_css ?? '' !!}
        </style>

        <!-- Styles -->
        @livewireStyles

        <!-- Reveal page once background is ready -->
        <script>
        (function(){
            var bgUrl = @json($authBgImageUrl ?? null);
            var revealed = false;
            function reveal() {
                if (revealed) return;
                revealed = true;
                window.__fouc_revealed = true;
                document.body.style.opacity = '1';
            }
            if (bgUrl) {
                var img = new Image();
                img.src = bgUrl;
                var setAndReveal = function() {
                    document.documentElement.style.backgroundImage = 'url(' + bgUrl + ')';
                    requestAnimationFrame(function() {
                        requestAnimationFrame(reveal);
                    });
                };
                if (img.decode) {
                    img.decode().then(setAndReveal).catch(setAndReveal);
                } else {
                    img.onload = setAndReveal;
                    img.onerror = setAndReveal;
                }
                setTimeout(function() { setAndReveal(); }, 2000);
            } else {
                requestAnimationFrame(reveal);
            }
        })();
        </script>
    </head>
    <body class="antialiased overflow-x-hidden auth-page {{ $theme === 'dark' ? 'text-blue-100' : 'text-slate-700' }}">
        <x-glass-filters />

        {{-- Background Layer --}}
        @if ($settings->auth_bg_type === 'video' && ($settings->auth_bg_video_file || $settings->auth_bg_video))
            <video autoplay muted loop playsinline class="fixed inset-0 w-full h-full object-cover -z-20">
                @if ($settings->auth_bg_video_file)
                    <source src="{{ asset('storage/' . $settings->auth_bg_video_file) }}" type="video/mp4">
                @else
                    <source src="{{ $settings->auth_bg_video }}" type="video/mp4">
                @endif
            </video>
            <div class="fixed inset-0 -z-10" style="background: {{ $activeTheme->bg_overlay_color }}; opacity: {{ $activeTheme->bg_overlay_opacity }}"></div>
        @elseif ($settings->auth_bg_type === 'image' && $settings->auth_bg_image)
            <div class="fixed inset-0 -z-20 bg-cover bg-center bg-no-repeat" style="background-image: url('{{ asset('storage/' . $settings->auth_bg_image) }}')"></div>
            <div class="fixed inset-0 -z-10" style="background: rgba(0,0,0,0.5)"></div>
        @else
            <div class="fixed inset-0 -z-20"
                 style="background: {{ $theme === 'dark' ? $activeTheme->getDarkGradient() : $activeTheme->getLightGradient() }}">
            </div>
        @endif

        {{-- Decorative Blobs (hidden when custom background is set) --}}
        @if ($activeTheme->blobs_enabled && $settings->auth_bg_type === 'gradient')
        <div class="fixed inset-0 -z-5 overflow-hidden pointer-events-none">
            <div class="blob blob-sm blob-1"></div>
            <div class="blob blob-sm blob-2"></div>
            <div class="blob blob-sm blob-3"></div>
            <div class="blob blob-sm blob-4"></div>
        </div>
        @endif

        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 relative z-10">
            {{ $slot }}
        </div>

        @include('partials.cookie-banner')

        @livewireScripts

        {{-- GSAP Auth Animations --}}
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                if (typeof gsap === 'undefined') return;

                // Ensure all glass cards are visible
                document.querySelectorAll('.glass-card').forEach(card => {
                    gsap.set(card, { opacity: 1, y: 0, scale: 1 });
                });

                // Animate blobs
                document.querySelectorAll('.blob').forEach((blob, i) => {
                    gsap.fromTo(blob, {
                        opacity: 0,
                        scale: 0.3
                    }, {
                        opacity: 0.6,
                        scale: 1,
                        duration: 1.5,
                        delay: i * 0.2,
                        ease: 'power2.out'
                    });
                });
            });
        </script>
    </body>
</html>
