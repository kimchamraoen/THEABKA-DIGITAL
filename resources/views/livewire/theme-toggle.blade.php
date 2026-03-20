<div class="flex items-center gap-3">
    {{-- Toggle Switch --}}
    <button
        wire:click="toggleTheme"
        class="relative w-14 h-8 rounded-full transition-all duration-500 ease-[cubic-bezier(0.22,1,0.36,1)] border shrink-0 focus:outline-none focus:ring-2 focus:ring-blue-400/40
               {{ $theme === 'dark'
                   ? 'bg-indigo-950/60 border-indigo-400/30 shadow-[inset_0_1px_4px_rgba(0,0,0,0.4)]'
                   : 'bg-amber-100/80 border-amber-300/50 shadow-[inset_0_1px_4px_rgba(0,0,0,0.1)]' }}"
        title="Switch to {{ $theme === 'dark' ? 'Light' : 'Dark' }} Mode"
    >
        {{-- Sliding Knob --}}
        <span class="absolute top-1 transition-all duration-500 ease-[cubic-bezier(0.22,1,0.36,1)] w-6 h-6 rounded-full flex items-center justify-center shadow-lg
                     {{ $theme === 'dark'
                         ? 'left-1 bg-gradient-to-br from-indigo-400 to-blue-500 shadow-blue-500/30'
                         : 'left-[26px] bg-gradient-to-br from-amber-300 to-orange-400 shadow-amber-500/30' }}">
            {{-- Moon icon (dark mode) --}}
            <svg class="w-3.5 h-3.5 text-white transition-all duration-300 {{ $theme === 'dark' ? 'opacity-100 rotate-0 scale-100' : 'opacity-0 rotate-90 scale-0' }}"
                 fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
            </svg>
            {{-- Sun icon (light mode) --}}
            <svg class="w-3.5 h-3.5 text-white absolute transition-all duration-300 {{ $theme === 'light' ? 'opacity-100 rotate-0 scale-100' : 'opacity-0 -rotate-90 scale-0' }}"
                 fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
            </svg>
        </span>

        {{-- Background stars (dark mode decoration) --}}
        <span class="absolute top-1.5 right-2 w-1 h-1 rounded-full transition-all duration-500 {{ $theme === 'dark' ? 'bg-white/40 scale-100' : 'bg-transparent scale-0' }}"></span>
        <span class="absolute bottom-2 right-3.5 w-0.5 h-0.5 rounded-full transition-all duration-500 {{ $theme === 'dark' ? 'bg-white/25 scale-100' : 'bg-transparent scale-0' }}"></span>
        <span class="absolute top-2.5 left-8 w-0.5 h-0.5 rounded-full transition-all duration-500 {{ $theme === 'dark' ? 'bg-white/20 scale-100' : 'bg-transparent scale-0' }}"></span>
    </button>

    {{-- Label (hidden in header, visible in sidebar) --}}
    <span class="theme-toggle-label text-xs font-medium opacity-50 whitespace-nowrap select-none hidden">
        {{ $theme === 'dark' ? 'Dark Mode' : 'Light Mode' }}
    </span>
</div>
