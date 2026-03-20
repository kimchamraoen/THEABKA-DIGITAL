<div x-data="{
        maxWidth: @js($auth_card_max_width),
        paddingX: @js($auth_card_padding_x),
        paddingY: @js($auth_card_padding_y),
        borderRadius: @js($auth_card_border_radius),
        fontSize: @js($auth_card_font_size),
        fontColor: @js($auth_card_font_color ?? ''),
        labelColor: @js($auth_label_color ?? ''),
        headingColor: @js($auth_heading_color ?? ''),
        linkColor: @js($auth_link_color ?? ''),
        btnBgColor: @js($auth_btn_bg_color ?? ''),
        btnTextColor: @js($auth_btn_text_color ?? ''),
        logoSize: @js($auth_logo_size),

        savedMaxWidth: @js($auth_card_max_width),
        savedPaddingX: @js($auth_card_padding_x),
        savedPaddingY: @js($auth_card_padding_y),
        savedBorderRadius: @js($auth_card_border_radius),
        savedFontSize: @js($auth_card_font_size),
        savedFontColor: @js($auth_card_font_color ?? ''),
        savedLabelColor: @js($auth_label_color ?? ''),
        savedHeadingColor: @js($auth_heading_color ?? ''),
        savedLinkColor: @js($auth_link_color ?? ''),
        savedBtnBgColor: @js($auth_btn_bg_color ?? ''),
        savedBtnTextColor: @js($auth_btn_text_color ?? ''),
        savedLogoSize: @js($auth_logo_size),

        dirty: false,
        saved: false,

        get isDirty() {
            return this.maxWidth !== this.savedMaxWidth
                || this.paddingX !== this.savedPaddingX
                || this.paddingY !== this.savedPaddingY
                || this.borderRadius !== this.savedBorderRadius
                || this.fontSize !== this.savedFontSize
                || this.fontColor !== this.savedFontColor
                || this.labelColor !== this.savedLabelColor
                || this.headingColor !== this.savedHeadingColor
                || this.linkColor !== this.savedLinkColor
                || this.btnBgColor !== this.savedBtnBgColor
                || this.btnTextColor !== this.savedBtnTextColor
                || this.logoSize !== this.savedLogoSize;
        },

        markDirty() {
            this.dirty = this.isDirty;
            this.saved = false;
        },

        revert() {
            this.maxWidth = this.savedMaxWidth;
            this.paddingX = this.savedPaddingX;
            this.paddingY = this.savedPaddingY;
            this.borderRadius = this.savedBorderRadius;
            this.fontSize = this.savedFontSize;
            this.fontColor = this.savedFontColor;
            this.labelColor = this.savedLabelColor;
            this.headingColor = this.savedHeadingColor;
            this.linkColor = this.savedLinkColor;
            this.btnBgColor = this.savedBtnBgColor;
            this.btnTextColor = this.savedBtnTextColor;
            this.logoSize = this.savedLogoSize;
            this.dirty = false;
            this.saved = false;
        },

        async save() {
            await $wire.call('save', this.maxWidth, this.paddingX, this.paddingY, this.borderRadius, this.fontSize, this.fontColor || null, this.labelColor || null, this.headingColor || null, this.linkColor || null, this.btnBgColor || null, this.btnTextColor || null, this.logoSize);
            this.savedMaxWidth = this.maxWidth;
            this.savedPaddingX = this.paddingX;
            this.savedPaddingY = this.paddingY;
            this.savedBorderRadius = this.borderRadius;
            this.savedFontSize = this.fontSize;
            this.savedFontColor = this.fontColor;
            this.savedLabelColor = this.labelColor;
            this.savedHeadingColor = this.headingColor;
            this.savedLinkColor = this.linkColor;
            this.savedBtnBgColor = this.btnBgColor;
            this.savedBtnTextColor = this.btnTextColor;
            this.savedLogoSize = this.logoSize;
            this.dirty = false;
            this.saved = true;
            setTimeout(() => this.saved = false, 3000);
        },

        async resetDefaults() {
            this.maxWidth = 448;
            this.paddingX = 32;
            this.paddingY = 24;
            this.borderRadius = 16;
            this.fontSize = 14;
            this.fontColor = '';
            this.labelColor = '';
            this.headingColor = '';
            this.linkColor = '';
            this.btnBgColor = '';
            this.btnTextColor = '';
            this.logoSize = 48;
            await this.save();
        }
    }"
    class="glass-card p-6 rounded-2xl">

    <h3 class="text-lg font-semibold mb-2 flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
        </svg>
        {{ __('Auth Card Appearance') }}
    </h3>

    <p class="text-sm opacity-60 mb-6">{{ __('Customize the login, register, and password reset glass cards — dimensions, spacing, colors, and typography.') }}</p>

    {{-- Saved toast --}}
    <div x-show="saved" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="mb-4 p-3 rounded-xl bg-emerald-500/20 border border-emerald-500/30 text-emerald-300 text-sm flex items-center gap-2" style="display:none;">
        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
        {{ __('Auth card settings saved!') }}
    </div>

    {{-- Unsaved indicator --}}
    <div x-show="dirty" x-transition class="mb-4 p-3 rounded-xl bg-amber-500/15 border border-amber-500/25 text-amber-300 text-sm flex items-center gap-2" style="display:none;">
        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" /></svg>
        {{ __('You have unsaved changes.') }}
    </div>

    {{-- {{ __('Live Preview') }} --}}
    <div class="mb-8 p-4 rounded-xl border border-white/10 bg-white/5">
        <h4 class="text-sm font-semibold uppercase tracking-wider opacity-70 mb-4">Live Preview</h4>
        <div class="flex justify-center">
            <div class="border border-white/10 bg-white/[0.06] backdrop-blur-sm transition-all duration-300 overflow-hidden"
                 :style="`max-width: ${maxWidth}px; width: 100%; padding: ${paddingY}px ${paddingX}px; border-radius: ${borderRadius}px; font-size: ${fontSize}px; color: ${fontColor || 'inherit'};`">

                {{-- Logo placeholder --}}
                <div class="flex justify-center mb-4">
                    <div class="rounded-xl bg-gradient-to-br from-blue-500/20 to-indigo-500/20 border border-white/10 flex items-center justify-center"
                         :style="`width: ${logoSize}px; height: ${logoSize}px;`">
                        <svg :style="`width: ${logoSize * 0.6}px; height: ${logoSize * 0.6}px;`" class="opacity-50" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0 0 22.5 18.75V5.25A2.25 2.25 0 0 0 20.25 3H3.75A2.25 2.25 0 0 0 1.5 5.25v13.5A2.25 2.25 0 0 0 3.75 21Z" />
                        </svg>
                    </div>
                </div>

                {{-- Preview fields --}}
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium mb-1 transition-colors" :style="`color: ${labelColor || 'inherit'};`">{{ __('Email') }}</label>
                        <div class="w-full h-9 rounded-lg bg-white/10 border border-white/15"></div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1 transition-colors" :style="`color: ${labelColor || 'inherit'};`">{{ __('Password') }}</label>
                        <div class="w-full h-9 rounded-lg bg-white/10 border border-white/15"></div>
                    </div>
                    <div class="flex items-center justify-between pt-2">
                        <span class="text-sm underline transition-colors" :style="`color: ${linkColor || '#818cf8'};`">Forgot?</span>
                        <div class="px-4 py-2 rounded-lg font-medium text-sm transition-colors"
                             :style="`background: ${btnBgColor || 'rgb(99, 102, 241)'}; color: ${btnTextColor || '#fff'};`">
                            {{ __('Log in') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Settings Groups --}}
    <div class="space-y-8">
        {{-- {{ __('Dimensions') }} --}}
        <div>
            <h4 class="text-sm font-semibold uppercase tracking-wider opacity-70 mb-4 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15" /></svg>
                Dimensions
            </h4>
            <div class="space-y-5">
                {{-- {{ __('Card Max Width') }} --}}
                <div>
                    <label class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium">Card Max Width</span>
                        <span class="text-xs opacity-50 tabular-nums" x-text="`${maxWidth}px`"></span>
                    </label>
                    <input type="range" x-model.number="maxWidth" @input="markDirty()" min="320" max="800" step="8"
                           class="w-full h-2 rounded-full appearance-none cursor-pointer bg-white/10 accent-blue-500">
                    <div class="flex justify-between text-[10px] opacity-30 mt-1">
                        <span>320px</span>
                        <span>800px</span>
                    </div>
                </div>

                {{-- Padding X --}}
                <div>
                    <label class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium">{{ __('Horizontal Padding') }}</span>
                        <span class="text-xs opacity-50 tabular-nums" x-text="`${paddingX}px`"></span>
                    </label>
                    <input type="range" x-model.number="paddingX" @input="markDirty()" min="12" max="64" step="2"
                           class="w-full h-2 rounded-full appearance-none cursor-pointer bg-white/10 accent-blue-500">
                    <div class="flex justify-between text-[10px] opacity-30 mt-1">
                        <span>12px</span>
                        <span>64px</span>
                    </div>
                </div>

                {{-- Padding Y --}}
                <div>
                    <label class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium">{{ __('Vertical Padding') }}</span>
                        <span class="text-xs opacity-50 tabular-nums" x-text="`${paddingY}px`"></span>
                    </label>
                    <input type="range" x-model.number="paddingY" @input="markDirty()" min="12" max="64" step="2"
                           class="w-full h-2 rounded-full appearance-none cursor-pointer bg-white/10 accent-blue-500">
                    <div class="flex justify-between text-[10px] opacity-30 mt-1">
                        <span>12px</span>
                        <span>64px</span>
                    </div>
                </div>

                {{-- {{ __('Border Radius') }} --}}
                <div>
                    <label class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium">Border Radius</span>
                        <span class="text-xs opacity-50 tabular-nums" x-text="`${borderRadius}px`"></span>
                    </label>
                    <input type="range" x-model.number="borderRadius" @input="markDirty()" min="0" max="32" step="1"
                           class="w-full h-2 rounded-full appearance-none cursor-pointer bg-white/10 accent-blue-500">
                    <div class="flex justify-between text-[10px] opacity-30 mt-1">
                        <span>0px</span>
                        <span>32px</span>
                    </div>
                </div>

                {{-- {{ __('Logo Size') }} --}}
                <div>
                    <label class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium">Logo Size</span>
                        <span class="text-xs opacity-50 tabular-nums" x-text="`${logoSize}px`"></span>
                    </label>
                    <input type="range" x-model.number="logoSize" @input="markDirty()" min="24" max="96" step="2"
                           class="w-full h-2 rounded-full appearance-none cursor-pointer bg-white/10 accent-blue-500">
                    <div class="flex justify-between text-[10px] opacity-30 mt-1">
                        <span>24px</span>
                        <span>96px</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- {{ __('Typography') }} --}}
        <div>
            <h4 class="text-sm font-semibold uppercase tracking-wider opacity-70 mb-4 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.076-4.076a1.526 1.526 0 0 1 1.037-.443 48.2 48.2 0 0 0 5.024-.41c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" /></svg>
                Typography
            </h4>
            <div class="space-y-5">
                {{-- {{ __('Font Size') }} --}}
                <div>
                    <label class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium">Font Size</span>
                        <span class="text-xs opacity-50 tabular-nums" x-text="`${fontSize}px`"></span>
                    </label>
                    <input type="range" x-model.number="fontSize" @input="markDirty()" min="11" max="20" step="1"
                           class="w-full h-2 rounded-full appearance-none cursor-pointer bg-white/10 accent-blue-500">
                    <div class="flex justify-between text-[10px] opacity-30 mt-1">
                        <span>11px</span>
                        <span>20px</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- {{ __('Colors') }} --}}
        <div>
            <h4 class="text-sm font-semibold uppercase tracking-wider opacity-70 mb-4 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.098 19.902a3.75 3.75 0 0 0 5.304 0l6.401-6.402M6.75 21A3.75 3.75 0 0 1 3 17.25V4.125C3 3.504 3.504 3 4.125 3h5.25c.621 0 1.125.504 1.125 1.125v4.072M6.75 21a3.75 3.75 0 0 0 3.75-3.75V8.197M6.75 21h13.125c.621 0 1.125-.504 1.125-1.125v-5.25c0-.621-.504-1.125-1.125-1.125h-4.072M10.5 8.197l2.88-2.88c.438-.439 1.15-.439 1.59 0l3.712 3.713c.44.44.44 1.152 0 1.59l-2.879 2.88M6.75 17.25h.008v.008H6.75v-.008Z" /></svg>
                Colors
            </h4>
            <p class="text-xs opacity-40 mb-4">{{ __('Leave blank to use theme defaults.') }}</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Font Color --}}
                <div>
                    <label class="block text-sm font-medium mb-2">{{ __('Body Text Color') }}</label>
                    <div class="flex items-center gap-2">
                        <input type="color" x-model="fontColor" @input="markDirty()"
                               class="w-9 h-9 rounded-lg cursor-pointer border border-white/15 bg-transparent appearance-none p-0.5">
                        <input type="text" x-model="fontColor" @input="markDirty()" placeholder="#c7d2fe"
                               class="flex-1 bg-white/10 border border-white/15 rounded-lg px-3 py-2 text-sm font-mono placeholder:opacity-30">
                        <button @click="fontColor = ''; markDirty()" class="p-2 rounded-lg hover:bg-white/10 opacity-40 hover:opacity-80 transition" title="Clear">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                </div>

                {{-- {{ __('Label Color') }} --}}
                <div>
                    <label class="block text-sm font-medium mb-2">Label Color</label>
                    <div class="flex items-center gap-2">
                        <input type="color" x-model="labelColor" @input="markDirty()"
                               class="w-9 h-9 rounded-lg cursor-pointer border border-white/15 bg-transparent appearance-none p-0.5">
                        <input type="text" x-model="labelColor" @input="markDirty()" placeholder="#a5b4fc"
                               class="flex-1 bg-white/10 border border-white/15 rounded-lg px-3 py-2 text-sm font-mono placeholder:opacity-30">
                        <button @click="labelColor = ''; markDirty()" class="p-2 rounded-lg hover:bg-white/10 opacity-40 hover:opacity-80 transition" title="Clear">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                </div>

                {{-- Heading Color --}}
                <div>
                    <label class="block text-sm font-medium mb-2">{{ __('Heading / App Name') }}</label>
                    <div class="flex items-center gap-2">
                        <input type="color" x-model="headingColor" @input="markDirty()"
                               class="w-9 h-9 rounded-lg cursor-pointer border border-white/15 bg-transparent appearance-none p-0.5">
                        <input type="text" x-model="headingColor" @input="markDirty()" placeholder="#818cf8"
                               class="flex-1 bg-white/10 border border-white/15 rounded-lg px-3 py-2 text-sm font-mono placeholder:opacity-30">
                        <button @click="headingColor = ''; markDirty()" class="p-2 rounded-lg hover:bg-white/10 opacity-40 hover:opacity-80 transition" title="Clear">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                </div>

                {{-- {{ __('Link Color') }} --}}
                <div>
                    <label class="block text-sm font-medium mb-2">Link Color</label>
                    <div class="flex items-center gap-2">
                        <input type="color" x-model="linkColor" @input="markDirty()"
                               class="w-9 h-9 rounded-lg cursor-pointer border border-white/15 bg-transparent appearance-none p-0.5">
                        <input type="text" x-model="linkColor" @input="markDirty()" placeholder="#818cf8"
                               class="flex-1 bg-white/10 border border-white/15 rounded-lg px-3 py-2 text-sm font-mono placeholder:opacity-30">
                        <button @click="linkColor = ''; markDirty()" class="p-2 rounded-lg hover:bg-white/10 opacity-40 hover:opacity-80 transition" title="Clear">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                </div>

                {{-- Button BG --}}
                <div>
                    <label class="block text-sm font-medium mb-2">{{ __('Button Background') }}</label>
                    <div class="flex items-center gap-2">
                        <input type="color" x-model="btnBgColor" @input="markDirty()"
                               class="w-9 h-9 rounded-lg cursor-pointer border border-white/15 bg-transparent appearance-none p-0.5">
                        <input type="text" x-model="btnBgColor" @input="markDirty()" placeholder="#6366f1"
                               class="flex-1 bg-white/10 border border-white/15 rounded-lg px-3 py-2 text-sm font-mono placeholder:opacity-30">
                        <button @click="btnBgColor = ''; markDirty()" class="p-2 rounded-lg hover:bg-white/10 opacity-40 hover:opacity-80 transition" title="Clear">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                </div>

                {{-- Button Text --}}
                <div>
                    <label class="block text-sm font-medium mb-2">{{ __('Button Text Color') }}</label>
                    <div class="flex items-center gap-2">
                        <input type="color" x-model="btnTextColor" @input="markDirty()"
                               class="w-9 h-9 rounded-lg cursor-pointer border border-white/15 bg-transparent appearance-none p-0.5">
                        <input type="text" x-model="btnTextColor" @input="markDirty()" placeholder="#ffffff"
                               class="flex-1 bg-white/10 border border-white/15 rounded-lg px-3 py-2 text-sm font-mono placeholder:opacity-30">
                        <button @click="btnTextColor = ''; markDirty()" class="p-2 rounded-lg hover:bg-white/10 opacity-40 hover:opacity-80 transition" title="Clear">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="flex items-center gap-3 mt-8">
        <button @click="save()" :disabled="!isDirty"
                class="px-5 py-2.5 rounded-xl bg-blue-500/80 hover:bg-blue-500 text-white font-medium text-sm transition-all duration-200 flex items-center gap-2 disabled:opacity-40 disabled:cursor-not-allowed">
            {{ __('Save Changes') }}
        </button>

        <button @click="revert()" x-show="isDirty"
                class="px-5 py-2.5 rounded-xl border border-amber-500/30 bg-amber-500/10 hover:bg-amber-500/20 text-amber-300 text-sm font-medium transition-all duration-200">
            {{ __('Revert') }}
        </button>

        <button @click="resetDefaults()"
                class="px-5 py-2.5 rounded-xl border border-white/15 hover:bg-white/10 text-sm font-medium transition-all duration-200">
            {{ __('Reset to Defaults') }}
        </button>
    </div>
</div>
