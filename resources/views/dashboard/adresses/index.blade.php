<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Mes adresses</h1>
                            <p class="text-sm text-gray-500 mt-1">Gérez vos adresses de livraison</p>
                        </div>
                        <a href="{{ route('dashboard.adresses.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Nouvelle adresse
                        </a>
                    </div>

                    @if (session('success'))
                        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($adresses->isEmpty())
                        <div class="text-center py-12">
                            <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune adresse</h3>
                            <p class="mt-1 text-sm text-gray-500">Commencez par ajouter une nouvelle adresse.</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach ($adresses as $adresse)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <h3 class="font-semibold text-gray-900">{{ $adresse->alias_adresse }}</h3>
                                            @if ($adresse->societe_adresse)
                                                <p class="text-sm text-gray-600 font-medium">{{ $adresse->societe_adresse }}</p>
                                            @endif
                                            <p class="text-sm text-gray-600 mt-1">
                                                {{ $adresse->prenom_adresse }} {{ $adresse->nom_adresse }}
                                            </p>
                                            @if ($adresse->telephone_adresse)
                                                <p class="text-sm text-gray-600">Tél: {{ $adresse->telephone_adresse }}</p>
                                            @endif
                                            <p class="text-sm text-gray-600 mt-1">
                                                {{ $adresse->num_voie_adresse }} {{ $adresse->rue_adresse }}
                                            </p>
                                            @if ($adresse->complement_adresse)
                                                <p class="text-sm text-gray-600">{{ $adresse->complement_adresse }}</p>
                                            @endif
                                            <p class="text-sm text-gray-600">
                                                {{ $adresse->ville->cp_ville }} {{ $adresse->ville->nom_ville }}
                                            </p>
                                            @if ($adresse->tva_adresse)
                                                <p class="text-sm text-gray-500 mt-1">TVA: {{ $adresse->tva_adresse }}</p>
                                            @endif
                                        </div>
                                        <form method="POST" action="{{ route('dashboard.adresses.destroy', $adresse) }}" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette adresse ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <a href="{{ route('dashboard.index') }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                            &larr; Retour au tableau de bord
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
