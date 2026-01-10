<x-staff-layout>
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Géométrie des Cadres</h1>
            <p class="mt-1 text-sm text-gray-600">Sélectionnez un modèle pour renseigner sa matrice de géométrie.</p>
        </div>
    </div>

    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
        <div class="border-b border-gray-200 bg-white px-6 py-4">
            <table class="min-w-full text-left text-sm font-light">
                <thead class="border-b bg-gray-50 font-medium text-gray-600">
                    <tr>
                        <th class="px-6 py-4">Modèle</th>
                        <th class="px-6 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($models as $model)
                        <tr class="transition hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <span class="font-bold text-gray-800">{{ $model->nom_modele_velo }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a 
                                    href="{{ route('technical.geometry.edit', $model->id_modele_velo) }}"
                                    class="inline-flex items-center rounded-full bg-gray-100 p-2 text-gray-600 hover:bg-blue-600 hover:text-white transition"
                                    title="Modifier la matrice"
                                >
                                    <x-heroicon-o-pencil class="h-5 w-5" />
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div class="mt-4">
                {{ $models->links() }}
            </div>
        </div>
    </div>
</x-staff-layout>