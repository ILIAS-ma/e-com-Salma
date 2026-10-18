<x-guest-layout>
    <h1 class="text-center section-title">Vérifiez votre email</h1>

    <p class="mt-6 text-center text-xs leading-relaxed text-stone">
        Merci de votre inscription ! Avant de continuer, veuillez confirmer votre adresse email
        en cliquant sur le lien que nous venons de vous envoyer. Si vous ne l'avez pas reçu,
        nous pouvons vous en renvoyer un.
    </p>

    @if (session('status') == 'verification-link-sent')
        <div class="mt-6 rounded-none bg-sand px-4 py-3 text-center text-xs text-ink">
            Un nouveau lien de vérification a été envoyé à l'adresse email indiquée lors de l'inscription.
        </div>
    @endif

    <div class="mt-8 space-y-3">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-primary-button>Renvoyer l'email de vérification</x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full text-center text-[11px] uppercase tracking-widest2 text-stone hover:text-ink">
                Se déconnecter
            </button>
        </form>
    </div>
</x-guest-layout>
