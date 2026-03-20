@php
    $settings = \App\Models\Setting::instance();
    $theme = auth()->check() ? auth()->user()->getEffectiveTheme() : $settings->default_theme;
    $glassStyle = auth()->check() ? auth()->user()->getEffectiveGlassStyle() : ($settings->default_glass_style ?? 'liquid');
    $activeTheme = \App\Models\Theme::active();
    $appName = $settings->app_name;
    $fontConfig = $settings->resolveLocaleFontConfig(app()->getLocale());
    $bodyFontFamily = $fontConfig['bodyFontFamily'];
    $localeGoogleFontUrl = $fontConfig['fontUrl'];
    $localeCustomFontName = $fontConfig['customFontName'];
    $localeCustomFontUrl = $fontConfig['customFontUrl'];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{ $theme }}" style="background-color: {{ $theme === 'light' ? '#f1f5f9' : '#0f172a' }}" x-data="{ theme: '{{ $theme }}', glassStyle: '{{ $glassStyle }}' }"
      x-on:theme-changed.window="theme = $event.detail.theme"
    x-on:theme-default-changed.window="theme = $event.detail.theme"
      x-on:glass-style-changed.window="glassStyle = $event.detail.glassStyle"
    x-on:glass-preview-updated.window="Object.entries($event.detail.vars || {}).forEach(([key, value]) => document.documentElement.style.setProperty(key, value))"
    x-on:gradient-preview-updated.window="(() => {
        const bg = document.querySelector('[data-gradient-bg]');
        if (bg) {
            const isDark = document.documentElement.classList.contains('dark') || !document.documentElement.classList.contains('light');
            bg.style.background = isDark ? ($event.detail.darkGradient || '') : ($event.detail.lightGradient || '');
        }
    })()"
      :data-glass-style="glassStyle"
      data-glass-style="{{ $glassStyle }}"
      :class="theme">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@hasSection('title')@yield('title') — {{ $appName }}@else{{ $appName }}@endif</title>

        @if ($settings->favicon_url)
            <link rel="icon" href="{{ $settings->favicon_url }}" type="image/png">
        @endif

        <!-- NoScript fallback: show content if JS is disabled -->
        <noscript><style>body{opacity:1!important}</style></noscript>

        <!-- Pre-hydration: read sidebar state + prevent layout flash -->
        <script>
        (function(){
            var open = true;
            try {
                var v = localStorage.getItem('sidebar-open');
                if (v !== null) open = JSON.parse(v);
            } catch(e){}
            var desk = window.innerWidth >= 1024;
            var ew = {{ $settings->sidebar_width ?? 360 }};
            var cw = {{ $settings->sidebar_collapsed_width ?? 72 }};
            var w = desk ? (open ? ew : cw) : ew;
            var r = document.documentElement;
            r.style.setProperty('--sb-init-w', w + 'px');
            r.style.setProperty('--content-init-ml', desk ? w + 'px' : '0px');
            r.dataset.sidebarInit = open ? 'expanded' : 'collapsed';
            // Mark as pre-hydrated to prevent sidebar animation on refresh
            r.dataset.sidebarPrehydrated = 'true';
        })();
        </script>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link id="app-locale-font-link" href="{{ $localeGoogleFontUrl }}" rel="stylesheet" />

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

        <!-- GSAP for Animations -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js" defer></script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @php
            $bgImageUrl = null;
            if ($settings->app_bg_type === 'image' && $settings->app_bg_image) {
                $bgImageUrl = asset('storage/' . $settings->app_bg_image);
            } elseif ($activeTheme->bg_type === 'image' && $activeTheme->bg_image) {
                $bgImageUrl = $activeTheme->getBackgroundImageUrl();
            }
        @endphp

        @if ($bgImageUrl)
            <link rel="preload" as="image" href="{{ $bgImageUrl }}">
        @endif

        <script>
        (function () {
            function applyRuntimeFont(detail) {
                var data = detail || {};
                var fontFamily = (data.fontFamily || '').trim();
                var fontUrl = (data.fontUrl || '').trim();

                if (fontFamily) {
                    var root = document.documentElement;
                    root.style.setProperty('--app-font', fontFamily);
                    root.style.setProperty('--font-sans', fontFamily);
                    root.style.setProperty('--default-font-family', fontFamily);
                    root.style.fontFamily = fontFamily;

                    if (document.body) {
                        document.body.style.fontFamily = fontFamily;
                    }
                }

                if (fontUrl) {
                    var link = document.getElementById('app-locale-font-link');
                    if (link && link.getAttribute('href') !== fontUrl) {
                        link.setAttribute('href', fontUrl);
                    }
                }
            }

            var initial = {
                fontFamily: @json($bodyFontFamily),
                fontUrl: @json($localeGoogleFontUrl),
            };

            applyRuntimeFont(initial);
            document.addEventListener('DOMContentLoaded', function () { applyRuntimeFont(initial); });
            document.addEventListener('livewire:navigated', function () { applyRuntimeFont(initial); });
            window.addEventListener('font-updated', function (event) {
                applyRuntimeFont((event && event.detail) || {});
            });
        })();
        </script>

        {{-- Critical inline styles to prevent FOUC (flash of unstyled content) --}}
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
            /* Flash Loader */
            .flash-loader {
                position: fixed;
                inset: 0;
                z-index: 99999;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: opacity 0.35s ease, visibility 0.35s ease;
            }
            html.dark .flash-loader, html:not(.light) .flash-loader {
                background: #0f172a;
            }
            html.light .flash-loader {
                background: #f1f5f9;
            }
            .flash-loader.is-hidden {
                opacity: 0;
                visibility: hidden;
                pointer-events: none;
            }
            .flash-loader-bar {
                width: 220px;
                height: 4px;
                border-radius: 4px;
                overflow: hidden;
                position: relative;
            }
            html.dark .flash-loader-bar, html:not(.light) .flash-loader-bar {
                background: rgba(255,255,255,0.08);
            }
            html.light .flash-loader-bar {
                background: rgba(0,0,0,0.06);
            }
            .flash-loader-bar::after {
                content: '';
                position: absolute;
                inset: 0;
                border-radius: inherit;
                animation: shimmer-slide 1.2s ease-in-out infinite;
            }
            html.dark .flash-loader-bar::after, html:not(.light) .flash-loader-bar::after {
                background: linear-gradient(90deg, transparent 0%, rgba(99,102,241,0.5) 50%, transparent 100%);
            }
            html.light .flash-loader-bar::after {
                background: linear-gradient(90deg, transparent 0%, rgba(99,102,241,0.4) 50%, transparent 100%);
            }
            @keyframes shimmer-slide {
                0% { transform: translateX(-100%); }
                100% { transform: translateX(100%); }
            }
            html.dark body, html:not(.light) body {
                color: var(--font-color-dark, #bfdbfe);
            }
            html.light body {
                color: var(--font-color-light, #334155);
            }
            .glass-card {
                position: relative;
                isolation: isolate;
                overflow: clip;
            }
            /* Background: start with matching color, image set via JS after load */
            @if ($bgImageUrl)
                html {
                    background-color: #0f172a;
                    background-size: cover;
                    background-position: center;
                    background-repeat: no-repeat;
                    background-attachment: fixed;
                }
                html.light { background-color: #f1f5f9; }
            @else
                html { background: {{ $activeTheme->getDarkGradient() }}; }
                html.light { background: {{ $activeTheme->getLightGradient() }}; }
            @endif
            [x-cloak] { display: none !important; }
            /* Pre-hydration: prevent sidebar content margin flash */
            .content-preload { margin-left: var(--content-init-ml, 0px); }
            .sidebar-no-transition { transition: none !important; }
            {!! $activeTheme->custom_css ?? '' !!}
            @if ($settings->custom_css_dashboard_enabled && $settings->custom_css_dashboard)
            {!! $settings->custom_css_dashboard !!}
            @endif
        </style>

        @stack('styles')

        <!-- Styles -->
        @livewireStyles

        <!-- Reveal page once critical CSS is parsed and background ready -->
        <script>
        (function(){
            var bgUrl = @json($bgImageUrl);
            var revealed = false;
            function reveal() {
                if (revealed) return;
                revealed = true;
                window.__fouc_revealed = true;
                // Fade out flash loader first, then reveal body
                var loader = document.getElementById('flash-loader');
                if (loader) loader.classList.add('is-hidden');
                document.body.style.opacity = '1';
                // Remove loader from DOM after transition
                if (loader) setTimeout(function() { loader.remove(); }, 400);
            }
            if (bgUrl) {
                // Load image, decode it, set as background, then reveal
                var img = new Image();
                img.src = bgUrl;
                var setAndReveal = function() {
                    // Set background image now that it's decoded
                    document.documentElement.style.backgroundImage = 'url(' + bgUrl + ')';
                    // Wait two frames for browser to composite the background, then reveal
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
                // Safety timeout
                setTimeout(function() { setAndReveal(); }, 2000);
            } else {
                // No bg image: reveal on next frame
                requestAnimationFrame(reveal);
            }
            // Handle Livewire SPA navigation (wire:navigate)
            document.addEventListener('livewire:navigated', function() {
                document.body.style.opacity = '1';
                // Remove flash loader on SPA navigation
                var loader = document.getElementById('flash-loader');
                if (loader) loader.remove();
            });
            // Safety: ensure loader is removed after DOM is ready (fallback)
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(function() {
                    var loader = document.getElementById('flash-loader');
                    if (loader) {
                        loader.classList.add('is-hidden');
                        setTimeout(function() { loader.remove(); }, 400);
                    }
                    document.body.style.opacity = '1';
                }, 3000);
            });
        })();
        </script>
    </head>
        <body class="antialiased transition-colors duration-500 overflow-x-hidden"
          :class="theme === 'dark' ? '' : ''"
    >
        {{-- Flash Loader --}}
        <div id="flash-loader" class="flash-loader">
            <div class="flash-loader-bar"></div>
        </div>

        <x-glass-filters />

        {{-- Background Layer --}}
        @if ($settings->app_bg_type === 'video' && $settings->app_bg_video)
            <video autoplay muted loop playsinline class="fixed inset-0 w-full h-full object-cover -z-20">
                <source src="{{ asset('storage/' . $settings->app_bg_video) }}" type="video/mp4">
            </video>
            <div class="fixed inset-0 -z-10" style="background: {{ $activeTheme->bg_overlay_color }}; opacity: {{ $activeTheme->bg_overlay_opacity }}"></div>
        @elseif ($settings->app_bg_type === 'image' && $settings->app_bg_image)
            <div class="fixed inset-0 -z-20 bg-cover bg-center bg-no-repeat" style="background-image: url('{{ asset('storage/' . $settings->app_bg_image) }}')"></div>
            <div class="fixed inset-0 -z-10" style="background: {{ $activeTheme->bg_overlay_color }}; opacity: {{ $activeTheme->bg_overlay_opacity }}"></div>
        @elseif ($activeTheme->bg_type === 'video' && $activeTheme->bg_video)
            <video autoplay muted loop playsinline class="fixed inset-0 w-full h-full object-cover -z-20">
                <source src="{{ $activeTheme->getBackgroundVideoUrl() }}" type="video/mp4">
            </video>
            <div class="fixed inset-0 -z-10" style="background: {{ $activeTheme->bg_overlay_color }}; opacity: {{ $activeTheme->bg_overlay_opacity }}"></div>
        @elseif ($activeTheme->bg_type === 'image' && $activeTheme->bg_image)
            <div class="fixed inset-0 -z-20 bg-cover bg-center bg-no-repeat" style="background-image: url('{{ $activeTheme->getBackgroundImageUrl() }}')"></div>
            <div class="fixed inset-0 -z-10" style="background: {{ $activeTheme->bg_overlay_color }}; opacity: {{ $activeTheme->bg_overlay_opacity }}"></div>
        @else
            <div class="fixed inset-0 -z-20 transition-all duration-700" data-gradient-bg
                 :style="theme === 'dark'
                     ? 'background: {{ $activeTheme->getDarkGradient() }}'
                     : 'background: {{ $activeTheme->getLightGradient() }}'">
            </div>
        @endif

        {{-- Decorative Blobs (hidden when background image/video is set) --}}
        @if ($activeTheme->blobs_enabled && !$bgImageUrl && !($activeTheme->bg_type === 'video' && $activeTheme->bg_video))
        <div class="fixed inset-0 -z-5 overflow-hidden pointer-events-none" id="decorative-blobs">
            <div class="blob blob-1"></div>
            <div class="blob blob-2"></div>
            <div class="blob blob-3"></div>
            <div class="blob blob-4"></div>
        </div>
        @endif

        {{-- Admin Live Background Preview Overlay --}}
        @auth
        @if (auth()->user()->isSuperAdmin())
        <div id="bg-live-preview" class="fixed inset-0 pointer-events-none transition-opacity duration-500 opacity-0" style="z-index: -18;"></div>
        @endif
        @endauth

        <x-banner />

        {{-- Sidebar + Main Content Layout --}}
           <div class="min-h-screen relative z-10"
               x-data="{ sidebarExpanded: $persist(true).as('sidebar-open'), expandedOffset: {{ $settings->sidebar_width ?? 360 }}, collapsedOffset: {{ $settings->sidebar_collapsed_width ?? 72 }}, isDesktop: window.innerWidth >= 1024, sidebarReady: false }"
               x-init="
                   window.addEventListener('resize', () => isDesktop = window.innerWidth >= 1024);
                   window.addEventListener('sidebar-width-changed', (e) => {
                       if (e.detail.width) expandedOffset = e.detail.width;
                       if (e.detail.collapsedWidth) collapsedOffset = e.detail.collapsedWidth;
                   });
                   $nextTick(() => {
                       sidebarReady = true;
                       document.documentElement.style.removeProperty('--content-init-ml');
                       document.querySelector('.content-preload')?.classList.remove('content-preload');
                   });
               "
               x-on:sidebar-state-changed.window="sidebarExpanded = !!$event.detail.open"
             x-on:keydown.escape.window="sidebarExpanded = false">

            {{-- Sidebar Navigation --}}
            @livewire('navigation-menu')

            {{-- Main Content Area (offset for sidebar) --}}
                        <div class="min-h-screen flex flex-col content-preload"
                  :class="sidebarReady ? 'transition-[margin] duration-500 ease-[cubic-bezier(0.22,1,0.36,1)]' : ''"
                  :style="!isDesktop
                    ? ''
                    : (sidebarExpanded ? `margin-left: ${expandedOffset}px` : `margin-left: ${collapsedOffset}px`)">

                {{-- Header Component --}}
                <x-app-header />

                <!-- Page Content -->
                <main class="flex-1">
                    {{ $slot }}
                </main>

                {{-- Footer Component --}}
                <x-app-footer />
            </div>
        </div>

        @stack('modals')


        {{-- Global Toast Notification --}}
        <div x-data="{
                show: false,
                message: '',
                type: 'success',
                init() {
                    // Check for email verification success
                    const params = new URLSearchParams(window.location.search);
                    if (params.get('verified') === '1') {
                        this.showToast('Email verified successfully! Welcome aboard.', 'success');
                        // Clean URL
                        const url = new URL(window.location);
                        url.searchParams.delete('verified');
                        window.history.replaceState({}, '', url.pathname + url.search);
                    }
                    // Listen for Livewire toast events
                    window.addEventListener('toast', (e) => {
                        this.showToast(e.detail.message || e.detail[0]?.message, e.detail.type || e.detail[0]?.type || 'success');
                    });
                },
                showToast(msg, t = 'success') {
                    this.message = msg;
                    this.type = t;
                    this.show = true;
                    setTimeout(() => this.show = false, 5000);
                }
            }"
            x-show="show"
            x-transition:enter="transition ease-out duration-500"
            x-transition:enter-start="opacity-0 translate-y-4 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            style="display: none;"
            class="fixed top-6 right-6 z-[9999] max-w-sm"
        >
            <div class="glass-card no-gsap gsap-animated rounded-2xl p-4 shadow-2xl border"
                 :class="type === 'success'
                    ? 'border-emerald-500/30 bg-emerald-500/10'
                    : type === 'error'
                        ? 'border-red-500/30 bg-red-500/10'
                        : 'border-blue-500/30 bg-blue-500/10'"
            >
                <div class="flex items-start gap-3">
                    {{-- Icon --}}
                    <div class="shrink-0 mt-0.5">
                        <template x-if="type === 'success'">
                            <div class="w-8 h-8 rounded-xl bg-emerald-500/20 flex items-center justify-center">
                                <svg class="w-5 h-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                            </div>
                        </template>
                        <template x-if="type === 'error'">
                            <div class="w-8 h-8 rounded-xl bg-red-500/20 flex items-center justify-center">
                                <svg class="w-5 h-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                                </svg>
                            </div>
                        </template>
                        <template x-if="type === 'info'">
                            <div class="w-8 h-8 rounded-xl bg-blue-500/20 flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                                </svg>
                            </div>
                        </template>
                    </div>
                    {{-- Message --}}
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold"
                           :class="type === 'success' ? 'text-emerald-300' : type === 'error' ? 'text-red-300' : 'text-blue-300'"
                           x-text="message"></p>
                    </div>
                    {{-- Close --}}
                    <button @click="show = false" class="shrink-0 opacity-50 hover:opacity-100 transition-opacity">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        @livewireScripts
        @stack('scripts')

        {{-- GSAP Page Animations --}}
        <script>
            function runGsapAnimations(isInitial) {
                if (typeof gsap === 'undefined') return;

                // Safety: remove any stale FOUC guard on SPA navigation
                var guard = document.getElementById('fouc-guard');
                if (guard) guard.remove();
                document.documentElement.style.visibility = '';
                window.__fouc_revealed = true;

                // Immediately show all glass cards (no entrance animation)
                gsap.utils.toArray('.glass-card:not(aside .glass-card)').forEach(function(card) {
                    gsap.set(card, { opacity: 1, y: 0, scale: 1, clearProps: 'transform' });
                    card.classList.add('gsap-animated');
                });

                // Immediately show GSAP helper classes
                gsap.utils.toArray('.gsap-fade-up, .gsap-fade-in, .gsap-scale-in, .gsap-slide-left, .gsap-slide-right').forEach(function(el) {
                    gsap.set(el, { opacity: 1, y: 0, x: 0, scale: 1, clearProps: 'transform' });
                });
            }

            // Run on initial page load
            document.addEventListener('DOMContentLoaded', function() { runGsapAnimations(true); });

            // Also run when GSAP finishes loading (in case defer loads after DOMContentLoaded)
            if (typeof gsap === 'undefined') {
                var gsapScript = document.querySelector('script[src*="gsap"]');
                if (gsapScript) gsapScript.addEventListener('load', function() { runGsapAnimations(true); });
            }

            // Re-run after Livewire SPA navigation (wire:navigate)  
            document.addEventListener('livewire:navigated', function() { runGsapAnimations(false); });

            // Ensure glass cards stay visible after Livewire component updates
            document.addEventListener('livewire:init', function() {
                Livewire.hook('commit', ({ succeed }) => {
                    succeed(() => {
                        requestAnimationFrame(() => {
                            if (typeof gsap === 'undefined') return;
                            document.querySelectorAll('.glass-card:not(aside .glass-card):not(.no-gsap):not(.gsap-animated)').forEach(card => {
                                gsap.set(card, { opacity: 1, y: 0, scale: 1 });
                                card.classList.add('gsap-animated');
                            });
                        });
                    });
                });
            });
        </script>

        {{-- Admin Live Background Preview System --}}
        @auth
        @if (auth()->user()->isSuperAdmin())
        <script>
        (function() {
            function getPreview() {
                return document.getElementById('bg-live-preview');
            }

            window.addEventListener('app-bg-preview', function(e) {
                var preview = getPreview();
                if (!preview) return;

                var type = e.detail.type;
                var url = e.detail.url;

                // Hide blobs during preview
                var blobs = document.getElementById('decorative-blobs');
                if (blobs) blobs.style.opacity = '0';

                if (type === 'image' && url) {
                    preview.innerHTML = '';
                    preview.style.backgroundImage = 'url(' + JSON.stringify(url) + ')';
                    preview.style.backgroundSize = 'cover';
                    preview.style.backgroundPosition = 'center';
                    preview.style.backgroundRepeat = 'no-repeat';
                    preview.style.opacity = '1';
                } else if (type === 'video' && url) {
                    preview.style.backgroundImage = '';
                    preview.innerHTML = '<video autoplay muted loop playsinline style="width:100%;height:100%;object-fit:cover"><source src="' + url + '"></video>';
                    preview.style.opacity = '1';
                }
            });

            window.addEventListener('app-bg-preview-clear', function() {
                var preview = getPreview();
                if (preview) {
                    preview.style.opacity = '0';
                    setTimeout(function() {
                        preview.innerHTML = '';
                        preview.style.backgroundImage = '';
                    }, 500);
                }
                // Restore blobs
                var blobs = document.getElementById('decorative-blobs');
                if (blobs) blobs.style.opacity = '1';
            });

            // Listen for Livewire dispatches too
            document.addEventListener('livewire:init', function() {
                if (typeof Livewire !== 'undefined') {
                    Livewire.on('app-bg-preview-clear', function() {
                        window.dispatchEvent(new CustomEvent('app-bg-preview-clear'));
                    });
                    Livewire.on('app-bg-preview', function(data) {
                        var detail = Array.isArray(data) ? data[0] : data;
                        window.dispatchEvent(new CustomEvent('app-bg-preview', {
                            detail: { type: detail.type, url: detail.url }
                        }));
                    });
                }
            });
        })();
        </script>
        @endif
        @endauth

        @guest
            @include('partials.cookie-banner')
        @endguest
    </body>
</html>
