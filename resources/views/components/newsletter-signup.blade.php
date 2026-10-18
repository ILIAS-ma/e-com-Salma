<section class="border-t border-line bg-sand py-16">
    <div class="mx-auto max-w-lg px-4 text-center sm:px-6" x-data="{ sent: false }">
        <p class="text-xs uppercase tracking-widest2 text-stone">Newsletter</p>
        <h2 class="mt-4 text-xl text-ink">Recevez nos nouveautés en avant-première</h2>
        <p class="mt-2 text-base text-stone">Une sélection, pas de spam. Vous pouvez vous désinscrire à tout moment.</p>

        <form class="mt-6 flex gap-2" @submit.prevent="sent = true" x-show="!sent">
            <input type="email" required placeholder="Votre adresse email" class="w-full rounded-none border-line bg-paper text-base">
            <button type="submit" class="shrink-0 border border-ink px-5 text-xs uppercase tracking-widest2 hover:bg-ink hover:text-paper">
                S'inscrire
            </button>
        </form>
        <p x-show="sent" x-cloak class="mt-6 text-base text-ink">Merci, votre inscription est confirmée.</p>
    </div>
</section>
