<div class="space-y-6">
    {{-- {{ __('Google Fonts API Key') }} --}}
    <div class="glass-card p-6 rounded-2xl" x-data="{ showFontsKey: false }">
        <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z" />
            </svg>
            Google Fonts API Key
        </h3>

        @if (session()->has('api-message'))
            <div class="mb-4 p-3 rounded-xl bg-emerald-500/20 border border-emerald-500/30 text-emerald-300 text-sm flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                {{ session('api-message') }}
            </div>
        @endif

        @if (session()->has('api-error'))
            <div class="mb-4 p-3 rounded-xl bg-red-500/20 border border-red-500/30 text-red-300 text-sm flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                </svg>
                {{ session('api-error') }}
            </div>
        @endif

        <p class="text-sm opacity-60 mb-4">
            {{ __('Enter your Google Fonts API key to enable font search and selection. Get one at') }}
            <a href="https://console.cloud.google.com/apis/credentials" target="_blank" class="text-blue-400 hover:text-blue-300 underline">Google Cloud Console</a>.
        </p>

        <div class="relative mb-4">
            <label class="block text-sm font-medium mb-2 opacity-80">API Key</label>
            <div class="relative">
                <input
                    :type="showFontsKey ? 'text' : 'password'"
                    wire:model="google_fonts_api_key"
                    placeholder="AIzaSy..."
                    class="w-full px-4 py-3 pr-12 rounded-xl bg-white/10 backdrop-blur-xl border border-white/20
                           focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20
                           placeholder-white/30 transition-all duration-200 outline-none font-mono text-sm"
                />
                <button type="button" @click="showFontsKey = !showFontsKey"
                        class="absolute right-3 top-3 opacity-50 hover:opacity-100 transition-opacity">
                    <svg x-show="!showFontsKey" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                    <svg x-show="showFontsKey" x-cloak class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                    </svg>
                </button>
            </div>
            @error('google_fonts_api_key') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
        </div>

        <div class="flex items-center gap-3">
            <button
                wire:click="save"
                class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-medium
                       transition-all duration-200 shadow-lg shadow-blue-600/25 hover:shadow-blue-500/40"
            >
                <span wire:loading.remove wire:target="save">{{ __('Save API Key') }}</span>
                <span wire:loading wire:target="save" class="flex items-center gap-2">
                    <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Saving...
                </span>
            </button>

            <button
                wire:click="testKey"
                class="px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20
                       transition-all duration-200 text-sm"
            >
                <span wire:loading.remove wire:target="testKey">{{ __('Test') }} {{ __('Connection') }}</span>
                <span wire:loading wire:target="testKey" class="flex items-center gap-2">
                    <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Testing...
                </span>
            </button>
        </div>
    </div>

    {{-- Gemini Translation API --}}
    <div class="glass-card p-6 rounded-2xl" x-data="{ showKeys: {} }">
        <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m10.5 21 5.25-11.25L21 21m-9-3h7.5M3 5.621a48.474 48.474 0 0 1 6-.371m0 0c1.12 0 2.233.038 3.334.114M9 5.25V3m3.334 2.364C11.176 10.658 7.69 15.08 3 17.502m9.334-12.138c.896.061 1.785.147 2.666.257m-4.589 8.495a18.023 18.023 0 0 1-3.827-5.802" />
            </svg>
            Translation Settings (Gemini AI)
        </h3>

        @if (session()->has('gemini-message'))
            <div class="mb-4 p-3 rounded-xl bg-emerald-500/20 border border-emerald-500/30 text-emerald-300 text-sm flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                {{ session('gemini-message') }}
            </div>
        @endif

        @if (session()->has('gemini-error'))
            <div class="mb-4 p-3 rounded-xl bg-red-500/20 border border-red-500/30 text-red-300 text-sm flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                </svg>
                {{ session('gemini-error') }}
            </div>
        @endif

        <p class="text-sm opacity-60 mb-4">
            {{ __('Add multiple API keys for auto-rotation when rate limits are hit. Get keys at') }}
            <a href="https://aistudio.google.com/apikey" target="_blank" class="text-blue-400 hover:text-blue-300 underline">Google AI Studio</a>.
            {{ __('Create keys from different projects for separate quotas.') }}
        </p>

        {{-- API Keys list --}}
        <div class="space-y-3 mb-4">
            <label class="block text-sm font-medium opacity-80">
                {{ __('Gemini API Keys') }}
                <span class="opacity-50 font-normal">({{ count(array_filter($gemini_api_keys, fn($k) => !empty(trim($k)))) }} active)</span>
            </label>
            @foreach($gemini_api_keys as $index => $key)
                <div class="flex items-center gap-2" wire:key="gemini-key-{{ $index }}">
                    <span class="text-xs opacity-40 w-5 text-right shrink-0">#{{ $index + 1 }}</span>
                    <div class="relative flex-1">
                        <input
                            :type="showKeys[{{ $index }}] ? 'text' : 'password'"
                            wire:model="gemini_api_keys.{{ $index }}"
                            placeholder="AIzaSy..."
                            class="w-full px-4 py-2.5 pr-10 rounded-xl bg-white/10 backdrop-blur-xl border border-white/20
                                   focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20
                                   placeholder-white/30 transition-all duration-200 outline-none font-mono text-sm"
                        />
                        <button type="button" @click="showKeys[{{ $index }}] = !showKeys[{{ $index }}]"
                                class="absolute right-3 top-2.5 opacity-40 hover:opacity-100 transition-opacity">
                            <svg x-show="!showKeys[{{ $index }}]" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                            <svg x-show="showKeys[{{ $index }}]" x-cloak class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                        </button>
                    </div>
                    <button wire:click="testGeminiKey({{ $index }})" title="Test this key"
                            class="px-3 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 transition-all text-xs shrink-0">
                        <span wire:loading.remove wire:target="testGeminiKey({{ $index }})">Test</span>
                        <span wire:loading wire:target="testGeminiKey({{ $index }})">
                            <svg class="animate-spin w-3 h-3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </span>
                    </button>
                    @if(count($gemini_api_keys) > 1)
                        <button wire:click="removeKey({{ $index }})" title="Remove key"
                                class="px-2 py-2.5 rounded-xl bg-red-500/10 hover:bg-red-500/20 border border-red-500/20 text-red-400 transition-all text-xs shrink-0">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                        </button>
                    @endif
                </div>
            @endforeach

            @if(count($gemini_api_keys) < 10)
                <button wire:click="addKey"
                        class="w-full py-2 rounded-xl border border-dashed border-white/20 hover:border-white/40 hover:bg-white/5
                               text-sm opacity-60 hover:opacity-100 transition-all flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    Add Another API Key ({{ count($gemini_api_keys) }}/10)
                </button>
            @endif

            <p class="text-xs opacity-40">
                {{ __('Keys auto-rotate when rate limits are hit. Create multiple keys from different Google Cloud projects for separate quotas.') }}
            </p>
        </div>

        <div class="relative mb-4">
            <label class="block text-sm font-medium mb-2 opacity-80">{{ __('Gemini Model') }}</label>
            <select wire:model="gemini_model"
                    class="w-full px-4 py-3 rounded-xl bg-white/10 backdrop-blur-xl border border-white/20
                           focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20
                           transition-all duration-200 outline-none text-sm appearance-none">
                <optgroup label="⭐ Recommended (Free Tier)" class="bg-gray-900">
                    <option value="gemini-2.5-flash" class="bg-gray-900">{{ __('Gemini 2.5 Flash — 5 RPM, 250K TPM, 20 RPD') }}</option>
                    <option value="gemini-2.0-flash-lite" class="bg-gray-900">{{ __('Gemini 2 Flash Lite — Lightweight & fast') }}</option>
                </optgroup>
                <optgroup label="Other Free Models" class="bg-gray-900">
                    <option value="gemini-2.5-pro" class="bg-gray-900">Gemini 2.5 Pro — Most capable (check quota)</option>
                    <option value="gemini-2.0-flash" class="bg-gray-900">{{ __('Gemini 2 Flash — Check if quota available') }}</option>
                    <option value="gemini-2.0-flash-exp" class="bg-gray-900">{{ __('Gemini 2 Flash Exp — Experimental') }}</option>
                </optgroup>
            </select>
            <p class="text-xs opacity-40 mt-1">
                ⭐ <strong>gemini-2.5-flash</strong> recommended — has free tier quota (5 RPM, 20 RPD)
            </p>
            @error('gemini_model') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
        </div>

        <div class="relative mb-4">
            <label class="block text-sm font-medium mb-2 opacity-80">{{ __('Default Translation Source Language') }}</label>
            <select wire:model="translation_source_language"
                    class="w-full px-4 py-3 rounded-xl bg-white/10 backdrop-blur-xl border border-white/20
                           focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20
                           transition-all duration-200 outline-none text-sm appearance-none">
                <option value="en" class="bg-gray-900">{{ __('English') }}</option>
                <option value="km" class="bg-gray-900">{{ __('Khmer') }}</option>
            </select>
            <p class="text-xs opacity-40 mt-1">{{ __('Used for auto-translating content to Khmer') }}</p>
        </div>

        <div class="flex items-center gap-3">
            <button
                wire:click="saveGemini"
                class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-medium
                       transition-all duration-200 shadow-lg shadow-blue-600/25 hover:shadow-blue-500/40"
            >
                <span wire:loading.remove wire:target="saveGemini">{{ __('Save Gemini Settings') }}</span>
                <span wire:loading wire:target="saveGemini" class="flex items-center gap-2">
                    <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Saving...
                </span>
            </button>

            <button
                wire:click="testAllKeys"
                class="px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20
                       transition-all duration-200 text-sm"
            >
                <span wire:loading.remove wire:target="testAllKeys">{{ __('Test All Keys') }}</span>
                <span wire:loading wire:target="testAllKeys" class="flex items-center gap-2">
                    <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Testing...
                </span>
            </button>
        </div>
    </div>
</div>
