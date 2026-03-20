@php
    $navSettings = \App\Models\Setting::instance();
    $navAppName = $navSettings->app_name;
    $sidebarFontSize = $navSettings->sidebar_font_size ?? 15;
    $sidebarIconSize = $navSettings->sidebar_icon_size ?? 20;
    $sidebarWidth = $navSettings->sidebar_width ?? 360;
    $sidebarCollapsed = $navSettings->sidebar_collapsed_width ?? 72;
    $sidebarActiveBgColor = $navSettings->sidebar_active_bg_color ?? 'rgba(255,255,255,0.15)';
    $sidebarActiveBorderColor = $navSettings->sidebar_active_border_color ?? 'rgba(255,255,255,0.2)';
    $sidebarActiveBorderRadius = $navSettings->sidebar_active_border_radius ?? 12;

    $activeLanguages = cache()->remember('active_languages_sidebar', 3600, function () {
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

{{-- Sidebar Navigation - iOS 26 Glass Style --}}
<div x-data="{
    ready: false,
        sidebarOpen: $persist(true).as('sidebar-open'),
        mobileOpen: false,
        isMobile: window.innerWidth < 1024,
        desktopOpenWidth: {{ $sidebarWidth }},
        desktopCollapsedWidth: {{ $sidebarCollapsed }},
        mobileWidth: {{ $sidebarWidth }},
        init() {
            // Prevent any animation on initial page load
            this.handleViewport();
            window.addEventListener('resize', () => this.handleViewport());
            this.$watch('sidebarOpen', value => {
                if (!this.isMobile) {
                    window.dispatchEvent(new CustomEvent('sidebar-state-changed', { detail: { open: value } }));
                }
            });
            {{-- Listen for real-time sidebar preview from settings sliders --}}
            window.addEventListener('sidebar-preview', (e) => {
                let d = e.detail;
                if (d.width) {
                    this.desktopOpenWidth = d.width;
                    this.mobileWidth = d.width;
                }
                if (d.collapsedWidth) {
                    this.desktopCollapsedWidth = d.collapsedWidth;
                }
                {{-- Notify content area of width changes --}}
                window.dispatchEvent(new CustomEvent('sidebar-width-changed', {
                    detail: { width: d.width || this.desktopOpenWidth, collapsedWidth: d.collapsedWidth || this.desktopCollapsedWidth }
                }));
                {{-- Update CSS custom properties on the glass card --}}
                let card = this.$el.querySelector('.glass-card');
                if (card && d.fontSize) card.style.setProperty('--sb-font', d.fontSize + 'px');
                if (card && d.iconSize) {
                    card.style.setProperty('--sb-icon', d.iconSize + 'px');
                    card.style.setProperty('--sb-icon-wrap', (d.iconSize + 8) + 'px');
                }
                if (card && d.activeBgColor) card.style.setProperty('--sb-active-bg', d.activeBgColor);
                if (card && d.activeBorderColor) card.style.setProperty('--sb-active-border', d.activeBorderColor);
                if (card && d.activeBorderRadius !== undefined) card.style.setProperty('--sb-active-radius', d.activeBorderRadius + 'px');
            });
            window.addEventListener('sidebar-settings-updated', (e) => {
                if (e.detail && e.detail[0]) {
                    let d = e.detail[0];
                    if (d.width) { this.desktopOpenWidth = d.width; this.mobileWidth = d.width; }
                }
            });
            // Enable transitions only after first render cycle completes
            // Use double requestAnimationFrame to ensure DOM is fully painted
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    this.ready = true;
                    document.documentElement.style.removeProperty('--sb-init-w');
                    document.documentElement.removeAttribute('data-sidebar-prehydrated');
                });
            });
        },
        handleViewport() {
            this.isMobile = window.innerWidth < 1024;

            if (this.isMobile) {
                this.mobileOpen = false;
            } else {
                window.dispatchEvent(new CustomEvent('sidebar-state-changed', { detail: { open: this.sidebarOpen } }));
            }
        },
        getSidebarWidth() {
            return this.isMobile
                ? `width: ${this.mobileWidth}px`
                : (this.sidebarOpen
                    ? `width: ${this.desktopOpenWidth}px`
                    : `width: ${this.desktopCollapsedWidth}px`);
        },
        toggleSidebar() {
            this.sidebarOpen = !this.sidebarOpen;
        }
     }"
     x-on:keydown.escape.window="mobileOpen = false"
     x-on:toggle-mobile-sidebar.window="mobileOpen = !mobileOpen"
     class="relative z-50">

    {{-- Mobile Overlay --}}
    <div x-show="mobileOpen" x-transition:enter="transition-opacity ease-out duration-300"
         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-200"
         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         @click="mobileOpen = false"
         class="fixed inset-0 bg-black/40 backdrop-blur-sm z-40 lg:hidden" style="display:none;"></div>

    {{-- Sidebar --}}
        <aside :class="[
               mobileOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
               ready ? 'transition-[width,transform] duration-500 ease-[cubic-bezier(0.22,1,0.36,1)]' : 'sidebar-no-transition'
           ]"
           class="fixed top-0 left-0 h-full z-50 flex flex-col p-3 will-change-[width,transform]"
            style="width: var(--sb-init-w, {{ $sidebarWidth }}px)"
            :style="getSidebarWidth()">

        <div class="flex flex-col h-full glass-card rounded-2xl overflow-hidden transition-all duration-500 ease-[cubic-bezier(0.22,1,0.36,1)]"
             style="--sb-font: {{ $sidebarFontSize }}px; --sb-icon: {{ $sidebarIconSize }}px; --sb-icon-wrap: {{ $sidebarIconSize + 8 }}px; --sb-active-bg: {{ $sidebarActiveBgColor }}; --sb-active-border: {{ $sidebarActiveBorderColor }}; --sb-active-radius: {{ $sidebarActiveBorderRadius }}px;">

            {{-- Logo Header --}}
              <div class="flex items-center border-b border-white/10 transition-all duration-500 ease-[cubic-bezier(0.22,1,0.36,1)]"
                 :class="sidebarOpen ? 'gap-3 px-4 py-5' : 'flex-col gap-2 px-2 py-4'">
                <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-3 shrink-0"
                   :class="!sidebarOpen && 'justify-center'">
                    @if ($navSettings->logo_url)
                        <img src="{{ $navSettings->logo_url }}" alt="{{ $navAppName }}" class="w-9 h-9 rounded-xl object-contain shrink-0">
                    @else
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-blue-500/25 shrink-0">
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                            </svg>
                        </div>
                    @endif
                      <span x-show="sidebarOpen"
                          :class="ready ? 'transition-all duration-300 ease-out' : ''"
                          class="font-bold text-base whitespace-nowrap overflow-hidden">{{ $navAppName }}</span>
                </a>
                {{-- Collapse toggle (desktop only) - always visible --}}
                <button @click="toggleSidebar()"
                        class="hidden lg:flex p-1.5 rounded-lg hover:bg-white/10 transition shrink-0 opacity-50 hover:opacity-100"
                        :class="sidebarOpen ? 'ml-auto' : 'mx-auto'">
                    <svg x-show="sidebarOpen" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                    </svg>
                    <svg x-show="!sidebarOpen" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                    </svg>
                </button>
            </div>

            {{-- Navigation Links --}}
              <nav class="flex-1 flex flex-col overflow-y-auto space-y-1 transition-all duration-500 ease-[cubic-bezier(0.22,1,0.36,1)]"
                 :class="sidebarOpen ? 'p-2' : 'p-1.5'">
                {{-- Dashboard --}}
                @if (is_nav_visible('sidebar.dashboard'))
                <a href="{{ route('dashboard') }}" wire:navigate
                   class="flex items-center transition-all duration-200 group
                          {{ request()->routeIs('dashboard') ? '' : 'hover:bg-white/8' }}"
                   :class="sidebarOpen ? 'gap-3 px-3 py-2.5' : 'justify-center px-1 py-2.5'"
                   style="order: {{ get_nav_sort_order('sidebar.dashboard', 10) }}; border-radius: var(--sb-active-radius); {{ request()->routeIs('dashboard') ? 'background: var(--sb-active-bg); border: 1px solid var(--sb-active-border);' : 'border: 1px solid transparent;' }}"
                   :title="!sidebarOpen ? '{{ get_nav_label('sidebar.dashboard', __('app.nav.dashboard')) }}' : ''">
                    <div class="rounded-lg flex items-center justify-center shrink-0
                                {{ request()->routeIs('dashboard') ? 'bg-blue-500/20 text-blue-400' : 'text-white/50 group-hover:text-white/80' }}"
                         style="width: var(--sb-icon-wrap); height: var(--sb-icon-wrap)">
                        {!! get_icon('sidebar.dashboard', '<svg style="width: var(--sb-icon); height: var(--sb-icon)" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" /></svg>') !!}
                    </div>
                    <span x-show="sidebarOpen" :class="ready ? 'transition-all duration-250 ease-out' : ''" class="font-medium whitespace-nowrap {{ request()->routeIs('dashboard') ? '' : 'opacity-70 group-hover:opacity-100' }}" style="font-size: var(--sb-font)">{{ get_nav_label('sidebar.dashboard', __('app.nav.dashboard')) }}</span>
                </a>
                @endif

                {{-- Chatbot --}}
                @if (is_nav_visible('sidebar.chatbot'))
                <a href="{{ route('chatbot.index') }}"
                   class="flex items-center transition-all duration-200 group
                          {{ request()->routeIs('chatbot.*') ? '' : 'hover:bg-white/8' }}"
                   :class="sidebarOpen ? 'gap-3 px-3 py-2.5' : 'justify-center px-1 py-2.5'"
                   style="order: {{ get_nav_sort_order('sidebar.chatbot', 20) }}; border-radius: var(--sb-active-radius); {{ request()->routeIs('chatbot.*') ? 'background: var(--sb-active-bg); border: 1px solid var(--sb-active-border);' : 'border: 1px solid transparent;' }}"
                   :title="!sidebarOpen ? '{{ get_nav_label('sidebar.chatbot', __('Chatbot')) }}' : ''">
                    <div class="rounded-lg flex items-center justify-center shrink-0
                                {{ request()->routeIs('chatbot.*') ? 'bg-cyan-500/20 text-cyan-400' : 'text-white/50 group-hover:text-white/80' }}"
                         style="width: var(--sb-icon-wrap); height: var(--sb-icon-wrap)">
                        {!! get_icon('sidebar.chatbot', '<svg style="width: var(--sb-icon); height: var(--sb-icon)" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z" /></svg>') !!}
                    </div>
                    <span x-show="sidebarOpen" :class="ready ? 'transition-all duration-250 ease-out' : ''" class="font-medium whitespace-nowrap {{ request()->routeIs('chatbot.*') ? '' : 'opacity-70 group-hover:opacity-100' }}" style="font-size: var(--sb-font)">{{ get_nav_label('sidebar.chatbot', __('Chatbot')) }}</span>
                </a>
                @endif

                     {{-- Profile --}}
                     @if (is_nav_visible('sidebar.profile'))
                <a href="{{ route('profile.show') }}" wire:navigate
                   class="flex items-center transition-all duration-200 group
                          {{ request()->routeIs('profile.show') ? '' : 'hover:bg-white/8' }}"
                   :class="sidebarOpen ? 'gap-3 px-3 py-2.5' : 'justify-center px-1 py-2.5'"
                         style="order: {{ get_nav_sort_order('sidebar.profile', 30) }}; border-radius: var(--sb-active-radius); {{ request()->routeIs('profile.show') ? 'background: var(--sb-active-bg); border: 1px solid var(--sb-active-border);' : 'border: 1px solid transparent;' }}"
                         :title="!sidebarOpen ? '{{ get_nav_label('sidebar.profile', __('app.nav.profile')) }}' : ''">
                    <div class="rounded-lg flex items-center justify-center shrink-0
                                {{ request()->routeIs('profile.show') ? 'bg-purple-500/20 text-purple-400' : 'text-white/50 group-hover:text-white/80' }}"
                         style="width: var(--sb-icon-wrap); height: var(--sb-icon-wrap)">
                        {!! get_icon('sidebar.profile', '<svg style="width: var(--sb-icon); height: var(--sb-icon)" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>') !!}
                    </div>
                    <span x-show="sidebarOpen" :class="ready ? 'transition-all duration-250 ease-out' : ''" class="font-medium whitespace-nowrap {{ request()->routeIs('profile.show') ? '' : 'opacity-70 group-hover:opacity-100' }}" style="font-size: var(--sb-font)">{{ get_nav_label('sidebar.profile', __('app.nav.profile')) }}</span>
                </a>
                @endif

                     {{-- Help & Documentation --}}
                     @if (is_nav_visible('sidebar.docs'))
                <a href="{{ route('documentation') }}"
                   class="flex items-center transition-all duration-200 group
                          {{ request()->routeIs('documentation') ? '' : 'hover:bg-white/8' }}"
                   :class="sidebarOpen ? 'gap-3 px-3 py-2.5' : 'justify-center px-1 py-2.5'"
                         style="order: {{ get_nav_sort_order('sidebar.docs', 40) }}; border-radius: var(--sb-active-radius); {{ request()->routeIs('documentation') ? 'background: var(--sb-active-bg); border: 1px solid var(--sb-active-border);' : 'border: 1px solid transparent;' }}"
                         :title="!sidebarOpen ? '{{ get_nav_label('sidebar.docs', __('app.nav.docs')) }}' : ''">
                    <div class="rounded-lg flex items-center justify-center shrink-0
                                {{ request()->routeIs('documentation') ? 'bg-teal-500/20 text-teal-400' : 'text-white/50 group-hover:text-white/80' }}"
                         style="width: var(--sb-icon-wrap); height: var(--sb-icon-wrap)">
                        {!! get_icon('sidebar.docs', '<svg style="width: var(--sb-icon); height: var(--sb-icon)" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" /></svg>') !!}
                    </div>
                    <span x-show="sidebarOpen" :class="ready ? 'transition-all duration-250 ease-out' : ''" class="font-medium whitespace-nowrap {{ request()->routeIs('documentation') ? '' : 'opacity-70 group-hover:opacity-100' }}" style="font-size: var(--sb-font)">{{ get_nav_label('sidebar.docs', __('app.nav.docs')) }}</span>
                </a>
                @endif

                {{-- Admin Settings --}}
                @if (auth()->user()->isSuperAdmin() || auth()->user()->isAdmin())
                    <div class="pt-2 pb-1 px-3" x-show="sidebarOpen" x-transition.opacity>
                        <p class="text-[10px] font-bold uppercase tracking-widest opacity-30">{{ __('app.nav.admin') }}</p>
                    </div>

                          {{-- Analytics --}}
                          @if (is_nav_visible('sidebar.analytics'))
                    <a href="{{ route('admin.analytics') }}" wire:navigate
                       class="flex items-center transition-all duration-200 group
                              {{ request()->routeIs('admin.analytics*') ? '' : 'hover:bg-white/8' }}"
                       :class="sidebarOpen ? 'gap-3 px-3 py-2.5' : 'justify-center px-1 py-2.5'"
                              style="order: {{ get_nav_sort_order('sidebar.analytics', 50) }}; border-radius: var(--sb-active-radius); {{ request()->routeIs('admin.analytics*') ? 'background: var(--sb-active-bg); border: 1px solid var(--sb-active-border);' : 'border: 1px solid transparent;' }}"
                              :title="!sidebarOpen ? '{{ get_nav_label('sidebar.analytics', __('Analytics')) }}' : ''">
                        <div class="rounded-lg flex items-center justify-center shrink-0
                                    {{ request()->routeIs('admin.analytics*') ? 'bg-violet-500/20 text-violet-400' : 'text-white/50 group-hover:text-white/80' }}"
                             style="width: var(--sb-icon-wrap); height: var(--sb-icon-wrap)">
                            {!! get_icon('sidebar.analytics', '<svg style="width: var(--sb-icon); height: var(--sb-icon)" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3v18h18" /><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 15.75 11 12l3 2.25 3.5-5.25" /></svg>') !!}
                        </div>
                        <span x-show="sidebarOpen" :class="ready ? 'transition-all duration-250 ease-out' : ''" class="font-medium whitespace-nowrap {{ request()->routeIs('admin.analytics*') ? '' : 'opacity-70 group-hover:opacity-100' }}" style="font-size: var(--sb-font)">{{ get_nav_label('sidebar.analytics', __('Analytics')) }}</span>
                    </a>
                    @endif

                          {{-- Broadcasts --}}
                          @if (is_nav_visible('sidebar.broadcasts'))
                    <a href="{{ route('admin.broadcasts') }}" wire:navigate
                       class="flex items-center transition-all duration-200 group
                              {{ request()->routeIs('admin.broadcasts') ? '' : 'hover:bg-white/8' }}"
                       :class="sidebarOpen ? 'gap-3 px-3 py-2.5' : 'justify-center px-1 py-2.5'"
                              style="order: {{ get_nav_sort_order('sidebar.broadcasts', 60) }}; border-radius: var(--sb-active-radius); {{ request()->routeIs('admin.broadcasts') ? 'background: var(--sb-active-bg); border: 1px solid var(--sb-active-border);' : 'border: 1px solid transparent;' }}"
                              :title="!sidebarOpen ? '{{ get_nav_label('sidebar.broadcasts', __('app.nav.broadcasts')) }}' : ''">
                        <div class="rounded-lg flex items-center justify-center shrink-0
                                    {{ request()->routeIs('admin.broadcasts') ? 'bg-rose-500/20 text-rose-400' : 'text-white/50 group-hover:text-white/80' }}"
                             style="width: var(--sb-icon-wrap); height: var(--sb-icon-wrap)">
                            {!! get_icon('sidebar.broadcasts', '<svg style="width: var(--sb-icon); height: var(--sb-icon)" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 1 1 0-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38c-.551.318-1.26.117-1.527-.461a20.845 20.845 0 0 1-1.44-4.282m3.102.069a18.03 18.03 0 0 1-.59-4.59c0-1.586.205-3.124.59-4.59m0 9.18a23.848 23.848 0 0 1 8.835 2.535M10.34 6.66a23.847 23.847 0 0 0 8.835-2.535m0 0A23.74 23.74 0 0 0 18.795 3m.38 1.125a23.91 23.91 0 0 1 1.014 5.395m-1.014 8.855c-.118.38-.245.754-.38 1.125m.38-1.125a23.91 23.91 0 0 0 1.014-5.395m0-3.46c.495.413.811 1.035.811 1.73 0 .695-.316 1.317-.811 1.73m0-3.46a24.347 24.347 0 0 1 0 3.46" /></svg>') !!}
                        </div>
                        <span x-show="sidebarOpen" :class="ready ? 'transition-all duration-250 ease-out' : ''" class="font-medium whitespace-nowrap {{ request()->routeIs('admin.broadcasts') ? '' : 'opacity-70 group-hover:opacity-100' }}" style="font-size: var(--sb-font)">{{ get_nav_label('sidebar.broadcasts', __('app.nav.broadcasts')) }}</span>
                    </a>
                    @endif
                @endif

                @if (auth()->user()->isSuperAdmin())
                    {{-- User Management --}}
                    @if (is_nav_visible('sidebar.users'))
                    <a href="{{ route('admin.users') }}" wire:navigate
                       class="flex items-center transition-all duration-200 group
                              {{ request()->routeIs('admin.users') ? '' : 'hover:bg-white/8' }}"
                       :class="sidebarOpen ? 'gap-3 px-3 py-2.5' : 'justify-center px-1 py-2.5'"
                       style="order: {{ get_nav_sort_order('sidebar.users', 70) }}; border-radius: var(--sb-active-radius); {{ request()->routeIs('admin.users') ? 'background: var(--sb-active-bg); border: 1px solid var(--sb-active-border);' : 'border: 1px solid transparent;' }}"
                       :title="!sidebarOpen ? '{{ get_nav_label('sidebar.users', __('app.nav.users')) }}' : ''">
                        <div class="rounded-lg flex items-center justify-center shrink-0
                                    {{ request()->routeIs('admin.users') ? 'bg-cyan-500/20 text-cyan-400' : 'text-white/50 group-hover:text-white/80' }}"
                             style="width: var(--sb-icon-wrap); height: var(--sb-icon-wrap)">
                            {!! get_icon('sidebar.users', '<svg style="width: var(--sb-icon); height: var(--sb-icon)" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" /></svg>') !!}
                        </div>
                        <span x-show="sidebarOpen" :class="ready ? 'transition-all duration-250 ease-out' : ''" class="font-medium whitespace-nowrap {{ request()->routeIs('admin.users') ? '' : 'opacity-70 group-hover:opacity-100' }}" style="font-size: var(--sb-font)">{{ get_nav_label('sidebar.users', __('app.nav.users')) }}</span>
                    </a>
                    @endif

                    {{-- Translations --}}
                    @if (is_nav_visible('sidebar.translations'))
                    <a href="{{ route('admin.translations.index') }}" wire:navigate
                       class="flex items-center transition-all duration-200 group
                              {{ request()->routeIs('admin.translations.*') ? '' : 'hover:bg-white/8' }}"
                       :class="sidebarOpen ? 'gap-3 px-3 py-2.5' : 'justify-center px-1 py-2.5'"
                       style="order: {{ get_nav_sort_order('sidebar.translations', 80) }}; border-radius: var(--sb-active-radius); {{ request()->routeIs('admin.translations.*') ? 'background: var(--sb-active-bg); border: 1px solid var(--sb-active-border);' : 'border: 1px solid transparent;' }}"
                       :title="!sidebarOpen ? '{{ get_nav_label('sidebar.translations', __('app.nav.translations')) }}' : ''">
                        <div class="rounded-lg flex items-center justify-center shrink-0
                                    {{ request()->routeIs('admin.translations.*') ? 'bg-emerald-500/20 text-emerald-400' : 'text-white/50 group-hover:text-white/80' }}"
                             style="width: var(--sb-icon-wrap); height: var(--sb-icon-wrap)">
                            {!! get_icon('sidebar.translations', '<svg style="width: var(--sb-icon); height: var(--sb-icon)" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m10.5 21 5.25-11.25L21 21m-9-3h7.5M3 5.621a48.474 48.474 0 0 1 6-.371m0 0c1.12 0 2.233.038 3.334.114M9 5.25V3m3.334 2.364C11.176 10.658 7.69 15.08 3 17.502m9.334-12.138c.896.061 1.785.147 2.666.257m-4.589 8.495a18.023 18.023 0 0 1-3.827-5.802" /></svg>') !!}
                        </div>
                        <span x-show="sidebarOpen" :class="ready ? 'transition-all duration-250 ease-out' : ''" class="font-medium whitespace-nowrap {{ request()->routeIs('admin.translations.*') ? '' : 'opacity-70 group-hover:opacity-100' }}" style="font-size: var(--sb-font)">{{ get_nav_label('sidebar.translations', __('app.nav.translations')) }}</span>
                    </a>
                    @endif

                    {{-- System Settings Dropdown --}}
                    @php
                        $settingsActive = request()->routeIs('admin.settings') || request()->routeIs('admin.chatbot.*');
                        $currentSection = request()->query('section', 'branding');
                        $appearanceSections = ['branding','themes','theme-mode','custom-theme','fonts','sidebar','auth-card'];
                        $contentSections = ['languages','footer','landing'];
                        $securitySections = ['security','smtp','captcha'];
                        $integrationSections = ['social','timezone','api'];
                        $isAppearance = in_array($currentSection, $appearanceSections) && request()->routeIs('admin.settings');
                        $isContent = in_array($currentSection, $contentSections) && request()->routeIs('admin.settings');
                        $isSecurity = in_array($currentSection, $securitySections) && request()->routeIs('admin.settings');
                        $isIntegration = in_array($currentSection, $integrationSections) && request()->routeIs('admin.settings');
                    @endphp
                    @if (is_nav_visible('sidebar.settings'))
                    <div x-data="{ settingsOpen: {{ $settingsActive ? 'true' : 'false' }}, subOpen: '{{ $isAppearance ? 'appearance' : ($isContent ? 'content' : ($isSecurity ? 'security' : ($isIntegration ? 'integrations' : ''))) }}' }">
                        {{-- Settings Toggle --}}
                                <button @click="settingsOpen = !settingsOpen; if (!sidebarOpen) { sidebarOpen = true; settingsOpen = true; }"
                           class="w-full flex items-center transition-all duration-200 group
                                  {{ $settingsActive ? '' : 'hover:bg-white/8' }}"
                           :class="sidebarOpen ? 'gap-3 px-3 py-2.5' : 'justify-center px-1 py-2.5'"
                                    style="order: {{ get_nav_sort_order('sidebar.settings', 90) }}; border-radius: var(--sb-active-radius); {{ $settingsActive ? 'background: var(--sb-active-bg); border: 1px solid var(--sb-active-border);' : 'border: 1px solid transparent;' }}"
                                    :title="!sidebarOpen ? '{{ get_nav_label('sidebar.settings', __('app.nav.settings')) }}' : ''">
                            <div class="rounded-lg flex items-center justify-center shrink-0
                                        {{ $settingsActive ? 'bg-amber-500/20 text-amber-400' : 'text-white/50 group-hover:text-white/80' }}"
                                 style="width: var(--sb-icon-wrap); height: var(--sb-icon-wrap)">
                                {!! get_icon('sidebar.settings', '<svg style="width: var(--sb-icon); height: var(--sb-icon)" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>') !!}
                            </div>
                            <span x-show="sidebarOpen" :class="ready ? 'transition-all duration-250 ease-out' : ''" class="flex-1 font-medium whitespace-nowrap text-left {{ $settingsActive ? '' : 'opacity-70 group-hover:opacity-100' }}" style="font-size: var(--sb-font)">{{ get_nav_label('sidebar.settings', __('app.nav.settings')) }}</span>
                            <svg x-show="sidebarOpen" class="w-4 h-4 shrink-0 opacity-40 sidebar-chevron" :class="settingsOpen ? 'is-rotated' : ''" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                            </svg>
                        </button>

                        {{-- Settings Sub-items (2-level grouped) --}}
                        <div class="sidebar-dropdown-content" :class="(settingsOpen && sidebarOpen) ? 'is-open' : ''"
                             style="margin-left: 1.25rem; margin-top: 2px; padding-left: 0.75rem; border-left: 1px solid rgba(255,255,255,0.1);">

                            @php
                                $settingsGroups = [
                                    ['key' => 'appearance', 'label' => 'Appearance', 'items' => [
                                        ['section' => 'branding', 'label' => __('app.settings.branding')],
                                        ['section' => 'themes', 'label' => __('app.settings.theme_designer')],
                                        ['section' => 'theme-mode', 'label' => __('app.settings.default_theme')],
                                        ['section' => 'custom-theme', 'label' => __('app.settings.custom_colors')],
                                        ['section' => 'fonts', 'label' => __('app.settings.fonts')],
                                        ['section' => 'sidebar', 'label' => __('app.settings.sidebar')],
                                        ['section' => 'auth-card', 'label' => __('app.settings.auth_card')],
                                    ]],
                                    ['key' => 'content', 'label' => 'Content', 'items' => [
                                        ['section' => 'languages', 'label' => __('Languages')],
                                        ['section' => 'footer', 'label' => __('app.settings.footer')],
                                        ['section' => 'landing', 'label' => __('app.settings.landing_page')],
                                    ]],
                                    ['key' => 'security', 'label' => 'Security', 'items' => [
                                        ['section' => 'security', 'label' => __('app.settings.security')],
                                        ['section' => 'smtp', 'label' => __('app.settings.smtp_security')],
                                        ['section' => 'captcha', 'label' => __('app.settings.captcha')],
                                    ]],
                                    ['key' => 'integrations', 'label' => 'Integrations', 'items' => [
                                        ['section' => 'social', 'label' => __('app.settings.social_login')],
                                        ['section' => 'timezone', 'label' => __('app.settings.timezone')],
                                        ['section' => 'api', 'label' => __('app.settings.api_keys')],
                                    ]],
                                ];
                            @endphp

                            @foreach ($settingsGroups as $group)
                                {{-- Group toggle --}}
                                <button @click="subOpen = subOpen === '{{ $group['key'] }}' ? '' : '{{ $group['key'] }}'"
                                        class="w-full flex items-center justify-between px-3 py-1.5 rounded-lg transition-all duration-150 hover:bg-white/5 mt-1"
                                        style="font-size: calc(var(--sb-font) - 2px)">
                                    <span class="font-semibold opacity-60 uppercase tracking-wider text-[10px]">{{ $group['label'] }}</span>
                                    <svg class="w-3 h-3 opacity-40 sidebar-chevron" :class="subOpen === '{{ $group['key'] }}' ? 'is-rotated' : ''" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </button>
                                {{-- Group items --}}
                                <div class="sidebar-dropdown-content pl-2" :class="subOpen === '{{ $group['key'] }}' ? 'is-open' : ''">
                                    @foreach ($group['items'] as $item)
                                        <a href="{{ route('admin.settings', ['section' => $item['section']]) }}" wire:navigate
                                           class="flex items-center gap-2 px-3 py-1.5 rounded-lg transition-all duration-150
                                                  {{ request()->routeIs('admin.settings') && $currentSection === $item['section']
                                                      ? 'bg-white/10 opacity-100 font-medium'
                                                      : 'opacity-50 hover:opacity-80 hover:bg-white/5' }}"
                                           style="font-size: calc(var(--sb-font) - 2px)">
                                            <span>{{ $item['label'] }}</span>
                                            @if (request()->routeIs('admin.settings') && $currentSection === $item['section'])
                                                <svg class="w-3 h-3 ml-auto opacity-60" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                                </svg>
                                            @endif
                                        </a>
                                    @endforeach
                                </div>
                            @endforeach

                            {{-- Chatbot Settings --}}
                            <a href="{{ route('admin.chatbot.index') }}"
                               class="flex items-center gap-2 px-3 py-1.5 rounded-lg transition-all duration-150 mt-1
                                      {{ request()->routeIs('admin.chatbot.*')
                                          ? 'bg-white/10 opacity-100 font-medium'
                                          : 'opacity-50 hover:opacity-80 hover:bg-white/5' }}"
                               style="font-size: calc(var(--sb-font) - 2px)">
                                <span>Chatbot</span>
                                @if (request()->routeIs('admin.chatbot.*'))
                                    <svg class="w-3 h-3 ml-auto opacity-60" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                    </svg>
                                @endif
                            </a>

                            <a href="{{ route('admin.settings.icons') }}"
                               class="flex items-center gap-2 px-3 py-1.5 rounded-lg transition-all duration-150 mt-1
                                      {{ request()->routeIs('admin.settings.icons*')
                                          ? 'bg-white/10 opacity-100 font-medium'
                                          : 'opacity-50 hover:opacity-80 hover:bg-white/5' }}"
                               style="font-size: calc(var(--sb-font) - 2px)">
                                <span>Icon Manager</span>
                            </a>

                            <a href="{{ route('admin.settings.nav-labels') }}"
                               class="flex items-center gap-2 px-3 py-1.5 rounded-lg transition-all duration-150 mt-1
                                      {{ request()->routeIs('admin.settings.nav-labels*')
                                          ? 'bg-white/10 opacity-100 font-medium'
                                          : 'opacity-50 hover:opacity-80 hover:bg-white/5' }}"
                               style="font-size: calc(var(--sb-font) - 2px)">
                                <span>Navigation Labels</span>
                            </a>
                        </div>
                    </div>
                    @endif
                @endif
            </nav>

            {{-- Bottom Section: User --}}
            <div class="border-t border-white/10 p-2 space-y-1">
                <div class="sidebar-language-switcher" data-lang-switcher>
                    <button type="button"
                            class="sidebar-language-trigger"
                            data-lang-toggle
                            :class="sidebarOpen ? 'is-expanded' : 'is-collapsed'"
                            :title="!sidebarOpen ? '{{ ($currentLanguage->flag ?: '🏳️') . ' ' . $currentLanguage->name }}' : ''"
                            aria-haspopup="true"
                            aria-expanded="false">
                        <span class="sidebar-language-flag">{{ $currentLanguage->flag ?: '🏳️' }}</span>
                        <span x-show="sidebarOpen" :class="ready ? 'transition-all duration-250 ease-out' : ''" class="sidebar-language-name">
                            {{ $currentLanguage->name }}
                        </span>
                        @if ($activeLanguages->count() > 1)
                            <svg x-show="sidebarOpen" class="w-3.5 h-3.5 opacity-60 sidebar-language-chevron" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                            </svg>
                        @endif
                    </button>

                    @if ($activeLanguages->count() > 1)
                        <div class="sidebar-language-menu" data-lang-menu>
                            @foreach ($activeLanguages as $language)
                                <a href="{{ route('lang.switch', $language->locale) }}"
                                   class="sidebar-language-option {{ $language->locale === $currentLanguage->locale ? 'is-active' : '' }}">
                                    <span class="sidebar-language-option-main">
                                        <span class="sidebar-language-flag">{{ $language->flag ?: '🏳️' }}</span>
                                        <span class="sidebar-language-option-name">{{ $language->name }}</span>
                                    </span>
                                    <span class="sidebar-language-active-indicator" aria-hidden="true"></span>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- User Info + Logout --}}
                <div class="flex items-center rounded-xl hover:bg-white/8 transition group" x-data
                     :class="sidebarOpen ? 'gap-3 px-3 py-2.5' : 'justify-center px-1 py-2.5'">
                    <div class="rounded-lg flex items-center justify-center shrink-0 text-white text-xs font-bold overflow-hidden"
                         style="width: var(--sb-icon-wrap); height: var(--sb-icon-wrap); border-radius: 50%;"
                         x-bind:title="!sidebarOpen ? '{{ Auth::user()->name }}' : ''">
                        <img src="{{ Auth::user()->profile_image }}" alt="{{ Auth::user()->name }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                    </div>
                    <div x-show="sidebarOpen" :class="ready ? 'transition-all duration-250 ease-out' : ''" class="flex-1 min-w-0">
                        <p class="font-medium truncate" style="font-size: var(--sb-font)">{{ Auth::user()->name }}</p>
                        <p class="text-[11px] opacity-40 truncate">{{ Auth::user()->email }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" x-show="sidebarOpen" :class="ready ? 'transition-opacity duration-250' : ''">
                        @csrf
                        <button type="submit" class="p-1.5 rounded-lg hover:bg-white/10 transition opacity-40 hover:opacity-100" title="{{ __('app.nav.log_out') }}">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </aside>

    {{-- Mobile Toggle Button (fixed top-left) --}}
    <button @click="mobileOpen = !mobileOpen"
            class="fixed top-4 left-4 z-40 p-2.5 rounded-xl glass-card lg:hidden transition-opacity"
            :class="mobileOpen ? 'opacity-0 pointer-events-none' : 'opacity-100'">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
        </svg>
    </button>

    {{-- Mobile Close Button --}}
    <button @click="mobileOpen = false" x-show="mobileOpen"
            class="fixed top-7 left-[286px] z-50 p-1.5 rounded-lg bg-white/10 hover:bg-white/20 transition lg:hidden" style="display:none;">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
        </svg>
    </button>

    {{-- Floating Sidebar Toggle (desktop) --}}
    <button @click="toggleSidebar()"
            x-show="ready"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-75"
            x-transition:enter-end="opacity-100 scale-100"
            class="fixed z-[60] hidden lg:flex items-center justify-center w-7 h-7 rounded-full border border-white/15 backdrop-blur-xl shadow-lg shadow-black/10 transition-all duration-500 ease-[cubic-bezier(0.22,1,0.36,1)] hover:scale-110 hover:bg-white/20 cursor-pointer group"
            :class="sidebarOpen ? 'bg-white/10' : 'bg-white/15'"
            :style="`top: 50%; transform: translateY(-50%); left: ${(sidebarOpen ? desktopOpenWidth : desktopCollapsedWidth) - 2}px`"
            title="Toggle sidebar">
        <svg class="w-3.5 h-3.5 opacity-60 group-hover:opacity-100 transition-all duration-300"
             :class="sidebarOpen ? '' : 'rotate-180'"
             fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
        </svg>
    </button>

    <script>
        (function() {
            function closeAllLanguageMenus() {
                document.querySelectorAll('[data-lang-switcher].is-open').forEach(function(switcher) {
                    switcher.classList.remove('is-open');
                    var toggle = switcher.querySelector('[data-lang-toggle]');
                    if (toggle) toggle.setAttribute('aria-expanded', 'false');
                });
            }

            function initLanguageSwitchers() {
                document.querySelectorAll('[data-lang-switcher]').forEach(function(switcher) {
                    if (switcher.dataset.langInit === '1') {
                        return;
                    }

                    var toggle = switcher.querySelector('[data-lang-toggle]');
                    var menu = switcher.querySelector('[data-lang-menu]');
                    switcher.dataset.langInit = '1';

                    if (!toggle || !menu) {
                        return;
                    }

                    toggle.addEventListener('click', function(event) {
                        event.preventDefault();
                        var willOpen = !switcher.classList.contains('is-open');
                        closeAllLanguageMenus();
                        if (willOpen) {
                            switcher.classList.add('is-open');
                            toggle.setAttribute('aria-expanded', 'true');
                        }
                    });
                });
            }

            if (!window.__sidebarLangOutsideBound) {
                window.__sidebarLangOutsideBound = true;
                document.addEventListener('click', function(event) {
                    if (!event.target.closest('[data-lang-switcher]')) {
                        closeAllLanguageMenus();
                    }
                });
            }

            document.addEventListener('DOMContentLoaded', initLanguageSwitchers);
            document.addEventListener('livewire:navigated', function() {
                closeAllLanguageMenus();
                initLanguageSwitchers();
            });
            initLanguageSwitchers();
        })();
    </script>
</div>
