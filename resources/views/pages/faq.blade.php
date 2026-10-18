<x-shop-layout title="FAQ">
    <div class="mx-auto max-w-2xl px-4 py-16 sm:px-6">
        <p class="text-[11px] uppercase tracking-widest2 text-stone">Aide</p>
        <h1 class="mt-4 section-title">Questions fréquentes</h1>

        <div class="mt-10 space-y-10">
            @foreach ($faqs as $section => $questions)
                <div>
                    <h2 class="text-[11px] uppercase tracking-widest2 text-stone">{{ $section }}</h2>
                    <div class="mt-4 divide-y divide-line border-y border-line">
                        @foreach ($questions as $question => $answer)
                            <details class="group faq-item scroll-mt-28 py-4">
                                <summary class="flex cursor-pointer list-none items-center justify-between text-sm">
                                    {{ $question }}
                                    <span class="text-stone group-open:hidden">+</span>
                                    <span class="hidden text-stone group-open:inline">−</span>
                                </summary>
                                <p class="faq-answer mt-3 text-xs leading-relaxed text-stone">{{ $answer }}</p>
                            </details>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <p class="mt-12 text-sm text-stone">
            Une autre question ? <a href="{{ route('orders.track') }}" class="text-ink underline">Suivez votre commande</a>
            ou contactez-nous via nos réseaux sociaux.
        </p>
    </div>
</x-shop-layout>
