<x-guest-layout>
    <h1 class="text-center section-title">Mot de passe oublié</h1>

    <p class="mt-6 text-center text-xs leading-relaxed text-stone">
        Indiquez votre adresse email et nous vous enverrons un lien pour choisir un nouveau mot de passe.
    </p>

    <x-auth-session-status class="mt-6" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="mt-8 space-y-5">
        @csrf

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="mt-1" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <x-primary-button>Envoyer le lien de réinitialisation</x-primary-button>

        <div class="text-center text-[11px] uppercase tracking-widest2 text-stone">
            <a href="{{ route('login') }}" class="hover:text-ink">Retour à la connexion</a>
        </div>
    </form>
</x-guest-layout>
