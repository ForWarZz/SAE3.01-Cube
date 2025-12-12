<x-commercial-layout>
    <div x-data="bikeForm()" class="min-h-screen bg-gray-50 pb-12">
        <form action="{{ route("commercial.bikes.store") }}" method="POST">
            @csrf

            <div class="mb-6 flex items-center justify-between border-b bg-white px-6 py-4 shadow-sm">
                <div>
                    <h1 class="text-xl font-bold text-gray-900">Nouveau Vélo</h1>
                    <p class="text-sm text-gray-500">Créer un article vélo avec ses déclinaisons</p>
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
                                        class="mt-1 block w-full rounded-md border-gray-300 p-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
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
                                        class="mt-1 block w-full rounded-md border-gray-300 p-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
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

                        <div class="rounded-lg bg-white p-6 shadow-sm">
                            <div class="mb-4 flex items-center justify-between border-b pb-3">
                                <h2 class="text-base font-semibold text-gray-900">Déclinaisons</h2>
                                <button type="button" @click="addRef()" class="text-sm font-medium text-blue-600 hover:text-blue-800">
                                    + Ajouter
                                </button>
                            </div>

                            <div class="space-y-4">
                                <template x-for="(ref, idx) in refs" :key="idx">
                                    <div class="relative rounded border border-gray-200 bg-gray-50 p-4">
                                        <button
                                            type="button"
                                            @click="removeRef(idx)"
                                            x-show="refs.length > 1"
                                            class="absolute top-2 right-2 text-xs text-gray-400 hover:text-red-500"
                                        >
                                            ✕
                                        </button>

                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="mb-1 block text-xs font-medium text-gray-600">Cadre *</label>
                                                <select
                                                    :name="`references[${idx}][id_cadre_velo]`"
                                                    x-model="ref.id_cadre_velo"
                                                    class="block w-full rounded-md border-gray-300 p-2 text-sm"
                                                >
                                                    <option value="">-- Choisir --</option>
                                                    @foreach ($frames as $frame)
                                                        <option value="{{ $frame->id_cadre_velo }}">
                                                            {{ $frame->label_cadre_velo }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <label class="mb-1 block text-xs font-medium text-gray-600">Couleur *</label>
                                                <select
                                                    :name="`references[${idx}][id_couleur]`"
                                                    x-model="ref.id_couleur"
                                                    class="block w-full rounded-md border-gray-300 p-2 text-sm"
                                                >
                                                    <option value="">-- Choisir --</option>
                                                    @foreach ($colors as $color)
                                                        <option value="{{ $color->id_couleur }}">{{ $color->label_couleur }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div x-show="isVae" x-transition class="mt-3">
                                            <label class="mb-1 block text-xs font-medium text-yellow-700">Batterie *</label>
                                            <select
                                                :name="`references[${idx}][id_batterie]`"
                                                x-model="ref.id_batterie"
                                                class="block w-full rounded-md border-gray-300 p-2 text-sm"
                                            >
                                                <option value="">-- Choisir --</option>
                                                @foreach ($batteries as $battery)
                                                    <option value="{{ $battery->id_batterie }}">{{ $battery->label_batterie }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="mt-3">
                                            <label class="mb-2 block text-xs font-medium text-gray-600">Tailles *</label>
                                            <div class="flex flex-wrap gap-2">
                                                @foreach ($sizes as $size)
                                                    <label
                                                        class="cursor-pointer rounded border bg-white px-3 py-1 text-sm hover:bg-gray-50 has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50 has-[:checked]:text-blue-600"
                                                    >
                                                        <input
                                                            type="checkbox"
                                                            :name="`references[${idx}][sizes][]`"
                                                            x-model="ref.sizes"
                                                            value="{{ $size->id_taille }}"
                                                            class="sr-only"
                                                        />
                                                        {{ $size->nom_taille }}
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
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
                                        @click="isVae = !isVae"
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
                                <select name="id_categorie" class="block w-full rounded-md border-gray-300 p-2 text-sm">
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id_categorie }}" @selected(old("id_categorie") == $cat->id_categorie)>
                                            {{ $cat->getFullPath() }}
                                        </option>
                                    @endforeach
                                </select>
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
            const emptyRow = {
                id_cadre_velo: '',
                id_couleur: '',
                id_batterie: '',
                sizes: [],
            };

            return {
                modelChoice: '{{ old("model_choice", "existing") }}',
                isVae: {{ old("is_vae", 0) == 1 ? "true" : "false" }},
                refs: @json(old("references", [""])),

                addRef() {
                    this.refs.push(emptyRow);
                },

                removeRef(idx) {
                    if (this.refs.length > 1) this.refs.splice(idx, 1);
                },
            };
        }
    </script>
</x-commercial-layout>
