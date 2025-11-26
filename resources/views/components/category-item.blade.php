@props(['category', 'n'])

<li class="group relative">
    <a href="{{ route('articles.by-category', $category) }}"
       class="block px-6 py-4 {{ $n === 0 ? 'font-bold uppercase text-sm tracking-wider text-gray-900 border-r border-gray-200 hover:bg-gray-50' : 'px-5 py-3 text-gray-800 hover:text-blue-600 hover:bg-gray-100 text-sm' }} transition-colors duration-200">
        {{ $category->nom_categorie }}
    </a>

    @if ($category->children->isNotEmpty())
        <ul class="hidden absolute {{ $n === 0 ? 'left-0 top-full mt-0' : 'left-full top-0' }} bg-white border border-gray-200 rounded-lg shadow-xl py-2 min-w-[200px] z-50">
            @foreach ($category->children as $child)
                <x-category-item :category="$child" :n="$n+1" />
            @endforeach
        </ul>
    @elseif ($category->articles->isNotEmpty())
        @php
            $modelList = collect()
        @endphp
        @foreach ($category->articles as $article)
            @if (!is_null($article->bike))
                @php
                    $model = $article->bike->bikeModel;
                    if (!$modelList->contains($model)){
                        $modelList->push($model);
                    }
                @endphp
            @endif
        @endforeach

        @if ($modelList->isNotEmpty())
            <ul class="hidden absolute {{ $n === 0 ? 'left-0 top-full mt-0' : 'left-full top-0' }} bg-white border border-gray-200 rounded-lg shadow-xl py-2 min-w-[200px] z-50">
                @foreach ($modelList as $model)
                    <li>
                        <a href="{{ route('articles.by-model', $model) }}"
                           class="block px-5 py-2.5 text-sm text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition whitespace-nowrap">
                            {{ $model->nom_modele_velo }}
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    @endif
</li>
