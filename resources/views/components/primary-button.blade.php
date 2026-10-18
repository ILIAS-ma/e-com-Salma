<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn-primary w-full py-3.5']) }}>
    {{ $slot }}
</button>
