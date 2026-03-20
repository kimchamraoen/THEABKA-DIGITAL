@php
    $settings = \App\Models\Setting::instance();
    $appName = $settings->app_name;
    $footerSticky = (bool) ($settings->footer_sticky ?? true);
    $footerGlass = (bool) ($settings->footer_glass ?? true);
    $footerShowCopyright = (bool) ($settings->footer_show_copyright ?? true);
    $footerShowTerms = (bool) ($settings->footer_show_terms ?? true);
    $footerShowPrivacy = (bool) ($settings->footer_show_privacy ?? true);
    $footerShowDocs = (bool) ($settings->footer_show_docs ?? true);
    $footerText = $settings->footer_text ?: '&copy; ' . date('Y') . ' ' . e($appName) . '. All rights reserved.';
    $footerLinks = $settings->footer_links ?? [];
    $footerSocialLinks = $settings->footer_social_links ?? [];
    $hasLinks = $footerShowTerms || $footerShowPrivacy || $footerShowDocs || count($footerLinks) > 0;
@endphp

@if ($footerGlass)
{{-- Glass Card Style --}}
<footer class="glass-card rounded-2xl mx-4 mb-4 {{ $footerSticky ? '' : 'mt-6' }}">
    <div class="px-5 py-3 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
        @if ($footerShowCopyright)
            <p class="opacity-50">{!! $footerText !!}</p>
        @endif

        @if ($hasLinks)
            <div class="flex items-center gap-4 flex-wrap justify-center">
                @foreach ($footerLinks as $link)
                    <a href="{{ $link['url'] }}" target="_blank" rel="noopener noreferrer" class="opacity-50 hover:opacity-100 transition">{{ $link['label'] }}</a>
                @endforeach
                @if ($footerShowTerms && Route::has('terms.show'))
                    <a href="{{ route('terms.show') }}" class="opacity-50 hover:opacity-100 transition">{{ __('app.footer.terms') }}</a>
                @endif
                @if ($footerShowPrivacy && Route::has('policy.show'))
                    <a href="{{ route('policy.show') }}" class="opacity-50 hover:opacity-100 transition">{{ __('app.footer.privacy') }}</a>
                @endif
                @if ($footerShowDocs)
                    <a href="{{ route('documentation') }}" class="opacity-50 hover:opacity-100 transition">{{ __('app.footer.docs') }}</a>
                @endif
            </div>
        @endif

        @if (count($footerSocialLinks) > 0)
            <div class="flex items-center gap-3">
                @foreach ($footerSocialLinks as $social)
                    <a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer" class="opacity-50 hover:opacity-100 transition" title="{{ ucfirst($social['platform']) }}">
                        @include('components.social-icon', ['platform' => $social['platform'], 'size' => 'w-4 h-4'])
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</footer>
@else
{{-- Flat Bar Style (like reference image) --}}
<footer class="w-full border-t border-white/10 bg-black/30 {{ $footerSticky ? '' : 'mt-6' }}">
    <div class="px-6 py-5 flex flex-col items-center gap-4">
        {{-- Social Icons Row (circles) --}}
        @if (count($footerSocialLinks) > 0)
            <div class="flex items-center gap-4">
                @foreach ($footerSocialLinks as $social)
                    <a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer"
                       class="w-10 h-10 rounded-full border border-white/20 flex items-center justify-center opacity-60 hover:opacity-100 hover:bg-white/10 transition-all"
                       title="{{ ucfirst($social['platform']) }}">
                        @include('components.social-icon', ['platform' => $social['platform'], 'size' => 'w-5 h-5'])
                    </a>
                @endforeach
            </div>
        @endif

        {{-- Links Row --}}
        @if ($hasLinks)
            <div class="flex items-center gap-6 flex-wrap justify-center text-sm">
                @foreach ($footerLinks as $link)
                    <a href="{{ $link['url'] }}" target="_blank" rel="noopener noreferrer" class="opacity-60 hover:opacity-100 transition">{{ $link['label'] }}</a>
                @endforeach
                @if ($footerShowTerms && Route::has('terms.show'))
                    <a href="{{ route('terms.show') }}" class="opacity-60 hover:opacity-100 transition">{{ __('app.footer.terms') }}</a>
                @endif
                @if ($footerShowPrivacy && Route::has('policy.show'))
                    <a href="{{ route('policy.show') }}" class="opacity-60 hover:opacity-100 transition">{{ __('app.footer.privacy') }}</a>
                @endif
                @if ($footerShowDocs)
                    <a href="{{ route('documentation') }}" class="opacity-60 hover:opacity-100 transition">{{ __('app.footer.docs') }}</a>
                @endif
            </div>
        @endif

        {{-- Copyright --}}
        @if ($footerShowCopyright)
            <p class="text-xs opacity-40">{!! $footerText !!}</p>
        @endif
    </div>
</footer>
@endif
