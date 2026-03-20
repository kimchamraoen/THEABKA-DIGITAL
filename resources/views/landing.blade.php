@php
    $settings = \App\Models\Setting::instance();
    $fontConfig = $settings->resolveLocaleFontConfig(app()->getLocale());
    $bodyFontFamily = $fontConfig['bodyFontFamily'];
    $fontUrl = $fontConfig['fontUrl'];
    $localeCustomFontName = $fontConfig['customFontName'];
    $localeCustomFontUrl = $fontConfig['customFontUrl'];
    $theme = $settings->default_theme;
    $glassStyle = auth()->check() ? auth()->user()->getEffectiveGlassStyle() : ($settings->default_glass_style ?? 'liquid');
    $activeTheme = \App\Models\Theme::active();
    $appName = $settings->app_name;
    $footerText = $settings->footer_text ?: '&copy; ' . date('Y') . ' ' . e($appName) . '. All rights reserved.';
    $sections = \App\Models\LandingSection::where('is_visible', true)->orderBy('sort_order')->get();

    // Hero settings with defaults
    $heroBadge = $settings->landing_hero_badge ?: 'Secure by Default';
    $heroLine1 = $settings->landing_hero_line1 ?: 'Next-Gen';
    $heroLine2 = $settings->landing_hero_line2 ?: 'Authentication';
    $heroLine3 = $settings->landing_hero_line3 ?: 'Platform';
    $heroSubtitle = $settings->landing_hero_subtitle ?: 'Enterprise-grade two-factor authentication with ' . __('Google Authenticator') . '. Protect your accounts with military-grade security.';
    $ctaPrimaryText = $settings->landing_cta_primary_text ?: 'Start Free';
    $ctaPrimaryUrl = $settings->landing_cta_primary_url ?: route('register');
    $ctaSecondaryText = $settings->landing_cta_secondary_text ?: 'Learn More';
    $ctaSecondaryUrl = $settings->landing_cta_secondary_url ?: '#features';

    // Section visibility (default true if null)
    $showFeatures = $settings->landing_features_visible !== false;
    $showCta = $settings->landing_cta_visible !== false;
    $showFloatingCards = $settings->landing_floating_cards !== false;
    $showParticles = $settings->landing_particles !== false;

    $featuresTitle = $settings->landing_features_title ?: 'Enterprise <span class="gradient-text">Security</span> Features';
    $featuresSubtitle = $settings->landing_features_subtitle ?: 'Everything you need to secure your application from day one.';
    $ctaSectionTitle = $settings->landing_cta_title ?: 'Ready to <span class="gradient-text">Secure</span> Your App?';
    $ctaSectionSubtitle = $settings->landing_cta_subtitle ?: 'Set up enterprise-grade authentication in minutes, not days.';

    // Determine background image URL for preloading
    $landingBgImageUrl = null;
    if ($settings->landing_bg_type === 'image' && $settings->landing_bg_image) {
        $landingBgImageUrl = asset('storage/' . $settings->landing_bg_image);
    }

    $activeLanguages = cache()->remember('active_languages', 3600, function () {
        if (!\Illuminate\Support\Facades\Schema::hasTable('languages')) {
            return collect([
                (object) ['locale' => 'en', 'name' => 'English', 'flag' => '🇬🇧', 'is_default' => true],
                (object) ['locale' => 'km', 'name' => 'Khmer', 'flag' => '🇰🇭', 'is_default' => false],
            ]);
        }

        $langs = \App\Models\Language::active()
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get(['locale', 'name', 'flag', 'is_default']);

        return $langs->isNotEmpty()
            ? $langs
            : collect([
                (object) ['locale' => 'en', 'name' => 'English', 'flag' => '🇬🇧', 'is_default' => true],
                (object) ['locale' => 'km', 'name' => 'Khmer', 'flag' => '🇰🇭', 'is_default' => false],
            ]);
    });

    $currentLocale = app()->getLocale();
    $currentLanguage = $activeLanguages->firstWhere('locale', $currentLocale) ?? $activeLanguages->first();
    $alternateLanguage = $activeLanguages->firstWhere('locale', '!=', $currentLocale);
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" style="background-color: #0f172a" data-glass-style="{{ $glassStyle }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $appName }} - {{ __('Secure Authentication Platform') }}</title>

    @if ($settings->favicon_url)
        <link rel="icon" href="{{ $settings->favicon_url }}" type="image/png">
    @endif

    <!-- NoScript fallback: show content if JS is disabled -->
    <noscript><style>body{opacity:1!important}</style></noscript>

    <!-- Dynamic Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="{{ $fontUrl }}" rel="stylesheet" />

    <!-- GSAP -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @if ($landingBgImageUrl)
        <link rel="preload" as="image" href="{{ $landingBgImageUrl }}">
    @endif

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
        body { opacity: 0; transition: opacity 0.15s ease-in; }
        /* Background: start with color, image set via JS after decode */
        @if ($landingBgImageUrl)
            html {
                background-color: #0f172a;
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
                background-attachment: fixed;
            }
        @else
            html { background: {{ $activeTheme->getDarkGradient() }}; }
        @endif
        {!! $activeTheme->custom_css ?? '' !!}
        @if ($settings->custom_css_landing_enabled && $settings->custom_css_landing)
        {!! $settings->custom_css_landing !!}
        @endif
        .float-card { animation: floatCard 6s ease-in-out infinite; }
        .float-card:nth-child(2) { animation-delay: -2s; }
        .float-card:nth-child(3) { animation-delay: -4s; }
        @keyframes floatCard {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(1deg); }
        }
        .glow-blue { box-shadow: 0 0 40px rgba(59, 130, 246, 0.3), 0 0 80px rgba(59, 130, 246, 0.1); }
        .particle { position: absolute; width: 4px; height: 4px; background: rgba(96, 165, 250, 0.6); border-radius: 50%; pointer-events: none; }
        .embed-container { position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; max-width: 100%; border-radius: 1rem; }
        .embed-container iframe { position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0; border-radius: 1rem; }
        .prose-glass h1, .prose-glass h2, .prose-glass h3 { font-weight: 700; margin-bottom: 0.5rem; }
        .prose-glass h1 { font-size: 2.25rem; }
        .prose-glass h2 { font-size: 1.5rem; }
        .prose-glass p { margin-bottom: 1rem; opacity: 0.7; line-height: 1.75; }
        .prose-glass ul, .prose-glass ol { margin-bottom: 1rem; padding-left: 1.5rem; opacity: 0.7; }
        .prose-glass li { margin-bottom: 0.25rem; }
        .prose-glass a { color: var(--color-primary, #3b82f6); text-decoration: underline; }
    </style>

    <!-- Reveal page once background is ready -->
    <script>
    (function(){
        var bgUrl = @json($landingBgImageUrl ?? null);
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
<body class="antialiased overflow-x-hidden landing-page"
      style="color: rgb(191 219 254);">
    <x-glass-filters />

    {{-- Decorative Blobs (hidden when background image is set) --}}
    @if ($activeTheme->blobs_enabled && !$landingBgImageUrl)
    <div class="fixed inset-0 overflow-hidden pointer-events-none" style="z-index: 0;">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="blob blob-3"></div>
        <div class="blob blob-4"></div>
    </div>
    @endif

    {{-- Landing Background (video or image) - overlay only, image is on html --}}
    @if ($settings->landing_bg_type === 'video' && $settings->landing_bg_video)
        <video autoplay muted loop playsinline class="fixed inset-0 w-full h-full object-cover" style="z-index: 0;">
            <source src="{{ asset('storage/' . $settings->landing_bg_video) }}" type="video/mp4">
        </video>
        <div class="fixed inset-0 bg-black/50" style="z-index: 0;"></div>
    @elseif ($landingBgImageUrl)
        {{-- Image is set on html element via CSS, just add overlay --}}
        <div class="fixed inset-0 bg-black/50" style="z-index: 0;"></div>
    @endif

    {{-- Particles Container --}}
    @if ($showParticles)
    <div id="particles" class="fixed inset-0 pointer-events-none" style="z-index: 1;"></div>
    @endif

    {{-- Navigation --}}
    <nav id="nav" class="fixed top-0 left-0 right-0 z-50 opacity-0" x-data="{ mobileMenuOpen: false }">
        <div class="max-w-7xl mx-auto px-6 py-4">
            <div class="glass-card landing-nav-shell rounded-2xl px-6 py-3">
                <div class="landing-nav-top flex justify-between items-center gap-4">
                <div class="flex items-center gap-3 min-w-0">
                    @if ($settings->logo_url)
                        <img src="{{ $settings->logo_url }}" alt="{{ $appName }}" class="w-10 h-10 rounded-xl object-contain" />
                    @else
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-blue-500/30">
                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                            </svg>
                        </div>
                    @endif
                    <span class="text-xl font-bold landing-brand">{{ $appName }}</span>
                </div>
                <div class="landing-nav-desktop hidden md:flex items-center gap-4">
                    @if ($activeLanguages->count() <= 1)
                        <span class="px-3 py-2 text-sm font-medium rounded-xl bg-white/5 border border-white/10">
                            {{ ($currentLanguage->flag ?: '🏳️') . ' ' . $currentLanguage->name }}
                        </span>
                    @elseif ($activeLanguages->count() <= 2)
                        @if ($alternateLanguage)
                            <a href="{{ route('lang.switch', $alternateLanguage->locale) }}"
                               class="px-3 py-2 text-sm font-medium rounded-xl hover:bg-white/10 transition-all duration-200">
                                {{ ($alternateLanguage->flag ?: '🏳️') . ' ' . $alternateLanguage->name }}
                            </a>
                        @endif
                    @else
                        <div x-data="{ open: false }" class="relative">
                            <button type="button" @click="open = !open"
                                    class="px-3 py-2 text-sm font-medium rounded-xl hover:bg-white/10 transition-all duration-200">
                                {{ ($currentLanguage->flag ?: '🏳️') . ' ' . $currentLanguage->name }}
                            </button>
                            <div x-show="open" x-cloak @click.outside="open = false"
                                 class="absolute right-0 mt-2 min-w-[200px] rounded-xl border border-white/15 bg-slate-900/95 p-1">
                                @foreach ($activeLanguages as $language)
                                    <a href="{{ route('lang.switch', $language->locale) }}"
                                       class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition {{ $language->locale === $currentLanguage->locale ? 'bg-white/15' : 'hover:bg-white/10' }}">
                                        <span>{{ $language->flag ?: '🏳️' }}</span>
                                        <span>{{ $language->name }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    <a href="{{ route('login') }}"
                       class="px-5 py-2 text-sm font-medium rounded-xl hover:bg-white/10 transition-all duration-200">
                        {{ __('app.nav.sign_in') }}
                    </a>
                    <a href="{{ route('register') }}"
                       class="px-5 py-2 text-sm font-medium rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white
                              hover:from-blue-500 hover:to-indigo-500 transition-all duration-200 shadow-lg shadow-blue-600/25">
                        {{ __('app.nav.get_started') }}
                    </a>
                </div>

                <button type="button"
                        class="landing-nav-toggle md:hidden inline-flex items-center justify-center w-11 h-11 rounded-xl border border-white/20 bg-white/5"
                        :aria-expanded="mobileMenuOpen ? 'true' : 'false'"
                        aria-controls="landing-mobile-nav"
                        @click="mobileMenuOpen = !mobileMenuOpen">
                    <span class="sr-only">Toggle navigation</span>
                    <svg x-show="!mobileMenuOpen" x-cloak class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 6.75h15m-15 5.25h15m-15 5.25h15" />
                    </svg>
                    <svg x-show="mobileMenuOpen" x-cloak class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
                </div>

                <div id="landing-mobile-nav"
                     x-cloak
                     x-show="mobileMenuOpen"
                     x-transition.origin.top.duration.200ms
                     class="landing-nav-mobile md:hidden mt-4 pt-4 border-t border-white/10 space-y-3">
                    @if ($activeLanguages->count() <= 1)
                        <div class="px-3 py-2 text-sm font-medium rounded-xl bg-white/5 border border-white/10 inline-flex items-center">
                            {{ ($currentLanguage->flag ?: '🏳️') . ' ' . $currentLanguage->name }}
                        </div>
                    @elseif ($activeLanguages->count() <= 2)
                        @if ($alternateLanguage)
                            <a href="{{ route('lang.switch', $alternateLanguage->locale) }}"
                               class="block px-4 py-3 text-sm font-medium rounded-xl hover:bg-white/10 transition-all duration-200">
                                {{ ($alternateLanguage->flag ?: '🏳️') . ' ' . $alternateLanguage->name }}
                            </a>
                        @endif
                    @else
                        <div class="grid grid-cols-1 gap-2">
                            @foreach ($activeLanguages as $language)
                                <a href="{{ route('lang.switch', $language->locale) }}"
                                   class="flex items-center gap-2 px-4 py-3 rounded-xl text-sm transition {{ $language->locale === $currentLanguage->locale ? 'bg-white/15' : 'hover:bg-white/10' }}">
                                    <span>{{ $language->flag ?: '🏳️' }}</span>
                                    <span>{{ $language->name }}</span>
                                </a>
                            @endforeach
                        </div>
                    @endif

                    <a href="{{ route('login') }}"
                       class="block text-center px-4 py-3 text-sm font-medium rounded-xl hover:bg-white/10 transition-all duration-200">
                        {{ __('app.nav.sign_in') }}
                    </a>
                    <a href="{{ route('register') }}"
                       class="block text-center px-4 py-3 text-sm font-medium rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white
                              hover:from-blue-500 hover:to-indigo-500 transition-all duration-200 shadow-lg shadow-blue-600/25">
                        {{ __('app.nav.get_started') }}
                    </a>
                </div>
            </div>
        </div>
    </nav>

    {{-- Hero Section --}}
    <section class="relative min-h-screen flex items-center justify-center px-6 landing-hero-section" style="z-index: 2;">
        <div class="max-w-7xl mx-auto text-center relative z-10 landing-hero-content">
            {{-- Badge --}}
            <div id="hero-badge" class="inline-flex items-center gap-2 px-4 py-2 rounded-full glass-card text-sm mb-8 opacity-0">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                {{ $heroBadge }}
            </div>

            {{-- Main Heading --}}
            <h1 id="hero-title" class="text-5xl md:text-7xl lg:text-8xl font-bold leading-tight mb-6">
                <span class="block opacity-0 hero-line">{{ $heroLine1 }}</span>
                <span class="block hero-shimmer opacity-0 hero-line">{{ $heroLine2 }}</span>
                <span class="block opacity-0 hero-line">{{ $heroLine3 }}</span>
            </h1>

            {{-- Subtitle --}}
            <p id="hero-subtitle" class="text-xl md:text-2xl max-w-2xl mx-auto mb-10 opacity-0">
                {{ $heroSubtitle }}
            </p>

            {{-- CTA Buttons --}}
            <div id="hero-cta" class="flex flex-col sm:flex-row items-center justify-center gap-4 opacity-0">
                <a href="{{ $ctaPrimaryUrl }}"
                   class="cta-pulse group px-8 py-4 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold text-lg
                          hover:from-blue-500 hover:to-indigo-500 transition-all duration-300 shadow-xl shadow-blue-600/30
                          hover:shadow-blue-500/50 hover:scale-105 flex items-center gap-2">
                    {{ $ctaPrimaryText }}
                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                    </svg>
                </a>
                <a href="{{ $ctaSecondaryUrl }}"
                   class="cta-ghost px-8 py-4 rounded-2xl glass-card font-semibold text-lg transition-all duration-300
                          hover:scale-105">
                    {{ $ctaSecondaryText }}
                </a>
            </div>
        </div>

        {{-- Floating Glass Cards --}}
        @if ($showFloatingCards)
        {{-- ENHANCEMENT: Desktop = absolute positioned, Mobile = stacked below hero --}}
        <div class="absolute inset-0 pointer-events-none overflow-hidden md:block hidden landing-float-desktop">
            <div class="float-card absolute top-1/4 left-10 glass-card rounded-2xl p-6 w-48 opacity-0" id="float-1" style="backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px);">
                <div class="w-10 h-10 rounded-lg bg-blue-500/30 flex items-center justify-center mb-3">
                    {!! get_icon('landing.float.2fa', '<svg class="w-5 h-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" /></svg>') !!}
                </div>
                <p class="text-sm font-medium">2FA Protected</p>
                <p class="text-xs opacity-50 mt-1">Google Authenticator</p>
            </div>
            <div class="float-card absolute top-1/3 right-16 glass-card rounded-2xl p-6 w-52 opacity-0" id="float-2" style="backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px);">
                <div class="w-10 h-10 rounded-lg bg-emerald-500/30 flex items-center justify-center mb-3">
                    {!! get_icon('landing.float.email_verified', '<svg class="w-5 h-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>') !!}
                </div>
                <p class="text-sm font-medium">{{ __('Email Verified') }}</p>
                <p class="text-xs opacity-50 mt-1">{{ __('Trusted identity') }}</p>
            </div>
            <div class="float-card absolute bottom-1/4 left-1/4 glass-card rounded-2xl p-6 w-56 opacity-0" id="float-3" style="backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px);">
                <div class="w-10 h-10 rounded-lg bg-purple-500/30 flex items-center justify-center mb-3">
                    {!! get_icon('landing.float.recovery_codes', '<svg class="w-5 h-5 text-purple-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z" /></svg>') !!}
                </div>
                <p class="text-sm font-medium">{{ __('Recovery Codes') }}</p>
                <p class="text-xs opacity-50 mt-1">{{ __('Backup access guaranteed') }}</p>
            </div>
        </div>
        {{-- ENHANCEMENT: Mobile stacked version of floating cards --}}
        <div class="md:hidden flex flex-col items-center gap-3 mt-8 px-4 landing-float-mobile" style="z-index: 10;">
            <div class="glass-card rounded-2xl p-5 w-full max-w-xs" style="backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px);">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-blue-500/30 flex items-center justify-center shrink-0">
                        {!! get_icon('landing.float.2fa', '<svg class="w-5 h-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" /></svg>') !!}
                    </div>
                    <div>
                        <p class="text-sm font-medium">2FA Protected</p>
                        <p class="text-xs opacity-50">Google Authenticator</p>
                    </div>
                </div>
            </div>
            <div class="glass-card rounded-2xl p-5 w-full max-w-xs" style="backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px);">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-emerald-500/30 flex items-center justify-center shrink-0">
                        {!! get_icon('landing.float.email_verified', '<svg class="w-5 h-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>') !!}
                    </div>
                    <div>
                        <p class="text-sm font-medium">{{ __('Email Verified') }}</p>
                        <p class="text-xs opacity-50">{{ __('Trusted identity') }}</p>
                    </div>
                </div>
            </div>
            <div class="glass-card rounded-2xl p-5 w-full max-w-xs" style="backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px);">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-purple-500/30 flex items-center justify-center shrink-0">
                        {!! get_icon('landing.float.recovery_codes', '<svg class="w-5 h-5 text-purple-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z" /></svg>') !!}
                    </div>
                    <div>
                        <p class="text-sm font-medium">{{ __('Recovery Codes') }}</p>
                        <p class="text-xs opacity-50">{{ __('Backup access guaranteed') }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </section>

    {{-- Features Section --}}
    @if ($showFeatures)
    <section id="features" class="relative py-32 px-6" style="z-index: 2;">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold mb-4 feature-title opacity-0">
                    {!! $featuresTitle !!}
                </h2>
                <p class="text-xl opacity-50 max-w-xl mx-auto feature-subtitle opacity-0">
                    {{ $featuresSubtitle }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @php
                    $features = [
                        ['key' => 'landing.feature.two_factor_auth', 'icon' => 'M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z', 'title' => 'Two-Factor Auth', 'desc' => 'Google Authenticator TOTP-based 2FA with QR code setup and recovery codes.', 'color' => 'blue'],
                        ['key' => 'landing.feature.email_verification', 'icon' => 'M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75', 'title' => 'Email Verification', 'desc' => 'Mandatory email verification ensures all users have valid, verified email addresses.', 'color' => 'emerald'],
                        ['key' => 'landing.feature.admin_controls', 'icon' => 'M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z', 'title' => 'Admin Controls', 'desc' => 'Super admin panel to manage themes, fonts, SMTP settings, and policies.', 'color' => 'amber'],
                        ['key' => 'landing.feature.dark_light_mode', 'icon' => 'M21.752 15.002A9.718 9.718 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z', 'title' => 'Dark/Light Mode', 'desc' => 'Beautiful glass morphism theme with system-wide default and per-user preferences.', 'color' => 'purple'],
                        ['key' => 'landing.feature.dynamic_fonts', 'icon' => 'M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12', 'title' => 'Dynamic Fonts', 'desc' => 'Choose from 1000+ Google Fonts with live preview and cached API responses.', 'color' => 'pink'],
                        ['key' => 'landing.feature.password_security', 'icon' => 'M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z', 'title' => 'Password Security', 'desc' => 'Bcrypt hashing, password confirmation for sensitive actions, and reset flow.', 'color' => 'cyan'],
                    ];
                @endphp

                @foreach ($features as $feature)
                    <div class="feature-card glass-card rounded-2xl p-8 hover:bg-white/20 transition-all duration-300 group cursor-default opacity-0">
                        <div class="w-14 h-14 rounded-xl bg-{{ $feature['color'] }}-500/20 flex items-center justify-center mb-5
                                    group-hover:scale-110 transition-transform duration-300">
                            {!! get_icon($feature['key'], '<svg class="w-7 h-7 text-' . $feature['color'] . '-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="' . $feature['icon'] . '" /></svg>') !!}
                        </div>
                        <h3 class="text-xl font-bold mb-2">{{ $feature['title'] }}</h3>
                        <p class="text-sm opacity-50 leading-relaxed">{{ $feature['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- Dynamic CMS Sections --}}
    @foreach ($sections as $section)
    <section class="relative py-20 px-6 cms-section" style="z-index: 2;" id="section-{{ $section->section_key }}">
        <div class="max-w-5xl mx-auto">
            <div class="glass-card rounded-3xl p-8 md:p-12">
                @if ($section->title)
                    <h2 class="text-3xl md:text-4xl font-bold mb-3 text-center">
                        {!! $section->title !!}
                    </h2>
                @endif

                @if ($section->subtitle)
                    <p class="text-lg opacity-60 text-center mb-8">{{ $section->subtitle }}</p>
                @endif

                @if ($section->image)
                    <div class="mb-8 rounded-2xl overflow-hidden">
                        <img src="{{ asset('storage/' . $section->image) }}" alt="{{ $section->title }}"
                             class="w-full h-auto rounded-2xl" />
                    </div>
                @endif

                @if ($section->video_url)
                    <div class="mb-8 embed-container">
                        @php
                            $videoUrl = $section->video_url;
                            // Convert YouTube URLs to embed format
                            if (str_contains($videoUrl, 'youtube.com/watch')) {
                                preg_match('/[?&]v=([^&]+)/', $videoUrl, $matches);
                                if (!empty($matches[1])) {
                                    $videoUrl = 'https://www.youtube.com/embed/' . $matches[1];
                                }
                            } elseif (str_contains($videoUrl, 'youtu.be/')) {
                                $videoUrl = 'https://www.youtube.com/embed/' . basename(parse_url($videoUrl, PHP_URL_PATH));
                            }
                        @endphp
                        <iframe src="{{ $videoUrl }}" allowfullscreen allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe>
                    </div>
                @endif

                @if ($section->body)
                    <div class="prose-glass">
                        {!! $section->body !!}
                    </div>
                @endif

                @if ($section->button_text && $section->button_url)
                    <div class="text-center mt-8">
                        <a href="{{ $section->button_url }}"
                           class="inline-flex items-center gap-2 px-8 py-3 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold
                                  hover:from-blue-500 hover:to-indigo-500 transition-all duration-300 shadow-lg shadow-blue-600/25 hover:scale-105">
                            {{ $section->button_text }}
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                            </svg>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </section>
    @endforeach

    {{-- CTA Section --}}
    @if ($showCta)
    <section class="relative py-32 px-6" style="z-index: 2;">
        <div class="max-w-4xl mx-auto text-center">
            <div class="glass-card rounded-3xl p-12 md:p-16 glow-blue cta-card opacity-0">
                <h2 class="text-4xl md:text-5xl font-bold mb-4">
                    {!! $ctaSectionTitle !!}
                </h2>
                <p class="text-xl opacity-50 mb-8 max-w-lg mx-auto">
                    {{ $ctaSectionSubtitle }}
                </p>
                <a href="{{ route('register') }}"
                   class="inline-flex items-center gap-2 px-10 py-4 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold text-lg
                          hover:from-blue-500 hover:to-indigo-500 transition-all duration-300 shadow-xl shadow-blue-600/30
                          hover:shadow-blue-500/50 hover:scale-105">
                    {{ __('Create Your Account') }}
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                    </svg>
                </a>
            </div>
        </div>
    </section>
    @endif

    {{-- Footer --}}
    @php
        $footerShowTerms = (bool) ($settings->footer_show_terms ?? true);
        $footerShowPrivacy = (bool) ($settings->footer_show_privacy ?? true);
        $footerLinks = $settings->footer_links ?? [];
        $footerSocialLinks = $settings->footer_social_links ?? [];
    @endphp
    <footer class="border-t border-white/10 py-8 px-6 relative" style="z-index: 2;">
        <div class="max-w-7xl mx-auto">
            <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="flex items-center gap-3">
                    @if ($settings->logo_url)
                        <img src="{{ $settings->logo_url }}" alt="{{ $appName }}" class="w-8 h-8 rounded-lg object-contain" />
                    @else
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                            </svg>
                        </div>
                    @endif
                    <span class="font-bold">{{ $appName }}</span>
                </div>

                <div class="flex items-center gap-6 text-sm opacity-50 flex-wrap">
                    @foreach ($footerLinks as $link)
                        <a href="{{ $link['url'] }}" target="_blank" rel="noopener noreferrer" class="hover:opacity-100 transition">{{ $link['label'] }}</a>
                    @endforeach
                    @if ($footerShowTerms && Route::has('terms.show'))
                        <a href="{{ route('terms.show') }}" class="hover:opacity-100 transition">{{ __('Terms of Service') }}</a>
                    @endif
                    @if ($footerShowPrivacy && Route::has('policy.show'))
                        <a href="{{ route('policy.show') }}" class="hover:opacity-100 transition">{{ __('Privacy Policy') }}</a>
                    @endif
                </div>

                <div class="flex items-center gap-4">
                    @if (count($footerSocialLinks) > 0)
                        <div class="flex items-center gap-3 opacity-50">
                            @foreach ($footerSocialLinks as $social)
                                <a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer" class="hover:opacity-100 transition" title="{{ ucfirst($social['platform']) }}">
                                    @include('components.social-icon', ['platform' => $social['platform'], 'size' => 'w-4 h-4'])
                                </a>
                            @endforeach
                        </div>
                    @endif
                    <p class="text-sm opacity-40">{!! $footerText !!}</p>
                </div>
            </div>
        </div>
    </footer>

    {{-- GSAP Animations --}}
    <script>
        gsap.registerPlugin(ScrollTrigger);

        // Create particles
        @if ($showParticles)
        const particlesContainer = document.getElementById('particles');
        if (particlesContainer) {
        for (let i = 0; i < 50; i++) {
            const particle = document.createElement('div');
            particle.className = 'particle';
            particle.style.left = Math.random() * 100 + '%';
            particle.style.top = Math.random() * 100 + '%';
            particle.style.opacity = Math.random() * 0.5 + 0.1;
            particle.style.width = particle.style.height = (Math.random() * 4 + 2) + 'px';
            particlesContainer.appendChild(particle);

            gsap.to(particle, {
                y: -100 + Math.random() * -200,
                x: (Math.random() - 0.5) * 100,
                opacity: 0,
                duration: 3 + Math.random() * 4,
                repeat: -1,
                delay: Math.random() * 3,
                ease: 'power1.out'
            });
        }
        }
        @endif

        // Nav animation
        gsap.to('#nav', {
            opacity: 1,
            y: 0,
            duration: 1,
            delay: 0.2,
            ease: 'power3.out'
        });

        // Hero badge
        gsap.to('#hero-badge', {
            opacity: 1,
            y: 0,
            duration: 0.8,
            delay: 0.5,
            ease: 'power3.out'
        });

        // Hero title lines - staggered
        gsap.to('.hero-line', {
            opacity: 1,
            y: 0,
            duration: 0.8,
            stagger: 0.15,
            delay: 0.7,
            ease: 'power3.out'
        });

        // Hero subtitle
        gsap.to('#hero-subtitle', {
            opacity: 0.6,
            y: 0,
            duration: 0.8,
            delay: 1.3,
            ease: 'power3.out'
        });

        // Hero CTA
        gsap.to('#hero-cta', {
            opacity: 1,
            y: 0,
            duration: 0.8,
            delay: 1.5,
            ease: 'power3.out'
        });

        // Floating cards
        @if ($showFloatingCards)
        gsap.to('#float-1', {
            opacity: 0.7,
            x: 0,
            duration: 1.2,
            delay: 1.8,
            ease: 'power3.out'
        });
        gsap.to('#float-2', {
            opacity: 0.7,
            x: 0,
            duration: 1.2,
            delay: 2,
            ease: 'power3.out'
        });
        gsap.to('#float-3', {
            opacity: 0.7,
            x: 0,
            duration: 1.2,
            delay: 2.2,
            ease: 'power3.out'
        });

        // Set initial positions
        gsap.set('#nav', { y: -20 });
        gsap.set('#hero-badge', { y: 20 });
        gsap.set('.hero-line', { y: 40 });
        gsap.set('#hero-subtitle', { y: 20 });
        gsap.set('#hero-cta', { y: 20 });
        gsap.set('#float-1', { x: -50 });
        gsap.set('#float-2', { x: 50 });
        gsap.set('#float-3', { x: -30 });
        @endif

        // Scroll-triggered animations for features
        gsap.to('.feature-title', {
            scrollTrigger: {
                trigger: '.feature-title',
                start: 'top 80%',
                toggleActions: 'play none none none'
            },
            opacity: 1,
            y: 0,
            duration: 0.8,
            ease: 'power3.out'
        });
        gsap.set('.feature-title', { y: 30 });

        gsap.to('.feature-subtitle', {
            scrollTrigger: {
                trigger: '.feature-subtitle',
                start: 'top 80%',
                toggleActions: 'play none none none'
            },
            opacity: 1,
            y: 0,
            duration: 0.8,
            delay: 0.2,
            ease: 'power3.out'
        });
        gsap.set('.feature-subtitle', { y: 20 });

        // Feature cards stagger
        gsap.to('.feature-card', {
            scrollTrigger: {
                trigger: '.feature-card',
                start: 'top 80%',
                toggleActions: 'play none none none'
            },
            opacity: 1,
            y: 0,
            duration: 0.6,
            stagger: 0.1,
            ease: 'power3.out'
        });
        gsap.set('.feature-card', { y: 40 });

        // CMS dynamic sections
        document.querySelectorAll('.cms-section').forEach(section => {
            gsap.set(section, { opacity: 0, y: 40 });
            gsap.to(section, {
                scrollTrigger: {
                    trigger: section,
                    start: 'top 80%',
                    toggleActions: 'play none none none'
                },
                opacity: 1,
                y: 0,
                duration: 0.8,
                ease: 'power3.out'
            });
        });

        // CTA card
        gsap.to('.cta-card', {
            scrollTrigger: {
                trigger: '.cta-card',
                start: 'top 80%',
                toggleActions: 'play none none none'
            },
            opacity: 1,
            y: 0,
            scale: 1,
            duration: 0.8,
            ease: 'power3.out'
        });
        gsap.set('.cta-card', { y: 40, scale: 0.95 });

        // Button hover animations
        document.querySelectorAll('a[href]').forEach(btn => {
            btn.addEventListener('mouseenter', () => {
                gsap.to(btn, { scale: 1.02, duration: 0.2, ease: 'power2.out' });
            });
            btn.addEventListener('mouseleave', () => {
                gsap.to(btn, { scale: 1, duration: 0.2, ease: 'power2.out' });
            });
        });
    </script>
</body>
</html>
