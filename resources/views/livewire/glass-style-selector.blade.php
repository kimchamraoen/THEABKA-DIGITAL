<div class="w-full" x-data>
    <div class="flex items-center gap-2 flex-wrap rounded-xl px-2 py-1.5 bg-white/5 border border-white/10">
        @foreach ($styles as $key => $label)
            <button
                type="button"
                wire:click="setStyle('{{ $key }}')"
                class="px-2.5 py-1.5 text-xs font-semibold rounded-lg transition-all duration-200 border"
                @class([
                    'bg-white/20 border-white/30 text-white shadow-sm' => $glassStyle === $key,
                    'bg-transparent border-transparent text-white/60 hover:text-white hover:bg-white/10' => $glassStyle !== $key,
                ])
                title="Use {{ $label }} glass style"
            >
                {{ $label }}
            </button>
        @endforeach
    </div>
</div>
