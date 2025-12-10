<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Success Message -->
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Profile Information -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Mon profil</h2>
                        <a href="{{ route('dashboard.profile.edit') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:bg-indigo-700 transition">
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
                                @if($client->google_id)
                                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <svg class="w-3 h-3 mr-1" viewBox="0 0 24 24">
                                            <path fill="currentColor" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                            <path fill="currentColor" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                            <path fill="currentColor" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                            <path fill="currentColor" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                                        </svg>
                                        Compte Google
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-4">
                            <div class="text-sm font-medium text-gray-500">Date de naissance</div>
                            <div class="col-span-2 text-sm text-gray-900">{{ \Carbon\Carbon::parse($client->naissance_client)->format('d/m/Y') }}</div>
                        </div>

                        <div class="grid grid-cols-3 gap-4">
                            <div class="text-sm font-medium text-gray-500">Dernière connexion</div>
                            <div class="col-span-2 text-sm text-gray-900">
                                @if($client->date_der_connexion)
                                    {{ \Carbon\Carbon::parse($client->date_der_connexion)->format('d/m/Y à H:i') }}
                                @else
                                    Jamais
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Change Password -->
            @if(!$client->google_id)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Changer le mot de passe</h3>
                    
                    <form method="POST" action="{{ route('dashboard.profile.password') }}">
                        @csrf
                        @method('PUT')

                        <div class="space-y-4">
                            <div>
                                <label for="current_password" class="block text-sm font-medium text-gray-700">Mot de passe actuel</label>
                                <input type="password" name="current_password" id="current_password" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @error('current_password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700">Nouveau mot de passe</label>
                                <input type="password" name="password" id="password" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @error('password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmer le mot de passe</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:bg-indigo-700 transition">
                                    Modifier le mot de passe
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @else
            <div class="bg-blue-50 overflow-hidden shadow-sm sm:rounded-lg border border-blue-200">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-blue-900 mb-2">Mot de passe</h3>
                    <p class="text-sm text-blue-700">
                        Vous êtes connecté avec votre compte Google. Pour modifier votre mot de passe, veuillez gérer votre sécurité via votre <a href="https://myaccount.google.com/security" target="_blank" class="underline hover:text-blue-900">compte Google</a>.
                    </p>
                </div>
            </div>
            @endif

            <!-- Two-Factor Authentication -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Authentification à deux facteurs</h3>
                            <p class="text-sm text-gray-600 mt-1">
                                Ajoutez une couche de sécurité supplémentaire à votre compte avec l'authentification à deux facteurs.
                            </p>
                        </div>
                        <div>
                            @if($client->two_factor_confirmed_at)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Activé
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                    Désactivé
                                </span>
                            @endif
                        </div>
                    </div>

                    @if(!$client->two_factor_confirmed_at)
                        <!-- Enable 2FA -->
                        <div id="enable-2fa-section">
                            <p class="text-sm text-gray-600 mb-4">
                                Pour activer l'authentification à deux facteurs, scannez le code QR ci-dessous avec votre application d'authentification (Google Authenticator, Authy, Microsoft Authenticator, etc.).
                            </p>
                            <button type="button" onclick="enable2FA()"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:bg-indigo-700 transition">
                                Activer la double authentification
                            </button>
                        </div>

                        <!-- QR Code Section (Hidden by default) -->
                        <div id="qr-code-section" class="hidden">
                            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                                <h4 class="text-sm font-medium text-gray-900 mb-3">Étape 1 : Scannez le code QR</h4>
                                <div id="qr-code" class="flex justify-center mb-4"></div>
                                <div class="text-center">
                                    <p class="text-xs text-gray-600 mb-2">Ou entrez cette clé manuellement :</p>
                                    <code id="secret-key" class="text-sm bg-gray-200 px-3 py-1 rounded"></code>
                                </div>
                            </div>

                            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                                <h4 class="text-sm font-medium text-gray-900 mb-3">Étape 2 : Vérifiez le code</h4>
                                <form onsubmit="confirm2FA(event)">
                                    <div class="flex space-x-2">
                                        <input type="text" id="verification-code" maxlength="6" pattern="[0-9]{6}" required
                                            placeholder="000000"
                                            class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-center text-lg tracking-widest">
                                        <button type="submit"
                                            class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 transition">
                                            Vérifier
                                        </button>
                                    </div>
                                    <p id="verification-error" class="mt-2 text-sm text-red-600 hidden"></p>
                                </form>
                            </div>
                        </div>

                        <!-- Recovery Codes Section (Hidden by default) -->
                        <div id="recovery-codes-section" class="hidden">
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                                <h4 class="text-sm font-medium text-yellow-900 mb-2">
                                    <svg class="inline w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    Codes de récupération
                                </h4>
                                <p class="text-sm text-yellow-800 mb-3">
                                    Conservez ces codes de récupération dans un endroit sûr. Ils vous permettront d'accéder à votre compte si vous perdez votre appareil.
                                </p>
                                <div id="recovery-codes-list" class="bg-white rounded p-3 mb-3 grid grid-cols-2 gap-2"></div>
                                <button type="button" onclick="document.location.reload()"
                                    class="w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition">
                                    J'ai sauvegardé mes codes
                                </button>
                            </div>
                        </div>
                    @else
                        <!-- 2FA is enabled -->
                        <div class="space-y-4">
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                <p class="text-sm text-green-800">
                                    L'authentification à deux facteurs est activée depuis le {{ $client->two_factor_confirmed_at->format('d/m/Y à H:i') }}.
                                </p>
                            </div>

                            <div class="flex space-x-3">
                                <button type="button" onclick="showRecoveryCodes()"
                                    class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition">
                                    Voir les codes de récupération
                                </button>
                                <button type="button" onclick="document.getElementById('disable2FAModal').classList.remove('hidden')"
                                    class="flex-1 px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition">
                                    Désactiver
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Delete Account (GDPR) -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-2 border-red-200">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-red-900 mb-4">Supprimer mon compte</h3>
                    
                    <div class="bg-red-50 border border-red-200 rounded-md p-4 mb-4">
                        <p class="text-sm text-red-800 mb-2">
                            <strong>Attention :</strong> Cette action est irréversible. Toutes vos données seront définitivement supprimées :
                        </p>
                        <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                            <li>Informations personnelles</li>
                            <li>Adresses de livraison</li>
                            <li>Historique des commandes</li>
                            <li>Vélos enregistrés</li>
                            <li>Demandes de service</li>
                        </ul>
                        <p class="text-sm text-red-800 mt-2">
                            Conformément au RGPD, vous avez le droit de demander la suppression de toutes vos données.
                        </p>
                    </div>

                    <button type="button" onclick="document.getElementById('deleteModal').classList.remove('hidden')"
                        class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:bg-red-700 transition">
                        Supprimer mon compte
                    </button>
                </div>
            </div>

            <!-- Back to Dashboard -->
            <div class="flex justify-center">
                <a href="{{ route('dashboard.index') }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                    &larr; Retour au tableau de bord
                </a>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Confirmer la suppression</h3>
                
                <form method="POST" action="{{ route('dashboard.profile.destroy') }}">
                    @csrf
                    @method('DELETE')

                    <div class="space-y-4">
                        @if(!$client->google_id)
                        <div>
                            <label for="delete_password" class="block text-sm font-medium text-gray-700">Mot de passe</label>
                            <input type="password" name="password" id="delete_password" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        @else
                        <div class="bg-blue-50 border border-blue-200 rounded-md p-3">
                            <p class="text-sm text-blue-700">
                                Compte connecté via Google. Aucun mot de passe requis pour la suppression.
                            </p>
                        </div>
                        @endif

                        <div class="flex items-center">
                            <input type="checkbox" name="confirmation" id="confirmation" required value="1"
                                class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                            <label for="confirmation" class="ml-2 block text-sm text-gray-900">
                                Je comprends que cette action est irréversible
                            </label>
                        </div>

                        <div class="flex space-x-3">
                            <button type="button" onclick="document.getElementById('deleteModal').classList.add('hidden')"
                                class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition">
                                Annuler
                            </button>
                            <button type="submit"
                                class="flex-1 px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition">
                                Supprimer définitivement
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Disable 2FA Modal -->
    <div id="disable2FAModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Désactiver l'authentification à deux facteurs</h3>
                
                <form onsubmit="disable2FA(event)">
                    <div class="space-y-4">
                        @if(!$client->google_id)
                        <div>
                            <label for="disable_password" class="block text-sm font-medium text-gray-700">Mot de passe</label>
                            <input type="password" name="password" id="disable_password" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                        </div>
                        @endif

                        <p id="disable-error" class="text-sm text-red-600 hidden"></p>

                        <div class="flex space-x-3">
                            <button type="button" onclick="document.getElementById('disable2FAModal').classList.add('hidden')"
                                class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition">
                                Annuler
                            </button>
                            <button type="submit"
                                class="flex-1 px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition">
                                Désactiver
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Recovery Codes Modal -->
    <div id="recoveryCodesModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Codes de récupération</h3>
                
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                    <p class="text-sm text-yellow-800 mb-3">
                        Conservez ces codes dans un endroit sûr. Chaque code ne peut être utilisé qu'une seule fois.
                    </p>
                    <div id="recovery-codes-display" class="bg-white rounded p-3 mb-3 grid grid-cols-2 gap-2"></div>
                    <button type="button" onclick="regenerateRecoveryCodes()"
                        class="w-full px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
                        Regénérer les codes
                    </button>
                </div>

                <button type="button" onclick="document.getElementById('recoveryCodesModal').classList.add('hidden')"
                    class="w-full px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition">
                    Fermer
                </button>
            </div>
        </div>
    </div>

    <script>
        function enable2FA() {
            fetch('{{ route('dashboard.profile.two-factor.enable') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Hide enable button
                    document.getElementById('enable-2fa-section').classList.add('hidden');
                    
                    // Show QR code
                    document.getElementById('secret-key').textContent = data.secret;
                    document.getElementById('qr-code').innerHTML = `<img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(data.qr_code_url)}" alt="QR Code">`;
                    document.getElementById('qr-code-section').classList.remove('hidden');
                }
            })
            .catch(error => console.error('Error:', error));
        }

        function confirm2FA(event) {
            event.preventDefault();
            
            const code = document.getElementById('verification-code').value;
            const errorElement = document.getElementById('verification-error');
            
            fetch('{{ route('dashboard.profile.two-factor.confirm') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ code })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Hide QR code section
                    document.getElementById('qr-code-section').classList.add('hidden');
                    
                    // Show recovery codes
                    const recoveryCodesList = document.getElementById('recovery-codes-list');
                    recoveryCodesList.innerHTML = data.recovery_codes.map(code => 
                        `<code class="text-sm bg-gray-200 px-2 py-1 rounded">${code}</code>`
                    ).join('');
                    document.getElementById('recovery-codes-section').classList.remove('hidden');
                } else {
                    errorElement.textContent = data.message;
                    errorElement.classList.remove('hidden');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                errorElement.textContent = 'Une erreur est survenue. Veuillez réessayer.';
                errorElement.classList.remove('hidden');
            });
        }

        function disable2FA(event) {
            event.preventDefault();
            
            const password = document.getElementById('disable_password')?.value;
            const errorElement = document.getElementById('disable-error');
            
            fetch('{{ route('dashboard.profile.two-factor.disable') }}', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ password })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    errorElement.textContent = data.message;
                    errorElement.classList.remove('hidden');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                errorElement.textContent = 'Une erreur est survenue. Veuillez réessayer.';
                errorElement.classList.remove('hidden');
            });
        }

        function showRecoveryCodes() {
            fetch('{{ route('dashboard.profile.two-factor.recovery-codes') }}', {
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const recoveryCodesDisplay = document.getElementById('recovery-codes-display');
                    recoveryCodesDisplay.innerHTML = data.recovery_codes.map(code => 
                        `<code class="text-sm bg-gray-200 px-2 py-1 rounded">${code}</code>`
                    ).join('');
                    document.getElementById('recoveryCodesModal').classList.remove('hidden');
                }
            })
            .catch(error => console.error('Error:', error));
        }

        function regenerateRecoveryCodes() {
            const password = prompt('Entrez votre mot de passe pour confirmer :');
            if (!password) return;

            fetch('{{ route('dashboard.profile.two-factor.recovery-codes.regenerate') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ password })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const recoveryCodesDisplay = document.getElementById('recovery-codes-display');
                    recoveryCodesDisplay.innerHTML = data.recovery_codes.map(code => 
                        `<code class="text-sm bg-gray-200 px-2 py-1 rounded">${code}</code>`
                    ).join('');
                    alert('Les codes de récupération ont été régénérés avec succès.');
                } else {
                    alert(data.message);
                }
            })
            .catch(error => console.error('Error:', error));
        }
    </script>
</x-app-layout>
