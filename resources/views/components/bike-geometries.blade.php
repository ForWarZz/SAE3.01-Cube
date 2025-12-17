@if ($bike != null)
    <div class="mt-16 overflow-hidden rounded-lg border-t border-gray-200 shadow">
        <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
            <h3 class="text-lg font-semibold text-gray-900">Géométrie : {{ $bike->bikeModel->nom_modele_velo }}</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="w-1/4 px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">
                            Caractéristiques / Tailles
                        </th>
                        @foreach ($sizes as $size)
                            <th scope="col" class="px-6 py-3 text-center text-xs font-bold tracking-wider text-gray-700 uppercase">
                                {{ $size->nom_taille }}
                            </th>
                        @endforeach
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 bg-white">
                    @foreach ($geometries as $row)
                        <tr class="transition-colors hover:bg-gray-50">
                            <td class="bg-gray-50/50 px-6 py-4 text-sm font-medium whitespace-nowrap text-gray-900">
                                {{ $row->label }}
                            </td>

                            @foreach ($row->values as $value)
                                <td class="px-6 py-4 text-center text-sm whitespace-nowrap text-gray-500">
                                    {{ $value }}
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
