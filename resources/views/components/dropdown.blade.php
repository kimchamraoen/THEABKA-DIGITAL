@props(['align' => 'right', 'width' => '48', 'contentClasses' => 'py-1 bg-gray-900/95 backdrop-blur-xl', 'dropdownClasses' => ''])

@php
$alignmentClasses = match ($align) {
    'left' => 'ltr:origin-top-left rtl:origin-top-right start-0',
    'top' => 'origin-top',
    'none', 'false' => '',
    default => 'ltr:origin-top-right rtl:origin-top-left end-0',
};

$width = match ($width) {
    '48' => 'w-48',
    '60' => 'w-60',
    default => 'w-48',
};
@endphp

<div class="relative" x-data="{ open: false }" x-on:click.outside="open = false" x-on:keydown.escape.window="open = false" @close.stop="open = false">
    <div @click="open = ! open">
        {{ $trigger }}
    </div>

    <div x-cloak x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="transform opacity-0 scale-95"
            x-transition:enter-end="transform opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="transform opacity-100 scale-100"
            x-transition:leave-end="transform opacity-0 scale-95"
            class="absolute z-[70] mt-2 {{ $width }} rounded-xl shadow-2xl {{ $alignmentClasses }} {{ $dropdownClasses }}"
            style="display: none;">
        <div class="rounded-xl ring-1 ring-white/20 {{ $contentClasses }}">
            {{ $content }}
        </div>
    </div>
</div>
