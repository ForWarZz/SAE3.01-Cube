<x-app-layout :currentCategory="$currentCategory ?? null">
    <div class="px-24 py-12">
        <x-breadcrumb :breadcrumbs="$breadcrumbs" />

        <div class="mb-10 flex items-start justify-between">
            <div id="header-infos">
                <h1 class="text-3xl font-bold text-gray-900">{{ $pageTitle ?? "Nos produits" }}</h1>
                <p class="mt-2 text-gray-600">
                    {{ $articles->total() ?? $articles->count() }} produit{{ ($articles->total() ?? $articles->count()) > 1 ? "s" : "" }}
                    disponible{{ ($articles->total() ?? $articles->count()) > 1 ? "s" : "" }}
                </p>
            </div>

            @if (($articles->total() ?? $articles->count()) > 0)
                <div id="sort-select" class="flex items-center gap-2">
                    <label for="sort" class="text-sm font-medium text-gray-700">Trier par:</label>
                    <select
                        id="sort"
                        name="sortBy"
                        onchange="window.location.href = updateQueryString('sortBy', this.value)"
                        class="rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                        @foreach ($sortOptions as $key => $label)
                            <option value="{{ $key }}" {{ $sortBy === $key ? "selected" : "" }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>

        <div class="flex gap-8">
            @if (! empty($filterOptions))
                <x-filter-bar :filterOptions="$filterOptions" :activeFilters="$activeFilters" :sortBy="$sortBy" />
            @endif

            <div class="flex-1">
                @if ($articles->count() > 0)
                    <div class="grid grid-cols-4 gap-6">
                        @foreach ($articles as $article)
                            <x-article-card :article="$article" :is-bike="$article->bike" />
                        @endforeach
                    </div>

                    <div class="mt-12">
                        {{ $articles->links() }}
                    </div>
                @else
                    <div class="py-16 text-center">
                        <p class="mb-4 text-lg text-gray-600">Aucun article ne correspond à votre recherche</p>
                        <a
                            href="{{ route("home") }}"
                            class="inline-block rounded-lg bg-blue-600 px-6 py-2 text-white transition-colors hover:bg-blue-700"
                        >
                            Retour à l'accueil
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function updateQueryString(key, value) {
            const url = new URL(window.location.href);
            url.searchParams.set(key, value);
            url.searchParams.delete('page');
            return url.toString();
        }
    </script>
</x-app-layout>
