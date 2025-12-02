<nav id="nav" class="border-b border-gray-200 bg-white shadow-sm">
    <div class="mx-auto max-w-7xl px-6 py-4">
        <div class="mb-4 flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900">Cube France</h1>

            <div class="flex items-center gap-6">
                <div class="relative w-64">
                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center">
                        <x-bi-search class="size-5 text-gray-500" />
                    </span>

                    <input
                        type="text"
                        id="search-input"
                        placeholder="Rechercher un article..."
                        class="w-full rounded-lg border border-gray-300 py-2 pr-4 pl-10 focus:border-transparent focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    />
                </div>

                <a href="{{ route("cart.index") }}" class="relative flex items-center" title="Voir le panier">
                    <x-bi-cart class="size-7 text-gray-700 transition hover:text-gray-900" />

                    @if ($cartItemCount > 0)
                        <span
                            class="absolute -top-1 -right-2 flex size-5 items-center justify-center rounded-full bg-black text-xs font-bold text-white"
                        >
                            {{ $cartItemCount }}
                        </span>
                    @endif
                </a>
            </div>
        </div>

        <ul class="flex items-center space-x-0">
            @foreach ($categories as $category)
                <x-category-item :category="$category" :n="0" />
            @endforeach
        </ul>
    </div>
</nav>
