<button {{ $attributes->merge(['type' => 'submit', 'class' => 'neu-pressable inline-flex items-center justify-center gap-2 rounded-2xl bg-red-600 px-5 py-2.5 text-sm font-semibold text-white shadow-neu-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-2 focus:ring-offset-surface disabled:cursor-not-allowed disabled:opacity-50 transition-colors']) }}>
    {{ $slot }}
</button>
