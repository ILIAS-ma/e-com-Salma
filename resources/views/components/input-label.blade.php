@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-[11px] uppercase tracking-widest2 text-stone']) }}>
    {{ $value ?? $slot }}
</label>
