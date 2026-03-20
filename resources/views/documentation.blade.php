@php
    $settings = \App\Models\Setting::instance();
    $glassStyle = auth()->check() ? auth()->user()->getEffectiveGlassStyle() : ($settings->default_glass_style ?? 'liquid');
    $fontConfig = $settings->resolveLocaleFontConfig(app()->getLocale());
    $bodyFontFamily = $fontConfig['bodyFontFamily'];
    $fontUrl = $fontConfig['fontUrl'];
    $localeCustomFontName = $fontConfig['customFontName'];
    $localeCustomFontUrl = $fontConfig['customFontUrl'];
    $activeTheme = \App\Models\Theme::active();
    $appName = $settings->app_name;
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" style="background-color: #0f172a" data-glass-style="{{ $glassStyle }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Documentation - {{ $appName }}</title>

    @if ($settings->favicon_url)
        <link rel="icon" href="{{ $settings->favicon_url }}" type="image/png">
    @endif

    <!-- NoScript fallback: show content if JS is disabled -->
    <noscript><style>body{opacity:1!important}</style></noscript>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="{{ $fontUrl }}" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

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
        html { background: {{ $activeTheme->getDarkGradient() }}; }
        {!! $activeTheme->custom_css ?? '' !!}
        .doc-nav a.active { background: rgba(59,130,246,0.2); border-color: rgba(59,130,246,0.4); }
        .doc-content h2 { scroll-margin-top: 6rem; }
        .doc-content code { background: rgba(255,255,255,0.1); padding: 0.15rem 0.4rem; border-radius: 0.375rem; font-size: 0.85em; }
        .doc-content pre { background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 1rem; overflow-x: auto; margin: 1rem 0; }
        .doc-content pre code { background: transparent; padding: 0; }
        .doc-content ul { list-style: disc; padding-left: 1.5rem; }
        .doc-content ol { list-style: decimal; padding-left: 1.5rem; }
        .doc-content li { margin-bottom: 0.375rem; }
    </style>

    <!-- Reveal page once styles are ready -->
    <script>
    (function(){
        window.__fouc_revealed = true;
        requestAnimationFrame(function() {
            document.body.style.opacity = '1';
        });
    })();
    </script>
</head>
<body class="antialiased overflow-x-hidden"
      style="color: rgb(191 219 254);">
    <x-glass-filters />

    @if ($activeTheme->blobs_enabled)
    <div class="fixed inset-0 overflow-hidden pointer-events-none" style="z-index: 0;">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
    </div>
    @endif

    {{-- Top Navigation --}}
    <nav class="fixed top-0 left-0 right-0 z-50">
        <div class="max-w-7xl mx-auto px-6 py-4">
            <div class="glass-card rounded-2xl px-6 py-3 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <a href="{{ route('landing') }}" class="flex items-center gap-3 hover:opacity-80 transition">
                        @if ($settings->logo_url)
                            <img src="{{ $settings->logo_url }}" alt="{{ $appName }}" class="w-10 h-10 rounded-xl object-contain" />
                        @else
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-blue-500/30">
                                <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                                </svg>
                            </div>
                        @endif
                        <span class="text-xl font-bold">{{ $appName }}</span>
                    </a>
                    <span class="text-sm opacity-40 ml-2">/ Documentation</span>
                </div>
                <div class="flex items-center gap-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="px-5 py-2 text-sm font-medium rounded-xl hover:bg-white/10 transition">{{ __('Dashboard') }}</a>
                    @else
                        <a href="{{ route('login') }}" class="px-5 py-2 text-sm font-medium rounded-xl hover:bg-white/10 transition">{{ __('Sign In') }}</a>
                        <a href="{{ route('register') }}" class="px-5 py-2 text-sm font-medium rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white hover:from-blue-500 hover:to-indigo-500 transition shadow-lg shadow-blue-600/25">{{ __('Get Started') }}</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-6 pt-28 pb-16 relative" style="z-index: 2;">
        <div class="flex flex-col lg:flex-row gap-8">

            {{-- Sidebar Navigation --}}
            <aside class="lg:w-64 shrink-0">
                <div class="glass-card rounded-2xl p-4 lg:sticky lg:top-28 doc-nav space-y-1">
                    <p class="text-xs font-bold uppercase tracking-wider opacity-40 px-3 py-1">{{ __('Getting Started') }}</p>
                    <a href="#overview" class="block px-3 py-2 rounded-xl text-sm hover:bg-white/10 transition border border-transparent">{{ __('Overview') }}</a>
                    <a href="#registration" class="block px-3 py-2 rounded-xl text-sm hover:bg-white/10 transition border border-transparent">Registration & {{ __('Login') }}</a>
                    <a href="#2fa" class="block px-3 py-2 rounded-xl text-sm hover:bg-white/10 transition border border-transparent">Two-Factor {{ __('Authentication') }}</a>
                    <a href="#recovery" class="block px-3 py-2 rounded-xl text-sm hover:bg-white/10 transition border border-transparent">{{ __('Recovery Codes') }}</a>

                    <p class="text-xs font-bold uppercase tracking-wider opacity-40 px-3 py-1 mt-4">{{ __('Admin Guide') }}</p>
                    <a href="#admin-settings" class="block px-3 py-2 rounded-xl text-sm hover:bg-white/10 transition border border-transparent">{{ __('Admin Settings') }}</a>
                    <a href="#smtp" class="block px-3 py-2 rounded-xl text-sm hover:bg-white/10 transition border border-transparent">SMTP / {{ __('Email') }}</a>
                    <a href="#branding" class="block px-3 py-2 rounded-xl text-sm hover:bg-white/10 transition border border-transparent">{{ __('Branding') }} & Themes</a>
                    <a href="#landing-cms" class="block px-3 py-2 rounded-xl text-sm hover:bg-white/10 transition border border-transparent">{{ __('Landing Page') }} CMS</a>

                    <p class="text-xs font-bold uppercase tracking-wider opacity-40 px-3 py-1 mt-4">{{ __('Technical') }}</p>
                    <a href="#2fa-code" class="block px-3 py-2 rounded-xl text-sm hover:bg-white/10 transition border border-transparent">2FA Code Logic</a>
                    <a href="#architecture" class="block px-3 py-2 rounded-xl text-sm hover:bg-white/10 transition border border-transparent">{{ __('Architecture') }}</a>
                    <a href="#env" class="block px-3 py-2 rounded-xl text-sm hover:bg-white/10 transition border border-transparent">{{ __('Environment Setup') }}</a>
                </div>
            </aside>

            {{-- Main Content --}}
            <main class="flex-1 min-w-0 doc-content space-y-10">

                {{-- Overview --}}
                <section id="overview" class="glass-card rounded-2xl p-8">
                    <h2 class="text-3xl font-bold mb-4">Overview</h2>
                    <p class="opacity-70 leading-relaxed mb-4">
                        <strong>{{ $appName }}</strong> is a secure SaaS authentication platform built with <strong>Laravel 12</strong>, <strong>Jetstream</strong>, and <strong>Livewire 3</strong>.
                        It provides enterprise-grade two-factor authentication using Google Authenticator (TOTP), a beautiful glass morphism UI with GSAP animations, and a complete admin panel for managing every aspect of the site.
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="p-4 rounded-xl bg-blue-500/10 border border-blue-500/20">
                            <h4 class="font-bold text-blue-300 text-sm mb-1">Authentication</h4>
                            <p class="text-xs opacity-60">{{ __('Register') }}, login, email verify, password reset, 2FA with TOTP, recovery codes</p>
                        </div>
                        <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20">
                            <h4 class="font-bold text-emerald-300 text-sm mb-1">{{ __('Admin Panel') }}</h4>
                            <p class="text-xs opacity-60">{{ __('SMTP, branding, themes, fonts, backgrounds, landing page CMS, terms & privacy') }}</p>
                        </div>
                        <div class="p-4 rounded-xl bg-purple-500/10 border border-purple-500/20">
                            <h4 class="font-bold text-purple-300 text-sm mb-1">{{ __('Customization') }}</h4>
                            <p class="text-xs opacity-60">Glass morphism themes, video/image backgrounds, Google Fonts, GSAP animations</p>
                        </div>
                    </div>
                </section>

                {{-- {{ __('Registration & Login') }} --}}
                <section id="registration" class="glass-card rounded-2xl p-8">
                    <h2 class="text-3xl font-bold mb-4">Registration & Login</h2>
                    <ol class="opacity-70 leading-relaxed space-y-2">
                        <li><strong>Register</strong> — {{ __('Navigate to') }} <code>/register</code>. Fill in your name, email, and password. You must agree to {{ __('Terms') }} of Service and {{ __('Privacy') }} Policy.</li>
                        <li><strong>Email {{ __('Verification') }}</strong> — {{ __('After registration, a verification email is sent.') }} {{ __('Click') }} the link in the email to verify your account. You cannot access the dashboard until verified.</li>
                        <li><strong>Login</strong> — {{ __('Go to') }} <code>/login</code> and enter your credentials. If 2FA is enabled on your account, you'll be prompted for a TOTP code from your authenticator app.</li>
                        <li><strong>{{ __('Forgot Password') }}</strong> — Click "Forgot your password?" on the login page. Enter your email to receive a password reset link.</li>
                    </ol>
                </section>

                {{-- 2FA --}}
                <section id="2fa" class="glass-card rounded-2xl p-8">
                    <h2 class="text-3xl font-bold mb-4">Two-Factor Authentication (2FA)</h2>
                    <p class="opacity-70 leading-relaxed mb-4">
                        {{ __('This platform uses') }} <strong>TOTP (Time-based One-Time Password)</strong> for 2FA, compatible with Google Authenticator, Authy, Microsoft Authenticator, and similar apps.
                    </p>
                    <h3 class="text-xl font-bold mb-2">{{ __('How to Enable 2FA') }}</h3>
                    <ol class="opacity-70 leading-relaxed space-y-2 mb-6">
                        <li>{{ __('Log in and navigate to your') }} <strong>{{ __('Profile Settings') }}</strong> (click your avatar → Profile).</li>
                        <li>{{ __('Find the') }} <strong>{{ __('Two Factor Authentication') }}</strong> section and click <strong>"Enable"</strong>.</li>
                        <li>{{ __('You\'ll be asked to confirm your password for security.') }}</li>
                        <li>A <strong>QR code</strong> will appear. Open your authenticator app, scan it, and enter the 6-digit code shown in the app.</li>
                        <li>{{ __('Once confirmed, 2FA is active. You\'ll receive') }} <strong>recovery codes</strong> — save these in a secure location!</li>
                    </ol>
                    <h3 class="text-xl font-bold mb-2">{{ __('Using 2FA at Login') }}</h3>
                    <p class="opacity-70 leading-relaxed mb-2">
                        {{ __('After entering your email and password, you\'ll see a glass morphism challenge page asking for a 6-digit code. Open your authenticator app, find the code for this site, and enter it. Codes refresh every 30 seconds.') }}
                    </p>
                    <div class="p-3 rounded-xl bg-amber-500/10 border border-amber-500/20 mt-4">
                        <p class="text-xs opacity-70"><strong class="text-amber-300">{{ __('Important') }}:</strong> {{ __('If you lose your device, use a') }} <strong>recovery code</strong> instead. Each code can only be used once.</p>
                    </div>
                </section>

                {{-- Recovery Codes --}}
                <section id="recovery" class="glass-card rounded-2xl p-8">
                    <h2 class="text-3xl font-bold mb-4">Recovery Codes</h2>
                    <p class="opacity-70 leading-relaxed mb-4">
                        {{ __('When you enable 2FA, the system generates a set of') }} <strong>one-time recovery codes</strong>. These are your backup if you lose access to your authenticator app.
                    </p>
                    <ul class="opacity-70 leading-relaxed space-y-1">
                        <li>{{ __('Each recovery code can only be used') }} <strong>once</strong>.</li>
                        <li>Store them somewhere safe (password manager, printed copy in a secure location).</li>
                        <li>You can regenerate new codes from your Profile settings at any time (this invalidates old ones).</li>
                        <li>{{ __('If you run out of codes and lose your authenticator, contact the super admin for account recovery.') }}</li>
                    </ul>
                </section>

                {{-- Admin Settings --}}
                <section id="admin-settings" class="glass-card rounded-2xl p-8">
                    <h2 class="text-3xl font-bold mb-4">Admin Settings</h2>
                    <p class="opacity-70 leading-relaxed mb-4">
                        {{ __('The admin panel is at') }} <code>/admin/settings</code> and is only accessible to <strong>super admin</strong> users (role = <code>super_admin</code> in the database).
                    </p>
                    <p class="opacity-70 leading-relaxed mb-4">{{ __('The settings panel includes these components') }}:</p>
                    <ul class="opacity-70 leading-relaxed space-y-1">
                        <li><strong>SMTP Settings</strong> — Configure email delivery (host, port, username, password, encryption, from address). Includes a "Send Test Email" button.</li>
                        <li><strong>{{ __('Branding & Customization') }}</strong> — {{ __('App name, logo, favicon, footer text') }}. 7 tabs covering everything from landing hero text to video backgrounds.</li>
                        <li><strong>{{ __('Theme Designer') }}</strong> — Create/edit color themes with live preview. Set gradient colors, accent colors, enable/disable blobs.</li>
                        <li><strong>{{ __('Font Manager') }}</strong> — {{ __('Browse 1000+ Google Fonts with live search and preview. Cached API responses.') }}</li>
                        <li><strong>{{ __('Landing Page Editor') }}</strong> — CMS for custom content sections on the landing page (title, body, images, videos, buttons).</li>
                    </ul>
                </section>

                {{-- SMTP --}}
                <section id="smtp" class="glass-card rounded-2xl p-8">
                    <h2 class="text-3xl font-bold mb-4">{{ __('SMTP / Email Configuration') }}</h2>
                    <p class="opacity-70 leading-relaxed mb-4">
                        {{ __('Email is configured through the admin panel, not the') }} <code>.env</code> file. The app loads SMTP settings from the database on every request via <code>AppServiceProvider</code>.
                    </p>
                    <h3 class="text-xl font-bold mb-2">{{ __('Setup Steps') }}</h3>
                    <ol class="opacity-70 leading-relaxed space-y-2 mb-4">
                        <li>Go to <strong>Admin Settings → SMTP Settings</strong>.</li>
                        <li>Enter your SMTP host (e.g., <code>smtp.gmail.com</code>), port (<code>587</code> for TLS), username, password, and encryption (<code>tls</code>).</li>
                        <li>{{ __('Set the') }} <strong>{{ __('From Address') }}</strong> to match your SMTP domain. For Gmail, use your full Gmail address.</li>
                        <li>Click <strong>Save</strong>, then use <strong>"Send Test Email"</strong> to verify.</li>
                    </ol>
                    <div class="p-3 rounded-xl bg-red-500/10 border border-red-500/20">
                        <p class="text-xs opacity-70">
                            <strong class="text-red-300">Common Error: 550 "classified as SPAM"</strong> — {{ __('This happens when') }}:
                            (1) The "From Address" doesn't match your SMTP domain,
                            (2) SPF/DKIM/DMARC records aren't configured for your domain, or
                            (3) You're using a generic <code>hello@example.com</code> address. For Gmail, use an <strong>{{ __('App Password') }}</strong> (not your regular password).
                        </p>
                    </div>
                </section>

                {{-- Branding --}}
                <section id="branding" class="glass-card rounded-2xl p-8">
                    <h2 class="text-3xl font-bold mb-4">{{ __('Branding & Themes') }}</h2>
                    <p class="opacity-70 leading-relaxed mb-4">
                        The <strong>Branding & Customization</strong> component has 7 tabs:
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-4">
                        <div class="p-3 rounded-xl bg-white/5 border border-white/10">
                            <h4 class="font-bold text-sm mb-1">Branding</h4>
                            <p class="text-xs opacity-50">App name, logo, favicon, footer text</p>
                        </div>
                        <div class="p-3 rounded-xl bg-white/5 border border-white/10">
                            <h4 class="font-bold text-sm mb-1">Landing Page</h4>
                            <p class="text-xs opacity-50">Hero badge, heading lines, subtitle, CTA buttons, features/CTA visibility, visual effects</p>
                        </div>
                        <div class="p-3 rounded-xl bg-white/5 border border-white/10">
                            <h4 class="font-bold text-sm mb-1">{{ __('Auth BG / App BG / Landing BG') }}</h4>
                            <p class="text-xs opacity-50">{{ __('Gradient, image upload, or video upload per area') }}</p>
                        </div>
                        <div class="p-3 rounded-xl bg-white/5 border border-white/10">
                            <h4 class="font-bold text-sm mb-1">{{ __('Email Templates & Legal') }}</h4>
                            <p class="text-xs opacity-50">Verify/reset messages, terms of service, privacy policy (HTML)</p>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold mb-2">{{ __('Backgrounds') }}</h3>
                    <p class="opacity-70 leading-relaxed">
                        Each background area (Auth pages, App/Dashboard, Landing page) supports three modes: <strong>{{ __('Theme Gradient') }}</strong> (default), <strong>{{ __('Custom Image') }}</strong> (upload a JPEG/PNG), or <strong>{{ __('Custom Video') }}</strong> (upload an MP4/WebM, max 20MB). Videos loop automatically and are overlaid with a dark tint for readability.
                    </p>
                </section>

                {{-- Landing CMS --}}
                <section id="landing-cms" class="glass-card rounded-2xl p-8">
                    <h2 class="text-3xl font-bold mb-4">{{ __('Landing Page CMS') }}</h2>
                    <p class="opacity-70 leading-relaxed mb-4">
                        {{ __('The landing page has two layers of customization') }}:
                    </p>
                    <ol class="opacity-70 leading-relaxed space-y-2">
                        <li><strong>{{ __('Global Settings') }}</strong> (Branding → Landing Page tab) — Controls the hero section text, badge, CTA button labels/URLs, features/CTA section titles and visibility, floating cards, and particles.</li>
                        <li><strong>CMS Sections</strong> (Landing Page Editor) — Add unlimited custom content sections between features and CTA. Each section can have a title, subtitle, rich text body, image, embedded video, and a CTA button. Drag to reorder, toggle visibility.</li>
                    </ol>
                </section>

                {{-- 2FA Code Logic --}}
                <section id="2fa-code" class="glass-card rounded-2xl p-8">
                    <h2 class="text-3xl font-bold mb-4">2FA Code Logic (Technical)</h2>
                    <p class="opacity-70 leading-relaxed mb-4">
                        {{ __('This app uses Laravel Fortify\'s built-in 2FA system with Google Authenticator compatibility') }}:
                    </p>
                    <h3 class="text-lg font-bold mb-2">{{ __('How TOTP Works') }}</h3>
                    <ul class="opacity-70 leading-relaxed space-y-1 mb-4">
                        <li><strong>{{ __('Secret Key') }}</strong> — {{ __('When the user enables 2FA, a random secret is generated and stored encrypted in') }} <code>users.two_factor_secret</code>. The QR code encodes this secret.</li>
                        <li><strong>{{ __('Code Generation') }}</strong> — The authenticator app uses the shared secret + current time (in 30-second intervals) with HMAC-SHA1 to produce a 6-digit code.</li>
                        <li><strong>Verification</strong> — {{ __('On login, the server computes the expected code using the same secret + time window and compares it. It checks the current window and ±1 to account for clock drift.') }}</li>
                        <li><strong>Recovery Codes</strong> — {{ __('Stored as hashed values in') }} <code>users.two_factor_recovery_codes</code>. Each code is single-use and removed after use.</li>
                    </ul>
                    <h3 class="text-lg font-bold mb-2">{{ __('Key Files') }}</h3>
                    <pre><code>app/Actions/Fortify/          # Custom Fortify actions
config/fortify.php            # Fortify config (features, guards)
resources/views/auth/         # Auth views including 2FA challenge
app/Providers/FortifyServiceProvider.php  # Customizes 2FA views</code></pre>
                    <p class="opacity-70 leading-relaxed mt-2">
                        The 2FA challenge page (<code>two-factor-challenge</code>) is styled with glass morphism and shows a numeric keypad UI for entering the 6-digit code or switching to recovery code input.
                    </p>
                </section>

                {{-- Architecture --}}
                <section id="architecture" class="glass-card rounded-2xl p-8">
                    <h2 class="text-3xl font-bold mb-4">Architecture</h2>
                    <pre><code>├── app/
│   ├── Livewire/Admin/       # Admin panel Livewire components
│   │   ├── BrandingSettings  # Branding, backgrounds, landing hero, legal
│   │   ├── SmtpSettings      # SMTP config + test email
│   │   ├── ThemeDesigner     # Theme creation/editing
│   │   ├── FontManager       # Google Fonts browser
│   │   └── LandingPageEditor # CMS sections CRUD
│   ├── Models/
│   │   ├── Setting.php       # Singleton settings (cached, all site config)
│   │   ├── Theme.php         # Color themes with CSS variables
│   │   └── LandingSection.php # CMS content sections
│   └── Providers/
│       └── AppServiceProvider # Loads SMTP from DB, forces HTTPS
├── resources/views/
│   ├── landing.blade.php     # Public landing page (GSAP animated)
│   ├── dashboard.blade.php   # Auth dashboard
│   ├── documentation.blade.php # This page
│   ├── layouts/
│   │   ├── app.blade.php     # Authenticated layout (app bg support)
│   │   └── guest.blade.php   # Auth layout (auth bg support)
│   └── livewire/admin/       # Admin component views
└── database/migrations/      # All schema definitions</code></pre>
                </section>

                {{-- Environment Setup --}}
                <section id="env" class="glass-card rounded-2xl p-8">
                    <h2 class="text-3xl font-bold mb-4">Environment Setup</h2>
                    <p class="opacity-70 leading-relaxed mb-4">
                        {{ __('For developers setting up the project locally') }}:
                    </p>
                    <pre><code># Clone & install
composer install
npm install

# Configure .env
cp .env.example .env
php artisan key:generate

# Database
php artisan migrate
php artisan storage:link

# Build & serve
npm run build
php artisan serve</code></pre>
                    <h3 class="text-lg font-bold mb-2 mt-4">{{ __('Key Environment Variables') }}</h3>
                    <ul class="opacity-70 leading-relaxed space-y-1">
                        <li><code>APP_URL</code> — Must match your actual URL (important for Cloudflare tunnels).</li>
                        <li><code>DB_DATABASE</code>, <code>DB_USERNAME</code>, <code>DB_PASSWORD</code> — MySQL connection.</li>
                        <li><code>MAIL_*</code> — Default mail settings (overridden by admin DB settings at runtime).</li>
                        <li><code>{{ __('GOOGLE_FONTS_API_KEY') }}</code> — {{ __('Required for the font manager to browse Google Fonts.') }}</li>
                    </ul>
                    <div class="p-3 rounded-xl bg-blue-500/10 border border-blue-500/20 mt-4">
                        <p class="text-xs opacity-70"><strong class="text-blue-300">HTTPS Note:</strong> {{ __('The app forces HTTPS via') }} <code>URL::forceScheme('https')</code> in AppServiceProvider and trusts all proxies for Cloudflare tunnel compatibility.</p>
                    </div>
                </section>

            </main>
        </div>
    </div>

    {{-- Footer --}}
    @php
        $footerShowTerms = (bool) ($settings->footer_show_terms ?? true);
        $footerShowPrivacy = (bool) ($settings->footer_show_privacy ?? true);
        $footerLinks = $settings->footer_links ?? [];
        $footerSocialLinks = $settings->footer_social_links ?? [];
        $footerText = $settings->footer_text ?: '&copy; ' . date('Y') . ' ' . e($appName);
    @endphp
    <footer class="border-t border-white/10 py-8 px-6 relative" style="z-index: 2;">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-3">
                @if ($settings->logo_url)
                    <img src="{{ $settings->logo_url }}" alt="{{ $appName }}" class="w-8 h-8 rounded-lg object-contain" />
                @endif
                <span class="font-bold">{{ $appName }}</span>
            </div>
            <div class="flex items-center gap-6 text-sm opacity-50 flex-wrap">
                <a href="{{ route('landing') }}" class="hover:opacity-100 transition">{{ __('Home') }}</a>
                @foreach ($footerLinks as $link)
                    <a href="{{ $link['url'] }}" target="_blank" rel="noopener noreferrer" class="hover:opacity-100 transition">{{ $link['label'] }}</a>
                @endforeach
                @if ($footerShowTerms && Route::has('terms.show'))
                    <a href="{{ route('terms.show') }}" class="hover:opacity-100 transition">Terms</a>
                @endif
                @if ($footerShowPrivacy && Route::has('policy.show'))
                    <a href="{{ route('policy.show') }}" class="hover:opacity-100 transition">Privacy</a>
                @endif
            </div>
            <div class="flex items-center gap-3">
                @if (count($footerSocialLinks) > 0)
                    <div class="flex items-center gap-2 opacity-50">
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
    </footer>

    <script>
        // Highlight active sidebar link on scroll
        const sections = document.querySelectorAll('.doc-content section[id]');
        const navLinks = document.querySelectorAll('.doc-nav a[href^="#"]');
        const observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    navLinks.forEach(l => l.classList.remove('active'));
                    const link = document.querySelector(`.doc-nav a[href="#${entry.target.id}"]`);
                    if (link) link.classList.add('active');
                }
            });
        }, { rootMargin: '-20% 0px -60% 0px' });
        sections.forEach(s => observer.observe(s));
    </script>
</body>
</html>
