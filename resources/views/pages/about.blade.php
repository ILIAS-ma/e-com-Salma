<x-shop-layout title="Qui sommes-nous">
    <div class="mx-auto max-w-2xl px-4 py-16 sm:px-6">
        <p class="text-[11px] uppercase tracking-widest2 text-stone">Qui sommes-nous</p>
        <h1 class="mt-4 section-title">Une garde-robe pensée depuis le Maroc</h1>

        <div class="mt-8 space-y-6 text-sm leading-relaxed text-stone">
            <p>
                {{ config('app.name') }} est née d'une idée simple : proposer des vêtements en matières nobles
                et des bijoux fins, pensés pour durer, à des femmes qui n'ont pas envie de suivre chaque tendance.
            </p>
            <p>
                Chaque pièce est sélectionnée avec soin — coupe, matière, finition — avant de rejoindre notre
                collection. Nous travaillons avec des ateliers marocains pour proposer une confection de qualité,
                livrée directement chez vous partout au Maroc.
            </p>
            <p>
                Pas de sur-stock, pas de collections jetables : nous préférons peu de pièces, bien choisies,
                que vous garderez longtemps.
            </p>
        </div>

        <div class="mt-12 grid grid-cols-3 gap-6 border-t border-line pt-8 text-center">
            <div>
                <p class="text-lg">25+</p>
                <p class="mt-1 text-[11px] uppercase tracking-widest2 text-stone">Villes livrées</p>
            </div>
            <div>
                <p class="text-lg">100%</p>
                <p class="mt-1 text-[11px] uppercase tracking-widest2 text-stone">Confection soignée</p>
            </div>
            <div>
                <p class="text-lg">14j</p>
                <p class="mt-1 text-[11px] uppercase tracking-widest2 text-stone">Retours acceptés</p>
            </div>
        </div>

        <a href="{{ route('shop.index') }}" class="mt-12 inline-block border-b border-ink pb-0.5 text-[11px] uppercase tracking-widest2 hover:opacity-60">
            Découvrir la collection
        </a>
    </div>
</x-shop-layout>
