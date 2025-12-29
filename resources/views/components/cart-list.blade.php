<section class="flex flex-2 flex-col gap-4">
    <h2 class="text-2xl font-semibold text-gray-900">Panier ({{ $count }})</h2>

    @if ($count <= 0)
        <p class="mb-6 text-gray-700">Votre panier ne contient actuellement aucun article.</p>
    @endif

    <div class="flex flex-col gap-4">
        @foreach ($cartData as $item)
            <x-cart-item :cartItem="$item" />
        @endforeach
    </div>

    <div class="mt-8 flex items-center justify-between rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
        <a href="{{ route("home") }}" class="flex cursor-pointer items-center gap-2 text-gray-700 transition hover:text-black">
            <x-bi-arrow-left class="h-5 w-5" />
            Continuer mes achats
        </a>

        <x-button :disabled="$count === 0" size="lg">Valider mon panier</x-button>
    </div>
</section>
