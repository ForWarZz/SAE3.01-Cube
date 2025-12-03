@props([
    "category",
    "n",
])

<li class="group relative">
    <a
        href="{{ route("articles.by-category", $category) }}"
        class="{{ $n === 0 ? "border-r border-gray-200 text-sm font-bold tracking-wider text-gray-900 uppercase hover:bg-gray-50" : "px-5 py-3 text-sm text-gray-800 hover:bg-gray-100 hover:text-blue-600" }} block px-6 py-4 transition-colors duration-200"
    >
        {{ $category->nom_categorie }}
    </a>

    @if ($category->childrenRecursive->isNotEmpty())
        <ul
            class="{{ $n === 0 ? "top-full left-0 mt-0" : "top-0 left-full" }} absolute z-50 hidden min-w-[200px] rounded-lg border border-gray-200 bg-white py-2 shadow-xl"
        >
            @foreach ($category->childrenRecursive as $child)
                <x-category-item :category="$child" :n="$n+1" />
            @endforeach
        </ul>
    @elseif ($category->articles->isNotEmpty())
        @php
            $modelList = collect();
        @endphp

        @foreach ($category->articles as $article)
            @if (! is_null($article->bike))
                @php
                    $model = $article->bike->bikeModel;
                    if (! $modelList->contains($model)) {
                        $modelList->push($model);
                    }
                @endphp
            @endif
        @endforeach

        @if ($modelList->isNotEmpty())
            <ul
                class="{{ $n === 0 ? "top-full left-0 mt-0" : "top-0 left-full" }} absolute z-50 hidden min-w-[200px] rounded-lg border border-gray-200 bg-white py-2 shadow-xl"
            >
                @foreach ($modelList as $model)
                    <li>
                        <a
                            href="{{ route("articles.by-model", $model) }}"
                            class="block px-5 py-2.5 text-sm whitespace-nowrap text-gray-700 transition hover:bg-gray-50 hover:text-gray-900"
                        >
                            {{ $model->nom_modele_velo }}
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    @endif
</li>
