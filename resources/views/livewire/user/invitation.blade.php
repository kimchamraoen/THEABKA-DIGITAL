<div>
    <x-app-layout>
        <div class="px-4 pb-6">
            {{-- Page Title --}}
            <div class="mb-5">
                <h1 class="text-2xl font-bold">{{ __('app.dashboard.invitation') }}</h1>
                <p class="text-sm opacity-50 mt-0.5">{{ __('app.dashboard.welcome_back') }} {{ auth()->user()->name }}</p>
            </div>

            {{-- Stats Row --}}
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
                {{-- Wedding Card --}}
                <div class="glass-card rounded-2xl overflow-hidden  cursor-pointer w-[22rem]">
                    <img src="{{ asset('images/cover.jpg') }}" class="w-full h-60 object-cover">
                    <div class="p-4">
                        <h3 class="font-semibold text-lg">{{ __('app.dashboard.Wedding') }}</h3>
                        <p class="text-xs opacity-60">{{ __('app.dashboard.Edit Wedding Invitation') }}</p>
                    </div>
                    <div class="flex items-center gap-4 mb-4 px-4 w-full">
                        <a 
                            href="{{ route('wedding') }}"
                            class="flex-1 flex items-center justify-center gap-2 p-3 text-sm font-semibold text-white transition-all duration-300 rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 active:scale-95 shadow-lg shadow-indigo-500/20"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.042 21.672 13.684 16.6m0 0-2.51 2.225.569-9.47 5.227 7.917-3.286-.672ZM12 2.25V4.5m5.834.166-1.591 1.591M20.25 10.5H18M18.757 17.243l-1.591-1.591m-9.996 1.591 1.591-1.591M3.75 10.5H6M4.509 4.666l1.591 1.591" />
                            </svg>
                            <span>{{ __('app.dashboard.Use Template') }}</span>
                        </a>

                        <a 
                            href="{{ route('invitation.default-landing-wedding') }}" 
                            target="_blank"
                            class="flex-1 flex items-center justify-center gap-2 p-3 text-sm font-medium text-gray-300 transition-all duration-200 border bg-white/5 border-white/10 rounded-xl hover:bg-white/10 hover:text-white active:scale-95"
                        >
                            <span>{{ __('app.dashboard.View Live') }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.644C3.391 8.816 7.24 6.5 12 6.5s8.609 2.316 9.964 5.178c.039.082.039.182 0 .264C20.609 15.184 16.76 17.5 12 17.5s-8.609-2.316-9.964-5.178Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                        </a>
                    </div>
                </div>

                {{-- Birthday Card --}}
                <!-- <div  class="glass-card rounded-2xl overflow-hidden hover:shadow-lg hover:-translate-y-1 hover:scale-[1.03] transition block cursor-pointer">
                    <img src="{{ asset('images/cards/birthday.jpg') }}" class="w-full h-40 object-cover">
                    <div class="p-4">
                        <h3 class="font-semibold text-lg">{{ __('app.dashboard.Wedding') }}</h3>
                        <p class="text-xs opacity-60">{{ __('app.dashboard.Edit Wedding Invitation') }}</p>
                    </div>
                    <div class="flex items-center gap-4 mb-4 px-4 w-full">
                        <a 
                            href="{{ route('birthday') }}"
                            class="flex-1 flex items-center justify-center gap-2 p-3 text-sm font-semibold text-white transition-all duration-300 rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 active:scale-95 shadow-lg shadow-indigo-500/20"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.042 21.672 13.684 16.6m0 0-2.51 2.225.569-9.47 5.227 7.917-3.286-.672ZM12 2.25V4.5m5.834.166-1.591 1.591M20.25 10.5H18M18.757 17.243l-1.591-1.591m-9.996 1.591 1.591-1.591M3.75 10.5H6M4.509 4.666l1.591 1.591" />
                            </svg>
                            <span>{{ __('app.dashboard.Use Template') }}</span>
                        </a>

                        <a 
                            href="/path-to-view" 
                            target="_blank"
                            class="flex-1 flex items-center justify-center gap-2 p-3 text-sm font-medium text-gray-300 transition-all duration-200 border bg-white/5 border-white/10 rounded-xl hover:bg-white/10 hover:text-white active:scale-95"
                        >
                            <span>{{ __('app.dashboard.View Live') }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.644C3.391 8.816 7.24 6.5 12 6.5s8.609 2.316 9.964 5.178c.039.082.039.182 0 .264C20.609 15.184 16.76 17.5 12 17.5s-8.609-2.316-9.964-5.178Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                        </a>
                    </div>
                </div> -->
            </div>
        </div>  
    </x-app-layout>
</div>