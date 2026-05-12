<x-app-layout>
    @php $section = request()->query('section', 'branding'); @endphp

    <div class="py-4 px-2 sm:px-4 lg:px-6 min-h-[calc(100vh-2rem)]">
        <div class="w-full">
            <div x-data="{ activeSection: '{{ $section }}' }"
                 x-on:popstate.window="activeSection = new URLSearchParams(window.location.search).get('section') || 'branding'">

                @php
                    $allSections = [
                        'branding' => 'admin.branding-settings',
                        'themes' => 'admin.theme-designer',
                        'theme-mode' => 'admin.theme-settings',
                        'custom-theme' => 'admin.custom-theme-settings',
                        'fonts' => 'admin.font-settings',
                        'languages' => 'admin.language-settings',
                        'sidebar' => 'admin.sidebar-settings',
                        'auth-card' => 'admin.auth-style-settings',
                        'footer' => 'admin.footer-settings',
                        'landing' => 'admin.landing-page-editor',
                        'security' => 'admin.security-settings',
                        'smtp' => 'admin.smtp-settings',
                        'captcha' => 'admin.captcha-settings',
                        'social' => 'admin.social-settings',
                        'timezone' => 'admin.timezone-settings',
                        'api' => 'admin.api-settings',
                    ];
                @endphp
                @foreach ($allSections as $sectionKey => $component)
                    <div x-show="activeSection === '{{ $sectionKey }}'"
                         style="{{ $section !== $sectionKey ? 'display:none' : '' }}">
                        @livewire($component, key($sectionKey))
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
