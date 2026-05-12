<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-widest hover:from-blue-500 hover:to-indigo-500 focus:outline-none focus:ring-2 focus:ring-blue-400/50 disabled:opacity-50 transition-all duration-200 shadow-lg shadow-blue-600/25 hover:shadow-blue-500/40']) }}>
    {{ $slot }}
</button>
