<x-staff-layout>
    <div x-data="{ showAddModal: false, activeTab: 'existing' }" class="flex h-full flex-col">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <a
                    href="{{ route("technical.geometry.index") }}"
                    class="mb-1 flex items-center gap-1 text-sm text-gray-500 transition hover:text-blue-600"
                >
                    <x-heroicon-o-arrow-left class="size-4" />
                    Retour
                </a>
                <h1 class="text-2xl font-bold text-gray-800">
                    Géométrie :
                    <span class="text-blue-600">{{ $model->nom_modele_velo }}</span>
                </h1>
            </div>

            <div class="flex items-center gap-3">
                <x-button @click="showAddModal = true" variant="secondary" icon="heroicon-o-plus">Ajouter une caractéristique</x-button>

                <x-button form="geometry-form" type="submit" variant="primary" icon="heroicon-o-check">
                    Enregistrer les modifications
                </x-button>
            </div>
        </div>

        <x-flash-message key="success" type="success" />
        <x-flash-message key="error" type="error" />

        <div class="flex-1 overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
            <form
                id="geometry-form"
                action="{{ route("technical.geometry.update", $model->id_modele_velo) }}"
                method="POST"
                class="flex h-full flex-col"
            >
                @csrf

                <div class="flex-1 overflow-auto">
                    <table class="w-full border-collapse text-sm">
                        <thead class="sticky top-0 z-20 bg-gray-50 text-xs font-semibold tracking-wider text-gray-600 uppercase shadow-sm">
                            <tr>
                                <th class="sticky left-0 z-30 border-r border-b border-gray-200 bg-gray-50 p-4 text-left">
                                    Mesure / Taille
                                </th>
                                @foreach ($sizes as $size)
                                    <th class="w-52 border-b border-gray-200 p-4 text-center">
                                        <div class="text-gray-900">{{ $size->nom_taille }}</div>
                                        <div class="text-xs font-normal text-gray-400">
                                            {{ $size->taille_min }}-{{ $size->taille_max }}
                                        </div>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @if ($matrixCharacteristics->isEmpty())
                                <tr>
                                    <td colspan="{{ count($sizes) + 1 }}" class="p-10 text-center text-gray-400">
                                        Aucune donnée configurée.
                                    </td>
                                </tr>
                            @endif

                            @foreach ($matrixCharacteristics as $geo)
                                <tr x-data class="group transition-colors hover:bg-blue-50">
                                    <td
                                        class="sticky left-0 z-10 flex items-center justify-between border-r border-gray-200 bg-white p-3 font-medium text-gray-700 shadow-[4px_0_8px_-4px_rgba(0,0,0,0.05)] group-hover:bg-blue-50"
                                    >
                                        <span>{{ $geo->label_carac_geo }}</span>
                                        <button
                                            type="button"
                                            @click="$el.closest('tr').remove()"
                                            class="text-gray-300 opacity-0 transition-all group-hover:opacity-100 hover:text-red-500"
                                            title="Supprimer cette ligne"
                                        >
                                            <x-heroicon-o-trash class="size-5" />
                                        </button>
                                    </td>

                                    @foreach ($sizes as $size)
                                        <td class="border-r border-gray-100 p-0 last:border-r-0">
                                            <input
                                                name="geo[{{ $geo->id_carac_geo }}][{{ $size->id_taille }}]"
                                                value="{{ $matrix[$geo->id_carac_geo][$size->id_taille] ?? "" }}"
                                                class="h-12 w-full border-none bg-transparent text-center text-gray-700 placeholder-gray-200 outline-none focus:bg-white focus:ring-2 focus:ring-blue-500 focus:ring-inset"
                                                placeholder="-"
                                            />
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </form>
        </div>

        <div
            x-show="showAddModal"
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
            @keydown.escape.window="showAddModal = false"
        >
            <div class="w-[500px] overflow-hidden rounded-xl bg-white shadow-2xl" @click.away="showAddModal = false">
                <div class="flex items-center justify-between border-b border-gray-100 bg-gray-50 px-6 py-4">
                    <h3 class="font-bold text-gray-800">Ajouter une ligne</h3>
                    <button @click="showAddModal = false" class="text-gray-400 hover:text-gray-600">
                        <x-heroicon-o-x-mark class="h-5 w-5" />
                    </button>
                </div>

                <form action="{{ route("technical.geometry.add", $model->id_modele_velo) }}" method="POST" class="p-6">
                    @csrf
                    <input type="hidden" name="action_type" :value="activeTab" />

                    <div class="mb-4 flex border-b border-gray-200">
                        <button
                            type="button"
                            @click="activeTab = 'existing'"
                            :class="activeTab === 'existing' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                            class="flex-1 border-b-2 py-2 text-center font-medium"
                        >
                            Existante
                        </button>
                        <button
                            type="button"
                            @click="activeTab = 'new'"
                            :class="activeTab === 'new' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                            class="flex-1 border-b-2 py-2 text-center font-medium"
                        >
                            Créer nouvelle
                        </button>
                    </div>

                    <div>
                        <div x-show="activeTab === 'existing'" class="space-y-3">
                            <label class="block text-sm font-medium text-gray-700">Sélectionner une mesure</label>
                            <select
                                name="existing_id"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                                @foreach ($availableCharacteristics as $c)
                                    <option value="{{ $c->id_carac_geo }}">{{ $c->label_carac_geo }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div x-show="activeTab === 'new'" class="space-y-3">
                            <label class="block text-sm font-medium text-gray-700">Nom de la nouvelle mesure</label>
                            <input
                                type="text"
                                name="new_name"
                                placeholder="Ex: Stack, Reach..."
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            />
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3 border-t border-gray-100 pt-4">
                        <x-button @click="showAddModal = false" type="button" variant="secondary">Annuler</x-button>
                        <x-button type="submit" variant="primary">Ajouter</x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-staff-layout>
