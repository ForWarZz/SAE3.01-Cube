<x-app-layout>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h1 class="mb-6 text-2xl font-bold text-gray-900">
                        Bienvenue, {{ $client->prenom_client }} {{ $client->nom_client }}
                    </h1>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                        <a
                            href="{{ route("dashboard.addresses.index") }}"
                            class="block rounded-lg border border-gray-200 bg-gray-50 p-6 transition-colors hover:bg-gray-100"
                        >
                            <div class="flex items-center">
                                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                <div class="ml-4">
                                    <h2 class="text-lg font-semibold text-gray-900">Mes adresses</h2>
                                    <p class="text-sm text-gray-500">Gérer mes adresses de livraison</p>
                                </div>
                            </div>
                        </a>

                        <a
                            href="{{ route("dashboard.orders.index") }}"
                            class="block rounded-lg border border-gray-200 bg-gray-50 p-6 transition-colors hover:bg-gray-100"
                        >
                            <div class="flex items-center">
                                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"
                                    ></path>
                                </svg>
                                <div class="ml-4">
                                    <h2 class="text-lg font-semibold text-gray-900">Mes commandes</h2>
                                    <p class="text-sm text-gray-500">Voir l'historique de mes commandes</p>
                                </div>
                            </div>
                        </a>

                        <a
                            href="{{ route("dashboard.profile.show") }}"
                            class="block rounded-lg border border-gray-200 bg-gray-50 p-6 transition-colors hover:bg-gray-100"
                        >
                            <div class="flex items-center">
                                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                    ></path>
                                </svg>
                                <div class="ml-4">
                                    <h2 class="text-lg font-semibold text-gray-900">Mon profil</h2>
                                    <p class="text-sm text-gray-500">Modifier mes informations personnelles</p>
                                </div>
                            </div>
                        </a>

                        <div class="block cursor-not-allowed rounded-lg border border-gray-200 bg-gray-50 p-6 opacity-50">
                            <div class="flex items-center">
                                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z"
                                    ></path>
                                </svg>
                                <div class="ml-4">
                                    <h2 class="text-lg font-semibold text-gray-900">Mes vélos</h2>
                                    <p class="text-sm text-gray-500">Gérer mes vélos enregistrés</p>
                                </div>
                            </div>
                        </div>

                        <div class="block cursor-not-allowed rounded-lg border border-gray-200 bg-gray-50 p-6 opacity-50">
                            <div class="flex items-center">
                                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"
                                    ></path>
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                    ></path>
                                </svg>
                                <div class="ml-4">
                                    <h2 class="text-lg font-semibold text-gray-900">Services</h2>
                                    <p class="text-sm text-gray-500">Mes demandes de service</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 border-t border-gray-200 pt-6">
                        <form method="POST" action="{{ route("logout") }}">
                            @csrf
                            <x-button type="submit" color="red" size="md">Se déconnecter</x-button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
