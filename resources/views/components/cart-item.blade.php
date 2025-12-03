<div class="flex items-center gap-5 rounded-lg bg-white p-4 shadow-sm">
    <img src="{{ $cartItem["img_url"] }}" class="size-56 rounded object-cover" alt="Produit" />

    <div class="flex flex-1 flex-col gap-1">
        <a href="{{ $cartItem["article_url"] }}" class="text-lg font-semibold text-gray-900 hover:text-gray-800 hover:underline">
            {{ $cartItem["article"]->nom_article }}
        </a>
        <span class="text-sm text-gray-500">Taille : {{ $cartItem["size"]->nom_taille }}</span>

        @if ($cartItem["color"] !== null)
            <span class="text-sm text-gray-500">Couleur : {{ $cartItem["color"] }}</span>
        @endif
    </div>

    <div class="flex flex-col items-end gap-2">
        <span class="text-lg font-semibold text-gray-900">{{ number_format($cartItem["article"]->prix_article, 0, ",", " ") }} € TTC</span>

        <form action="{{ route("cart.update-quantity") }}" method="POST" class="flex items-center gap-2">
            @csrf
            @method("PATCH")

            <input type="hidden" name="reference_id" value="{{ $cartItem["reference"]->id_reference }}" />
            <input type="hidden" name="size_id" value="{{ $cartItem["size"]->id_taille }}" />

            <input
                type="number"
                name="quantity"
                value="{{ $cartItem["quantity"] }}"
                min="1"
                class="w-16 rounded-md border border-gray-300 px-2 py-1 text-center text-gray-900 focus:border-gray-900 focus:ring-1 focus:ring-gray-900 focus:outline-none"
            />
        </form>

        <form action="{{ route("cart.delete") }}" method="POST">
            @csrf
            @method("DELETE")

            <input type="hidden" name="reference_id" value="{{ $cartItem["reference"]->id_reference }}" />
            <input type="hidden" name="size_id" value="{{ $cartItem["size"]->id_taille }}" />

            <button type="submit" class="cursor-pointer text-sm text-red-500 hover:text-red-700">Supprimer</button>
        </form>
    </div>
</div>
