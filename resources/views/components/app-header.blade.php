@php
    $settings = \App\Models\Setting::instance();
    $user = Auth::user();
@endphp

<header class="glass-card rounded-2xl mx-4 mt-4 mb-4 z-30 !overflow-visible" x-data="{
    userDropdown: false,
    appTimezone: '{{ $settings->timezone ?? 'Asia/Phnom_Penh' }}',
    phnomPenhTime: (() => { const now = new Date(); return now.toLocaleTimeString('en-US', { timeZone: '{{ $settings->timezone ?? 'Asia/Phnom_Penh' }}', hour: '2-digit', minute: '2-digit', hour12: true }); })(),
    phnomPenhDate: (() => { const now = new Date(); return now.toLocaleDateString('en-US', { timeZone: '{{ $settings->timezone ?? 'Asia/Phnom_Penh' }}', weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' }); })(),
    tick() {
        const now = new Date();
        this.phnomPenhTime = now.toLocaleTimeString('en-US', { timeZone: this.appTimezone, hour: '2-digit', minute: '2-digit', hour12: true });
        this.phnomPenhDate = now.toLocaleDateString('en-US', { timeZone: this.appTimezone, weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' });
        setTimeout(() => this.tick(), 1000);
    }
}" x-init="tick()" @click.outside="userDropdown = false">
    <div class="px-5 py-3 flex items-center justify-between gap-4">
        {{-- Left: Hamburger (mobile) + Page Title + Search --}}
        <div class="flex items-center gap-3 flex-1 min-w-0">
            {{-- Hamburger for mobile sidebar toggle --}}
            <button @click="$dispatch('toggle-mobile-sidebar')"
                    class="lg:hidden p-1.5 -ml-1 rounded-lg hover:bg-white/10 transition shrink-0"
                    aria-label="Toggle sidebar">
                <svg class="w-5 h-5 opacity-70" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
            </button>

            {{-- Page Title --}}
            <h1 class="text-base font-semibold truncate hidden sm:block opacity-80">
                @yield('page-title', '')
            </h1>

            {{-- Global Search (hidden on mobile) --}}
            <div class="header-search-container flex-1 min-w-0 hidden md:block">
                @livewire('global-search')
            </div>
        </div>

        {{-- Center: Date & Time --}}
        <div class="hidden md:flex items-center shrink-0">
            <div class="header-time-pill">
                <svg class="w-4 h-4 header-time-icon shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                <span x-text="phnomPenhDate"></span>
                <span class="header-time-dot">&middot;</span>
                <span class="header-time-main" x-text="phnomPenhTime"></span>
                <span class="header-time-dot">&middot;</span>
                <span class="header-timezone">{{ $settings->timezone ?? 'Asia/Phnom_Penh' }}</span>
            </div>
        </div>

        {{-- Right: Actions & User Dropdown --}}
        <div class="flex items-center gap-2 shrink-0">
            @livewire('theme-toggle')
            <div class="h-6 w-px bg-white/10"></div>
            @livewire('notification-bell')
            <div class="h-6 w-px bg-white/10"></div>

            {{-- User Avatar Dropdown --}}
            <div class="relative">
                <button @click="userDropdown = !userDropdown"
                        class="flex items-center gap-2.5 pl-2 pr-3 py-1.5 rounded-xl hover:bg-white/8 transition"
                        aria-label="User menu">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-xs font-bold overflow-hidden shrink-0">
                        <img src="{{ $user->profile_image }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                    </div>
                    <div class="hidden sm:block text-left">
                        <p class="text-sm font-medium leading-tight truncate max-w-[120px]">{{ $user->name }}</p>
                    </div>
                    <svg class="w-3.5 h-3.5 opacity-40 hidden sm:block sidebar-chevron" :class="userDropdown ? 'is-rotated' : ''" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                    </svg>
                </button>

                {{-- Dropdown Menu --}}
                <div x-show="userDropdown" x-cloak
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="!absolute right-0 top-[calc(100%+0.5rem)] mt-2 w-64 rounded-xl glass-card border border-white/10 shadow-2xl p-1.5 z-50 origin-top-right !bg-slate-900/90"
                     style="display: none;">
                    
                    {{-- User Info Header --}}
                    <div class="px-3 py-3 border-b border-white/10 mb-1">
                        <p class="text-sm font-medium leading-tight text-white truncate">{{ $user->name }}</p>
                        <p class="text-xs opacity-60 truncate mt-0.5">{{ $user->email }}</p>
                    </div>

                    <a href="{{ route('profile.show') }}" wire:navigate
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-white/8 transition text-sm">
                        <svg class="w-4 h-4 opacity-50" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                        <span>{{ __('app.nav.profile') }}</span>
                    </a>
                    @if ($user->isSuperAdmin())
                    <a href="{{ route('admin.settings') }}" wire:navigate
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-white/8 transition text-sm">
                        <svg class="w-4 h-4 opacity-50" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93l.164.072c.39.17.844.14 1.2-.082l.756-.47a1.125 1.125 0 0 1 1.37.49l.546.945c.28.483.166 1.09-.266 1.44l-.613.518c-.326.275-.475.676-.463 1.08l.005.178c.01.404.153.8.47 1.068l.614.518c.432.35.546.957.266 1.44l-.547.944a1.125 1.125 0 0 1-1.369.491l-.757-.47c-.355-.222-.81-.252-1.199-.082l-.164.072c-.396.166-.71.506-.78.93l-.15.894c-.09.542-.56.94-1.109.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93a3.12 3.12 0 0 1-.164-.072c-.39-.17-.844-.14-1.2.082l-.755.47a1.125 1.125 0 0 1-1.37-.491l-.546-.944a1.125 1.125 0 0 1 .266-1.44l.613-.518c.326-.275.475-.676.463-1.08l-.005-.178a1.965 1.965 0 0 0-.47-1.068l-.613-.518a1.125 1.125 0 0 1-.266-1.44l.547-.944a1.125 1.125 0 0 1 1.369-.491l.756.47c.356.222.81.252 1.2.082l.163-.072c.397-.166.711-.506.781-.93l.15-.894Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                        <span>{{ __('app.nav.settings') }}</span>
                    </a>
                    @endif
                    <div class="border-t border-white/10 my-1"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-red-500/10 transition text-sm text-red-400">
                            <svg class="w-4 h-4 opacity-70" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                            </svg>
                            <span>{{ __('app.nav.log_out') }}</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
