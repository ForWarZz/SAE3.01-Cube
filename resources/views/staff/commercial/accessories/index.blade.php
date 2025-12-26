<x-staff-layout>
    <div x-data="{ showModal: false }">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-3xl font-bold text-gray-800">Gestion des Accessoires</h1>
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
                            <th class="px-4 py-3">Matière</th>
                            <th class="px-4 py-3">Catégorie</th>
                            <th class="px-4 py-3">Prix</th>
                            <th class="px-4 py-3">Promo</th>
                            <th class="px-4 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($accessories as $accessory)
                            <tr class="border-b transition hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium whitespace-nowrap">{{ $accessory->id_article }}</td>
                                <td class="px-4 py-3 font-bold text-gray-700">
                                    {{ $accessory->nom_article }}
                                </td>
                                <td class="px-4 py-3 text-gray-600">{{ $accessory->material->nom_matiere_accessoire }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $accessory->category->nom_categorie }}</td>
                                <td class="px-4 py-3">
                                    @if ($accessory->hasDiscount())
                                        <span class="text-gray-400 line-through">{{ number_format($accessory->prix_article, 2) }} €</span>
                                        <span class="font-bold text-green-600">
                                            {{ number_format($accessory->getDiscountedPrice(), 2) }} €
                                        </span>
                                    @else
                                        {{ number_format($accessory->prix_article, 2) }} €
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if ($accessory->pourcentage_remise > 0)
                                        <span class="rounded-full bg-red-100 px-2 py-0.5 text-xs text-red-800">
                                            -{{ $accessory->pourcentage_remise }}%
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex space-x-2">
                                        <a
                                            href="{{ route("commercial.accessories.edit", $accessory) }}"
                                            class="text-blue-600 hover:text-blue-800"
                                            title="Modifier"
                                        >
                                            <x-heroicon-o-pencil class="h-5 w-5" />
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-8 text-center text-gray-500">Aucun accessoire trouvé.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">{{ $accessories->links() }}</div>
            </div>
        </div>
    </div>
</x-staff-layout>
