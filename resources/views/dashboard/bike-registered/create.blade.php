<x-app-layout>
    <div class="m-auto max-w-2xl px-8">
        <div class="overflow-hidden rounded-lg bg-white shadow-sm">
            <div class="p-6">
                <div class="mb-6">
                    <a
                        href="{{ route("dashboard.bike-registered.index") }}"
                        class="mb-2 inline-flex items-center text-sm text-blue-600 hover:text-blue-800"
                    >
                        <x-heroicon-o-arrow-left class="mr-1 h-4 w-4" />
                        Retour à mes vélos
                    </a>

                    <h1 class="text-2xl font-bold text-gray-900">
                        {{ $isEdit ? "Modifier mon vélo" : "Enregistrer mon vélo" }}
                    </h1>
                    <p class="mt-2 text-gray-600">
                        {{ $isEdit ? "Modifiez les informations de votre vélo et mettez à jour sa facture si nécessaire." : 'Prenez quelques minutes pour compléter le formulaire et enregistrer votre vélo à l\'Assistance CUBE en ligne.' }}
                    </p>
                </div>

                <x-flash-message key="success" type="success" />

                <form
                    method="POST"
                    enctype="multipart/form-data"
                    action="{{
                        $isEdit
                            ? route("dashboard.bike-registered.update", $bike)
                            : route("dashboard.bike-registered.store")
                    }}"
                >
                    @csrf
                    @if ($isEdit)
                        @method("PUT")
                    @endif

                    <div class="mb-6 border-b border-gray-200 pb-2">
                        <h2 class="text-lg font-semibold text-gray-900">Mon vélo</h2>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="id_magasin" value="Votre magasin" required class="mb-1" />
                            <select
                                name="id_magasin"
                                id="id_magasin"
                                class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                                required
                            >
                                <option value="">Sélectionnez le magasin</option>
                                @foreach ($shops as $shop)
                                    <option
                                        value="{{ $shop->id_magasin }}"
                                        {{ old("id_magasin", $bike->id_magasin ?? null) == $shop->id_magasin ? "selected" : "" }}
                                    >
                                        {{ $shop->nom_magasin }} - {{ $shop->city->nom_ville }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('id_magasin')" class="mt-1" />
                        </div>

                        <div>
                            <x-form-input
                                name="num_serie_velo_enr"
                                label="Numéro de série du cadre"
                                placeholder="Ex: WOW12345678"
                                :value="old('num_serie_velo_enr', $bike->num_serie_velo_enr ?? '')"
                            />
                        </div>

                        <div>
                            <x-input-label for="date_achat_velo_enr" value="Date d'achat" class="mb-1" />
                            <input
                                type="date"
                                name="date_achat_velo_enr"
                                id="date_achat_velo_enr"
                                value="{{ old("date_achat_velo_enr", isset($bike) ? $bike->date_achat_velo_enr?->format("Y-m-d") : "") }}"
                                max="{{ date("Y-m-d") }}"
                                class="w-full rounded-lg border border-gray-300 p-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                            />
                            <x-input-error :messages="$errors->get('date_achat_velo_enr')" class="mt-1" />
                        </div>

                        <div>
                            <x-form-input
                                name="millesime_velo_enr"
                                label="Millésime"
                                placeholder="Ex: 2022"
                                :value="old('millesime_velo_enr', $bike->millesime_velo_enr ?? '')"
                            />
                        </div>
                    </div>

                    <div class="mt-6" x-data="{ fileName: null }">
                        <label for="facture" class="block text-sm font-medium text-gray-700">
                            Votre facture
                            <span class="text-red-500">*</span>
                        </label>

                        <label
                            for="facture"
                            class="mt-2 flex cursor-pointer items-center justify-center rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 px-6 py-4 text-sm text-gray-600 transition hover:border-blue-400 hover:bg-blue-50"
                            :class="fileName && 'border-green-400 bg-green-50 text-green-700'"
                        >
                            <template x-if="!fileName">
                                <div class="flex items-center">
                                    <x-heroicon-o-document-arrow-up class="mr-2 size-5 text-gray-400" />
                                    <span>Choisir un fichier PDF</span>
                                </div>
                            </template>

                            <template x-if="fileName">
                                <div class="flex items-center">
                                    <x-heroicon-s-check-circle class="mr-2 size-5 text-green-500" />
                                    <span x-text="fileName"></span>
                                </div>
                            </template>

                            <input
                                id="facture"
                                name="facture"
                                type="file"
                                accept=".pdf"
                                {{ $isEdit ? "" : "required" }}
                                class="sr-only"
                                @change="fileName = $event.target.files[0]?.name"
                            />
                        </label>

                        <p class="mt-1 text-xs text-gray-500">
                            PDF uniquement · 5 Mo max
                            @if ($isEdit)
                                · Laisser vide pour conserver la facture actuelle
                            @endif
                        </p>

                        <x-input-error :messages="$errors->get('facture')" class="mt-1" />
                    </div>

                    <div class="mt-8">
                        <x-button type="submit" size="lg" class="w-full" icon="heroicon-o-check">
                            {{ $isEdit ? "Mettre à jour le vélo" : "Enregistrer mon vélo" }}
                        </x-button>
                        <p class="mt-2 text-center text-xs text-gray-500">* Champs obligatoires</p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
