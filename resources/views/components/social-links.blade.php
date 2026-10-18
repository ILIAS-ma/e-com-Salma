@props(['class' => 'flex items-center gap-8'])

<div {{ $attributes->merge(['class' => $class.' text-[11px] uppercase tracking-widest2']) }}>
    <a href="#" class="link-underline">Instagram</a>
    <a href="#" class="link-underline">TikTok</a>
    <a href="#" class="link-underline">Facebook</a>
</div>
