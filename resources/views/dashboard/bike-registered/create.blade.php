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
                                wrapper-class="mb-1"
                                name="num_serie_velo_enr"
                                label="Numéro de série du cadre"
                                placeholder="Ex: WOW12345678"
                                :value="old('num_serie_velo_enr', $bike->num_serie_velo_enr ?? '')"
                            />
                            <a
                                href="#"
                                id="open-serial-modal"
                                class="inline-block text-sm font-medium text-blue-600 hover:text-blue-800 hover:underline"
                            >
                                Où trouver mon numéro ?
                            </a>
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

    <div id="serial-modal" class="fixed inset-0 z-50 hidden bg-gray-500/75">
        <div class="flex min-h-screen items-center justify-center px-4">
            <div class="w-full max-w-lg overflow-hidden rounded-lg bg-white shadow-xl">
                <div class="p-6">
                    <div class="flex items-start gap-4">
                        <div class="flex size-10 shrink-0 items-center justify-center rounded-full bg-blue-100">
                            <x-heroicon-o-question-mark-circle class="size-6 text-blue-600" />
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Où trouver le numéro de série ?</h3>

                            <p class="mt-3 text-sm text-gray-600">
                                Le numéro de série de votre vélo CUBE se trouve
                                <strong>sous le cadre</strong>
                                , au niveau du
                                <strong>boîtier de pédalier</strong>
                                .
                            </p>

                            <div class="mt-4 flex justify-center rounded-lg bg-gray-100 p-4">
                                <svg class="h-32 w-48" viewBox="0 0 200 100" fill="none">
                                    <path d="M40 70 L80 30 L140 30 L160 70" stroke="#9CA3AF" stroke-width="3" />
                                    <path d="M80 30 L80 70" stroke="#9CA3AF" stroke-width="3" />
                                    <path d="M80 70 L140 30" stroke="#9CA3AF" stroke-width="3" />
                                    <circle cx="40" cy="70" r="20" stroke="#9CA3AF" stroke-width="2" />
                                    <circle cx="160" cy="70" r="20" stroke="#9CA3AF" stroke-width="2" />
                                    <circle cx="80" cy="70" r="8" fill="#EF4444" opacity="0.3" />
                                    <circle cx="80" cy="70" r="5" fill="#EF4444" />
                                    <text x="60" y="95" fill="#EF4444" font-size="10" font-weight="bold">N° série ici</text>
                                </svg>
                            </div>

                            <div class="mt-4 rounded-lg border bg-gray-50 p-3 text-sm text-gray-600">
                                <p>
                                    <strong>Format :</strong>
                                    commence généralement par
                                    <code class="rounded bg-gray-200 px-1 font-mono text-xs">WOW</code>
                                </p>
                                <p class="mt-1">
                                    <strong>Exemple :</strong>
                                    <code class="rounded bg-gray-200 px-1 font-mono text-xs">WOW12345678901</code>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-6 py-3 text-right">
                    <x-button id="close-serial-modal">Fermer</x-button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const modal = document.getElementById('serial-modal');
        const openBtn = document.getElementById('open-serial-modal');
        const closeBtn = document.getElementById('close-serial-modal');

        openBtn.addEventListener('click', (e) => {
            e.preventDefault();
            modal.classList.remove('hidden');
        });

        closeBtn.addEventListener('click', () => {
            modal.classList.add('hidden');
        });

        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.add('hidden');
            }
        });
    </script>
</x-app-layout>
