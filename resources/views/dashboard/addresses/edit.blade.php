<x-app-layout>
    <div class="py-12">
        <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h1 class="mb-6 text-2xl font-bold text-gray-900">Modifier l'adresse</h1>

                    <form method="POST" action="{{ route("dashboard.addresses.update", $address) }}" id="address-form">
                        @csrf
                        @method("PUT")

                        <!-- Alias -->
                        <x-form-input
                            name="alias_adresse"
                            label="Alias (ex: Ma maison, Bureau)"
                            placeholder="Ma maison"
                            :value="old('alias_adresse', $address->alias_adresse)"
                        />

                        <!-- Société (optionnel) -->
                        <x-form-input
                            name="societe_adresse"
                            label="Société (optionnel)"
                            placeholder="Nom de l'entreprise"
                            :value="old('societe_adresse', $address->societe_adresse)"
                        />

                        <!-- Numéro TVA (optionnel) -->
                        <x-form-input
                            name="tva_adresse"
                            label="Numéro de TVA (optionnel)"
                            placeholder="FR12345678901"
                            :value="old('tva_adresse', $address->tva_adresse)"
                        />

                        <!-- Prénom -->
                        <x-form-input
                            name="prenom_adresse"
                            label="Prénom"
                            :value="old('prenom_adresse', $address->prenom_adresse)"
                            required
                        />

                        <!-- Nom -->
                        <x-form-input name="nom_adresse" label="Nom" :value="old('nom_adresse', $address->nom_adresse)" required />

                        <!-- Téléphone -->
                        <x-form-input
                            type="tel"
                            name="telephone_adresse"
                            label="Téléphone"
                            placeholder="04 50 10 25 21"
                            :value="old('telephone_adresse', $address->telephone_adresse)"
                            required
                        />

                        <!-- Téléphone mobile (optionnel) -->
                        <x-form-input
                            type="tel"
                            name="tel_mobile_adresse"
                            label="Téléphone mobile (optionnel)"
                            placeholder="06 12 34 56 78"
                            :value="old('tel_mobile_adresse', $address->tel_mobile_adresse)"
                        />

                        <!-- Google Places Autocomplete -->
                        <x-form-input
                            name="address_autocomplete"
                            id="address_autocomplete"
                            label="Rechercher une adresse"
                            placeholder="Commencez à taper votre adresse..."
                            wrapperClass="mb-4"
                        />

                        <!-- Numéro de voie -->
                        <x-form-input
                            name="num_voie_adresse"
                            label="Numéro de voie"
                            placeholder="12"
                            :value="old('num_voie_adresse', $address->num_voie_adresse)"
                            required
                        />

                        <!-- Rue -->
                        <x-form-input
                            name="rue_adresse"
                            label="Rue"
                            placeholder="Rue de la Paix"
                            :value="old('rue_adresse', $address->rue_adresse)"
                            required
                        />

                        <!-- Complément d'adresse -->
                        <x-form-input
                            name="complement_adresse"
                            label="Complément d'adresse (optionnel)"
                            placeholder="Appartement 5, Bâtiment B"
                            :value="old('complement_adresse', $address->complement_adresse)"
                        />

                        <!-- Code postal -->
                        <x-form-input
                            name="code_postal"
                            label="Code postal"
                            placeholder="75001"
                            :value="old('code_postal', $address->city->cp_ville ?? '')"
                            required
                            readonly
                        />

                        <!-- City -->
                        <x-form-input
                            name="nom_ville"
                            label="Ville"
                            placeholder="Paris"
                            :value="old('nom_ville', $address->city->nom_ville ?? '')"
                            required
                            readonly
                            wrapperClass="mb-6"
                        />

                        <div class="flex items-center justify-between">
                            <a
                                href="{{ route("dashboard.addresses.index") }}"
                                class="text-sm font-medium text-indigo-600 hover:text-indigo-800"
                            >
                                &larr; Retour aux adresses
                            </a>
                            <button
                                type="submit"
                                class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition duration-150 ease-in-out hover:bg-indigo-500 focus:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-none active:bg-indigo-900"
                            >
                                Enregistrer les modifications
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

        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('address-form');

            form.addEventListener('submit', function () {
                function cleanPhoneNumber(phoneInput) {
                    if (phoneInput && phoneInput.value) {
                        phoneInput.value = phoneInput.value.replace(/[^\d+]/g, '');
                    }
                }

                const telephoneAdresse = document.getElementById('telephone_adresse');
                const telMobileAdresse = document.getElementById('tel_mobile_adresse');

                cleanPhoneNumber(telephoneAdresse);
                cleanPhoneNumber(telMobileAdresse);
            });
        });
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
