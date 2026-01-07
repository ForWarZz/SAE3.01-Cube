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

            @if ($articles->count() > 0)
                <div id="sort-select" class="flex items-center gap-2">
                    <label for="sort" class="flex items-center gap-1 text-sm font-medium text-gray-700">
                        Trier par:
                        <x-info-tooltip text="Organisez les produits selon vos préférences : prix, ventes, noms, etc." width="w-48" />
                    </label>
                    <select
                        id="sort"
                        name="sortBy"
                        onchange="window.location.href = updateQueryString('sortBy', this.value)"
                        class="rounded-lg border-gray-300 text-sm shadow-sm transition-all hover:border-gray-400 focus:border-blue-500 focus:ring-blue-500"
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
                    <div class="mb-4 flex items-center justify-between">
                        @if ($articles->hasPages())
                            <div class="flex items-center gap-2 text-sm text-gray-500">
                                <x-heroicon-o-document-duplicate class="size-4" />
                                <span>Page {{ $articles->currentPage() }} sur {{ $articles->lastPage() }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="grid grid-cols-4 gap-6">
                        @foreach ($articles as $article)
                            <x-article-card :article="$article" :is-bike="$article->bike" />
                        @endforeach
                    </div>

                    @if ($articles->hasPages())
                        <div class="mt-12">
                            {{ $articles->links() }}
                        </div>
                    @endif
                @else
                    <div class="rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 py-16 text-center">
                        <x-heroicon-o-magnifying-glass class="mx-auto mb-4 size-16 text-gray-400" />
                        <p class="mb-2 text-lg font-semibold text-gray-900">Aucun article ne correspond à vos critères</p>
                        <p class="mb-6 text-sm text-gray-600">
                            Essayez de modifier vos filtres ou de réinitialiser la recherche pour voir plus de produits.
                        </p>

                        <div class="flex items-center justify-center gap-4">
                            <x-button href="{{ url()->current() }}" icon="heroicon-o-arrow-left" variant="secondary">
                                Réinitialiser les filtres
                            </x-button>

                            <a
                                href="{{ route("user-guide") }}"
                                class="inline-flex items-center gap-2 text-sm text-blue-600 transition-colors hover:text-blue-700"
                            >
                                <x-heroicon-o-question-mark-circle class="size-5" />
                                Besoin d'aide ?
                            </a>
                        </div>

                        <div class="mx-auto mt-8 max-w-md rounded-lg bg-white p-4 text-left shadow-sm">
                            <p class="mb-2 text-sm font-semibold text-gray-900">Astuces de recherche :</p>
                            <ul class="space-y-1 text-xs text-gray-600">
                                <li class="flex items-start gap-2">
                                    <x-heroicon-o-check-circle class="mt-0.5 size-4 flex-shrink-0 text-green-600" />
                                    <span>Essayez moins de filtres pour élargir les résultats</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <x-heroicon-o-check-circle class="mt-0.5 size-4 flex-shrink-0 text-green-600" />
                                    <span>Vérifiez l'orthographe de votre recherche</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <x-heroicon-o-check-circle class="mt-0.5 size-4 flex-shrink-0 text-green-600" />
                                    <span>Utilisez des mots-clés plus généraux</span>
                                </li>
                            </ul>
                        </div>
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
