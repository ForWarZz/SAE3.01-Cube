<x-guest-layout>
    <form method="POST" action="{{ route("register") }}">
        @csrf

        <!-- Civilité -->
        <div>
            <x-input-label for="civilite" :value="__('Civilité')" />
            <select
                id="civilite"
                name="civilite"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600"
                required
            >
                <option value="">{{ __("Sélectionnez") }}</option>
                <option value="M" {{ old("civilite") == "M" ? "selected" : "" }}>{{ __("Monsieur") }}</option>
                <option value="F" {{ old("civilite") == "F" ? "selected" : "" }}>{{ __("Madame") }}</option>
            </select>
            <x-input-error :messages="$errors->get('civilite')" class="mt-2" />
        </div>

        <!-- Nom -->
        <div class="mt-4">
            <x-input-label for="nom_client" :value="__('Nom')" />
            <x-text-input
                id="nom_client"
                class="mt-1 block w-full"
                type="text"
                name="nom_client"
                :value="old('nom_client')"
                required
                autofocus
                autocomplete="family-name"
            />
            <x-input-error :messages="$errors->get('nom_client')" class="mt-2" />
        </div>

        <!-- Prénom -->
        <div class="mt-4">
            <x-input-label for="prenom_client" :value="__('Prénom')" />
            <x-text-input
                id="prenom_client"
                class="mt-1 block w-full"
                type="text"
                name="prenom_client"
                :value="old('prenom_client')"
                required
                autocomplete="given-name"
            />
            <x-input-error :messages="$errors->get('prenom_client')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Adresse électronique')" />
            <x-text-input
                id="email"
                class="mt-1 block w-full"
                type="email"
                name="email"
                :value="old('email')"
                required
                autocomplete="username"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Date de naissance -->
        <div class="mt-4">
            <x-input-label for="naissance_client" :value="__('Date de naissance')" />
            <x-text-input
                id="naissance_client"
                class="mt-1 block w-full"
                type="date"
                name="naissance_client"
                :value="old('naissance_client')"
                required
            />
            <x-input-error :messages="$errors->get('naissance_client')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Mot de passe')" />

            <x-text-input id="password" class="mt-1 block w-full" type="password" name="password" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirmer votre mot de passe')" />

            <x-text-input
                id="password_confirmation"
                class="mt-1 block w-full"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
            />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="mt-4 flex items-center justify-end">
            <a
                class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-none dark:text-gray-400 dark:hover:text-gray-100 dark:focus:ring-offset-gray-800"
                href="{{ route("login") }}"
            >
                {{ __("Déjà un compte ?") }}
            </a>

            <x-primary-button class="ms-4">
                {{ __("S'enregistrer") }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
