<section class="flex flex-col gap-4">
    <h2 class="text-xl font-semibold text-gray-900">Récapitulatif</h2>

    <div class="flex flex-col gap-4 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <div class="flex justify-between text-sm text-gray-700">
            <span>Sous-total</span>
            <span>{{ number_format($summaryData["subtotal"], 2, ",", " ") }} €</span>
        </div>

        <div class="flex justify-between text-sm text-gray-700">
            <span>Frais de livraison</span>
            <span class="font-medium text-green-600"></span>
        </div>

        <div class="flex justify-between text-sm text-gray-700">
            <span>Taxes</span>
            <span>{{ number_format($summaryData["tax"], 2, ",", " ") }} €</span>
        </div>

        @if ($discountData)
            <div class="flex justify-between text-sm font-medium text-green-700">
                <span>Remise ({{ $discountData->pourcentage_remise }}%)</span>
                <span>-{{ number_format($summaryData["discount"], 2, ",", " ") }} €</span>
            </div>
        @endif

        <div class="border-t pt-4"></div>

        <div class="flex justify-between text-lg font-bold text-gray-900">
            <span>Total TTC</span>
            <span>{{ number_format($summaryData["total"], 2, ",", " ") }} €</span>
        </div>

        @if ($count > 0)
            <button
                class="mt-4 cursor-pointer rounded-md bg-red-500 px-5 py-3 text-lg font-medium text-white shadow-sm transition hover:bg-red-700 hover:shadow-md"
            >
                Valider mon panier
            </button>
        @else
            <button disabled class="mt-4 cursor-not-allowed rounded-md bg-gray-300 px-5 py-3 text-lg font-medium text-white shadow-sm">
                Valider mon panier
            </button>
        @endif
    </div>
</section>
