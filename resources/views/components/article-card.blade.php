<article class="group overflow-hidden rounded-lg border border-gray-200 bg-white transition-shadow hover:shadow-lg">
    <a href="{{ route("articles.show", $article->id_article) }}" class="block">
        <div class="relative h-56 overflow-hidden bg-gray-50">
            @if ($article->hasDiscount())
                <span class="absolute top-2 left-2 z-10 rounded bg-red-600 px-2 py-1 text-xs font-bold text-white">
                    -{{ $article->pourcentage_remise }}%
                </span>
            @endif

            @if ($article->bike?->isNew())
                <span
                    class="absolute top-3 right-3 z-10 flex items-center gap-1 rounded-full bg-gradient-to-r from-lime-400 to-green-500 px-3 py-1 text-xs font-bold tracking-wide text-gray-900 uppercase shadow-lg"
                >
                    <x-bi-star-fill class="size-3" />
                    Nouveau
                </span>
            @endif

            <img
                src="{{ $article->getCoverUrl() }}"
                alt="{{ $article->nom_article }}"
                class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                loading="lazy"
            />
        </div>

        <div class="p-5">
            <h3 class="mb-1 line-clamp-2 text-base font-semibold text-gray-900">
                {{ $article->nom_article }}
            </h3>

            @if ($article->bike)
                <p class="mb-3 text-sm text-gray-500">
                    {{ $article->bike->bikeModel->nom_modele_velo }}
                </p>
            @elseif ($article->accessory)
                <p class="mb-3 text-sm text-gray-500">
                    {{ $article->category->nom_categorie ?? "Accessoire" }}
                </p>
            @endif

            <div class="mt-4 flex items-center justify-between">
                <div class="flex flex-col">
                    <span class="text-xl font-bold text-blue-600">{{ number_format($article->getDiscountedPrice(), 0, ",", " ") }} €</span>

                    @if ($article->hasDiscount())
                        <span class="text-sm text-gray-400 line-through">{{ number_format($article->prix_article, 0, ",", " ") }} €</span>
                    @endif
                </div>

                <span class="text-sm font-medium text-blue-600 group-hover:underline">Voir →</span>
            </div>
        </div>
    </a>
</article>
