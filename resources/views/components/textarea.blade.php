@props(['disabled' => false])

<textarea @disabled($disabled) {{ $attributes->merge(['class' => 'w-full rounded-2xl border-0 neu-inset-sm px-4 py-2.5 text-sm text-slate-700 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-400 disabled:cursor-not-allowed disabled:opacity-50']) }}>{{ $slot }}</textarea>
