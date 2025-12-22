<x-commercial-layout>
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
                    <a
                        href="{{ route("commercial.bikes.index") }}"
                        class="rounded-md border bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                    >
                        Annuler
                    </a>
                    <button type="submit" class="rounded-md bg-blue-600 px-5 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                        Enregistrer
                    </button>
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

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
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
                    </div>

                    <div class="space-y-6">
                        <div class="rounded-lg bg-white p-6 shadow-sm">
                            <h2 class="mb-4 text-base font-semibold text-gray-900">Configuration</h2>

                            <div class="mb-4">
                                <label class="mb-1 block text-sm font-medium text-gray-700">Catégorie *</label>
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
                                <label class="mb-1 block text-sm font-medium text-gray-700">Matière *</label>
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
                                :value="old('pourcentage_remise', $accessory->article->pourcentage_remise ?? 0)"
                                min="0"
                                max="100"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-commercial-layout>
