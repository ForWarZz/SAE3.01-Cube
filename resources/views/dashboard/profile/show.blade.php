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
</x-app-layout>
