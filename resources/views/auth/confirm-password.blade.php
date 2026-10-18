<x-guest-layout>
    <h1 class="text-center text-sm uppercase tracking-widest2">Confirmer le mot de passe</h1>

    <p class="mt-6 text-center text-xs leading-relaxed text-stone">
        Ceci est une zone sécurisée. Merci de confirmer votre mot de passe avant de continuer.
    </p>

    <form method="POST" action="{{ route('password.confirm') }}" class="mt-8 space-y-5">
        @csrf

        <div>
            <x-input-label for="password" value="Mot de passe" />
            <x-text-input id="password" class="mt-1" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <x-primary-button>Confirmer</x-primary-button>
    </form>
</x-guest-layout>
