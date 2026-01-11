<x-app-layout>
    <div class="m-auto max-w-2xl px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="mb-6">
                    <a
                        href="{{ route("dashboard.bike-registered.index") }}"
                        class="mb-2 inline-flex items-center text-sm text-blue-600 hover:text-blue-800"
                    >
                        <x-heroicon-o-arrow-left class="mr-1 h-4 w-4" />
                        Retour à mes vélos
                    </a>
                    <h1 class="text-2xl font-bold text-gray-900">Enregistrer mon vélo</h1>
                    <p class="mt-2 text-gray-600">
                        Prenez quelques minutes pour compléter le formulaire et enregistrer votre vélo à l'Assistance CUBE en ligne.
                    </p>
                </div>

                {{-- <div class="mb-6 rounded-lg border border-blue-200 bg-blue-50 p-4"> --}}
                {{-- <div class="flex items-start"> --}}
                {{-- <x-heroicon-o-information-circle class="mt-0.5 h-5 w-5 flex-shrink-0 text-blue-500" /> --}}
                {{-- <div class="ml-3"> --}}
                {{-- <h3 class="font-semibold text-blue-800">Pourquoi enregistrer mon vélo ?</h3> --}}
                {{-- <p class="mt-1 text-sm text-blue-700"> --}}
                {{-- Cette procédure simple et rapide vous permettra de bénéficier d'un contact facilité avec nos services en --}}
                {{-- cas de problème avec votre vélo. --}}
                {{-- </p> --}}
                {{-- </div> --}}
                {{-- </div> --}}
                {{-- </div> --}}

                {{--  --}}
                {{-- Information sur l'éligibilité --}}
                {{-- <div class="mb-6 rounded-lg border border-yellow-200 bg-yellow-50 p-4"> --}}
                {{-- <div class="flex items-start"> --}}
                {{-- <x-heroicon-o-exclamation-triangle class="mt-0.5 h-5 w-5 flex-shrink-0 text-yellow-500" /> --}}
                {{-- <div class="ml-3"> --}}
                {{-- <h3 class="font-semibold text-yellow-800">Conditions d'éligibilité</h3> --}}
                {{-- <ul class="mt-1 list-inside list-disc text-sm text-yellow-700"> --}}
                {{-- <li>Seuls les vélos achetés auprès d'un revendeur agréé CUBE France sont éligibles</li> --}}
                {{-- <li>Les vélos dont le millésime est de moins de 5 ans bénéficient du contrat d'assistance</li> --}}
                {{-- <li> --}}
                {{-- Sont exclus les vélos achetés auprès d'un revendeur étranger ou d'un site Internet appartenant à une --}}
                {{-- société étrangère --}}
                {{-- </li> --}}
                {{-- </ul> --}}
                {{-- <a --}}
                {{-- href="{{ route("shops.index") }}" --}}
                {{-- class="mt-2 inline-flex items-center text-sm font-medium text-yellow-800 hover:text-yellow-900" --}}
                {{-- > --}}
                {{-- <x-heroicon-o-map-pin class="mr-1 h-4 w-4" /> --}}
                {{-- Voir la liste des magasins agréés --}}
                {{-- </a> --}}
                {{-- </div> --}}
                {{-- </div> --}}
                {{-- </div> --}}

                <form method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-6 border-b border-gray-200 pb-2">
                        <h2 class="text-lg font-semibold text-gray-900">Mon vélo</h2>
                    </div>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
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
                                        {{ old("id_magasin") == $shop->id_magasin ? "selected" : "" }}
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
                                :value="old('num_serie_velo_enr')"
                            />
                        </div>

                        <div>
                            <x-input-label for="date_achat_velo_enr" value="Date d'achat" required class="mb-1" />
                            <input
                                type="date"
                                name="date_achat_velo_enr"
                                id="date_achat_velo_enr"
                                value="{{ old("date_achat_velo_enr") }}"
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
                                :value="old('millesime_velo_enr')"
                                required
                            />
                        </div>
                    </div>

                    <div class="mt-6">
                        <label for="facture" class="block text-sm font-medium text-gray-700">
                            Votre facture
                            <span class="text-red-500">*</span>
                        </label>

                        <label
                            for="facture"
                            class="mt-2 flex cursor-pointer items-center justify-center rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 px-6 py-4 text-sm text-gray-600 transition hover:border-blue-400 hover:bg-blue-50"
                        >
                            <x-heroicon-o-document-arrow-up class="mr-2 size-5 text-gray-400" />
                            <span>Choisir un fichier PDF</span>
                            <input id="facture" name="facture" type="file" accept=".pdf" required class="sr-only" />
                        </label>

                        <p class="mt-1 text-xs text-gray-500">PDF uniquement · 5 Mo max</p>

                        <x-input-error :messages="$errors->get('facture')" class="mt-1" />
                    </div>

                    <div class="mt-8">
                        <x-button type="submit" size="lg" class="w-full" icon="heroicon-o-check">Enregistrer mon vélo</x-button>
                        <p class="mt-2 text-center text-xs text-gray-500">* Champs obligatoires</p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
