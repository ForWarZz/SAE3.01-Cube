<x-app-layout>
    <div class="py-12">
        <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h1 class="mb-6 text-2xl font-bold text-gray-900">Nouvelle adresse</h1>

                    <form method="POST" action="{{ route("dashboard.addresses.store") }}" id="address-form">
                        @csrf

                        @if (isset($intended))
                            <input type="hidden" name="intended" value="{{ $intended }}" />
                        @endif

                        <x-form-input name="alias_adresse" label="Alias (ex: Ma maison, Bureau)" placeholder="Ma maison" />

                        <x-form-input name="societe_adresse" label="Société (optionnel)" placeholder="Nom de l'entreprise" />

                        <x-form-input name="tva_adresse" label="Numéro de TVA (optionnel)" placeholder="FR12345678901" />

                        <x-form-input name="prenom_adresse" label="Prénom" :value="$client->prenom_client" required />

                        <x-form-input name="nom_adresse" label="Nom" :value="$client->nom_client" required />

                        <x-form-input type="tel" name="telephone_adresse" label="Téléphone" placeholder="04 50 10 25 21" required />

                        <x-form-input type="tel" name="tel_mobile_adresse" label="Téléphone mobile" placeholder="06 12 34 56 78" />

                        <div class="mb-4">
                            <div id="google-cookie-alert" class="mb-2 hidden rounded-md border border-yellow-200 bg-yellow-50 p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path
                                                fill-rule="evenodd"
                                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd"
                                            />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-yellow-800">Autocomplétion désactivée</h3>
                                        <div class="mt-2 text-sm text-yellow-700">
                                            <p>
                                                Vous avez refusé le cookie Google Maps. La recherche automatique d'adresse est donc bloquée.
                                            </p>
                                            <p class="mt-2">
                                                Vous pouvez soit
                                                <button
                                                    type="button"
                                                    onclick="tarteaucitron.userInterface.openPanel()"
                                                    class="font-bold underline hover:text-yellow-900"
                                                >
                                                    activer le cookie Google Places
                                                </button>
                                                pour gagner du temps, soit remplir les champs manuellement ci-dessous.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <x-form-input
                                name="address_autocomplete"
                                id="address_autocomplete"
                                label="Rechercher une adresse"
                                help="Complétez automatiquement les champs d'adresse en tapant votre adresse ici."
                                placeholder="Commencez à taper votre adresse..."
                            />
                        </div>

                        <x-form-input name="num_voie_adresse" label="Numéro de voie" placeholder="12" required />

                        <x-form-input name="rue_adresse" label="Rue" placeholder="Rue de la Paix" required />

                        <x-form-input
                            name="complement_adresse"
                            label="Complément d'adresse (optionnel)"
                            placeholder="Appartement 5, Bâtiment B"
                        />

                        <x-form-input
                            name="code_postal"
                            id="code_postal"
                            label="Code postal"
                            placeholder="Tapez le code postal..."
                            required
                            maxlength="5"
                        />

                        <div class="mb-4">
                            <label for="nom_ville" class="block text-sm font-medium text-gray-700">Ville</label>
                            <div class="relative mt-1">
                                <select
                                    name="nom_ville"
                                    id="nom_ville"
                                    class="block w-full rounded-md border-gray-300 bg-gray-50 text-gray-500 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                    required
                                >
                                    <option value="">-- Veuillez d'abord saisir un code postal --</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <a
                                href="{{ route("dashboard.addresses.index") }}"
                                class="text-sm font-medium text-blue-600 hover:text-blue-800"
                            >
                                &larr; Retour aux adresses
                            </a>
                            <x-button type="submit" color="blue" size="sm">+ Nouvelle adresse</x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const cpInput = document.getElementById('code_postal');
            const villeSelect = document.getElementById('nom_ville');

            const loadCities = async (cp, preselectCity = null) => {
                if (cp.length !== 5) return;

                villeSelect.disabled = true;
                villeSelect.innerHTML = '<option>Chargement...</option>';

                try {
                    const response = await fetch(`https://api-adresse.data.gouv.fr/search/?q=${cp}&type=municipality`);
                    const data = await response.json();

                    villeSelect.innerHTML = '';

                    if (data.features.length === 0) {
                        villeSelect.innerHTML = '<option value="">Aucune ville trouvée</option>';
                    } else {
                        data.features.forEach((feature) => {
                            const city = feature.properties.city.toUpperCase();
                            const option = document.createElement('option');
                            option.value = city;
                            option.textContent = city;

                            if (feature.properties.postcode === cp) {
                                villeSelect.appendChild(option);
                            }
                        });

                        if (preselectCity) {
                            villeSelect.value = preselectCity.toUpperCase();
                        }
                    }
                } catch (error) {
                    console.error(error);
                    villeSelect.innerHTML = '<option value="">Erreur de chargement</option>';
                } finally {
                    villeSelect.disabled = false;
                    villeSelect.classList.remove('bg-gray-50', 'text-gray-500');
                }
            };

            cpInput.addEventListener('input', function () {
                if (this.value.length === 5) {
                    loadCities(this.value);
                }
            });

            window.initAutocomplete = function () {
                const input = document.getElementById('address_autocomplete');
                if (!input || !window.google) return;

                const autocomplete = new google.maps.places.Autocomplete(input, {
                    componentRestrictions: { country: 'fr' },
                    fields: ['address_components'],
                    types: ['address'],
                });

                autocomplete.addListener('place_changed', function () {
                    const place = autocomplete.getPlace();
                    let cp = '';
                    let ville = '';

                    if (place.address_components) {
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
                                    cp = component.long_name;
                                    break;
                                case 'locality':
                                    document.getElementById('nom_ville').value = component.long_name;
                                    ville = component.long_name;
                                    break;
                            }
                        }
                    }

                    if (cp && ville) {
                        document.getElementById('code_postal').value = cp;
                        loadCities(cp, ville);
                    }
                });
            };
        });
    </script>
</x-app-layout>
