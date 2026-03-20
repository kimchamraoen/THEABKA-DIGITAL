@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'bg-white/10 backdrop-blur-xl border border-white/20 focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 rounded-xl shadow-sm text-inherit placeholder-gray-400 transition-all duration-200 outline-none px-4 py-2.5']) !!}>
