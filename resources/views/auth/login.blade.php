<x-guest-layout max-width="4xl">
    <div class="grid grid-cols-1 gap-10 lg:grid-cols-12 lg:gap-14">
        <!-- Connexion Form (Left Column) -->
        <div class="lg:col-span-7">
            <div class="mb-6">
                <span class="text-[10px] font-medium uppercase tracking-widest3 text-stone">Espace personnel</span>
                <h1 class="mt-2 section-title">Connexion</h1>
                <p class="mt-2 text-xs leading-relaxed text-stone">
                    Identifiez-vous pour accéder à vos commandes, vos adresses et vos pièces coup de cœur.
                </p>
            </div>

            <x-auth-session-status class="mb-6" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-5" x-data="{ showPassword: false }">
                @csrf

                <!-- Email -->
                <div>
                    <label for="email" class="block text-[11px] uppercase tracking-widest2 text-stone">
                        Adresse email <span class="text-ink">*</span>
                    </label>
                    <div class="relative mt-1.5">
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="nom@exemple.com"
                            class="w-full rounded-none border border-line bg-paper px-3.5 py-3 text-sm text-ink placeholder-stone/40 transition-colors focus:border-brand focus:outline-none focus:ring-1 focus:ring-brand/30"
                        />
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
                </div>

                <!-- Password with Show/Hide toggle -->
                <div>
                    <div class="flex items-center justify-between">
                        <label for="password" class="block text-[11px] uppercase tracking-widest2 text-stone">
                            Mot de passe <span class="text-ink">*</span>
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-[10px] uppercase tracking-widest2 text-stone hover:text-ink hover:underline transition-colors">
                                Mot de passe oublié ?
                            </a>
                        @endif
                    </div>
                    <div class="relative mt-1.5">
                        <input
                            id="password"
                            :type="showPassword ? 'text' : 'password'"
                            name="password"
                            required
                            autocomplete="current-password"
                            placeholder="••••••••"
                            class="w-full rounded-none border border-line bg-paper px-3.5 py-3 pr-10 text-sm text-ink placeholder-stone/40 transition-colors focus:border-brand focus:outline-none focus:ring-1 focus:ring-brand/30"
                        />
                        <button
                            type="button"
                            @click="showPassword = !showPassword"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-stone hover:text-ink transition-colors p-1"
                            tabindex="-1"
                            aria-label="Afficher ou masquer le mot de passe"
                        >
                            <!-- Eye icon (when hidden) -->
                            <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <!-- Eye slash icon (when visible) -->
                            <svg x-show="showPassword" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
                </div>

                <!-- Remember Me -->
                <div class="flex items-center">
                    <label class="group flex cursor-pointer items-center gap-2.5 text-xs text-stone hover:text-ink transition-colors">
                        <input
                            id="remember_me"
                            type="checkbox"
                            name="remember"
                            class="h-4 w-4 rounded-none border-line text-ink focus:ring-0 focus:ring-offset-0 cursor-pointer"
                        />
                        <span>Se souvenir de moi sur cet appareil</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button
                    type="submit"
                    class="group btn-primary w-full py-4"
                >
                    <span>Se connecter</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </button>

                <!-- Reassurance mini-bar -->
                <div class="mt-6 border-t border-line/60 pt-4">
                    <div class="grid grid-cols-2 gap-3 text-[11px] text-stone sm:grid-cols-3">
                        <div class="flex items-center gap-1.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-ink shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                            </svg>
                            <span>Connexion sécurisée</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-ink shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.25V3.75m0 3.75a1.5 1.5 0 013 0m-3 0a1.5 1.5 0 003 0" />
                            </svg>
                            <span>Suivi en direct</span>
                        </div>
                        <div class="col-span-2 flex items-center gap-1.5 sm:col-span-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-ink shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                            </svg>
                            <span>Données protégées</span>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Nouveau Client (Right Column Card) -->
        <div class="flex flex-col justify-between bg-sand p-6 sm:p-8 lg:col-span-5">
            <div>
                <span class="text-[10px] font-medium uppercase tracking-widest3 text-stone">Nouveau client</span>
                <h2 class="mt-2 section-title">Créer un compte</h2>
                <p class="mt-2 text-xs leading-relaxed text-stone">
                    Rejoignez la maison {{ config('app.name') }} pour bénéficier d'une expérience d'achat privilégiée.
                </p>

                <ul class="mt-6 space-y-3.5 border-t border-line/60 pt-6 text-xs text-ink/90">
                    <li class="flex items-start gap-2.5">
                        <span class="mt-0.5 flex h-4 w-4 shrink-0 items-center justify-center rounded-full bg-ink/10 text-ink">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-2.5 w-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                        </span>
                        <span><strong class="font-normal text-ink">Suivi des commandes</strong> et téléchargement de vos factures en un clic.</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="mt-0.5 flex h-4 w-4 shrink-0 items-center justify-center rounded-full bg-ink/10 text-ink">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-2.5 w-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                        </span>
                        <span><strong class="font-normal text-ink">Paiement accéléré</strong> grâce à la sauvegarde de vos adresses.</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="mt-0.5 flex h-4 w-4 shrink-0 items-center justify-center rounded-full bg-ink/10 text-ink">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-2.5 w-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                        </span>
                        <span><strong class="font-normal text-ink">Invitations exclusives</strong> à nos lancements et ventes privées.</span>
                    </li>
                </ul>
            </div>

            <div class="mt-8 border-t border-line/60 pt-6">
                <a
                    href="{{ route('register') }}"
                    class="btn-outline w-full"
                >
                    Créer mon compte
                </a>
                <p class="mt-3 text-center text-[10px] text-stone">
                    Inscription rapide et sans engagement en moins d'une minute.
                </p>
            </div>
        </div>
    </div>
</x-guest-layout>
