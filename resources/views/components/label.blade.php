@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm opacity-80']) }}>
    {{ $value ?? $slot }}
</label>
