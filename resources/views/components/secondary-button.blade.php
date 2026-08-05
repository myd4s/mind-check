<button {{ $attributes->merge(['type' => 'button', 'class' => 'neu-pressable neu-raised-sm inline-flex items-center justify-center gap-2 rounded-2xl px-5 py-2.5 text-sm font-semibold text-slate-600 hover:text-primary-600 focus:outline-none focus:ring-2 focus:ring-primary-400 focus:ring-offset-2 focus:ring-offset-surface disabled:cursor-not-allowed disabled:opacity-40 transition-colors']) }}>
    {{ $slot }}
</button>
