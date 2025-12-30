@props([
    "sizes",
])

<form
    id="bike-calculator-container"
    data-chart='@json($sizes->toArray())'
    class="my-12 rounded-lg border border-gray-200 bg-gray-50 p-8 shadow-sm"
>
    <div class="flex gap-10 flex-row">
        <div class="w-full w-1/3">
            <div class="mb-6">
                <h3 class="flex items-center gap-2 text-lg font-bold text-gray-900">
                    <x-heroicon-o-calculator class="h-5 w-5 text-gray-600" />
                    Calculateur de taille
                </h3>
                <p class="mt-2 text-sm text-gray-500">
                    Remplissez le formulaire pour obtenir une recommandation fiable basée sur vos mensurations.
                </p>
            </div>

            <div class="space-y-4 border-t border-gray-200 pt-6">
                <h4 class="text-sm font-semibold text-gray-900">Comment mesurer ?</h4>

                <ul class="space-y-3 text-sm text-gray-600">
                    <li class="flex items-start gap-3">
                        <x-heroicon-m-user class="mt-0.5 h-5 w-5 shrink-0 text-gray-400" />
                        <span>Tenez-vous droit, pieds nus et légèrement écartés.</span>
                    </li>

                    <li class="flex items-start gap-3">
                        <x-heroicon-m-arrows-up-down class="mt-0.5 h-5 w-5 shrink-0 text-gray-400" />
                        <span>
                            Mesurez du
                            <strong>sol jusqu'au périnée</strong>
                            (haut de l'intérieur des cuisses).
                        </span>
                    </li>

                    <li class="flex items-start gap-3">
                        <x-heroicon-m-light-bulb class="mt-0.5 h-5 w-5 shrink-0 text-gray-400" />
                        <span class="italic">Astuce : Coincez un livre entre vos jambes pour simuler la selle.</span>
                    </li>
                </ul>
            </div>
        </div>

        <div
            class="flex w-full flex-col justify-center gap-6 border-gray-200 pt-8 w-2/3 border-t-0 border-l pt-0 pl-10"
        >
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <x-form-input
                    type="number"
                    id="client-height"
                    name="client-height"
                    label="Votre taille (cm)"
                    placeholder="Ex: 175"
                    min="100"
                    max="250"
                    required
                />

                <x-form-input
                    type="number"
                    id="client-inseam"
                    name="client-inseam"
                    label="Hauteur d'entrejambe (cm)"
                    placeholder="Ex: 82"
                    min="50"
                    max="120"
                    required
                />
            </div>

            <button
                id="calc-btn"
                type="submit"
                class="mt-2 w-full rounded bg-gray-900 px-6 py-2.5 font-bold text-white transition hover:bg-gray-800"
            >
                Calculer ma taille
            </button>

            <div id="result-box" class="mt-2 hidden rounded border border-gray-200 bg-white p-4 shadow-sm transition-all duration-300">
                <div class="flex items-center gap-2">
                    <x-heroicon-s-check-circle class="h-5 w-5 text-green-500" />
                    <p class="text-sm font-semibold text-gray-500">Taille recommandée :</p>
                </div>
                <p id="result-text" class="mt-1 pl-7 text-2xl font-bold text-gray-900">--</p>
                <p id="result-details" class="mt-1 hidden pl-7 text-sm text-gray-600"></p>
            </div>
        </div>
    </div>
</form>

@vite(["resources/js/bikesize/main.js"])
