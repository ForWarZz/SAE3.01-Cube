<x-commercial-layout>
    <div x-data="{ showModal: false }">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-3xl font-bold text-gray-800">Gestion des Vélos</h1>
            <a
                href="{{ route("commercial.bikes.create") }}"
                class="flex items-center rounded bg-blue-600 px-4 py-2 text-white shadow transition hover:bg-blue-700"
            >
                <span class="mr-2 text-xl">+</span>
                Nouveau Vélo
            </a>
        </div>

        <x-flash-message key="success" type="success" />

        @if ($errors->any())
            <div class="mb-4 rounded border-l-4 border-red-500 bg-red-100 p-4 text-red-700">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
            <div class="border-b border-gray-200 bg-white p-6">
                <table class="min-w-full text-left text-sm font-light">
                    <thead class="border-b bg-gray-50 font-medium">
                        <tr>
                            <th class="px-4 py-3">ID</th>
                            <th class="px-4 py-3">Nom</th>
                            <th class="px-4 py-3">Modèle</th>
                            <th class="px-4 py-3">Catégorie</th>
                            <th class="px-4 py-3">Prix</th>
                            <th class="px-4 py-3">Promo</th>
                            <th class="px-4 py-3">Millésime</th>
                            <th class="px-4 py-3">Réf.</th>
                            <th class="px-4 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($bikes as $bike)
                            <tr class="border-b transition hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium whitespace-nowrap">{{ $bike->id_article }}</td>
                                <td class="px-4 py-3 font-bold text-gray-700">
                                    {{ $bike->nom_article }}
                                    @if ($bike->isNew())
                                        <span class="ml-2 rounded-full bg-green-100 px-2 py-0.5 text-xs text-green-800">Nouveau</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-gray-600">{{ $bike->bikeModel->nom_modele_velo ?? "-" }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $bike->category->nom_categorie ?? "-" }}</td>
                                <td class="px-4 py-3">
                                    @if ($bike->article && $bike->article->hasDiscount())
                                        <span class="text-gray-400 line-through">{{ number_format($bike->prix_article, 2) }} €</span>
                                        <span class="font-bold text-green-600">
                                            {{ number_format($bike->article->getDiscountedPrice(), 2) }} €
                                        </span>
                                    @else
                                        {{ number_format($bike->prix_article, 2) }} €
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if ($bike->article && $bike->article->pourcentage_remise > 0)
                                        <span class="rounded-full bg-red-100 px-2 py-0.5 text-xs text-red-800">
                                            -{{ $bike->article->pourcentage_remise }}%
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-gray-600">{{ $bike->vintage->millesime_velo ?? "-" }}</td>
                                <td class="px-4 py-3">
                                    <span class="rounded-full bg-blue-100 px-2 py-0.5 text-xs text-blue-800">
                                        {{ $bike->references->count() }} réf.
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex space-x-2">
                                        <a
                                            href="{{ route("commercial.bikes.show", $bike) }}"
                                            class="text-blue-600 hover:text-blue-800"
                                            title="Voir"
                                        >
                                            <x-heroicon-o-eye class="h-5 w-5" />
                                        </a>
                                        <a
                                            href="{{ route("commercial.bikes.edit", $bike) }}"
                                            class="text-yellow-600 hover:text-yellow-800"
                                            title="Modifier"
                                        >
                                            <x-heroicon-o-pencil class="h-5 w-5" />
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-8 text-center text-gray-500">
                                    Aucun vélo enregistré.
                                    <a href="{{ route("commercial.bikes.create") }}" class="text-blue-600 hover:underline">
                                        Créer le premier vélo
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">{{ $bikes->links() }}</div>
            </div>
        </div>
    </div>
</x-commercial-layout>
