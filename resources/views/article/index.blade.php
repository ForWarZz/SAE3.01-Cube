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
                    {{ $pageTitle ?? 'Nos produits' }}
                </h1>
                <p class="text-gray-600 mt-2">{{ $articles->total() }} produit{{ $articles->total() > 1 ? 's' : '' }} disponible{{ $articles->total() > 1 ? 's' : '' }}</p>
            </div>
            
            <!-- Sort Dropdown -->
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
        </div>
        
        <script>
            function updateQueryString(key, value) {
                const url = new URL(window.location.href);
                url.searchParams.set(key, value);
                return url.toString();
            }
        </script>

        <div class="grid grid-cols-4 gap-6">
            @foreach ($articles as $article)
                <x-article-card :article="$article" />
            @endforeach
        </div>

        <div class="mt-12">
            {{ $articles->links() }}
        </div>

    </div>
</x-app-layout>
