<article class="group rounded-lg border border-gray-200 bg-white transition-shadow hover:shadow-lg">
    <a href="{{ route("articles.show", $article->id_article) }}" class="relative block p-6">
        @if ($article->hasDiscount())
            <x-discount-badge class="absolute top-3 left-3" :discount-percent="$article->pourcentage_remise" />
        @endif

        @if ($article->bike?->isNew())
            <span
                class="absolute top-3 right-3 z-10 flex items-center gap-1 rounded-full bg-gradient-to-r from-lime-400 to-green-500 px-3 py-1 text-xs font-bold tracking-wide text-gray-900 uppercase shadow-lg"
            >
                <x-bi-star-fill class="size-3" />
                Nouveau
            </span>
        @endif

        <div class="mb-6 overflow-visible">
            <img
                src="{{ $article->getCoverUrl() }}"
                alt="{{ $article->nom_article }}"
                class="object-contain transition-transform duration-300 group-hover:scale-105"
                loading="lazy"
            />
        </div>

        <div>
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
                    <span class="text-xl font-bold text-blue-600">{{ number_format($article->getDiscountedPrice(), 2, ",", " ") }} €</span>

                    @if ($article->hasDiscount())
                        <span class="text-sm text-gray-400 line-through">{{ number_format($article->prix_article, 2, ",", " ") }} €</span>
                    @endif
                </div>

                <span class="text-sm font-medium text-blue-600 group-hover:underline">Voir →</span>
            </div>
        </div>
    </a>
</article>
