<div>
    {{-- Flash Messages --}}
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

    {{-- Header Bar --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
        <div>
            <h1 class="text-2xl font-bold">{{ __('User Management') }}</h1>
            <p class="text-sm opacity-50 mt-0.5">{{ __('Manage users, roles, and email verification') }}</p>
        </div>
        <button wire:click="openCreateModal" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-500/20 hover:bg-blue-500/30 border border-blue-500/25 text-blue-400 text-sm font-medium transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            {{ __('Add User') }}
        </button>
    </div>

    {{-- Filters --}}
    <div class="glass-card rounded-2xl p-4 mb-4">
        <div class="flex flex-col sm:flex-row gap-3">
            {{-- Search --}}
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 opacity-40" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search by name or email..."
                       class="w-full pl-9 pr-4 py-2.5 rounded-xl bg-white/5 border border-white/10 focus:border-white/25 focus:bg-white/8 transition-all outline-none text-sm placeholder:opacity-40">
            </div>
            {{-- Role Filter --}}
            <select wire:model.live="roleFilter" class="px-3 py-2.5 rounded-xl bg-white/5 border border-white/10 text-sm outline-none focus:border-white/25 min-w-[130px]">
                <option value="">{{ __('All Roles') }}</option>
                <option value="user">{{ __('User') }}</option>
                <option value="admin">{{ __('Admin') }}</option>
                <option value="super_admin">{{ __('Super Admin') }}</option>
            </select>
            {{-- Verified Filter --}}
            <select wire:model.live="verifiedFilter" class="px-3 py-2.5 rounded-xl bg-white/5 border border-white/10 text-sm outline-none focus:border-white/25 min-w-[150px]">
                <option value="">{{ __('All Status') }}</option>
                <option value="verified">{{ __('Verified') }}</option>
                <option value="unverified">{{ __('Unverified') }}</option>
            </select>
        </div>
    </div>

    {{-- Users Table --}}
    <div class="glass-card rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-white/10">
                        <th class="text-left px-5 py-3.5 font-semibold opacity-60 text-xs uppercase tracking-wider">{{ __('User') }}</th>
                        <th class="text-left px-5 py-3.5 font-semibold opacity-60 text-xs uppercase tracking-wider cursor-pointer hover:opacity-100 transition" wire:click="sortBy('email')">
                            <span class="inline-flex items-center gap-1">{{ __('Email') }}
                                @if ($sortField === 'email')
                                    <svg class="w-3 h-3 {{ $sortDirection === 'asc' ? '' : 'rotate-180' }}" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 15.75 7.5-7.5 7.5 7.5" /></svg>
                                @endif
                            </span>
                        </th>
                        <th class="text-left px-5 py-3.5 font-semibold opacity-60 text-xs uppercase tracking-wider cursor-pointer hover:opacity-100 transition" wire:click="sortBy('role')">
                            <span class="inline-flex items-center gap-1">{{ __('Role') }}
                                @if ($sortField === 'role')
                                    <svg class="w-3 h-3 {{ $sortDirection === 'asc' ? '' : 'rotate-180' }}" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 15.75 7.5-7.5 7.5 7.5" /></svg>
                                @endif
                            </span>
                        </th>
                        <th class="text-left px-5 py-3.5 font-semibold opacity-60 text-xs uppercase tracking-wider">{{ __('Email Status') }}</th>
                        <th class="text-left px-5 py-3.5 font-semibold opacity-60 text-xs uppercase tracking-wider">{{ __('Provider') }}</th>
                        <th class="text-left px-5 py-3.5 font-semibold opacity-60 text-xs uppercase tracking-wider">{{ __('Bypass') }}</th>
                        <th class="text-left px-5 py-3.5 font-semibold opacity-60 text-xs uppercase tracking-wider">{{ __('2FA') }}</th>
                        <th class="text-left px-5 py-3.5 font-semibold opacity-60 text-xs uppercase tracking-wider">{{ __('Terms Accepted') }}</th>
                        <th class="text-left px-5 py-3.5 font-semibold opacity-60 text-xs uppercase tracking-wider">{{ __('Cookie Consent') }}</th>
                        <th class="text-left px-5 py-3.5 font-semibold opacity-60 text-xs uppercase tracking-wider cursor-pointer hover:opacity-100 transition" wire:click="sortBy('created_at')">
                            <span class="inline-flex items-center gap-1">{{ __('Joined') }}
                                @if ($sortField === 'created_at')
                                    <svg class="w-3 h-3 {{ $sortDirection === 'asc' ? '' : 'rotate-180' }}" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 15.75 7.5-7.5 7.5 7.5" /></svg>
                                @endif
                            </span>
                        </th>
                        <th class="text-right px-5 py-3.5 font-semibold opacity-60 text-xs uppercase tracking-wider">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse ($users as $user)
                        <tr class="hover:bg-white/5 transition" wire:key="user-{{ $user->id }}">
                            {{-- User Info --}}
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $user->profile_image }}" alt="{{ $user->name }}" class="w-9 h-9 rounded-xl object-cover shrink-0">
                                    <div class="min-w-0">
                                        <p class="font-medium truncate">{{ $user->name }}</p>
                                        @if ($user->id === auth()->id())
                                            <p class="text-[10px] text-blue-400 font-semibold">You</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            {{-- Email --}}
                            <td class="px-5 py-3.5 opacity-70 truncate max-w-[200px]">{{ $user->email }}</td>
                            {{-- Role --}}
                            <td class="px-5 py-3.5">
                                @if ($user->role === 'super_admin')
                                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-lg bg-amber-500/15 text-amber-400">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" /></svg>
                                        {{ __('Admin') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center text-[11px] font-medium px-2 py-0.5 rounded-lg bg-white/10 opacity-60">{{ __('User') }}</span>
                                @endif
                            </td>
                            {{-- Email Verified --}}
                            <td class="px-5 py-3.5">
                                @if ($user->hasVerifiedEmail())
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-lg bg-emerald-500/15 text-emerald-400">
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                                            {{ __('Verified') }}
                                        </span>
                                        <button wire:click="unverifyUserEmail({{ $user->id }})" wire:confirm="Are you sure you want to unverify {{ $user->name }}'s email?"
                                                class="p-1 rounded-lg hover:bg-white/10 opacity-30 hover:opacity-70 transition" title="Unverify">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                                        </button>
                                    </div>
                                @else
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-lg bg-red-500/15 text-red-400">
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                                            {{ __('Unverified') }}
                                        </span>
                                        <button wire:click="verifyUserEmail({{ $user->id }})"
                                                class="p-1 rounded-lg bg-emerald-500/15 hover:bg-emerald-500/25 text-emerald-400 transition" title="Verify email">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                                        </button>
                                    </div>
                                @endif
                            </td>
                            {{-- Provider(s) --}}
                            <td class="px-5 py-3.5">
                                @php $socialProviders = $user->socialAccounts->pluck('provider')->toArray(); @endphp
                                @if (count($socialProviders) > 0)
                                    <div style="display: flex; flex-wrap: wrap; gap: 4px;">
                                    @foreach ($socialProviders as $sp)
                                        @if ($sp === 'google')
                                            <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-lg bg-red-500/10 text-red-400">
                                                <svg class="w-3 h-3" viewBox="0 0 24 24" fill="currentColor"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                                                G
                                            </span>
                                        @elseif ($sp === 'facebook')
                                            <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-lg bg-blue-500/10 text-blue-400">
                                                <svg class="w-3 h-3" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                                FB
                                            </span>
                                        @elseif ($sp === 'telegram')
                                            <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-lg bg-sky-500/10 text-sky-400">
                                                <svg class="w-3 h-3" viewBox="0 0 24 24" fill="currentColor"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>
                                                TG
                                            </span>
                                        @elseif ($sp === 'twitter')
                                            <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-lg bg-white/10">
                                                <svg class="w-3 h-3" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                                                X
                                            </span>
                                        @endif
                                    @endforeach
                                    </div>
                                @else
                                    <span class="inline-flex items-center gap-1 text-[11px] font-medium px-2 py-0.5 rounded-lg bg-white/10 opacity-50">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" /></svg>
                                        Email
                                    </span>
                                @endif
                            </td>
                            {{-- Bypass Email Verification --}}
                            <td class="px-5 py-3.5">
                                <button wire:click="toggleBypassEmailVerification({{ $user->id }})"
                                        class="p-1.5 rounded-lg transition {{ $user->bypass_email_verification ? 'bg-amber-500/15 text-amber-400 hover:bg-amber-500/25' : 'bg-white/5 opacity-40 hover:opacity-70 hover:bg-white/10' }}"
                                        title="{{ $user->bypass_email_verification ? __('Disable bypass') : __('Enable bypass') }}">
                                    @if ($user->bypass_email_verification)
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636" />
                                        </svg>
                                    @endif
                                </button>
                            </td>
                            {{-- 2FA --}}
                            <td class="px-5 py-3.5">
                                @if ($user->two_factor_secret)
                                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-lg bg-emerald-500/15 text-emerald-400">On</span>
                                @else
                                    <span class="inline-flex items-center text-[11px] font-medium px-2 py-0.5 rounded-lg bg-white/10 opacity-40">Off</span>
                                @endif
                            </td>
                            {{-- Terms Accepted --}}
                            <td class="px-5 py-3.5">
                                @if ($user->terms_accepted)
                                    <span title="{{ $user->terms_accepted_at ? $user->terms_accepted_at->format('Y-m-d H:i:s') : 'No timestamp' }}" class="inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-lg bg-emerald-500/15 text-emerald-400">✅</span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-lg bg-red-500/15 text-red-400">❌</span>
                                @endif
                            </td>
                            {{-- Cookie Consent --}}
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center text-[11px] font-semibold px-2 py-0.5 rounded-lg bg-white/10">
                                    {{ $user->cookie_consent ?? 'pending' }}
                                </span>
                            </td>
                            {{-- Joined --}}
                            <td class="px-5 py-3.5 text-xs opacity-50">{{ $user->created_at->format('M d, Y') }}</td>
                            {{-- Actions --}}
                            <td class="px-5 py-3.5">
                                <div class="flex items-center justify-end gap-1">
                                    <button wire:click="editUser({{ $user->id }})" class="p-1.5 rounded-lg hover:bg-white/10 opacity-50 hover:opacity-100 transition" title="Edit">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" /></svg>
                                    </button>
                                    @if ($user->id !== auth()->id())
                                        <button wire:click="toggleRole({{ $user->id }})" class="p-1.5 rounded-lg hover:bg-white/10 opacity-50 hover:opacity-100 transition" title="Toggle role">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" /></svg>
                                        </button>
                                        <button wire:click="confirmDelete({{ $user->id }})" class="p-1.5 rounded-lg hover:bg-red-500/15 text-red-400 opacity-50 hover:opacity-100 transition" title="Delete">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="px-5 py-12 text-center">
                                <div class="flex flex-col items-center gap-2 opacity-40">
                                    <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                                    </svg>
                                    <p class="text-sm">{{ __('No users found') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($users->hasPages())
            <div class="px-5 py-3 border-t border-white/10">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    {{-- Edit User Modal --}}
    @if ($showEditModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4" x-data x-init="document.body.classList.add('overflow-hidden')" x-on:remove="document.body.classList.remove('overflow-hidden')">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" wire:click="$set('showEditModal', false)"></div>
            <div class="relative glass-card rounded-2xl p-6 w-full max-w-md z-10">
                <h3 class="text-lg font-bold mb-4">{{ __('Edit User') }}</h3>
                <form wire:submit="updateUser">
                    <div class="space-y-4">
                        {{-- Photo --}}
                        <div>
                            <label class="block text-sm font-medium opacity-70 mb-1.5">{{ __('Profile Photo') }}</label>
                            <input type="file" wire:model="editPhoto" accept="image/*"
                                   class="w-full text-sm file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-white/10 file:text-white/70 file:font-medium hover:file:bg-white/20 file:transition file:cursor-pointer cursor-pointer opacity-70">
                            @error('editPhoto') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        {{-- Name --}}
                        <div>
                            <label class="block text-sm font-medium opacity-70 mb-1.5">{{ __('Name') }}</label>
                            <input wire:model="editName" type="text" class="w-full px-4 py-2.5 rounded-xl bg-white/5 border border-white/10 focus:border-white/25 outline-none text-sm transition">
                            @error('editName') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        {{-- Email --}}
                        <div>
                            <label class="block text-sm font-medium opacity-70 mb-1.5">{{ __('Email') }}</label>
                            <input wire:model="editEmail" type="email" class="w-full px-4 py-2.5 rounded-xl bg-white/5 border border-white/10 focus:border-white/25 outline-none text-sm transition">
                            @error('editEmail') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        {{-- Role --}}
                        <div>
                            <label class="block text-sm font-medium opacity-70 mb-1.5">{{ __('Role') }}</label>
                            <select wire:model="editRole" class="w-full px-4 py-2.5 rounded-xl bg-white/5 border border-white/10 focus:border-white/25 outline-none text-sm transition">
                                <option value="user">{{ __('User') }}</option>
                                <option value="admin">{{ __('Admin') }}</option>
                                <option value="super_admin">{{ __('Super Admin') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-3 mt-6">
                        <button type="button" wire:click="$set('showEditModal', false)" class="px-4 py-2 rounded-xl bg-white/5 hover:bg-white/10 text-sm transition">{{ __('Cancel') }}</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-blue-500/20 hover:bg-blue-500/30 border border-blue-500/25 text-blue-400 text-sm font-medium transition">
                            <span wire:loading.remove wire:target="updateUser">{{ __('Save Changes') }}</span>
                            <span wire:loading wire:target="updateUser">Saving...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Create User Modal --}}
    @if ($showCreateModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" wire:click="$set('showCreateModal', false)"></div>
            <div class="relative glass-card rounded-2xl p-6 w-full max-w-md z-10">
                <h3 class="text-lg font-bold mb-4">{{ __('Create User') }}</h3>
                <form wire:submit="createUser">
                    <div class="space-y-4">
                        {{-- Name --}}
                        <div>
                            <label class="block text-sm font-medium opacity-70 mb-1.5">{{ __('Name') }}</label>
                            <input wire:model="createName" type="text" class="w-full px-4 py-2.5 rounded-xl bg-white/5 border border-white/10 focus:border-white/25 outline-none text-sm transition" placeholder="Full name">
                            @error('createName') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        {{-- Email --}}
                        <div>
                            <label class="block text-sm font-medium opacity-70 mb-1.5">Email</label>
                            <input wire:model="createEmail" type="email" class="w-full px-4 py-2.5 rounded-xl bg-white/5 border border-white/10 focus:border-white/25 outline-none text-sm transition" placeholder="user@example.com">
                            @error('createEmail') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        {{-- Password --}}
                        <div>
                            <label class="block text-sm font-medium opacity-70 mb-1.5">{{ __('Password') }}</label>
                            <input wire:model="createPassword" type="password" class="w-full px-4 py-2.5 rounded-xl bg-white/5 border border-white/10 focus:border-white/25 outline-none text-sm transition" placeholder="Min 8 characters">
                            @error('createPassword') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        {{-- Role --}}
                        <div>
                            <label class="block text-sm font-medium opacity-70 mb-1.5">{{ __('Role') }}</label>
                            <select wire:model="createRole" class="w-full px-4 py-2.5 rounded-xl bg-white/5 border border-white/10 focus:border-white/25 outline-none text-sm transition">
                                <option value="user">{{ __('User') }}</option>
                                <option value="admin">{{ __('Admin') }}</option>
                                <option value="super_admin">{{ __('Super Admin') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-3 mt-6">
                        <button type="button" wire:click="$set('showCreateModal', false)" class="px-4 py-2 rounded-xl bg-white/5 hover:bg-white/10 text-sm transition">{{ __('Cancel') }}</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-500/20 hover:bg-emerald-500/30 border border-emerald-500/25 text-emerald-400 text-sm font-medium transition">
                            <span wire:loading.remove wire:target="createUser">{{ __('Create User') }}</span>
                            <span wire:loading wire:target="createUser">Creating...</span>
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
                <h3 class="text-lg font-bold mb-1">{{ __('Delete User') }}</h3>
                <p class="text-sm opacity-60 mb-5">{{ __('Are you sure you want to delete') }} <strong class="opacity-100">{{ $deletingUserName }}</strong>? {{ __('This action cannot be undone.') }}</p>
                <div class="flex items-center justify-center gap-3">
                    <button wire:click="$set('showDeleteModal', false)" class="px-5 py-2 rounded-xl bg-white/5 hover:bg-white/10 text-sm transition">{{ __('Cancel') }}</button>
                    <button wire:click="deleteUser" class="px-5 py-2 rounded-xl bg-red-500/20 hover:bg-red-500/30 border border-red-500/25 text-red-400 text-sm font-medium transition">
                        <span wire:loading.remove wire:target="deleteUser">{{ __('Delete') }}</span>
                        <span wire:loading wire:target="deleteUser">Deleting...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
