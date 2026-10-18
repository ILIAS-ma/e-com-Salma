<button {{ $attributes->merge(['type' => 'submit', 'class' => 'w-full bg-ink py-3 text-[11px] uppercase tracking-widest2 text-paper transition hover:opacity-90']) }}>
    {{ $slot }}
</button>
