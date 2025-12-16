<x-app-layout>
    <div class="py-12">
        <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
            @if (session("info"))
                <div class="relative mb-6 rounded border border-blue-400 bg-blue-100 px-4 py-3 text-blue-700">
                    {{ session("info") }}
                </div>
            @endif

            @if ($client->google_id)
                <div class="mb-6 rounded-lg border border-blue-200 bg-blue-50 p-4">
                    <div class="flex items-start">
                        <x-bi-info-circle class="mt-0.5 mr-3 h-5 w-5 text-blue-600" />
                        <div>
                            <h3 class="text-sm font-medium text-blue-900">Compte créé avec Google</h3>
                            <p class="mt-1 text-sm text-blue-700">
                                Vous avez créé votre compte en utilisant votre compte Google. Veuillez vérifier les données saisies afin de
                                compléter votre profil (date de naissance et civilité).
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h2 class="mb-6 text-2xl font-bold text-gray-900">Modifier mon profil</h2>

                    <form method="POST" action="{{ route("dashboard.profile.update") }}">
                        @csrf
                        @method("PUT")

                        <div class="space-y-6">
                            <!-- Civilité -->
                            <div>
                                <label for="civilite" class="block text-sm font-medium text-gray-700">Civilité *</label>
                                <select
                                    name="civilite"
                                    id="civilite"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                >
                                    <option value="Monsieur" {{ old("civilite", $client->civilite) == "Monsieur" ? "selected" : "" }}>
                                        Monsieur
                                    </option>
                                    <option value="Madame" {{ old("civilite", $client->civilite) == "Madame" ? "selected" : "" }}>
                                        Madame
                                    </option>
                                </select>
                                @error("civilite")
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Prénom -->
                            <x-form-input
                                name="prenom_client"
                                id="prenom_client"
                                label="Prénom"
                                :value="$client->prenom_client"
                                required
                            />

                            <!-- Nom -->
                            <x-form-input name="nom_client" id="nom_client" label="Nom" :value="$client->nom_client" required />

                            <!-- Email -->
                            <x-form-input
                                type="email"
                                name="email_client"
                                id="email_client"
                                label="Email"
                                :value="$client->email_client"
                                required
                            />

                            <!-- Date de naissance -->
                            <x-form-input
                                type="date"
                                name="naissance_client"
                                id="naissance_client"
                                label="Date de naissance"
                                :value="$client->naissance_client ? $client->naissance_client->format('Y-m-d') : ''"
                            />

                            <div class="flex items-center justify-between pt-4">
                                <a
                                    href="{{ route("dashboard.profile.show") }}"
                                    class="text-sm font-medium text-indigo-600 hover:text-indigo-800"
                                >
                                    &larr; Annuler
                                </a>
                                <button
                                    type="submit"
                                    class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition hover:bg-indigo-500 focus:bg-indigo-700"
                                >
                                    Enregistrer les modifications
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
