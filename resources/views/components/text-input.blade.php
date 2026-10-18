@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'w-full rounded-none border-line text-sm focus:border-ink focus:ring-0']) }}>
