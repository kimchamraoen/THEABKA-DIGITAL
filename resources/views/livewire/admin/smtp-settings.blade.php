<div class="glass-card p-6 rounded-2xl">
    <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
        </svg>
        {{ __('SMTP Settings') }}
    </h3>

    @if (session()->has('smtp-message'))
        <div class="mb-4 p-3 rounded-xl bg-emerald-500/20 border border-emerald-500/30 text-emerald-300 text-sm">
            {{ session('smtp-message') }}
        </div>
    @endif

    @if (session()->has('smtp-error'))
        <div class="mb-4 p-3 rounded-xl bg-red-500/20 border border-red-500/30 text-red-300 text-sm">
            {{ session('smtp-error') }}
        </div>
    @endif

    {{-- Spam Prevention Tips --}}
    <div class="mb-6 p-4 rounded-xl bg-amber-500/10 border border-amber-500/20">
        <p class="text-xs opacity-80 leading-relaxed">
            <strong class="text-amber-300">{{ __('Common Email Issues') }}:</strong><br>
            • <strong>Error 550 "classified as SPAM"</strong> — {{ __('Your') }} "{{ __('From Address') }}" must match your SMTP account domain (e.g. use <code class="bg-white/10 px-1 rounded">noreply@yourdomain.com</code> not <code class="bg-white/10 px-1 rounded">hello@example.com</code>)<br>
            • For Gmail: use an <a href="https://support.google.com/accounts/answer/185833" target="_blank" class="text-blue-400 underline">{{ __('App Password') }}</a>, enable 2-Step Verification first, and set From Address to your Gmail address<br>
            • For custom domains: set up SPF, DKIM, and DMARC DNS records to prevent spam classification<br>
            • {{ __('Make sure the') }} "{{ __('From Name') }}" matches something recognizable (your app name)
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        {{-- SMTP Host --}}
        <div>
            <label class="block text-sm font-medium mb-1 opacity-80">SMTP Host</label>
            <input type="text" wire:model="smtp_host" placeholder="smtp.gmail.com"
                   class="w-full px-4 py-2.5 rounded-xl bg-white/10 backdrop-blur-xl border border-white/20
                          focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 outline-none transition-all" />
            @error('smtp_host') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
        </div>

        {{-- SMTP Port --}}
        <div>
            <label class="block text-sm font-medium mb-1 opacity-80">SMTP Port</label>
            <input type="number" wire:model="smtp_port" placeholder="587"
                   class="w-full px-4 py-2.5 rounded-xl bg-white/10 backdrop-blur-xl border border-white/20
                          focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 outline-none transition-all" />
            @error('smtp_port') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
        </div>

        {{-- SMTP {{ __('Username') }} --}}
        <div>
            <label class="block text-sm font-medium mb-1 opacity-80">Username</label>
            <input type="text" wire:model="smtp_username" placeholder="your@email.com"
                   class="w-full px-4 py-2.5 rounded-xl bg-white/10 backdrop-blur-xl border border-white/20
                          focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 outline-none transition-all" />
            @error('smtp_username') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
        </div>

        {{-- SMTP Password --}}
        <div>
            <label class="block text-sm font-medium mb-1 opacity-80">Password</label>
            <input type="password" wire:model="smtp_password" placeholder="••••••••"
                   class="w-full px-4 py-2.5 rounded-xl bg-white/10 backdrop-blur-xl border border-white/20
                          focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 outline-none transition-all" />
            @error('smtp_password') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
        </div>

        {{-- {{ __('Encryption') }} --}}
        <div>
            <label class="block text-sm font-medium mb-1 opacity-80">Encryption</label>
            <select wire:model="smtp_encryption"
                    class="w-full px-4 py-2.5 rounded-xl bg-white/10 backdrop-blur-xl border border-white/20
                           focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 outline-none transition-all">
                <option value="tls">TLS</option>
                <option value="ssl">SSL</option>
                <option value="null">{{ __('None') }}</option>
            </select>
            @error('smtp_encryption') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
        </div>

        {{-- From Address --}}
        <div>
            <label class="block text-sm font-medium mb-1 opacity-80">From Address</label>
            <input type="email" wire:model="smtp_from_address" placeholder="noreply@yourapp.com"
                   class="w-full px-4 py-2.5 rounded-xl bg-white/10 backdrop-blur-xl border border-white/20
                          focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 outline-none transition-all" />
            <p class="text-xs opacity-40 mt-1">{{ __('Must match your SMTP domain to avoid spam filters') }}</p>
            @error('smtp_from_address') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
        </div>

        {{-- From Name --}}
        <div class="md:col-span-2">
            <label class="block text-sm font-medium mb-1 opacity-80">From Name</label>
            <input type="text" wire:model="smtp_from_name" placeholder="My Application"
                   class="w-full px-4 py-2.5 rounded-xl bg-white/10 backdrop-blur-xl border border-white/20
                          focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 outline-none transition-all" />
            @error('smtp_from_name') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
        </div>
    </div>

    {{-- Test Email --}}
    <div class="mb-6 p-4 rounded-xl bg-white/5 border border-white/10">
        <label class="block text-sm font-medium mb-2 opacity-80">{{ __('Send Test') }} Email</label>
        <div class="flex gap-3">
            <input type="email" wire:model="test_email" placeholder="your@email.com"
                   class="flex-1 px-4 py-2.5 rounded-xl bg-white/10 border border-white/20 text-sm outline-none focus:border-blue-400/50" />
            <button type="button" wire:click="sendTestEmail" wire:loading.attr="disabled"
                    class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-medium transition-all flex items-center gap-2">
                <span wire:loading.remove wire:target="sendTestEmail">
                    <svg class="w-4 h-4 inline" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" /></svg>
                    Send Test
                </span>
                <span wire:loading wire:target="sendTestEmail">Sending...</span>
            </button>
        </div>
        <p class="text-xs opacity-40 mt-1">{{ __('Save your SMTP settings first, then send a test email to verify they work') }}</p>
    </div>

    <button
        wire:click="save"
        class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-medium
               transition-all duration-200 shadow-lg shadow-blue-600/25 hover:shadow-blue-500/40"
    >
        {{ __('Save Settings') }}
    </button>
</div>
