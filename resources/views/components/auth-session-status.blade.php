@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'border border-line bg-sand px-4 py-3 text-xs text-ink']) }}>
        {{ $status }}
    </div>
@endif
