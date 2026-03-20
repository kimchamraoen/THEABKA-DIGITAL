<div class="glass-card p-6 rounded-2xl">
    <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
        </svg>
        {{ __('Security Settings') }}
    </h3>

    @if (session()->has('security-message'))
        <div class="mb-4 p-3 rounded-xl bg-emerald-500/20 border border-emerald-500/30 text-emerald-300 text-sm">
            {{ session('security-message') }}
        </div>
    @endif

    {{-- Email Verification Bypass --}}
    <div class="mb-6 p-4 rounded-xl bg-white/5 border border-white/10">
        <div class="flex items-start justify-between gap-4">
            <div class="flex-1">
                <h4 class="text-sm font-semibold flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                    {{ __('Allow Login Without Email Verification') }}
                </h4>
                <p class="text-xs opacity-60 mt-1">
                    {{ __('When enabled, users can access their dashboard without verifying their email address.') }}
                </p>
                <p class="text-xs text-amber-400/80 mt-2">
                    <svg class="w-3 h-3 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                    </svg>
                    {{ __('Warning: This reduces account security. Enable only if necessary.') }}
                </p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" wire:model="allow_unverified_login" class="sr-only peer">
                <div class="w-11 h-6 bg-white/10 rounded-full peer peer-checked:bg-amber-500 peer-focus:ring-2 peer-focus:ring-amber-400/50 transition-all after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
            </label>
        </div>
    </div>

    {{-- Info Box --}}
    <div class="mb-6 p-4 rounded-xl bg-blue-500/10 border border-blue-500/20">
        <p class="text-xs opacity-80 leading-relaxed">
            <strong class="text-blue-300">{{ __('Note') }}:</strong><br>
            {{ __('You can also allow specific users to bypass email verification from the User Management page. Per-user settings override the global setting.') }}
        </p>
    </div>

    {{-- Save Button --}}
    <div class="flex justify-end">
        <button wire:click="save" wire:loading.attr="disabled"
                class="px-6 py-2.5 rounded-xl bg-blue-500/80 hover:bg-blue-500 text-white font-medium text-sm
                       transition-all duration-200 disabled:opacity-50 flex items-center gap-2">
            <svg wire:loading wire:target="save" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span wire:loading.remove wire:target="save">{{ __('Save Settings') }}</span>
            <span wire:loading wire:target="save">{{ __('Saving...') }}</span>
        </button>
    </div>
</div>
