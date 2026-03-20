<div class="relative max-w-xs w-full" x-data="{
    open: false,
    selectedIndex: -1,
    init() {
        this.$watch('$wire.showResults', (value) => {
            this.open = value;
            this.selectedIndex = -1;
        });
    },
    navigate(direction) {
        const items = this.$refs.results?.querySelectorAll('[data-result-item]');
        if (!items || items.length === 0) return;
        if (direction === 'down') {
            this.selectedIndex = this.selectedIndex < items.length - 1 ? this.selectedIndex + 1 : 0;
        } else {
            this.selectedIndex = this.selectedIndex > 0 ? this.selectedIndex - 1 : items.length - 1;
        }
        items[this.selectedIndex]?.scrollIntoView({ block: 'nearest' });
    },
    selectCurrent() {
        const items = this.$refs.results?.querySelectorAll('[data-result-item]');
        if (items && items[this.selectedIndex]) {
            items[this.selectedIndex].click();
        }
    }
}" @click.outside="open = false" @keydown.escape="open = false; $wire.set('query', ''); $wire.set('showResults', false)">

    {{-- Search Icon --}}
    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 opacity-40 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
    </svg>

    {{-- Search Input --}}
    <input type="text"
           wire:model.live.debounce.300ms="query"
           placeholder="Search..."
           autocomplete="off"
           @focus="if ($wire.query.length >= 2) open = true"
           @keydown.arrow-down.prevent="navigate('down')"
           @keydown.arrow-up.prevent="navigate('up')"
           @keydown.enter.prevent="selectCurrent()"
           class="w-full pl-9 pr-8 py-2 rounded-xl bg-white/5 border border-white/10 focus:border-white/25 focus:bg-white/8 transition-all outline-none text-sm placeholder:opacity-40">

    {{-- Clear Button --}}
    @if ($query)
        <button wire:click="$set('query', '')"
                class="absolute right-3 top-1/2 -translate-y-1/2 opacity-40 hover:opacity-80 transition">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
        </button>
    @endif

    {{-- Results Dropdown --}}
    @if ($showResults)
        <div x-ref="results" x-show="open"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
             class="absolute top-full left-0 right-0 mt-2 glass-card rounded-xl overflow-hidden shadow-2xl shadow-black/30 border border-white/10 z-[100] max-h-80 overflow-y-auto"
             style="min-width: 320px;">

            @if (count($this->results) > 0)
                <div class="p-1.5">
                    @php $lastType = ''; @endphp
                    @foreach ($this->results as $index => $result)
                        {{-- Type header --}}
                        @if ($result['type'] !== $lastType)
                            @php $lastType = $result['type']; @endphp
                            <div class="px-3 pt-2 pb-1">
                                <span class="text-[10px] font-bold uppercase tracking-widest opacity-30">
                                    @if ($result['type'] === 'page') Pages
                                    @elseif ($result['type'] === 'user') Users
                                    @endif
                                </span>
                            </div>
                        @endif

                        {{-- Result item --}}
                        <button data-result-item
                                wire:click="navigate('{{ $result['url'] }}')"
                                class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-left transition-all duration-150"
                                :class="selectedIndex === {{ $index }} ? 'bg-white/15' : 'hover:bg-white/8'"
                                @mouseenter="selectedIndex = {{ $index }}">
                            {{-- Icon --}}
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0
                                @if ($result['icon'] === 'dashboard') bg-blue-500/20 text-blue-400
                                @elseif ($result['icon'] === 'profile') bg-purple-500/20 text-purple-400
                                @elseif ($result['icon'] === 'docs') bg-teal-500/20 text-teal-400
                                @elseif ($result['icon'] === 'users') bg-cyan-500/20 text-cyan-400
                                @elseif ($result['icon'] === 'settings') bg-amber-500/20 text-amber-400
                                @elseif ($result['icon'] === 'user') bg-indigo-500/20 text-indigo-400
                                @endif">

                                @if ($result['icon'] === 'dashboard')
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                                    </svg>
                                @elseif ($result['icon'] === 'profile')
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                    </svg>
                                @elseif ($result['icon'] === 'docs')
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                                    </svg>
                                @elseif ($result['icon'] === 'users')
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                                    </svg>
                                @elseif ($result['icon'] === 'settings')
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                @elseif ($result['icon'] === 'user')
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                    </svg>
                                @endif
                            </div>

                            {{-- Text --}}
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium truncate">{{ $result['title'] }}</p>
                                <p class="text-xs opacity-40 truncate">{{ $result['description'] }}</p>
                            </div>

                            {{-- Arrow --}}
                            <svg class="w-3.5 h-3.5 opacity-20 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                            </svg>
                        </button>
                    @endforeach
                </div>
            @else
                {{-- No results --}}
                <div class="p-6 text-center">
                    <svg class="w-8 h-8 mx-auto opacity-20 mb-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                    <p class="text-sm opacity-40">No results for "<span class="font-medium">{{ $query }}</span>"</p>
                </div>
            @endif

            {{-- Keyboard hints --}}
            <div class="border-t border-white/5 px-3 py-2 flex items-center gap-3 text-[10px] opacity-30">
                <span class="flex items-center gap-1">
                    <kbd class="px-1.5 py-0.5 rounded bg-white/10 font-mono">↑↓</kbd> navigate
                </span>
                <span class="flex items-center gap-1">
                    <kbd class="px-1.5 py-0.5 rounded bg-white/10 font-mono">↵</kbd> select
                </span>
                <span class="flex items-center gap-1">
                    <kbd class="px-1.5 py-0.5 rounded bg-white/10 font-mono">esc</kbd> close
                </span>
            </div>
        </div>
    @endif
</div>
