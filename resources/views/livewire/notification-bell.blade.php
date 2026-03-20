<div class="relative" x-data="{ open: false }" @click.outside="open = false">
    {{-- Bell Button --}}
    <button @click="open = !open; if(open) $wire.refreshCount()" class="relative p-2 rounded-lg hover:bg-white/10 transition">
        <svg class="w-5 h-5 opacity-60 hover:opacity-100 transition" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
        </svg>
        @if ($unreadCount > 0)
            <span class="absolute top-0.5 right-0.5 min-w-[18px] h-[18px] flex items-center justify-center px-1 rounded-full bg-red-500 text-white text-[10px] font-bold leading-none shadow-lg shadow-red-500/30">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </button>

    {{-- Dropdown --}}
    <div x-show="open" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
         class="fixed right-6 w-[26rem] max-h-[32rem] rounded-2xl overflow-hidden shadow-2xl z-[200] border border-white/10 backdrop-blur-xl bg-slate-900/95"
         x-ref="dropdown"
         x-effect="if(open){ $nextTick(() => { const r = $root.getBoundingClientRect(); $refs.dropdown.style.top = (r.bottom + 8) + 'px'; }) }"
         style="display: none;">

        {{-- Header --}}
        <div class="flex items-center justify-between px-4 py-3 border-b border-white/10">
            <h3 class="text-sm font-bold">{{ __('Notifications') }}</h3>
            @if ($unreadCount > 0)
                <button wire:click="markAllRead" class="text-xs text-blue-400 hover:text-blue-300 transition">
                    {{ __('Mark all read') }}
                </button>
            @endif
        </div>

        {{-- Notification List --}}
        <div class="overflow-y-auto max-h-72 divide-y divide-white/5">
            @forelse ($notifications as $notification)
                <div wire:click="markAsRead({{ $notification->id }})"
                     class="px-4 py-3 hover:bg-white/5 transition cursor-pointer {{ is_null($notification->pivot->read_at) ? 'bg-white/3' : '' }}">
                    <div class="flex items-start gap-2.5">
                        @if (is_null($notification->pivot->read_at))
                            <span class="w-2 h-2 rounded-full bg-blue-400 mt-1.5 shrink-0"></span>
                        @else
                            <span class="w-2 h-2 rounded-full bg-transparent mt-1.5 shrink-0"></span>
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium leading-snug {{ is_null($notification->pivot->read_at) ? '' : 'opacity-60' }}">{{ $notification->title }}</p>
                            <p class="text-xs opacity-50 mt-0.5 line-clamp-2">{{ $notification->message }}</p>
                            <p class="text-[10px] opacity-30 mt-1">{{ $notification->created_at->diffForHumans() }} &middot; from {{ $notification->sender->name ?? 'System' }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-4 py-8 text-center">
                    <svg class="w-8 h-8 mx-auto opacity-20 mb-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                    </svg>
                    <p class="text-xs opacity-40">{{ __('No notifications yet') }}</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
