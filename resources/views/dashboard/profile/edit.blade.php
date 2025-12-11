<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Info Message for Google OAuth -->
            @if (session('info'))
                <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative mb-6">
                    {{ session('info') }}
                </div>
            @endif

            @if($client->google_id)
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <h3 class="text-sm font-medium text-blue-900">Compte créé avec Google</h3>
                            <p class="mt-1 text-sm text-blue-700">
                                Vous avez créé votre compte en utilisant votre compte Google. Veuillez vérifier les données saisies afin de compléter votre profil (date de naissance et civilité).
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Modifier mon profil</h2>

                    <form method="POST" action="{{ route('dashboard.profile.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="space-y-6">
                            <!-- Civilité -->
                            <div>
                                <label for="civilite" class="block text-sm font-medium text-gray-700">Civilité *</label>
                                <select name="civilite" id="civilite" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="Monsieur" {{ old('civilite', $client->civilite) == 'Monsieur' ? 'selected' : '' }}>Monsieur</option>
                                    <option value="Madame" {{ old('civilite', $client->civilite) == 'Madame' ? 'selected' : '' }}>Madame</option>
                                </select>
                                @error('civilite')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Prénom -->
                            <div>
                                <label for="prenom_client" class="block text-sm font-medium text-gray-700">Prénom *</label>
                                <input type="text" name="prenom_client" id="prenom_client" value="{{ old('prenom_client', $client->prenom_client) }}" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @error('prenom_client')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Nom -->
                            <div>
                                <label for="nom_client" class="block text-sm font-medium text-gray-700">Nom *</label>
                                <input type="text" name="nom_client" id="nom_client" value="{{ old('nom_client', $client->nom_client) }}" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @error('nom_client')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email_client" class="block text-sm font-medium text-gray-700">Email *</label>
                                <input type="email" name="email_client" id="email_client" value="{{ old('email_client', $client->email_client) }}" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @error('email_client')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Date de naissance -->
                            <div>
                                <label for="naissance_client" class="block text-sm font-medium text-gray-700">Date de naissance *</label>
                                <input type="date" name="naissance_client" id="naissance_client" value="{{ old('naissance_client', $client->naissance_client) }}" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @error('naissance_client')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex items-center justify-between pt-4">
                                <a href="{{ route('dashboard.profile.show') }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                    &larr; Annuler
                                </a>
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:bg-indigo-700 transition">
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
