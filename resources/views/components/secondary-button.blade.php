<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-xl border border-white/20 rounded-xl font-semibold text-xs uppercase tracking-widest shadow-sm hover:bg-white/20 focus:outline-none focus:ring-2 focus:ring-blue-400/50 disabled:opacity-25 transition-all duration-200']) }}>
    {{ $slot }}
</button>
