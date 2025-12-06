<x-app-layout>
    <div class="py-12">
        <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h1 class="mb-6 text-2xl font-bold text-gray-900">Nouvelle adresse</h1>

                    <form method="POST" action="{{ route("dashboard.adresses.store") }}" id="address-form">
                        @csrf

                        <!-- Alias -->
                        <div class="mb-4">
                            <label for="alias_adresse" class="block text-sm font-medium text-gray-700">Alias (ex: Ma maison, Bureau)</label>
                            <input
                                type="text"
                                name="alias_adresse"
                                id="alias_adresse"
                                value="{{ old("alias_adresse") }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                placeholder="Ma maison"
                            />
                            @error("alias_adresse")
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Société (optionnel) -->
                        <div class="mb-4">
                            <label for="societe_adresse" class="block text-sm font-medium text-gray-700">Société (optionnel)</label>
                            <input
                                type="text"
                                name="societe_adresse"
                                id="societe_adresse"
                                value="{{ old("societe_adresse") }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                placeholder="Nom de l'entreprise"
                            />
                            @error("societe_adresse")
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Numéro TVA (optionnel) -->
                        <div class="mb-4">
                            <label for="tva_adresse" class="block text-sm font-medium text-gray-700">Numéro de TVA (optionnel)</label>
                            <input
                                type="text"
                                name="tva_adresse"
                                id="tva_adresse"
                                value="{{ old("tva_adresse") }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                placeholder="FR12345678901"
                            />
                            @error("tva_adresse")
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Prénom -->
                        <div class="mb-4">
                            <label for="prenom_adresse" class="block text-sm font-medium text-gray-700">
                                Prénom
                                <span class="text-red-600">*</span>
                            </label>
                            <input
                                type="text"
                                name="prenom_adresse"
                                id="prenom_adresse"
                                value="{{ old("prenom_adresse", $client->prenom_client) }}"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            />
                            @error("prenom_adresse")
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Nom -->
                        <div class="mb-4">
                            <label for="nom_adresse" class="block text-sm font-medium text-gray-700">
                                Nom
                                <span class="text-red-600">*</span>
                            </label>
                            <input
                                type="text"
                                name="nom_adresse"
                                id="nom_adresse"
                                value="{{ old("nom_adresse", $client->nom_client) }}"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            />
                            @error("nom_adresse")
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Téléphone -->
                        <div class="mb-4">
                            <label for="telephone_adresse" class="block text-sm font-medium text-gray-700">
                                Téléphone
                                <span class="text-red-600">*</span>
                            </label>
                            <input
                                type="tel"
                                name="telephone_adresse"
                                id="telephone_adresse"
                                value="{{ old("telephone_adresse") }}"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                placeholder="06 12 34 56 78"
                            />
                            @error("telephone_adresse")
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Téléphone mobile (optionnel) -->
                        <div class="mb-4">
                            <label for="tel_mobile_adresse" class="block text-sm font-medium text-gray-700">
                                Téléphone mobile (optionnel)
                            </label>
                            <input
                                type="tel"
                                name="tel_mobile_adresse"
                                id="tel_mobile_adresse"
                                value="{{ old("tel_mobile_adresse") }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                placeholder="06 12 34 56 78"
                            />
                            @error("tel_mobile_adresse")
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Google Places Autocomplete -->
                        <div class="mb-4">
                            <label for="address_autocomplete" class="block text-sm font-medium text-gray-700">Rechercher une adresse</label>
                            <input
                                type="text"
                                id="address_autocomplete"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                placeholder="Commencez à taper votre adresse..."
                            />
                            <p class="mt-1 text-xs text-gray-500">
                                Utilisez la recherche pour remplir automatiquement les champs ci-dessous
                            </p>
                        </div>

                        <!-- Numéro de voie -->
                        <div class="mb-4">
                            <label for="num_voie_adresse" class="block text-sm font-medium text-gray-700">
                                Numéro de voie
                                <span class="text-red-600">*</span>
                            </label>
                            <input
                                type="text"
                                name="num_voie_adresse"
                                id="num_voie_adresse"
                                value="{{ old("num_voie_adresse") }}"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                placeholder="12"
                            />
                            @error("num_voie_adresse")
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Rue -->
                        <div class="mb-4">
                            <label for="rue_adresse" class="block text-sm font-medium text-gray-700">
                                Rue
                                <span class="text-red-600">*</span>
                            </label>
                            <input
                                type="text"
                                name="rue_adresse"
                                id="rue_adresse"
                                value="{{ old("rue_adresse") }}"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                placeholder="Rue de la Paix"
                            />
                            @error("rue_adresse")
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Complément d'adresse -->
                        <div class="mb-4">
                            <label for="complement_adresse" class="block text-sm font-medium text-gray-700">
                                Complément d'adresse (optionnel)
                            </label>
                            <input
                                type="text"
                                name="complement_adresse"
                                id="complement_adresse"
                                value="{{ old("complement_adresse") }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                placeholder="Appartement 5, Bâtiment B"
                            />
                            @error("complement_adresse")
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Code postal -->
                        <div class="mb-4">
                            <label for="code_postal" class="block text-sm font-medium text-gray-700">
                                Code postal
                                <span class="text-red-600">*</span>
                            </label>
                            <input
                                type="text"
                                name="code_postal"
                                id="code_postal"
                                value="{{ old("code_postal") }}"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                placeholder="75001"
                            />
                            @error("code_postal")
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Ville -->
                        <div class="mb-6">
                            <label for="nom_ville" class="block text-sm font-medium text-gray-700">
                                Ville
                                <span class="text-red-600">*</span>
                            </label>
                            <input
                                type="text"
                                name="nom_ville"
                                id="nom_ville"
                                value="{{ old("nom_ville") }}"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                placeholder="Paris"
                            />
                            @error("nom_ville")
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-between">
                            <a
                                href="{{ route("dashboard.adresses.index") }}"
                                class="text-sm font-medium text-indigo-600 hover:text-indigo-800"
                            >
                                &larr; Retour aux adresses
                            </a>
                            <button
                                type="submit"
                                class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition duration-150 ease-in-out hover:bg-indigo-500 focus:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-none active:bg-indigo-900"
                            >
                                Enregistrer l'adresse
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Google Places API -->
    <script>
        window.initAutocomplete = function () {
            const input = document.getElementById('address_autocomplete');
            if (!input) return;

            const options = {
                componentRestrictions: { country: 'fr' },
                fields: ['address_components', 'formatted_address'],
                types: ['address'],
            };

            const autocomplete = new google.maps.places.Autocomplete(input, options);

            autocomplete.addListener('place_changed', function () {
                const place = autocomplete.getPlace();

                if (!place.address_components) {
                    return;
                }

                // Reset fields
                document.getElementById('num_voie_adresse').value = '';
                document.getElementById('rue_adresse').value = '';
                document.getElementById('code_postal').value = '';
                document.getElementById('nom_ville').value = '';

                // Parse address components
                for (const component of place.address_components) {
                    const type = component.types[0];

                    switch (type) {
                        case 'street_number':
                            document.getElementById('num_voie_adresse').value = component.long_name;
                            break;
                        case 'route':
                            document.getElementById('rue_adresse').value = component.long_name;
                            break;
                        case 'postal_code':
                            document.getElementById('code_postal').value = component.long_name;
                            break;
                        case 'locality':
                            document.getElementById('nom_ville').value = component.long_name;
                            break;
                    }
                }
            });
        };
    </script>

    @if (config("services.google.places_api_key"))
        <script
            src="https://maps.googleapis.com/maps/api/js?key={{ config("services.google.places_api_key") }}&libraries=places&callback=initAutocomplete"
            async
            defer
        ></script>
    @else
        <script>
            console.error('Google Places API Key is missing. Please add GOOGLE_PLACES_API_KEY to your .env file.');
        </script>
    @endif
</x-app-layout>
