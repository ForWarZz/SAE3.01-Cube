<x-staff-layout>
    <div x-data="bikeForm()" x-effect="onModelChoiceChange()" class="min-h-screen bg-gray-50 pb-12">
        <form action="{{ route("commercial.bikes.store") }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-6 flex items-center justify-between border-b bg-white px-6 py-4 shadow-sm">
                <div>
                    <h1 class="text-xl font-bold text-gray-900">Nouveau Vélo</h1>
                    <p class="text-sm text-gray-500">Créer un article vélo avec ses déclinaisons</p>
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

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                    <div class="space-y-6 lg:col-span-2">
                        <div class="rounded-lg bg-white p-6 shadow-sm">
                            <h2 class="mb-4 border-b pb-2 text-base font-semibold text-gray-900">Informations générales</h2>

                            <div class="mb-4">
                                <label class="mb-2 block text-sm font-medium text-gray-700">
                                    Modèle de vélo
                                    <span class="text-red-600">*</span>
                                </label>
                                <div class="mb-2 flex gap-4">
                                    <label class="inline-flex items-center text-sm">
                                        <input
                                            type="radio"
                                            name="model_choice"
                                            value="existing"
                                            x-model="modelChoice"
                                            class="text-blue-600"
                                        />
                                        <span class="ml-2">Existant</span>
                                    </label>
                                    <label class="inline-flex items-center text-sm">
                                        <input
                                            type="radio"
                                            name="model_choice"
                                            value="new"
                                            x-model="modelChoice"
                                            class="text-blue-600"
                                        />
                                        <span class="ml-2">Nouveau</span>
                                    </label>
                                </div>

                                <div x-show="modelChoice === 'existing'">
                                    <select
                                        name="id_modele_velo"
                                        x-model="selectedModel"
                                        @change="applyModelCategory()"
                                        class="mt-1 block w-full rounded-md border-gray-300 p-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                    >
                                        <option value="">-- Sélectionner --</option>
                                        @foreach ($models as $model)
                                            <option
                                                value="{{ $model->id_modele_velo }}"
                                                @selected(old("id_modele_velo") == $model->id_modele_velo)
                                            >
                                                {{ $model->nom_modele_velo }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div x-show="modelChoice === 'new'">
                                    <input
                                        type="text"
                                        name="new_model_name"
                                        value="{{ old("new_model_name") }}"
                                        placeholder="Nom du nouveau modèle"
                                        class="mt-1 block w-full rounded-md border-gray-300 p-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                    />
                                </div>
                            </div>

                            <x-form-input
                                name="nom_article"
                                label="Nom de l'article"
                                :value="old('nom_article')"
                                placeholder="Ex: VTT Trail 500 Carbone 2024"
                                required
                            />

                            <x-form-input
                                type="textarea"
                                name="resumer_article"
                                label="Résumé"
                                :value="old('resumer_article')"
                                placeholder="Description courte..."
                                required
                                rows="2"
                            />

                            <x-form-input
                                type="textarea"
                                name="description_article"
                                label="Description complète"
                                :value="old('description_article')"
                                placeholder="Description détaillée..."
                                required
                                rows="4"
                            />
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="rounded-lg bg-white p-6 shadow-sm">
                            <h2 class="mb-4 text-base font-semibold text-gray-900">Configuration</h2>

                            <div class="mb-4 rounded-md border border-gray-200 bg-gray-50 p-3">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <span class="text-sm font-medium text-gray-900">Vélo Électrique (VAE)</span>
                                        <p class="text-xs text-gray-500">Nécessite une batterie par référence</p>
                                    </div>
                                    <button
                                        type="button"
                                        @click="toggleVae()"
                                        :class="isVae ? 'bg-blue-600' : 'bg-gray-200'"
                                        class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors"
                                    >
                                        <span
                                            :class="isVae ? 'translate-x-5' : 'translate-x-0'"
                                            class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow transition"
                                        ></span>
                                    </button>
                                    <input type="hidden" name="is_vae" :value="isVae ? 1 : 0" />
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="mb-1 block text-sm font-medium text-gray-700">Catégorie *</label>
                                <select
                                    x-model="selectedCategory"
                                    :disabled="categoryLocked"
                                    name="id_categorie"
                                    class="block w-full rounded-md border-gray-300 p-2 text-sm"
                                >
                                    <option value="">-- Sélectionner --</option>
                                    <template x-for="cat in filteredCategories" :key="cat.id">
                                        <option :value="cat.id" x-text="cat.path"></option>
                                    </template>
                                </select>
                                <p x-show="categoryLocked" class="mt-1 text-xs text-gray-500 italic">
                                    Catégorie verrouillée par le modèle.
                                </p>

                                <input name="id_categorie" hidden x-model="selectedCategory" x-show="categoryLocked" />
                            </div>

                            <div class="mb-4">
                                <label class="mb-1 block text-sm font-medium text-gray-700">Matériau cadre *</label>
                                <select name="id_materiau_cadre" class="block w-full rounded-md border-gray-300 p-2 text-sm">
                                    @foreach ($materials as $mat)
                                        <option
                                            value="{{ $mat->id_materiau_cadre }}"
                                            @selected(old("id_materiau_cadre") == $mat->id_materiau_cadre)
                                        >
                                            {{ $mat->label_materiau_cadre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="mb-1 block text-sm font-medium text-gray-700">Millésime *</label>
                                <select name="id_millesime" class="block w-full rounded-md border-gray-300 p-2 text-sm">
                                    @foreach ($vintages as $vin)
                                        <option value="{{ $vin->id_millesime }}" @selected(old("id_millesime") == $vin->id_millesime)>
                                            {{ $vin->millesime_velo }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="mb-1 block text-sm font-medium text-gray-700">Usage *</label>
                                <select name="id_usage" class="block w-full rounded-md border-gray-300 p-2 text-sm">
                                    @foreach ($usages as $use)
                                        <option value="{{ $use->id_usage }}" @selected(old("id_usage") == $use->id_usage)>
                                            {{ $use->label_usage }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div x-show="isVae">
                                <label class="mb-1 block text-sm font-medium text-gray-700">Type VAE *</label>
                                <select name="id_type_vae" class="block w-full rounded-md border-gray-300 p-2 text-sm">
                                    @foreach ($eBikeTypes as $type)
                                        <option value="{{ $type->id_type_vae }}" @selected(old("id_type_vae") == $type->id_type_vae)>
                                            {{ $type->label_type_vae }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mt-4">
                                <x-form-input
                                    type="number"
                                    name="poids_article"
                                    label="Poids du vélo (kg)"
                                    :value="old('poids_velo')"
                                    placeholder="Ex: 12,5"
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
                                :value="old('prix_article')"
                                placeholder="1876,99 €"
                                step="0.01"
                                min="0"
                                required
                            />

                            <x-form-input
                                type="number"
                                name="pourcentage_remise"
                                label="Remise (%)"
                                :value="old('pourcentage_remise', 0)"
                                min="0"
                                max="100"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        function bikeForm() {
            const modelsCategory = @json($modelsCategory);
            const bikeCategories = @json($bikeCategories->map(fn ($cat) => ["id" => $cat->id_categorie, "path" => $cat->getFullPath()])->values());
            const eBikeCategories = @json($eBikeCategories->map(fn ($cat) => ["id" => $cat->id_categorie, "path" => $cat->getFullPath()])->values());

            return {
                modelChoice: '{{ old("model_choice", "existing") }}',
                isVae: {{ old("is_vae", 0) == 1 ? "true" : "false" }},
                selectedModel: '{{ old("id_modele_velo") }}',
                selectedCategory: '{{ old("id_categorie") }}',
                categoryLocked: false,

                get filteredCategories() {
                    return this.isVae ? eBikeCategories : bikeCategories;
                },

                init() {
                    this.applyModelCategory();
                },

                toggleVae() {
                    this.isVae = !this.isVae;

                    if (!this.categoryLocked) {
                        const currentCatInList = this.filteredCategories.find((c) => c.id == this.selectedCategory);
                        if (!currentCatInList) {
                            this.selectedCategory = '';
                        }
                    }
                },

                applyModelCategory() {
                    console.log('Applying category for model:', this.selectedModel);

                    if (this.modelChoice !== 'existing') {
                        this.categoryLocked = false;
                        return;
                    }

                    const cat = modelsCategory[this.selectedModel] ?? null;

                    if (cat) {
                        this.selectedCategory = cat;
                        this.categoryLocked = true;
                    } else {
                        this.categoryLocked = false;
                    }
                },

                onModelChoiceChange() {
                    if (this.modelChoice !== 'existing') {
                        this.categoryLocked = false;
                        return;
                    }

                    this.applyModelCategory();
                },
            };
        }
    </script>
</x-staff-layout>
