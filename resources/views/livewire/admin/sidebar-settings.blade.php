<div x-data="{
        fontSize: @js($sidebar_font_size),
        iconSize: @js($sidebar_icon_size),
        width: @js($sidebar_width),
        collapsedWidth: @js($sidebar_collapsed_width),
        activeBgColor: @js($sidebar_active_bg_color),
        activeBorderColor: @js($sidebar_active_border_color),
        activeBorderRadius: @js($sidebar_active_border_radius),
        savedFontSize: @js($sidebar_font_size),
        savedIconSize: @js($sidebar_icon_size),
        savedWidth: @js($sidebar_width),
        savedCollapsedWidth: @js($sidebar_collapsed_width),
        savedActiveBgColor: @js($sidebar_active_bg_color),
        savedActiveBorderColor: @js($sidebar_active_border_color),
        savedActiveBorderRadius: @js($sidebar_active_border_radius),
        dirty: false,
        saved: false,

        get isDirty() {
            return this.fontSize !== this.savedFontSize
                || this.iconSize !== this.savedIconSize
                || this.width !== this.savedWidth
                || this.collapsedWidth !== this.savedCollapsedWidth
                || this.activeBgColor !== this.savedActiveBgColor
                || this.activeBorderColor !== this.savedActiveBorderColor
                || this.activeBorderRadius !== this.savedActiveBorderRadius;
        },

        preview() {
            this.dirty = this.isDirty;
            this.saved = false;
            window.dispatchEvent(new CustomEvent('sidebar-preview', {
                detail: { fontSize: this.fontSize, iconSize: this.iconSize, width: this.width, collapsedWidth: this.collapsedWidth, activeBgColor: this.activeBgColor, activeBorderColor: this.activeBorderColor, activeBorderRadius: this.activeBorderRadius }
            }));
        },

        revert() {
            this.fontSize = this.savedFontSize;
            this.iconSize = this.savedIconSize;
            this.width = this.savedWidth;
            this.collapsedWidth = this.savedCollapsedWidth;
            this.activeBgColor = this.savedActiveBgColor;
            this.activeBorderColor = this.savedActiveBorderColor;
            this.activeBorderRadius = this.savedActiveBorderRadius;
            this.dirty = false;
            this.saved = false;
            window.dispatchEvent(new CustomEvent('sidebar-preview', {
                detail: { fontSize: this.fontSize, iconSize: this.iconSize, width: this.width, collapsedWidth: this.collapsedWidth, activeBgColor: this.activeBgColor, activeBorderColor: this.activeBorderColor, activeBorderRadius: this.activeBorderRadius }
            }));
        },

        async save() {
            await $wire.call('save', this.fontSize, this.iconSize, this.width, this.collapsedWidth, this.activeBgColor, this.activeBorderColor, this.activeBorderRadius);
            this.savedFontSize = this.fontSize;
            this.savedIconSize = this.iconSize;
            this.savedWidth = this.width;
            this.savedCollapsedWidth = this.collapsedWidth;
            this.savedActiveBgColor = this.activeBgColor;
            this.savedActiveBorderColor = this.activeBorderColor;
            this.savedActiveBorderRadius = this.activeBorderRadius;
            this.dirty = false;
            this.saved = true;
            setTimeout(() => this.saved = false, 3000);
        },

        async resetDefaults() {
            this.fontSize = 15;
            this.iconSize = 20;
            this.width = 360;
            this.collapsedWidth = 72;
            this.activeBgColor = 'rgba(255,255,255,0.15)';
            this.activeBorderColor = 'rgba(255,255,255,0.2)';
            this.activeBorderRadius = 12;
            this.preview();
            await this.save();
        }
    }"
    x-init="
        window.addEventListener('livewire:navigating', () => { if (dirty) revert(); });
        window.addEventListener('beforeunload', () => {
            if (dirty) {
                window.dispatchEvent(new CustomEvent('sidebar-preview', {
                    detail: { fontSize: savedFontSize, iconSize: savedIconSize, width: savedWidth, collapsedWidth: savedCollapsedWidth, activeBgColor: savedActiveBgColor, activeBorderColor: savedActiveBorderColor, activeBorderRadius: savedActiveBorderRadius }
                }));
            }
        });
    "
    class="glass-card p-6 rounded-2xl">

    <h3 class="text-lg font-semibold mb-2 flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12" />
        </svg>
        {{ __('Sidebar Appearance') }}
    </h3>

    <p class="text-sm opacity-60 mb-6">{{ __('Adjust the sliders to preview changes on the actual sidebar in real time. Click') }} <strong>{{ __('Save') }}</strong> to keep them.</p>

    <div class="mb-6 p-3 rounded-xl border border-white/10 bg-white/5 flex flex-wrap gap-2 items-center">
        <span class="text-sm opacity-70">Advanced:</span>
        <a href="{{ route('admin.settings.icons') }}" class="px-3 py-1.5 rounded-lg border border-white/20 hover:bg-white/10 text-sm transition">Icon Manager</a>
        <a href="{{ route('admin.settings.nav-labels') }}" class="px-3 py-1.5 rounded-lg border border-white/20 hover:bg-white/10 text-sm transition">Navigation Labels</a>
    </div>

    {{-- Saved toast --}}
    <div x-show="saved" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="mb-4 p-3 rounded-xl bg-emerald-500/20 border border-emerald-500/30 text-emerald-300 text-sm flex items-center gap-2" style="display:none;">
        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
        {{ __('Sidebar settings saved!') }}
    </div>

    {{-- Unsaved indicator --}}
    <div x-show="dirty" x-transition class="mb-4 p-3 rounded-xl bg-amber-500/15 border border-amber-500/25 text-amber-300 text-sm flex items-center gap-2" style="display:none;">
        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" /></svg>
        {{ __('You have unsaved changes — the sidebar is previewing your adjustments.') }}
    </div>

    {{-- Live {{ __('Preview') }} Mini --}}
    <div class="mb-8 p-4 rounded-xl border border-white/10 bg-white/5">
        <h4 class="text-sm font-semibold uppercase tracking-wider opacity-70 mb-4">Preview</h4>
        <div class="rounded-xl overflow-hidden border border-white/10 bg-white/[0.03]" :style="`max-width: ${width}px`">
            <div class="flex items-center gap-3 p-3"
                 :style="`font-size: ${fontSize}px; background: ${activeBgColor}; border: 1px solid ${activeBorderColor}; border-radius: ${activeBorderRadius}px`">
                <div class="rounded-lg bg-blue-500/20 text-blue-400 flex items-center justify-center shrink-0"
                     :style="`width: ${iconSize + 8}px; height: ${iconSize + 8}px`">
                    <svg :style="`width: ${iconSize}px; height: ${iconSize}px`" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                </div>
                <span class="font-medium">{{ __('Dashboard') }}</span>
            </div>
            <div class="flex items-center gap-3 p-3 rounded-xl mt-0.5 opacity-60"
                 :style="`font-size: ${fontSize}px`">
                <div class="rounded-lg text-white/50 flex items-center justify-center shrink-0"
                     :style="`width: ${iconSize + 8}px; height: ${iconSize + 8}px`">
                    <svg :style="`width: ${iconSize}px; height: ${iconSize}px`" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                    </svg>
                </div>
                <span class="font-medium">{{ __('Profile') }}</span>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        {{-- Sidebar Width (Expanded) --}}
        <div>
            <label class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium">{{ __('Expanded Width') }}</span>
                <span class="text-xs opacity-50 tabular-nums" x-text="`${width}px`"></span>
            </label>
            <input type="range" x-model.number="width" @input="preview()" min="280" max="480" step="10"
                   class="w-full h-2 rounded-full appearance-none cursor-pointer bg-white/10 accent-blue-500">
            <div class="flex justify-between text-[10px] opacity-30 mt-1">
                <span>280px</span>
                <span>480px</span>
            </div>
        </div>

        {{-- Sidebar Width (Collapsed) --}}
        <div>
            <label class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium">{{ __('Collapsed Width') }}</span>
                <span class="text-xs opacity-50 tabular-nums" x-text="`${collapsedWidth}px`"></span>
            </label>
            <input type="range" x-model.number="collapsedWidth" @input="preview()" min="56" max="120" step="2"
                   class="w-full h-2 rounded-full appearance-none cursor-pointer bg-white/10 accent-blue-500">
            <div class="flex justify-between text-[10px] opacity-30 mt-1">
                <span>56px</span>
                <span>120px</span>
            </div>
        </div>

        {{-- {{ __('Font Size') }} --}}
        <div>
            <label class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium">Font Size</span>
                <span class="text-xs opacity-50 tabular-nums" x-text="`${fontSize}px`"></span>
            </label>
            <input type="range" x-model.number="fontSize" @input="preview()" min="11" max="22" step="1"
                   class="w-full h-2 rounded-full appearance-none cursor-pointer bg-white/10 accent-blue-500">
            <div class="flex justify-between text-[10px] opacity-30 mt-1">
                <span>11px</span>
                <span>22px</span>
            </div>
        </div>

        {{-- {{ __('Icon Size') }} --}}
        <div>
            <label class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium">Icon Size</span>
                <span class="text-xs opacity-50 tabular-nums" x-text="`${iconSize}px`"></span>
            </label>
            <input type="range" x-model.number="iconSize" @input="preview()" min="14" max="32" step="1"
                   class="w-full h-2 rounded-full appearance-none cursor-pointer bg-white/10 accent-blue-500">
            <div class="flex justify-between text-[10px] opacity-30 mt-1">
                <span>14px</span>
                <span>32px</span>
            </div>
        </div>

        {{-- Active Item Section --}}
        <div class="pt-4 mt-4 border-t border-white/10">
            <h4 class="text-sm font-semibold uppercase tracking-wider opacity-70 mb-4 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.53 16.122a3 3 0 0 0-5.78 1.128 2.25 2.25 0 0 1-2.4 2.245 4.5 4.5 0 0 0 8.4-2.245c0-.399-.078-.78-.22-1.128Zm0 0a15.998 15.998 0 0 0 3.388-1.62m-5.043-.025a15.994 15.994 0 0 1 1.622-3.39m3.421 3.421a15.995 15.995 0 0 0 4.764-4.648l3.876-5.814a1.151 1.151 0 0 0-1.597-1.597L14.146 6.32a15.996 15.996 0 0 0-4.649 4.763m3.42 3.42a6.776 6.776 0 0 0-3.42-3.42" />
                </svg>
                {{ __('Active Item Style') }}
            </h4>
        </div>

        {{-- Active Background Color --}}
        <div>
            <label class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium">{{ __('Background Color') }}</span>
            </label>
            <div class="flex items-center gap-3">
                <div class="relative">
                    <input type="color" x-model="activeBgColor" @input="preview()"
                           class="w-12 h-12 rounded-xl cursor-pointer bg-transparent border border-white/20 hover:border-white/40 transition-all"
                           :value="activeBgColor.startsWith('rgba') ? '#ffffff' : activeBgColor">
                    <div class="absolute inset-0 rounded-xl pointer-events-none" :style="`background: ${activeBgColor}`"></div>
                </div>
                <input type="text" x-model="activeBgColor" @input="preview()"
                       class="flex-1 px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-sm focus:border-blue-500/50 focus:outline-none transition-colors"
                       placeholder="rgba(255,255,255,0.15)">
            </div>
            <p class="text-[11px] opacity-40 mt-1">{{ __('Use rgba() for transparency, e.g. rgba(255,255,255,0.15)') }}</p>
        </div>

        {{-- Active Border Color --}}
        <div>
            <label class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium">{{ __('Border Color') }}</span>
            </label>
            <div class="flex items-center gap-3">
                <div class="relative">
                    <input type="color" x-model="activeBorderColor" @input="preview()"
                           class="w-12 h-12 rounded-xl cursor-pointer bg-transparent border border-white/20 hover:border-white/40 transition-all"
                           :value="activeBorderColor.startsWith('rgba') ? '#ffffff' : activeBorderColor">
                    <div class="absolute inset-0 rounded-xl pointer-events-none border-2" :style="`border-color: ${activeBorderColor}`"></div>
                </div>
                <input type="text" x-model="activeBorderColor" @input="preview()"
                       class="flex-1 px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-sm focus:border-blue-500/50 focus:outline-none transition-colors"
                       placeholder="rgba(255,255,255,0.2)">
            </div>
            <p class="text-[11px] opacity-40 mt-1">{{ __('Use rgba() for transparency, e.g. rgba(255,255,255,0.2)') }}</p>
        </div>

        {{-- Active Border Radius --}}
        <div>
            <label class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium">{{ __('Border Radius') }}</span>
                <span class="text-xs opacity-50 tabular-nums" x-text="`${activeBorderRadius}px`"></span>
            </label>
            <input type="range" x-model.number="activeBorderRadius" @input="preview()" min="0" max="24" step="1"
                   class="w-full h-2 rounded-full appearance-none cursor-pointer bg-white/10 accent-blue-500">
            <div class="flex justify-between text-[10px] opacity-30 mt-1">
                <span>0px</span>
                <span>24px</span>
            </div>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="flex items-center gap-3 mt-8">
        <button @click="save()" :disabled="!dirty"
                class="px-5 py-2.5 rounded-xl bg-blue-500/80 hover:bg-blue-500 text-white font-medium text-sm transition-all duration-200 flex items-center gap-2 disabled:opacity-40 disabled:cursor-not-allowed">
            {{ __('Save Changes') }}
        </button>

        <button @click="revert()" x-show="dirty"
                class="px-5 py-2.5 rounded-xl border border-amber-500/30 bg-amber-500/10 hover:bg-amber-500/20 text-amber-300 text-sm font-medium transition-all duration-200">
            {{ __('Revert') }}
        </button>

        <button @click="resetDefaults()"
                class="px-5 py-2.5 rounded-xl border border-white/15 hover:bg-white/10 text-sm font-medium transition-all duration-200">
            {{ __('Reset to Defaults') }}
        </button>
    </div>
</div>
