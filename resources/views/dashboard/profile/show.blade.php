@php
    use Carbon\Carbon;
@endphp

<x-app-layout>
    <div class="py-12">
        <div class="mx-auto max-w-4xl space-y-6 px-8">
            <!-- Success Message -->
            <x-flash-message key="success" type="success" />

            <!-- Profile Information -->
            <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                <div class="p-6">
                    <div class="mb-6 flex items-center justify-between">
                        <h2 class="text-2xl font-bold text-gray-900">Mon profil</h2>
                        <a
                            href="{{ route("dashboard.profile.edit") }}"
                            class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition hover:bg-indigo-500 focus:bg-indigo-700"
                        >
                            Modifier
                        </a>
                    </div>

                    <div class="space-y-4">
                        <div class="grid grid-cols-3 gap-4">
                            <div class="text-sm font-medium text-gray-500">Civilité</div>
                            <div class="col-span-2 text-sm text-gray-900">{{ $client->civilite }}</div>
                        </div>

                        <div class="grid grid-cols-3 gap-4">
                            <div class="text-sm font-medium text-gray-500">Prénom</div>
                            <div class="col-span-2 text-sm text-gray-900">{{ $client->prenom_client }}</div>
                        </div>

                        <div class="grid grid-cols-3 gap-4">
                            <div class="text-sm font-medium text-gray-500">Nom</div>
                            <div class="col-span-2 text-sm text-gray-900">{{ $client->nom_client }}</div>
                        </div>

                        <div class="grid grid-cols-3 gap-4">
                            <div class="text-sm font-medium text-gray-500">Email</div>
                            <div class="col-span-2 text-sm text-gray-900">
                                {{ $client->email_client }}
                                @if ($client->google_id)
                                    <span
                                        class="ml-2 inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800"
                                    >
                                        <x-bi-google class="mr-1 h-3 w-3" />
                                        Compte Google
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-4">
                            <div class="text-sm font-medium text-gray-500">Date de naissance</div>
                            <div class="col-span-2 text-sm text-gray-900">
                                {{ $client->naissance_client ? $client->naissance_client->format("d/m/Y") : "Non renseignée" }}
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-4">
                            <div class="text-sm font-medium text-gray-500">Dernière connexion</div>
                            <div class="col-span-2 text-sm text-gray-900">
                                @if ($client->date_der_connexion)
                                    <x-date-local :date="$client->date_der_connexion" />
                                @else
                                    Jamais
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if (! $client->google_id)
                <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="p-6">
                        <h3 class="mb-4 text-lg font-semibold text-gray-900">Changer le mot de passe</h3>

                        <form method="POST" action="{{ route("dashboard.profile.password") }}">
                            @csrf
                            @method("PUT")

                            <div class="space-y-4">
                                <x-form-input
                                    type="password"
                                    name="current_password"
                                    id="current_password"
                                    label="Mot de passe actuel"
                                    required
                                />

                                <x-form-input type="password" name="password" id="password" label="Nouveau mot de passe" required />

                                <x-form-input
                                    type="password"
                                    name="password_confirmation"
                                    id="password_confirmation"
                                    label="Confirmer le mot de passe"
                                    required
                                />

                                <div class="flex justify-end">
                                    <button
                                        type="submit"
                                        class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition hover:bg-indigo-500 focus:bg-indigo-700"
                                    >
                                        Modifier le mot de passe
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @else
                <div class="overflow-hidden border border-blue-200 bg-blue-50 shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="mb-2 text-lg font-semibold text-blue-900">Mot de passe</h3>
                        <p class="text-sm text-blue-700">
                            Vous êtes connecté avec votre compte Google. Pour modifier votre mot de passe, veuillez gérer votre sécurité via
                            votre
                            <a href="https://myaccount.google.com/security" target="_blank" class="underline hover:text-blue-900">
                                compte Google
                            </a>
                            .
                        </p>
                    </div>
                </div>
            @endif

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-4 flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Authentification à deux facteurs</h3>
                            <p class="mt-1 text-sm text-gray-600">
                                Ajoutez une couche de sécurité supplémentaire à votre compte avec l'authentification à deux facteurs.
                            </p>
                        </div>
                        <div>
                            @if ($client->two_factor_confirmed_at)
                                <span
                                    class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-sm font-medium text-green-800"
                                >
                                    <svg class="mr-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                    Activé
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-sm font-medium text-gray-800">
                                    Désactivé
                                </span>
                            @endif
                        </div>
                    </div>

                    @if (! $client->two_factor_confirmed_at)
                        <div id="enable-2fa-section">
                            <p class="mb-4 text-sm text-gray-600">
                                Pour activer l'authentification à deux facteurs, scannez le code QR ci-dessous avec votre application
                                d'authentification (Google Authenticator, Authy, Microsoft Authenticator, etc.).
                            </p>
                            <button
                                type="button"
                                onclick="enable2FA()"
                                class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition hover:bg-indigo-500 focus:bg-indigo-700"
                            >
                                Activer la double authentification
                            </button>
                        </div>

                        <div id="qr-code-section" class="hidden">
                            <div class="mb-4 rounded-lg bg-gray-50 p-4">
                                <h4 class="mb-3 text-sm font-medium text-gray-900">Étape 1 : Scannez le code QR</h4>
                                <div id="qr-code" class="mb-4 flex justify-center"></div>
                                <div class="text-center">
                                    <p class="mb-2 text-xs text-gray-600">Ou entrez cette clé manuellement :</p>
                                    <code id="secret-key" class="rounded bg-gray-200 px-3 py-1 text-sm"></code>
                                </div>
                            </div>

                            <div class="mb-4 rounded-lg bg-gray-50 p-4">
                                <h4 class="mb-3 text-sm font-medium text-gray-900">Étape 2 : Vérifiez le code</h4>
                                <form onsubmit="confirm2FA(event)">
                                    <div class="flex space-x-2">
                                        <input
                                            type="text"
                                            id="verification-code"
                                            maxlength="6"
                                            pattern="[0-9]{6}"
                                            required
                                            placeholder="000000"
                                            class="flex-1 rounded-md border-gray-300 text-center text-lg tracking-widest shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        />
                                        <button
                                            type="submit"
                                            class="rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition hover:bg-indigo-500"
                                        >
                                            Vérifier
                                        </button>
                                    </div>
                                    <p id="verification-error" class="mt-2 hidden text-sm text-red-600"></p>
                                </form>
                            </div>
                        </div>

                        <div id="recovery-codes-section" class="hidden">
                            <div class="mb-4 rounded-lg border border-yellow-200 bg-yellow-50 p-4">
                                <h4 class="mb-2 text-sm font-medium text-yellow-900">
                                    <svg class="mr-1 inline h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            fill-rule="evenodd"
                                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                    Codes de récupération
                                </h4>
                                <p class="mb-3 text-sm text-yellow-800">
                                    Conservez ces codes de récupération dans un endroit sûr. Ils vous permettront d'accéder à votre compte
                                    si vous perdez votre appareil.
                                </p>
                                <div id="recovery-codes-list" class="mb-3 grid grid-cols-2 gap-2 rounded bg-white p-3"></div>
                                <button
                                    type="button"
                                    onclick="document.location.reload()"
                                    class="w-full rounded-md bg-green-600 px-4 py-2 text-white transition hover:bg-green-700"
                                >
                                    J'ai sauvegardé mes codes
                                </button>
                            </div>
                        </div>
                    @else
                        <div class="space-y-4">
                            <div class="rounded-lg border border-green-200 bg-green-50 p-4">
                                <p class="text-sm text-green-800">
                                    L'authentification à deux facteurs est activée depuis le
                                    <x-date-local :date="$client->two_factor_confirmed_at" />
                                    .
                                </p>
                            </div>

                            <div class="flex justify-end space-x-2">
                                {{-- <button --}}
                                {{-- type="button" --}}
                                {{-- onclick="showRecoveryCodes()" --}}
                                {{-- class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition hover:bg-indigo-500 focus:bg-indigo-700" --}}
                                {{-- > --}}
                                {{-- Voir les codes de récupération --}}
                                {{-- </button> --}}
                                <button
                                    type="button"
                                    onclick="document.getElementById('disable2FAModal').classList.remove('hidden')"
                                    class="inline-flex items-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition hover:bg-red-500 focus:bg-red-700"
                                >
                                    Désactiver
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="overflow-hidden border-2 border-red-200 bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <x-flash-message key="delete_error" />

                    <h3 class="mb-4 text-lg font-semibold text-red-900">Supprimer mon compte</h3>

                    <div class="mb-4 rounded-md border border-red-200 bg-red-50 p-4">
                        <p class="mb-2 text-sm text-red-800">
                            <strong>Attention :</strong>
                            Cette action est irréversible. Toutes vos données seront définitivement supprimées :
                        </p>
                        <ul class="list-inside list-disc space-y-1 text-sm text-red-700">
                            <li>Informations personnelles</li>
                            <li>Adresses de livraison</li>
                            <li>Historique des commandes</li>
                            <li>Vélos enregistrés</li>
                            <li>Demandes de service</li>
                        </ul>
                        <p class="mt-2 text-sm text-red-800">
                            Conformément au RGPD, vous avez le droit de demander la suppression de toutes vos données.
                        </p>
                    </div>

                    <div class="flex gap-2">
                        <button
                            type="button"
                            onclick="document.getElementById('deleteModal').classList.remove('hidden')"
                            class="inline-flex items-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition hover:bg-red-500 focus:bg-red-700"
                        >
                            Supprimer mon compte
                        </button>

                        <a
                            href="{{ route("dashboard.profile.export") }}"
                            class="inline-flex items-center rounded-md border border-transparent bg-gray-600 px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition hover:bg-gray-500 focus:bg-gray-700"
                        >
                            Demander mes données (RGPD)
                        </a>
                    </div>
                </div>
            </div>

            <div class="flex justify-center">
                <a href="{{ route("dashboard.index") }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">
                    &larr; Retour au tableau de bord
                </a>
            </div>
        </div>
    </div>

    <div id="deleteModal" class="fixed inset-0 z-50 flex hidden h-full w-full items-center justify-center overflow-y-auto bg-black/75">
        <div class="w-md rounded-md border bg-white p-5 shadow-lg">
            <h3 class="mb-4 text-lg font-medium text-gray-900">Confirmer la suppression</h3>

            <form method="POST" action="{{ route("dashboard.profile.destroy") }}">
                @csrf
                @method("DELETE")

                <div class="space-y-4">
                    @if (! $client->google_id)
                        <x-form-input type="password" name="password" id="delete_password" label="Mot de passe" required />
                    @else
                        <div class="rounded-md border border-blue-200 bg-blue-50 p-3">
                            <p class="text-sm text-blue-700">Compte connecté via Google. Aucun mot de passe requis pour la suppression.</p>
                        </div>
                    @endif

                    <div class="flex items-center">
                        <input
                            type="checkbox"
                            name="confirmation"
                            id="confirmation"
                            required
                            value="1"
                            class="h-4 w-4 rounded border-gray-300 text-red-600 focus:ring-red-500"
                        />
                        <label for="confirmation" class="ml-2 block text-sm text-gray-900">
                            Je comprends que cette action est irréversible
                        </label>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button
                            type="button"
                            onclick="document.getElementById('deleteModal').classList.add('hidden')"
                            class="inline-flex items-center rounded-md border border-transparent bg-gray-300 px-4 py-2 text-xs font-semibold tracking-widest text-gray-700 uppercase transition hover:bg-gray-400 focus:bg-gray-500"
                        >
                            Annuler
                        </button>
                        <button
                            type="submit"
                            class="inline-flex items-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition hover:bg-red-500 focus:bg-red-700"
                        >
                            Supprimer définitivement
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div id="disable2FAModal" class="fixed inset-0 z-50 flex hidden h-full w-full items-center justify-center overflow-y-auto bg-black/75">
        <div class="w-md rounded-md border bg-white p-5 shadow-lg">
            <h3 class="mb-4 text-lg font-medium text-gray-900">Désactiver l'authentification double facteurs ?</h3>

            <form onsubmit="disable2FA(event)">
                <div class="space-y-4">
                    @if (! $client->google_id)
                        <x-form-input type="password" name="password" id="disable_password" label="Mot de passe" required />
                    @endif

                    <p id="disable-error" class="hidden text-sm text-red-600"></p>

                    <div class="flex justify-end space-x-3">
                        <button
                            type="button"
                            onclick="document.getElementById('disable2FAModal').classList.add('hidden')"
                            class="inline-flex items-center rounded-md border border-transparent bg-gray-300 px-4 py-2 text-xs font-semibold tracking-widest text-gray-700 uppercase transition hover:bg-gray-400 focus:bg-gray-500"
                        >
                            Annuler
                        </button>
                        <button
                            type="submit"
                            class="inline-flex items-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition hover:bg-red-500 focus:bg-red-700"
                        >
                            Désactiver
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- <div --}}
    {{-- id="recoveryCodesModal" --}}
    {{-- class="fixed inset-0 z-50 flex hidden h-full w-full items-center justify-center overflow-y-auto bg-black/75" --}}
    {{-- > --}}
    {{-- <div class="relative top-20 mx-auto w-96 rounded-md border bg-white p-5 shadow-lg"> --}}
    {{-- <div class="mt-3"> --}}
    {{-- <h3 class="mb-4 text-lg font-medium text-gray-900">Codes de récupération</h3> --}}

    {{-- <div class="mb-4 rounded-lg border border-yellow-200 bg-yellow-50 p-4"> --}}
    {{-- <p class="mb-3 text-sm text-yellow-800"> --}}
    {{-- Conservez ces codes dans un endroit sûr. Chaque code ne peut être utilisé qu'une seule fois. --}}
    {{-- </p> --}}
    {{-- <div id="recovery-codes-display" class="mb-3 grid grid-cols-2 gap-2 rounded bg-white p-3"></div> --}}
    {{-- <button --}}
    {{-- type="button" --}}
    {{-- onclick="regenerateRecoveryCodes()" --}}
    {{-- class="inline-flex items-center rounded-md border border-transparent bg-yellow-600 px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition hover:bg-yellow-400 focus:bg-yellow-700" --}}
    {{-- > --}}
    {{-- Régénérer les codes --}}
    {{-- </button> --}}
    {{-- </div> --}}

    {{-- <button --}}
    {{-- type="button" --}}
    {{-- onclick="document.getElementById('recoveryCodesModal').classList.add('hidden')" --}}
    {{-- class="w-full rounded-md bg-gray-300 px-4 py-2 text-gray-700 transition hover:bg-gray-400" --}}
    {{-- > --}}
    {{-- Fermer --}}
    {{-- </button> --}}
    {{-- </div> --}}
    {{-- </div> --}}
    {{-- </div> --}}

    <script>
        function enable2FA() {
            fetch('{{ route("dashboard.profile.two-factor.enable") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        // Hide enable button
                        document.getElementById('enable-2fa-section').classList.add('hidden');

                        // Show QR code
                        document.getElementById('secret-key').textContent = data.secret;
                        document.getElementById('qr-code').innerHTML =
                            `<img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(data.qr_code_url)}" alt="QR Code">`;
                        document.getElementById('qr-code-section').classList.remove('hidden');
                    }
                })
                .catch((error) => console.error('Error:', error));
        }

        function confirm2FA(event) {
            event.preventDefault();

            const code = document.getElementById('verification-code').value;
            const errorElement = document.getElementById('verification-error');

            fetch('{{ route("dashboard.profile.two-factor.confirm") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({ code }),
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        // Hide QR code section
                        document.getElementById('qr-code-section').classList.add('hidden');

                        // Show recovery codes
                        const recoveryCodesList = document.getElementById('recovery-codes-list');
                        recoveryCodesList.innerHTML = data.recovery_codes
                            .map((code) => `<code class="text-sm bg-gray-200 px-2 py-1 rounded">${code}</code>`)
                            .join('');
                        document.getElementById('recovery-codes-section').classList.remove('hidden');
                    } else {
                        errorElement.textContent = data.message;
                        errorElement.classList.remove('hidden');
                    }
                })
                .catch((error) => {
                    console.error('Error:', error);
                    errorElement.textContent = 'Une erreur est survenue. Veuillez réessayer.';
                    errorElement.classList.remove('hidden');
                });
        }

        function disable2FA(event) {
            event.preventDefault();

            const password = document.getElementById('disable_password')?.value;
            const errorElement = document.getElementById('disable-error');

            fetch('{{ route("dashboard.profile.two-factor.disable") }}', {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({ password }),
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        location.reload();
                    } else {
                        errorElement.textContent = data.message;
                        errorElement.classList.remove('hidden');
                    }
                })
                .catch((error) => {
                    console.error('Error:', error);
                    errorElement.textContent = 'Une erreur est survenue. Veuillez réessayer.';
                    errorElement.classList.remove('hidden');
                });
        }

        function showRecoveryCodes() {
            fetch('{{ route("dashboard.profile.two-factor.recovery-codes") }}', {
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        const recoveryCodesDisplay = document.getElementById('recovery-codes-display');
                        recoveryCodesDisplay.innerHTML = data.recovery_codes
                            .map((code) => `<code class="text-sm bg-gray-200 px-2 py-1 rounded">${code}</code>`)
                            .join('');
                        document.getElementById('recoveryCodesModal').classList.remove('hidden');
                    }
                })
                .catch((error) => console.error('Error:', error));
        }

        {{-- function regenerateRecoveryCodes() { --}}
        {{-- const password = prompt('Entrez votre mot de passe pour confirmer :'); --}}
        {{-- if (!password) return; --}}

        {{-- fetch('{{ route("dashboard.profile.two-factor.recovery-codes.regenerate") }}', { --}}
        {{-- method: 'POST', --}}
        {{-- headers: { --}}
        {{-- 'Content-Type': 'application/json', --}}
        {{-- 'X-CSRF-TOKEN': '{{ csrf_token() }}', --}}
        {{-- }, --}}
        {{-- body: JSON.stringify({ password }), --}}
        {{-- }) --}}
        {{-- .then((response) => response.json()) --}}
        {{-- .then((data) => { --}}
        {{-- if (data.success) { --}}
        {{-- const recoveryCodesDisplay = document.getElementById('recovery-codes-display'); --}}
        {{-- recoveryCodesDisplay.innerHTML = data.recovery_codes --}}
        {{-- .map((code) => `<code class="text-sm bg-gray-200 px-2 py-1 rounded">${code}</code>`) --}}
        {{-- .join(''); --}}
        {{-- alert('Les codes de récupération ont été régénérés avec succès.'); --}}
        {{-- } else { --}}
        {{-- alert(data.message); --}}
        {{-- } --}}
        {{-- }) --}}
        {{-- .catch((error) => console.error('Error:', error)); --}}
        {{-- } --}}
    </script>
</x-app-layout>
