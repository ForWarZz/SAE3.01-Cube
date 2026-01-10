<x-staff-layout>
    <div x-data="{ showAddModal: false }">
        
        <div class="mb-6 flex items-center justify-between">
            <div>
                <a href="{{ route('technical.geometry.index') }}" class="mb-2 inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700">
                    <x-heroicon-o-arrow-left class="mr-1 h-4 w-4" /> Retour
                </a>
                <h1 class="text-3xl font-bold text-gray-800">
                    Géométrie : <span class="text-blue-600">{{ $model->nom_modele_velo }}</span>
                </h1>
            </div>
            
            <div class="flex gap-3">
                <x-button @click="showAddModal = true" type="button" class="!bg-white !text-gray-700 border border-gray-300 hover:!bg-gray-50">
                    <x-heroicon-o-plus class="mr-2 h-5 w-5" />
                    Ajouter une caractéristique
                </x-button>

                <x-button form="geometry-form" type="submit" class="!bg-green-600 hover:!bg-green-700">
                    <x-heroicon-o-check class="mr-2 h-5 w-5" />
                    Enregistrer
                </x-button>
            </div>
        </div>

        <x-flash-message key="success" type="success" />

        <div class="relative overflow-hidden rounded-lg border border-gray-300 bg-white shadow-sm">
            <form id="geometry-form" action="{{ route('technical.geometry.update', $model->id_modele_velo) }}" method="POST">
                @csrf
                
                <div class="max-h-[75vh] overflow-auto">
                    <table class="min-w-full border-collapse text-left text-sm">
                        <thead class="bg-gray-100 text-gray-700">
                            <tr>
                                <th class="sticky left-0 top-0 z-20 min-w-[250px] border-b border-r border-gray-300 bg-gray-100 p-4 font-bold uppercase tracking-wider text-gray-500 shadow-sm">
                                    Caractéristiques / Tailles
                                </th>
                                @foreach ($sizes as $size)
                                    <th class="sticky top-0 z-10 min-w-[100px] border-b border-gray-300 bg-gray-100 p-4 text-center font-bold text-gray-800">
                                        {{ $size->nom_taille }}
                                        <div class="text-xs font-normal text-gray-500 whitespace-nowrap">
                                            {{ $size->taille_min }}-{{ $size->taille_max }}
                                        </div>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @if($matrixCharacteristics->isEmpty())
                                <tr>
                                    <td colspan="{{ count($sizes) + 1 }}" class="p-8 text-center text-gray-500 italic">
                                        Aucune donnée de géométrie. Cliquez sur "Ajouter une mesure" pour commencer.
                                    </td>
                                </tr>
                            @endif

                            @foreach ($matrixCharacteristics as $geo)
                                <tr x-data class="group hover:bg-blue-50" x-ref="row_{{ $geo->id_carac_geo }}">
                                    <td class="sticky left-0 z-10 flex items-center justify-between border-r border-gray-200 bg-white p-3 font-medium text-gray-900 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)] group-hover:bg-blue-50">
                                        <span>{{ $geo->label_carac_geo }}</span>
                                        
                                        <button 
                                            type="button" 
                                            @click="$refs.row_{{ $geo->id_carac_geo }}.remove()"
                                            class="opacity-0 transition-opacity hover:text-red-600 group-hover:opacity-100"
                                            title="Retirer cette ligne"
                                        >
                                            <x-heroicon-o-trash class="h-4 w-4" />
                                        </button>
                                    </td>

                                    @foreach ($sizes as $size)
                                        <td class="border-r border-gray-100 p-1">
                                            <input 
                                                type="number" 
                                                step="0.1" 
                                                name="geo[{{ $geo->id_carac_geo }}][{{ $size->id_taille }}]"
                                                value="{{ $matrix[$geo->id_carac_geo][$size->id_taille] ?? '' }}"
                                                class="h-10 w-full border-none bg-transparent text-center text-gray-700 focus:bg-white focus:ring-2 focus:ring-inset focus:ring-blue-500"
                                                placeholder="-"
                                            >
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
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
            @keydown.escape.window="showAddModal = false"
        >
            <div class="mx-4 w-full max-w-lg rounded-lg bg-white p-6 shadow-xl" @click.away="showAddModal = false">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-xl font-bold text-gray-800">Ajouter une ligne</h3>
                    <button @click="showAddModal = false" class="text-gray-500 hover:text-gray-700">✕</button>
                </div>

                <form action="{{ route('technical.geometry.add', $model->id_modele_velo) }}" method="POST" x-data="{ tab: 'existing' }">
                    @csrf
                    
                    <div class="mb-4 flex border-b border-gray-200">
                        <button 
                            type="button" 
                            @click="tab = 'existing'" 
                            :class="tab === 'existing' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                            class="flex-1 border-b-2 py-2 text-center font-medium"
                        >
                            Existante
                        </button>
                        <button 
                            type="button" 
                            @click="tab = 'new'" 
                            :class="tab === 'new' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                            class="flex-1 border-b-2 py-2 text-center font-medium"
                        >
                            Créer nouvelle
                        </button>
                    </div>

                    <input type="hidden" name="action_type" :value="tab">

                    <div x-show="tab === 'existing'" class="space-y-4">
                        <p class="text-sm text-gray-600">Choisissez une caractéristique déjà utilisée sur d'autres vélos.</p>
                        <select name="existing_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @foreach ($availableCharacteristics as $c)
                                <option value="{{ $c->id_carac_geo }}">{{ $c->label_carac_geo }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div x-show="tab === 'new'" class="space-y-4">
                        <p class="text-sm text-gray-600">Créez une nouvelle définition (ex: "Stack", "Reach", "Angle tube selle").</p>
                        <input 
                            type="text" 
                            name="new_name" 
                            placeholder="Nom de la mesure..." 
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <x-button @click="showAddModal = false" type="button" color="gray">Annuler</x-button>
                        <x-button type="submit">Ajouter</x-button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-staff-layout>