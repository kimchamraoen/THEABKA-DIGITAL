@props(['submit'])

<div {{ $attributes->merge(['class' => 'md:grid md:grid-cols-3 md:gap-6']) }}>
    <x-section-title>
        <x-slot name="title">{{ $title }}</x-slot>
        <x-slot name="description">{{ $description }}</x-slot>
    </x-section-title>

    <div class="mt-5 md:mt-0 md:col-span-2">
        <form wire:submit="{{ $submit }}">
            <div class="px-4 py-5 glass-card sm:p-6 {{ isset($actions) ? 'sm:rounded-tl-2xl sm:rounded-tr-2xl' : 'sm:rounded-2xl' }}">
                <div class="grid grid-cols-6 gap-6">
                    {{ $form }}
                </div>
            </div>

            @if (isset($actions))
                <div class="flex items-center justify-end px-4 py-3 bg-white/5 text-end sm:px-6 glass-card sm:rounded-bl-2xl sm:rounded-br-2xl border-t border-white/10">
                    {{ $actions }}
                </div>
            @endif
        </form>
    </div>
</div>
