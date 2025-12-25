<div class="my-12 rounded-lg border border-gray-200 bg-gray-50 p-8 shadow-sm">
    <div class="flex flex-row gap-8">
        <div class="md:w-1/3">
            <h2 class="mb-2 text-2xl font-bold text-gray-900">Calculateur de taille</h2>
            <p class="text-sm text-gray-600">
                Vous hésitez entre deux tailles ? Renseignez vos mensurations ci-contre pour obtenir notre recommandation personnalisée
                basée sur la géométrie de ce vélo.
            </p>

            <div class="mt-4 rounded bg-blue-50 p-3 text-xs text-blue-700">
                <strong>Astuce :</strong>
                Pour l'entrejambe, mesurez du sol jusqu'au périnée, pieds nus et légèrement écartés.
            </div>
        </div>

        <div class="flex w-2/3 flex-col justify-center gap-6">
            <form id="bike-size-form" class="flex flex-col gap-6" action="#" method="get">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="user-height" class="mb-1 block text-sm font-medium text-gray-700">Votre taille (cm)</label>
                        <x-form-input
                            type="number"
                            id="user-height"
                            name="user-height"
                            required
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Taille..."
                            min="100"
                            max="250"
                        />
                    </div>

                    <div>
                        <label for="user-inseam" class="mb-1 block text-sm font-medium text-gray-700">Entrejambe (cm)</label>
                        <x-form-input
                            type="number"
                            id="user-inseam"
                            name="user-inseam"
                            required
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Entrejambe..."
                            min="40"
                            max="120"
                        />
                    </div>
                </div>

                <button
                    type="submit"
                    id="calculate-size-btn"
                    class="w-full rounded bg-gray-900 px-4 py-2 font-bold text-white transition hover:bg-gray-800 md:w-auto md:self-start"
                >
                    Calculer ma taille idéale
                </button>

                <div id="size-result-container" class="animate-fade-in mt-2 hidden rounded-md bg-white p-4 shadow-sm">
                    <p class="text-sm font-semibold text-gray-500">Taille de vélo recommandée :</p>
                    <p class="text-xl font-bold text-gray-900" id="size-result-text" aria-live="polite"></p>
                </div>
            </form>
        </div>
    </div>
</div>

@vite(["resources/js/bikesize/main.js"])
