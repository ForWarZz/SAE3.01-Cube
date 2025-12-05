<x-app-layout>
    <div class="flex min-h-screen items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100 px-4 py-12">
        <div class="w-full max-w-2xl">
            <div class="rounded-2xl border border-gray-200 bg-white p-8 shadow-lg">
                <div class="mb-8 text-center">
                    <a href="{{ route("home") }}" class="mb-4 inline-block">
                        <h1 class="text-3xl font-bold tracking-tight text-gray-900 uppercase">Cube France</h1>
                    </a>
                    <h2 class="text-2xl font-semibold text-gray-800">Créer un compte</h2>
                    <p class="mt-2 text-sm text-gray-600">Rejoignez-nous pour profiter de tous nos services</p>
                </div>

                @if ($errors->any())
                    <div class="mb-6 rounded-lg border border-red-300 bg-red-50 p-4 text-sm text-red-700">
                        <strong>Veuillez corriger les erreurs ci-dessous.</strong>
                    </div>
                @endif

                <form method="POST" action="{{ route("register") }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="civilite" class="mb-2 block text-sm font-medium text-gray-700">Civilité</label>
                        <select
                            id="civilite"
                            name="civilite"
                            required
                            class="w-full rounded-lg border-gray-300 bg-gray-50 px-4 py-3 focus:ring-2 focus:ring-blue-200"
                        >
                            <option value="">Sélectionnez</option>
                            <option value="M" {{ old("civilite") === "M" ? "selected" : "" }}>Monsieur</option>
                            <option value="F" {{ old("civilite") === "F" ? "selected" : "" }}>Madame</option>
                        </select>
                        <x-input-error :messages="$errors->get('civilite')" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">Nom</label>
                            <input
                                type="text"
                                name="nom_client"
                                value="{{ old("nom_client") }}"
                                required
                                class="w-full rounded-lg border-gray-300 bg-gray-50 px-4 py-3 focus:ring-2 focus:ring-blue-200"
                            />
                            <x-input-error :messages="$errors->get('nom_client')" class="mt-2" />
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">Prénom</label>
                            <input
                                type="text"
                                name="prenom_client"
                                value="{{ old("prenom_client") }}"
                                required
                                class="w-full rounded-lg border-gray-300 bg-gray-50 px-4 py-3 focus:ring-2 focus:ring-blue-200"
                            />
                            <x-input-error :messages="$errors->get('prenom_client')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Email / Date de naissance -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">Adresse email</label>
                            <input
                                type="email"
                                name="email"
                                value="{{ old("email") }}"
                                required
                                class="w-full rounded-lg border-gray-300 bg-gray-50 px-4 py-3 focus:ring-2 focus:ring-blue-200"
                            />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">Date de naissance</label>
                            <input
                                type="date"
                                name="naissance_client"
                                value="{{ old("naissance_client") }}"
                                required
                                class="w-full rounded-lg border-gray-300 bg-gray-50 px-4 py-3 focus:ring-2 focus:ring-blue-200"
                            />
                            <x-input-error :messages="$errors->get('naissance_client')" class="mt-2" />
                        </div>
                    </div>

                    <!-- MDP / Confirmation -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">Mot de passe</label>
                            <input
                                type="password"
                                name="password"
                                required
                                class="w-full rounded-lg border-gray-300 bg-gray-50 px-4 py-3 focus:ring-2 focus:ring-blue-200"
                            />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">Confirmation</label>
                            <input
                                type="password"
                                name="password_confirmation"
                                required
                                class="w-full rounded-lg border-gray-300 bg-gray-50 px-4 py-3 focus:ring-2 focus:ring-blue-200"
                            />
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>
                    </div>

                    <button
                        type="submit"
                        class="w-full rounded-lg bg-blue-600 py-3 font-semibold text-white shadow-md hover:bg-blue-700 hover:shadow-lg"
                    >
                        Créer mon compte
                    </button>
                </form>
            </div>

            <p class="mt-6 text-center text-sm text-gray-600">
                Déjà un compte ?
                <a href="{{ route("login") }}" class="font-semibold text-blue-600 hover:text-blue-700">Se connecter</a>
            </p>
        </div>
    </div>
</x-app-layout>
