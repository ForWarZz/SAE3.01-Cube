<x-app-layout>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-6 flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Mes adresses</h1>
                            <p class="mt-1 text-sm text-gray-500">Gérez vos adresses de livraison</p>
                        </div>
                        <a
                            href="{{ route("dashboard.adresses.create") }}"
                            class="rounded-lg bg-indigo-600 px-3 py-2 text-sm font-medium text-white hover:bg-indigo-800"
                        >
                            + Nouvelle adresse
                        </a>
                    </div>

                    @if (session("success"))
                        <div class="mb-4 rounded border border-green-400 bg-green-100 p-4 text-green-700">
                            {{ session("success") }}
                        </div>
                    @endif

                    @if ($adresses->isEmpty())
                        <div class="py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                                ></path>
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                                ></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune adresse</h3>
                            <p class="mt-1 text-sm text-gray-500">Commencez par ajouter une nouvelle adresse.</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            @foreach ($adresses as $adresse)
                                <x-address-card :address="$adresse">
                                    {{-- Injection du bouton supprimer via le slot 'actions' --}}
                                    <x-slot name="actions">
                                        <form
                                            method="POST"
                                            action="{{ route("dashboard.adresses.destroy", $adresse) }}"
                                            onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette adresse ?');"
                                        >
                                            @csrf
                                            @method("DELETE")

                                            <button
                                                type="submit"
                                                class="rounded-md p-2 text-gray-400 transition hover:bg-red-50 hover:text-red-600"
                                                title="Supprimer"
                                            >
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                                    ></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </x-slot>
                                </x-address-card>
                            @endforeach
                        </div>
                    @endif

                    <div class="mt-6 border-t border-gray-200 pt-6">
                        <a href="{{ route("dashboard.index") }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">
                            &larr; Retour au tableau de bord
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
