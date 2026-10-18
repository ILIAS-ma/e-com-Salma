@props(['class' => 'flex items-center gap-4 text-ink'])

<div {{ $attributes->merge(['class' => $class]) }}>
    <a href="#" aria-label="Instagram" class="opacity-70 hover:opacity-100">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.3">
            <rect x="3" y="3" width="18" height="18" rx="5" />
            <circle cx="12" cy="12" r="4" />
            <circle cx="17.2" cy="6.8" r="0.6" fill="currentColor" stroke="none" />
        </svg>
    </a>
    <a href="#" aria-label="Facebook" class="opacity-70 hover:opacity-100">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.3">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14 9h2V6h-2c-1.657 0-3 1.343-3 3v2H9v3h2v6h3v-6h2.5l.5-3H14V9z" />
        </svg>
    </a>
    <a href="#" aria-label="TikTok" class="opacity-70 hover:opacity-100">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.3">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 3v10.5a3.5 3.5 0 11-3-3.46M15 3c.4 2.2 2 4 5 4" />
        </svg>
    </a>
</div>
