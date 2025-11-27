<x-app-layout>
    <div class="max-w-7xl mx-auto px-6 py-12">
        @if($breadcrumbs ?? false)
            <nav class="mb-8 flex items-center space-x-2 text-sm text-gray-600">
                @foreach($breadcrumbs as $crumb)
                    @if($crumb['url'])
                        <a href="{{ $crumb['url'] }}" class="hover:text-gray-900">{{ $crumb['label'] }}</a>
                    @else
                        <span class="text-gray-900">{{ $crumb['label'] }}</span>
                    @endif
                    @if(!$loop->last)
                        <span>/</span>
                    @endif
                @endforeach
            </nav>
        @endif

        <div class="mb-10 flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    {{ $pageTitle ?? 'Résultats de recherche' }}
                </h1>
                <p class="text-gray-600 mt-2">
                    @if($articles->count() > 0)
                        {{ $articles->count() }} résultat{{ $articles->count() > 1 ? 's' : '' }} trouvé{{ $articles->count() > 1 ? 's' : '' }}
                    @else
                        Aucun résultat pour votre recherche
                    @endif
                </p>
            </div>
            
            <!-- Sort Dropdown -->
            @if($articles->count() > 0)
                <div class="flex items-center gap-2">
                    <label for="sort" class="text-sm font-medium text-gray-700">Trier par:</label>
                    <select 
                        id="sort" 
                        name="sort_by"
                        onchange="window.location.href = updateQueryString('sort_by', this.value)"
                        class="rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                    >
                        <option value="name_asc" {{ ($sortBy ?? 'name_asc') == 'name_asc' ? 'selected' : '' }}>Nom (A-Z)</option>
                        <option value="name_desc" {{ ($sortBy ?? '') == 'name_desc' ? 'selected' : '' }}>Nom (Z-A)</option>
                        <option value="price_asc" {{ ($sortBy ?? '') == 'price_asc' ? 'selected' : '' }}>Prix (croissant)</option>
                        <option value="price_desc" {{ ($sortBy ?? '') == 'price_desc' ? 'selected' : '' }}>Prix (décroissant)</option>
                        <option value="reference_asc" {{ ($sortBy ?? '') == 'reference_asc' ? 'selected' : '' }}>Référence (croissant)</option>
                        <option value="reference_desc" {{ ($sortBy ?? '') == 'reference_desc' ? 'selected' : '' }}>Référence (décroissant)</option>
                    </select>
                </div>
            @endif
        </div>
        
        @if($articles->count() > 0)
            <script>
                function updateQueryString(key, value) {
                    const url = new URL(window.location.href);
                    url.searchParams.set(key, value);
                    return url.toString();
                }
            </script>
        @endif

        @if($articles->count() > 0)
            <div class="grid grid-cols-4 gap-6">
                @foreach ($articles as $article)
                    <x-article-card :article="$article" />
                @endforeach
            </div>
        @else
            <div class="text-center py-16">
                <p class="text-gray-600 text-lg mb-4">Aucun article ne correspond à votre recherche</p>
                <a href="{{ route('home') }}" class="inline-block px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Retour à l'accueil
                </a>
            </div>
        @endif
    </div>
</x-app-layout>

