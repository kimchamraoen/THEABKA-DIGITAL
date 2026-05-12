<x-app-layout>
    @section('title', __('app.dashboard.title'))
    @section('page-title', __('app.dashboard.title'))
    <div class="px-4 pb-6">
        {{-- Page Title --}}
        <div class="mb-5">
            <h1 class="text-2xl font-bold">{{ __('app.dashboard.title') }}</h1>
            <p class="text-sm opacity-50 mt-0.5">{{ __('app.dashboard.welcome_back') }} {{ auth()->user()->name }}</p>
        </div>

        {{-- Stats Row --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
            {{-- Total Users --}}
            <div class="glass-card rounded-2xl p-5">
                <div class="flex items-start justify-between">
                    <div class="w-10 h-10 rounded-xl bg-blue-500/20 flex items-center justify-center mb-3">
                        <svg class="w-5 h-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                        </svg>
                    </div>
                    <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded-md bg-emerald-500/15 text-emerald-400">+12%</span>
                </div>
                <p class="text-2xl font-bold">{{ \App\Models\User::count() }}</p>
                <p class="text-xs opacity-50 mt-0.5">{{ __('app.dashboard.total_users') }}</p>
            </div>

            {{-- Role --}}
            <div class="glass-card rounded-2xl p-5">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/20 flex items-center justify-center mb-3">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                    </svg>
                </div>
                <p class="text-2xl font-bold capitalize">{{ str_replace('_', ' ', auth()->user()->role) }}</p>
                <p class="text-xs opacity-50 mt-0.5">{{ __('app.dashboard.your_role') }}</p>
            </div>

            {{-- Email --}}
            <div class="glass-card rounded-2xl p-5">
                <div class="flex items-start justify-between">
                    <div class="w-10 h-10 rounded-xl bg-purple-500/20 flex items-center justify-center mb-3">
                        <svg class="w-5 h-5 text-purple-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                        </svg>
                    </div>
                    @if (auth()->user()->hasVerifiedEmail())
                        <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded-md bg-emerald-500/15 text-emerald-400">{{ __('app.dashboard.verified') }}</span>
                    @else
                        <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded-md bg-amber-500/15 text-amber-400">{{ __('app.dashboard.pending') }}</span>
                    @endif
                </div>
                <p class="text-2xl font-bold">
                    @if (auth()->user()->hasVerifiedEmail())
                        <span class="text-emerald-400">{{ __('app.dashboard.active') }}</span>
                    @else
                        <span class="text-amber-400">{{ __('app.dashboard.pending') }}</span>
                    @endif
                </p>
                <p class="text-xs opacity-50 mt-0.5">{{ __('app.dashboard.email_status') }}</p>
            </div>

            {{-- 2FA --}}
            <div class="glass-card rounded-2xl p-5">
                <div class="flex items-start justify-between">
                    <div class="w-10 h-10 rounded-xl bg-amber-500/20 flex items-center justify-center mb-3">
                        <svg class="w-5 h-5 text-amber-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                        </svg>
                    </div>
                    @if (auth()->user()->two_factor_secret)
                        <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded-md bg-emerald-500/15 text-emerald-400">{{ __('app.dashboard.on') }}</span>
                    @else
                        <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded-md bg-red-500/15 text-red-400">{{ __('app.dashboard.off') }}</span>
                    @endif
                </div>
                <p class="text-2xl font-bold">
                    @if (auth()->user()->two_factor_secret)
                        <span class="text-emerald-400">{{ __('app.dashboard.enabled') }}</span>
                    @else
                        <span class="text-red-400">{{ __('app.dashboard.disabled') }}</span>
                    @endif
                </p>
                <p class="text-xs opacity-50 mt-0.5">{{ __('app.dashboard.two_factor_auth') }}</p>
            </div>
        </div>

        {{-- Bottom Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">
            {{-- Quick Actions - 3 cols --}}
            <div class="lg:col-span-3 glass-card rounded-2xl p-5">
                <h2 class="text-base font-bold mb-3">{{ __('app.dashboard.quick_actions') }}</h2>
                <div class="space-y-2">
                    <a href="{{ route('profile.show') }}" wire:navigate class="flex items-center gap-3 p-3 rounded-xl bg-white/5 hover:bg-white/10 transition group">
                        <div class="w-9 h-9 rounded-lg bg-blue-500/20 flex items-center justify-center shrink-0">
                            <svg class="w-4.5 h-4.5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium">{{ __('app.dashboard.profile_settings') }}</p>
                            <p class="text-xs opacity-50">{{ __('app.dashboard.profile_settings_desc') }}</p>
                        </div>
                        <svg class="w-4 h-4 opacity-30 group-hover:opacity-70 group-hover:translate-x-0.5 transition shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>
                    </a>
                    <a href="{{ route('documentation') }}" class="flex items-center gap-3 p-3 rounded-xl bg-white/5 hover:bg-white/10 transition group">
                        <div class="w-9 h-9 rounded-lg bg-teal-500/20 flex items-center justify-center shrink-0">
                            <svg class="w-4.5 h-4.5 text-teal-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium">{{ __('app.dashboard.documentation') }}</p>
                            <p class="text-xs opacity-50">{{ __('app.dashboard.documentation_desc') }}</p>
                        </div>
                        <svg class="w-4 h-4 opacity-30 group-hover:opacity-70 group-hover:translate-x-0.5 transition shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>
                    </a>
                    @if (auth()->user()->isSuperAdmin())
                    <a href="{{ route('admin.users') }}" wire:navigate class="flex items-center gap-3 p-3 rounded-xl bg-white/5 hover:bg-white/10 transition group">
                        <div class="w-9 h-9 rounded-lg bg-cyan-500/20 flex items-center justify-center shrink-0">
                            <svg class="w-4.5 h-4.5 text-cyan-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium">{{ __('app.dashboard.manage_users') }}</p>
                            <p class="text-xs opacity-50">{{ __('app.dashboard.manage_users_desc') }}</p>
                        </div>
                        <svg class="w-4 h-4 opacity-30 group-hover:opacity-70 group-hover:translate-x-0.5 transition shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>
                    </a>
                    <a href="{{ route('admin.settings') }}" wire:navigate class="flex items-center gap-3 p-3 rounded-xl bg-white/5 hover:bg-white/10 transition group">
                        <div class="w-9 h-9 rounded-lg bg-amber-500/20 flex items-center justify-center shrink-0">
                            <svg class="w-4.5 h-4.5 text-amber-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium">{{ __('app.dashboard.admin_settings') }}</p>
                            <p class="text-xs opacity-50">{{ __('app.dashboard.admin_settings_desc') }}</p>
                        </div>
                        <svg class="w-4 h-4 opacity-30 group-hover:opacity-70 group-hover:translate-x-0.5 transition shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>
                    </a>
                    @endif
                </div>
            </div>

            {{-- Account Info - 2 cols --}}
            <div class="lg:col-span-2 glass-card rounded-2xl p-5">
                <h2 class="text-base font-bold mb-3">{{ __('app.dashboard.account') }}</h2>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-lg font-bold shrink-0 overflow-hidden">
                        <img src="{{ auth()->user()->profile_image }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover">
                    </div>
                    <div class="min-w-0">
                        <p class="font-semibold text-sm truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs opacity-50 truncate">{{ auth()->user()->email }}</p>
                    </div>
                </div>
                <div class="space-y-2.5 text-sm border-t border-white/10 pt-4">
                    <div class="flex justify-between">
                        <span class="opacity-50">{{ __('app.dashboard.member_since') }}</span>
                        <span class="font-medium">{{ auth()->user()->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="opacity-50">{{ __('app.dashboard.role') }}</span>
                        <span class="font-medium capitalize">{{ str_replace('_', ' ', auth()->user()->role) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="opacity-50">{{ __('app.dashboard.two_fa') }}</span>
                        <span class="font-medium {{ auth()->user()->two_factor_secret ? 'text-emerald-400' : 'text-red-400' }}">
                            {{ auth()->user()->two_factor_secret ? __('app.dashboard.enabled') : __('app.dashboard.disabled') }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="opacity-50">{{ __('app.dashboard.email') }}</span>
                        <span class="font-medium {{ auth()->user()->hasVerifiedEmail() ? 'text-emerald-400' : 'text-amber-400' }}">
                            {{ auth()->user()->hasVerifiedEmail() ? __('app.dashboard.verified') : __('app.dashboard.unverified') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
