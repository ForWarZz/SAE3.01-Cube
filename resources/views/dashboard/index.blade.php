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
                            id="addresses-nav-link"
                            href="{{ route("dashboard.addresses.index") }}"
                            class="block rounded-lg border border-gray-200 bg-gray-50 p-6 transition-colors hover:bg-gray-100"
                        >
                            <div class="flex items-center">
                                <x-heroicon-o-map-pin class="h-6 w-6 text-blue-600" />
                                <div class="ml-4">
                                    <h2 class="text-lg font-semibold text-gray-900">Mes adresses</h2>
                                    <p class="text-sm text-gray-500">Gérer mes adresses de livraison</p>
                                </div>
                            </div>
                        </a>

                        <a
                            id="orders-nav-link"
                            href="{{ route("dashboard.orders.index") }}"
                            class="block rounded-lg border border-gray-200 bg-gray-50 p-6 transition-colors hover:bg-gray-100"
                        >
                            <div class="flex items-center">
                                <x-heroicon-o-shopping-cart class="h-6 w-6 text-blue-600" />
                                <div class="ml-4">
                                    <h2 class="text-lg font-semibold text-gray-900">Mes commandes</h2>
                                    <p class="text-sm text-gray-500">Voir l'historique de mes commandes</p>
                                </div>
                            </div>
                        </a>

                        <a
                            id="personal-info-nav-link"
                            href="{{ route("dashboard.profile.show") }}"
                            class="block rounded-lg border border-gray-200 bg-gray-50 p-6 transition-colors hover:bg-gray-100"
                        >
                            <div class="flex items-center">
                                <x-heroicon-o-user class="h-6 w-6 text-blue-600" />
                                <div class="ml-4">
                                    <h2 class="text-lg font-semibold text-gray-900">Mon profil</h2>
                                    <p class="text-sm text-gray-500">Modifier mes informations personnelles</p>
                                </div>
                            </div>
                        </a>

                        <div class="block cursor-not-allowed rounded-lg border border-gray-200 bg-gray-50 p-6 opacity-50">
                            <div class="flex items-center">
                                <x-heroicon-o-archive-box class="h-6 w-6 text-blue-600" />
                                <div class="ml-4">
                                    <h2 class="text-lg font-semibold text-gray-900">Mes vélos</h2>
                                    <p class="text-sm text-gray-500">Gérer mes vélos enregistrés</p>
                                </div>
                            </div>
                        </div>

                        <div class="block cursor-not-allowed rounded-lg border border-gray-200 bg-gray-50 p-6 opacity-50">
                            <div class="flex items-center">
                                <x-heroicon-o-cog class="h-6 w-6 text-blue-600" />
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
                            <x-button id="logout-btn" type="submit" color="red" size="md">Se déconnecter</x-button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
