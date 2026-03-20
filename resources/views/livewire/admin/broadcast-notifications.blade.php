<div>
    {{-- Flash {{ __('Message') }}s --}}
    @if (session()->has('message'))
        <div class="mb-4 px-4 py-3 rounded-xl bg-emerald-500/15 border border-emerald-500/20 text-emerald-400 text-sm flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
            </svg>
            {{ session('message') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="mb-4 px-4 py-3 rounded-xl bg-red-500/15 border border-red-500/20 text-red-400 text-sm flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
            </svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
        <div>
            <h1 class="text-2xl font-bold">{{ __('Broadcast Notifications') }}</h1>
            <p class="text-sm opacity-50 mt-0.5">{{ __('Send notifications to users') }}{{ auth()->user()->isSuperAdmin() ? ' and admins' : '' }}</p>
        </div>
        <button wire:click="openCreate" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-500/20 hover:bg-blue-500/30 border border-blue-500/25 text-blue-400 text-sm font-medium transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 1 1 0-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38c-.551.318-1.26.117-1.527-.461a20.845 20.845 0 0 1-1.44-4.282m3.102.069a18.03 18.03 0 0 1-.59-4.59c0-1.586.205-3.124.59-4.59m0 9.18a23.848 23.848 0 0 1 8.835 2.535M10.34 6.66a23.847 23.847 0 0 0 8.835-2.535m0 0A23.74 23.74 0 0 0 18.795 3m.38 1.125a23.91 23.91 0 0 1 1.014 5.395m-1.014 8.855c-.118.38-.245.754-.38 1.125m.38-1.125a23.91 23.91 0 0 0 1.014-5.395m0-3.46c.495.413.811 1.035.811 1.73 0 .695-.316 1.317-.811 1.73m0-3.46a24.347 24.347 0 0 1 0 3.46" />
            </svg>
            {{ __('New Broadcast') }}
        </button>
    </div>

    {{-- Notifications Table --}}
    <div class="glass-card rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-white/10">
                        <th class="text-left px-5 py-3.5 font-semibold opacity-60 text-xs uppercase tracking-wider">{{ __('Title') }}</th>
                        <th class="text-left px-5 py-3.5 font-semibold opacity-60 text-xs uppercase tracking-wider">{{ __('Audience') }}</th>
                        <th class="text-left px-5 py-3.5 font-semibold opacity-60 text-xs uppercase tracking-wider">{{ __('Recipients') }}</th>
                        <th class="text-left px-5 py-3.5 font-semibold opacity-60 text-xs uppercase tracking-wider">{{ __('Sent') }}</th>
                        <th class="text-right px-5 py-3.5 font-semibold opacity-60 text-xs uppercase tracking-wider">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse ($notifications as $notification)
                        <tr class="hover:bg-white/3 transition">
                            <td class="px-5 py-4">
                                <p class="font-medium">{{ $notification->title }}</p>
                                <p class="text-xs opacity-50 mt-0.5 line-clamp-1">{{ Str::limit($notification->message, 80) }}</p>
                            </td>
                            <td class="px-5 py-4">
                                @php
                                    $badgeColors = [
                                        'user' => 'bg-blue-500/15 text-blue-400 border-blue-500/20',
                                        'admin' => 'bg-amber-500/15 text-amber-400 border-amber-500/20',
                                        'all' => 'bg-purple-500/15 text-purple-400 border-purple-500/20',
                                    ];
                                    $badgeLabels = ['user' => 'Users', 'admin' => 'Admins', 'all' => 'Everyone'];
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold border {{ $badgeColors[$notification->target_role] ?? 'bg-white/10' }}">
                                    {{ $badgeLabels[$notification->target_role] ?? $notification->target_role }}
                                </span>
                            </td>
                            <td class="px-5 py-4 opacity-70">{{ $notification->recipients_count }}</td>
                            <td class="px-5 py-4 opacity-50 text-xs whitespace-nowrap">{{ $notification->created_at->diffForHumans() }}</td>
                            <td class="px-5 py-4 text-right">
                                <button wire:click="confirmDelete({{ $notification->id }})" class="p-1.5 rounded-lg hover:bg-red-500/15 text-red-400/60 hover:text-red-400 transition" title="{{ __('Delete') }}">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center">
                                <div class="flex flex-col items-center gap-2 opacity-40">
                                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 1 1 0-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38c-.551.318-1.26.117-1.527-.461a20.845 20.845 0 0 1-1.44-4.282m3.102.069a18.03 18.03 0 0 1-.59-4.59c0-1.586.205-3.124.59-4.59m0 9.18a23.848 23.848 0 0 1 8.835 2.535M10.34 6.66a23.847 23.847 0 0 0 8.835-2.535m0 0A23.74 23.74 0 0 0 18.795 3m.38 1.125a23.91 23.91 0 0 1 1.014 5.395m-1.014 8.855c-.118.38-.245.754-.38 1.125m.38-1.125a23.91 23.91 0 0 0 1.014-5.395m0-3.46c.495.413.811 1.035.811 1.73 0 .695-.316 1.317-.811 1.73m0-3.46a24.347 24.347 0 0 1 0 3.46" />
                                    </svg>
                                    <p class="text-sm">{{ __('No broadcasts sent yet') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($notifications->hasPages())
            <div class="px-5 py-3 border-t border-white/10">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>

    {{-- Create Broadcast Modal --}}
    @if ($showCreateModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" wire:click="$set('showCreateModal', false)"></div>
            <div class="relative glass-card rounded-2xl p-6 w-full max-w-lg z-10">
                <h3 class="text-lg font-bold mb-4">New Broadcast</h3>
                <form wire:submit="send">
                    <div class="space-y-4">
                        {{-- Title --}}
                        <div>
                            <label class="block text-sm font-medium opacity-70 mb-1.5">Title</label>
                            <input wire:model="title" type="text" class="w-full px-4 py-2.5 rounded-xl bg-white/5 border border-white/10 focus:border-white/25 outline-none text-sm transition" placeholder="Notification title">
                            @error('title') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        {{-- Message --}}
                        <div>
                            <label class="block text-sm font-medium opacity-70 mb-1.5">Message</label>
                            <textarea wire:model="message" rows="4" class="w-full px-4 py-2.5 rounded-xl bg-white/5 border border-white/10 focus:border-white/25 outline-none text-sm transition resize-none" placeholder="Write your notification message..."></textarea>
                            @error('message') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        {{-- Target Audience --}}
                        <div>
                            <label class="block text-sm font-medium opacity-70 mb-1.5">{{ __('Send To') }}</label>
                            <select wire:model="targetRole" class="w-full px-4 py-2.5 rounded-xl bg-white/5 border border-white/10 focus:border-white/25 outline-none text-sm transition">
                                @foreach ($allowedTargets as $target)
                                    <option value="{{ $target }}">
                                        @if ($target === 'user') All Users
                                        @elseif ($target === 'admin') All Admins
                                        @elseif ($target === 'all') Everyone (Admins & Users)
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('targetRole') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-3 mt-6">
                        <button type="button" wire:click="$set('showCreateModal', false)" class="px-4 py-2 rounded-xl bg-white/5 hover:bg-white/10 text-sm transition">{{ __('Cancel') }}</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-blue-500/20 hover:bg-blue-500/30 border border-blue-500/25 text-blue-400 text-sm font-medium transition">
                            <span wire:loading.remove wire:target="send">{{ __('Send Broadcast') }}</span>
                            <span wire:loading wire:target="send">Sending...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Delete Confirmation Modal --}}
    @if ($showDeleteModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" wire:click="$set('showDeleteModal', false)"></div>
            <div class="relative glass-card rounded-2xl p-6 w-full max-w-sm z-10 text-center">
                <div class="w-12 h-12 rounded-full bg-red-500/15 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold mb-1">{{ __('Delete Broadcast') }}</h3>
                <p class="text-sm opacity-60 mb-5">{{ __('Are you sure? This will remove the notification for all recipients.') }}</p>
                <div class="flex items-center justify-center gap-3">
                    <button wire:click="$set('showDeleteModal', false)" class="px-5 py-2 rounded-xl bg-white/5 hover:bg-white/10 text-sm transition">Cancel</button>
                    <button wire:click="delete" class="px-5 py-2 rounded-xl bg-red-500/20 hover:bg-red-500/30 border border-red-500/25 text-red-400 text-sm font-medium transition">
                        <span wire:loading.remove wire:target="delete">Delete</span>
                        <span wire:loading wire:target="delete">Deleting...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
