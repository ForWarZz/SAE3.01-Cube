<div class="mx-auto max-w-7xl px-6 py-12">
    <h2 class="mb-6 text-2xl font-bold text-gray-900">Produits similaires</h2>
    <div class="grid grid-cols-4 gap-6">
        @forelse ($similarArticles as $similarArticle)
            <x-article-card :article="$similarArticle" />
        @empty
            <p class="text-gray-600">Aucun produit similaire trouvé.</p>
        @endforelse
    </div>
</div>
