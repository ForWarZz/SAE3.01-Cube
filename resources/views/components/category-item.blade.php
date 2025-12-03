@props([
    "category",
    "n",
])

<li class="relative">
    <a
        href="{{ route("articles.by-category", $category) }}"
        class="{{ $n === 0 ? "py-5 text-sm font-medium text-gray-700 hover:text-blue-600" : "block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-blue-600" }} flex items-center justify-between transition-colors duration-200"
    >
        <span>{{ $category->nom_categorie }}</span>

        @if (($category->childrenRecursive->isNotEmpty() || $category->articles->isNotEmpty()) && $n > 0)
            <x-bi-chevron-right />
        @elseif (($category->childrenRecursive->isNotEmpty() || $category->articles->isNotEmpty()) && $n === 0)
            <x-bi-chevron-down class="ml-2 size-3" />
        @endif
    </a>

    @if ($category->childrenRecursive->isNotEmpty())
        <ul
            class="{{ $n === 0 ? "top-full left-0 mt-0" : "top-0 left-full -ml-1" }} absolute z-50 hidden min-w-[220px] rounded-lg border border-gray-100 bg-white py-2 shadow-xl"
        >
            @foreach ($category->childrenRecursive as $child)
                <x-category-item :category="$child" :n="$n + 1" />
            @endforeach
        </ul>
    @elseif ($category->articles->isNotEmpty())
        @php
            $modelList = collect();
            foreach ($category->articles as $article) {
                if (! is_null($article->bike)) {
                    $model = $article->bike->bikeModel;
                    if (! $modelList->contains("id_modele_velo", $model->id_modele_velo)) {
                        $modelList->push($model);
                    }
                }
            }
        @endphp

        @if ($modelList->isNotEmpty())
            <ul
                class="{{ $n === 0 ? "top-full left-0 mt-0" : "top-0 left-full -ml-1" }} absolute z-50 hidden min-w-[220px] rounded-lg border border-gray-100 bg-white py-2 shadow-xl"
            >
                @foreach ($modelList as $model)
                    <li>
                        <a
                            href="{{ route("articles.by-model", $model) }}"
                            class="block px-4 py-2 text-sm text-gray-700 transition hover:bg-gray-50 hover:text-blue-600"
                        >
                            {{ $model->nom_modele_velo }}
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    @endif
</li>
