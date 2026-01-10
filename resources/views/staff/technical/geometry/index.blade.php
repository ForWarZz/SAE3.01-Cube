<x-staff-layout>
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Géométrie des Cadres</h1>
            <p class="mt-1 text-sm text-gray-600">Sélectionnez un modèle pour renseigner sa matrice de géométrie.</p>
        </div>
    </div>

    <div class="overflow-hidden rounded-lg bg-white shadow-sm">
        <div class="border-b border-gray-200 bg-white p-6">
            <table class="min-w-full text-left text-sm font-light">
                <thead class="border-b bg-gray-50 font-medium">
                    <tr>
                        <th class="px-4 py-3">Modèle</th>
                        <th class="px-4 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($models as $model)
                        <tr class="border-b transition hover:bg-gray-50">
                            <td class="px-4 py-3 font-bold text-gray-700">
                                {{ $model->nom_modele_velo }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a
                                    href="{{ route("technical.geometry.edit", $model->id_modele_velo) }}"
                                    class="text-blue-600 hover:text-blue-800"
                                    title="Modifier la matrice"
                                >
                                    <x-heroicon-o-pencil class="inline-block h-5 w-5" />
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
