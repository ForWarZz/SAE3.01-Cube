@php
    use App\Models\Category;
@endphp

<section id="cart-items-list" class="flex flex-2 flex-col gap-4">
    <h2 class="text-2xl font-semibold text-gray-900">Panier ({{ $count }})</h2>

    @if ($count <= 0)
        <div class="py-6 text-center">
            <x-heroicon-o-shopping-cart class="mx-auto mb-4 h-24 w-24 text-gray-300" />
            <h2 class="text-2xl font-bold text-gray-900">Oups, c'est vide ici !</h2>
            <p class="mb-8 text-gray-500">Vous ne pouvez pas commander de vent. Il faut choisir un vélo d'abord.</p>

            <x-button href="{{ route('articles.by-category', Category::BIKE_CATEGORY_ID) }}" size="lg" icon="heroicon-o-arrow-right">
                Voir les vélos disponibles
            </x-button>
        </div>
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
