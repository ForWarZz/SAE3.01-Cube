<x-app-layout>
    <div class="max-w-7xl mx-auto px-6 py-12">
        {{-- Breadcrumbs --}}
        @if(!empty($breadcrumbs))
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

        {{-- Page title & sort --}}
        <div class="mb-10 flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $pageTitle ?? 'Nos produits' }}</h1>
                <p class="text-gray-600 mt-2">
                    {{ $articles->total() ?? $articles->count() }} produit{{ ($articles->total() ?? $articles->count()) > 1 ? 's' : '' }} disponible{{ ($articles->total() ?? $articles->count()) > 1 ? 's' : '' }}
                </p>
            </div>

            @if(($articles->total() ?? $articles->count()) > 0)
                <div class="flex items-center gap-2">
                    <label for="sort" class="text-sm font-medium text-gray-700">Trier par:</label>
                    <select
                        id="sort"
                        name="sortBy"
                        onchange="window.location.href = updateQueryString('sortBy', this.value)"
                        class="rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                    >
                        @foreach($sortOptions as $key => $label)
                            <option value="{{ $key }}" {{ ($sortBy === $key) ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>

        <div class="flex gap-8">
            @if(!empty($filterOptions))
                <x-filter-bar
                    :filterOptions="$filterOptions"
                    :activeFilters="$activeFilters"
                    :sortBy="$sortBy"
                />
            @endif

            {{-- Products grid --}}
            <div class="flex-1">
                @if($articles->count() > 0)
                    <div class="grid grid-cols-4 gap-6">
                        @foreach ($articles as $article)
                            <x-article-card :article="$article" />
                        @endforeach
                    </div>

                    <div class="mt-12">
                        {{ $articles->links() }}
                    </div>
                @else
                    <div class="text-center py-16">
                        <p class="text-gray-600 text-lg mb-4">Aucun article ne correspond à votre recherche</p>
                        <a href="{{ route('home') }}" class="inline-block px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">Retour à l'accueil</a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function updateQueryString(key, value) {
            const url = new URL(window.location.href);
            url.searchParams.set(key, value);
            return url.toString();
        }
    </script>
</x-app-layout>
