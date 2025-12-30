<x-staff-layout>
    <div class="min-h-screen bg-gray-50 pb-12">
        <form action="{{ route("commercial.accessories.update", $accessory) }}" method="POST">
            @csrf
            @method("PUT")

            <div class="mb-6 flex items-center justify-between border-b bg-white px-6 py-4 shadow-sm">
                <div>
                    <h1 class="text-xl font-bold text-gray-900">Modifier {{ $accessory->nom_article }}</h1>
                    <p class="text-sm text-gray-500">Modifier les informations de l'accessoire</p>
                </div>
                <div class="flex gap-3">
                    <x-button :href="route('commercial.bikes.index')" color="gray">Annuler</x-button>
                    <x-button type="submit">Enregistrer</x-button>
                </div>
            </div>

            <div class="px-6">
                @if ($errors->any())
                    <div class="mb-6 rounded-md border border-red-200 bg-red-50 p-4">
                        <h3 class="text-sm font-medium text-red-800">Erreurs de validation</h3>
                        <ul class="mt-2 list-disc pl-5 text-sm text-red-700">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="grid grid-cols-3 gap-6">
                    <div class="space-y-6 lg:col-span-2">
                        <div class="rounded-lg bg-white p-6 shadow-sm">
                            <h2 class="mb-4 border-b pb-2 text-base font-semibold text-gray-900">Informations générales</h2>

                            <x-form-input
                                name="nom_article"
                                label="Nom de l'article"
                                :value="old('nom_article', $accessory->nom_article)"
                                placeholder="Ex: ACID 500"
                                required
                            />

                            <x-form-input
                                type="textarea"
                                name="resumer_article"
                                label="Résumé"
                                :value="old('resumer_article', $accessory->resumer_article)"
                                placeholder="Description courte..."
                                required
                                rows="2"
                            />

                            <x-form-input
                                type="textarea"
                                name="description_article"
                                label="Description complète"
                                :value="old('description_article', $accessory->description_article)"
                                placeholder="Description détaillée..."
                                required
                                rows="4"
                            />
                        </div>

                        <div class="rounded-lg bg-white p-6 shadow-sm">
                            <h2 class="mb-4 border-b pb-2 text-base font-semibold text-gray-900">
                                Tailles disponible
                                <span class="text-red-600">*</span>
                            </h2>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($sizes as $size)
                                    <label class="flex items-center space-x-1">
                                        <input
                                            type="checkbox"
                                            name="sizes[]"
                                            @checked(in_array($size->id_taille, old("sizes", $accessorySizes)))
                                            value="{{ $size->id_taille }}"
                                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                        />
                                        <span class="text-sm">{{ $size->label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="rounded-lg bg-white p-6 shadow-sm">
                            <h2 class="mb-4 text-base font-semibold text-gray-900">Configuration</h2>

                            <div class="mb-4">
                                <label class="mb-1 block text-sm font-medium text-gray-700">
                                    Catégorie
                                    <span class="text-red-600">*</span>
                                </label>
                                <select name="id_categorie" class="block w-full rounded-md border-gray-300 p-2 text-sm">
                                    <option value="">-- Sélectionner --</option>
                                    @foreach ($categories as $cat)
                                        <option
                                            value="{{ $cat->id_categorie }}"
                                            @selected(old("id_categorie", $accessory->id_categorie) == $cat->id_categorie)
                                        >
                                            {{ $cat->getFullPath() }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="mb-1 block text-sm font-medium text-gray-700">
                                    Matière
                                    <span class="text-red-600">*</span>
                                </label>
                                <select name="id_matiere_accessoire" class="block w-full rounded-md border-gray-300 p-2 text-sm">
                                    @foreach ($accessoryMaterials as $material)
                                        <option
                                            value="{{ $material->id_matiere_accessoire }}"
                                            @selected(old("id_matiere_accessoire", $accessory->id_matiere_accessoire) == $material->id_matiere_accessoire)
                                        >
                                            {{ $material->nom_matiere_accessoire }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-4">
                                <x-form-input
                                    type="number"
                                    name="poids_article"
                                    label="Poids du vélo (kg)"
                                    :value="old('poids_velo', $accessory->poids_article)"
                                    placeholder="Ex: 12,5"
                                    required
                                    step="0.1"
                                    min="0"
                                />
                            </div>
                        </div>

                        <div class="rounded-lg bg-white p-6 shadow-sm">
                            <h2 class="mb-4 text-base font-semibold text-gray-900">Tarification</h2>

                            <x-form-input
                                type="number"
                                name="prix_article"
                                label="Prix (€)"
                                :value="old('prix_article', $accessory->prix_article)"
                                placeholder="1876,99 €"
                                step="0.01"
                                min="0"
                                required
                            />

                            <x-form-input
                                type="number"
                                name="pourcentage_remise"
                                label="Remise (%)"
                                :value="old('pourcentage_remise', $accessory->pourcentage_remise ?? 0)"
                                min="0"
                                max="100"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-staff-layout>
