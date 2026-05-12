{{-- Reusable background picker with instant live preview --}}
{{-- Required vars: $prefix (e.g. 'auth_bg'), $current_image, $current_video, $removeImageMethod, $removeVideoMethod --}}
@php
    $typeVar = $prefix . '_type';
    $imageUploadVar = $prefix . '_image_upload';
    $videoUploadVar = $prefix . '_video_upload';
    $isAppBg = ($prefix === 'app_bg');
    $currentImageProp = 'current_' . $prefix . '_image';
    $currentVideoProp = 'current_' . $prefix . '_video';
@endphp

<div x-data="{
    bgType: '{{ $this->$typeVar }}',
    localImageUrl: null,
    localVideoUrl: null,
    isAppBg: {{ $isAppBg ? 'true' : 'false' }},

    get savedImageUrl() {
        const path = $wire.{{ $currentImageProp }};
        return path ? '/storage/' + path : null;
    },
    get savedVideoUrl() {
        const path = $wire.{{ $currentVideoProp }};
        return path ? '/storage/' + path : null;
    },
    get effectiveImageUrl() {
        return this.localImageUrl || this.savedImageUrl;
    },
    get effectiveVideoUrl() {
        return this.localVideoUrl || this.savedVideoUrl;
    },

    handleImageSelect(e) {
        const file = e.target.files[0];
        if (!file) return;
        this.localImageUrl = URL.createObjectURL(file);
        if (this.isAppBg) {
            window.dispatchEvent(new CustomEvent('app-bg-preview', {
                detail: { type: 'image', url: this.localImageUrl }
            }));
        }
    },
    handleVideoSelect(e) {
        const file = e.target.files[0];
        if (!file) return;
        this.localVideoUrl = URL.createObjectURL(file);
        if (this.isAppBg) {
            window.dispatchEvent(new CustomEvent('app-bg-preview', {
                detail: { type: 'video', url: this.localVideoUrl }
            }));
        }
    },
    switchType(type) {
        this.bgType = type;
        if (this.isAppBg) {
            if (type === 'gradient') {
                window.dispatchEvent(new CustomEvent('app-bg-preview-clear'));
            } else if (type === 'image' && this.effectiveImageUrl) {
                window.dispatchEvent(new CustomEvent('app-bg-preview', {
                    detail: { type: 'image', url: this.effectiveImageUrl }
                }));
            } else if (type === 'video' && this.effectiveVideoUrl) {
                window.dispatchEvent(new CustomEvent('app-bg-preview', {
                    detail: { type: 'video', url: this.effectiveVideoUrl }
                }));
            }
        }
    }
}"
x-on:app-bg-preview-clear.window="localImageUrl = null; localVideoUrl = null">

    {{-- Type Toggle --}}
    <div class="flex gap-2 mb-4">
        @foreach (['gradient' => 'Theme Gradient', 'image' => 'Custom Image', 'video' => 'Custom Video'] as $type => $label)
            <button type="button"
                    wire:click="$set('{{ $typeVar }}', '{{ $type }}')"
                    @click="switchType('{{ $type }}')"
                    class="px-4 py-2 rounded-xl text-sm font-medium transition"
                    :class="bgType === '{{ $type }}' ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/25' : 'bg-white/10 border border-white/20 hover:bg-white/20'">
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- ===== Image Section ===== --}}
    <div x-show="bgType === 'image'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
         class="p-4 rounded-xl bg-white/5 border border-white/10 space-y-3" style="display:none;">

        {{-- Live Image Preview --}}
        <div x-show="effectiveImageUrl" x-transition class="relative overflow-hidden rounded-xl group">
            <img :src="effectiveImageUrl" class="w-full h-44 rounded-xl object-cover transition-transform duration-500 group-hover:scale-105" alt="Background preview" />
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent flex items-end p-3">
                <span class="text-xs text-white/80 font-medium bg-black/40 backdrop-blur-sm px-2.5 py-1 rounded-lg flex items-center gap-1.5">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                    {{ __('Live Preview') }}
                </span>
            </div>
            @if ($current_image)
                <button type="button" wire:click="{{ $removeImageMethod }}"
                        class="absolute top-2 right-2 px-3 py-1 rounded-lg bg-red-600/80 text-white text-xs hover:bg-red-500 transition-colors">Remove</button>
            @endif
        </div>

        <div class="relative">
            <input type="file" wire:model="{{ $imageUploadVar }}" accept="image/*"
                   @change="handleImageSelect($event)"
                   class="text-sm file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-blue-600 file:text-white file:font-medium file:cursor-pointer" />
            <div wire:loading wire:target="{{ $imageUploadVar }}" class="mt-2 text-xs text-blue-300 flex items-center gap-2">
                <svg class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                {{ __('Uploading to server...') }}
            </div>
        </div>

        <p class="text-xs opacity-40">{{ __('Recommended') }}: <strong>1920×1080px</strong> {{ __('JPEG or PNG, max 5MB') }}</p>

        @if ($isAppBg)
            <div class="p-2.5 rounded-lg bg-emerald-500/10 border border-emerald-500/20">
                <p class="text-[11px] text-emerald-300/80 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                    {{ __('Changes preview live on this page. Save to make permanent.') }}
                </p>
            </div>
        @endif
    </div>

    {{-- ===== Video Section ===== --}}
    <div x-show="bgType === 'video'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
         class="p-4 rounded-xl bg-white/5 border border-white/10 space-y-3" style="display:none;">

        {{-- Live Video Preview --}}
        <div x-show="effectiveVideoUrl" x-transition class="relative overflow-hidden rounded-xl">
            <video x-ref="bgVideoPreview" muted autoplay loop playsinline
                   class="w-full h-44 rounded-xl object-cover"
                   x-effect="if (effectiveVideoUrl && $refs.bgVideoPreview) { $refs.bgVideoPreview.src = effectiveVideoUrl; $refs.bgVideoPreview.load(); $refs.bgVideoPreview.play().catch(()=>{}); }">
            </video>
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent flex items-end p-3 pointer-events-none">
                <span class="text-xs text-white/80 font-medium bg-black/40 backdrop-blur-sm px-2.5 py-1 rounded-lg flex items-center gap-1.5">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z" /></svg>
                    Live Preview
                </span>
            </div>
            @if ($current_video)
                <button type="button" wire:click="{{ $removeVideoMethod }}"
                        class="absolute top-2 right-2 px-3 py-1 rounded-lg bg-red-600/80 text-white text-xs hover:bg-red-500 transition-colors">Remove</button>
            @endif
        </div>

        <div class="relative">
            <input type="file" wire:model="{{ $videoUploadVar }}" accept="video/mp4,video/webm"
                   @change="handleVideoSelect($event)"
                   class="text-sm file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-blue-600 file:text-white file:font-medium file:cursor-pointer" />
            <div wire:loading wire:target="{{ $videoUploadVar }}" class="mt-2 text-xs text-blue-300 flex items-center gap-2">
                <svg class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                Uploading to server...
            </div>
        </div>

        <div class="p-3 rounded-lg bg-blue-500/10 border border-blue-500/20">
            <p class="text-xs opacity-70 leading-relaxed">
                <strong class="text-blue-300">{{ __('Video tips') }}:</strong>
                1920×1080 MP4/WebM, 10–30 sec loop, under 20MB. Subtle slow-motion works best with glass morphism.
            </p>
        </div>

        @if ($isAppBg)
            <div class="p-2.5 rounded-lg bg-emerald-500/10 border border-emerald-500/20">
                <p class="text-[11px] text-emerald-300/80 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                    Changes preview live on this page. Save to make permanent.
                </p>
            </div>
        @endif
    </div>

    {{-- ===== Gradient Section ===== --}}
    <div x-show="bgType === 'gradient'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
         class="p-4 rounded-xl bg-white/5 border border-white/10">
        <p class="text-sm opacity-50 flex items-center gap-2">
            <svg class="w-4 h-4 opacity-60" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.53 16.122a3 3 0 0 0-5.78 1.128 2.25 2.25 0 0 1-2.4 2.245 4.5 4.5 0 0 0 8.4-2.245c0-.399-.078-.78-.22-1.128Zm0 0a15.998 15.998 0 0 0 3.388-1.62m-5.043-.025a15.994 15.994 0 0 1 1.622-3.395m3.42 3.42a15.995 15.995 0 0 0 4.764-4.648l3.876-5.814a1.151 1.151 0 0 0-1.597-1.597L14.146 6.32a15.996 15.996 0 0 0-4.649 4.763m3.42 3.42a6.776 6.776 0 0 0-3.42-3.42" /></svg>
            {{ __('Will use the active theme\'s gradient background with decorative blobs.') }}
        </p>
    </div>
</div>
