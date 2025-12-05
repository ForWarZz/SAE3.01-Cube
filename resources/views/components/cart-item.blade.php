<div class="flex items-center gap-6 rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
    <div class="relative shrink-0 overflow-hidden rounded-lg bg-gray-50">
        <img src="{{ $cartItem["img_url"] }}" class="h-56 object-contain" alt="Produit" />

        @if ($cartItem["has_discount"])
            <div class="absolute top-0 right-0 rounded-bl-lg bg-red-600 px-2 py-1 text-[10px] font-bold text-white shadow-sm">
                -{{ $cartItem["discount_percent"] }}%
            </div>
        @endif
    </div>

    <div class="flex flex-1 flex-col gap-2">
        <a
            href="{{ $cartItem["article_url"] }}"
            class="text-lg font-bold text-gray-900 transition-colors hover:text-blue-600 hover:underline"
        >
            {{ $cartItem["article"]->nom_article }}
        </a>

        <div class="flex flex-wrap items-center gap-3 text-sm text-gray-500">
            <span
                class="inline-flex items-center gap-1 rounded-md bg-gray-50 px-2 py-1 font-medium text-gray-700 ring-1 ring-gray-500/10 ring-inset"
            >
                Taille : {{ $cartItem["size"]->nom_taille }}
            </span>

            @if ($cartItem["color"] !== null)
                <span
                    class="inline-flex items-center gap-1 rounded-md bg-gray-50 px-2 py-1 font-medium text-gray-700 ring-1 ring-gray-500/10 ring-inset"
                >
                    {{ $cartItem["color"] }}
                </span>
            @endif
        </div>
    </div>

    <div class="flex flex-col items-end justify-between gap-4">
        <div class="flex flex-col items-end">
            @if ($cartItem["has_discount"])
                <div class="mb-1 flex items-center gap-2">
                    <span class="text-sm font-medium text-gray-400 line-through decoration-gray-300">
                        {{ number_format($cartItem["real_price"], 2, ",", " ") }} €
                    </span>
                    <x-discount-badge :discount-percent="$cartItem['discount_percent']" />
                </div>

                <span class="text-xl font-bold text-blue-600">{{ number_format($cartItem["price_per_unit"], 2, ",", " ") }} €</span>
            @else
                <span class="text-xl font-bold text-gray-900">{{ number_format($cartItem["price_per_unit"], 2, ",", " ") }} €</span>
            @endif

            <span class="text-[10px] font-medium tracking-wide text-gray-400 uppercase">Prix unitaire TTC</span>
        </div>

        <div class="flex items-center gap-4">
            <form
                action="{{ route("cart.update-quantity") }}"
                method="POST"
                class="flex items-center rounded-lg border border-gray-200 shadow-sm transition-colors focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500 hover:border-gray-300"
            >
                @csrf
                @method("PATCH")
                <input type="hidden" name="reference_id" value="{{ $cartItem["reference"]->id_reference }}" />
                <input type="hidden" name="size_id" value="{{ $cartItem["size"]->id_taille }}" />

                <input
                    type="number"
                    name="quantity"
                    value="{{ $cartItem["quantity"] }}"
                    min="1"
                    class="h-9 w-16 border-none bg-transparent p-0 text-center text-sm font-bold text-gray-900 outline-none focus:ring-0"
                />
            </form>

            <form action="{{ route("cart.delete") }}" method="POST">
                @csrf
                @method("DELETE")
                <input type="hidden" name="reference_id" value="{{ $cartItem["reference"]->id_reference }}" />
                <input type="hidden" name="size_id" value="{{ $cartItem["size"]->id_taille }}" />

                <button
                    type="submit"
                    class="flex cursor-pointer items-center gap-1.5 rounded-lg p-2 text-gray-400 transition-colors hover:bg-red-50 hover:text-red-600"
                    title="Supprimer l'article"
                >
                    <x-bi-trash class="size-5 transition-transform" />
                    <span class="sr-only sm:not-sr-only sm:text-xs sm:font-medium">Retirer</span>
                </button>
            </form>
        </div>
    </div>
</div>
