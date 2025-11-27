<article class="group bg-white rounded-lg border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow">
    <a href="{{ route('articles.show', $article->id_article) }}" class="block">
        <div class="relative bg-gray-50 h-56 overflow-hidden">
            <img
                src="{{ $article->getCoverUrl() }}"
                alt="{{ $article->nom_article }}"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                loading="lazy"
            >
        </div>

        <div class="p-5">
            <h3 class="text-base font-semibold text-gray-900 mb-1 line-clamp-2">
                {{ $article->nom_article }}
            </h3>

            @if($article->bike)
                <p class="text-sm text-gray-500 mb-3">
                    {{ $article->bike->bikeModel->nom_modele_velo }}
                </p>
            @endif

            <div class="flex items-center justify-between mt-4">
                <span class="text-xl font-bold text-gray-900">
                    {{ number_format($article->prix_article, 0, ',', ' ') }} €
                </span>

                <span class="text-sm text-blue-600 font-medium group-hover:underline">
                    Voir →
                </span>
            </div>
        </div>
    </a>
</article>
