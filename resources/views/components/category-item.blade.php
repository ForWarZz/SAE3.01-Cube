@props(['categorie', 'n'])

<li class="categorie{{ $n }}">
    <a href="{{ route('articles.by-category', $categorie) }}">{{ $categorie->nom_categorie }}</a>
    @if ($categorie->children->isNotEmpty())
        <ul>
            @foreach ($categorie->children as $child)
                <x-category-item :categorie="$child" :n="$n+1" />
            @endforeach
        </ul>
    @elseif ($categorie->articles->isNotEmpty())
        @php
            $modelList = collect([])
        @endphp
        @foreach ($categorie->articles as $article)
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
            <ul>
                @foreach ($modelList as $model)
                    <li class="modele"><a href="{{ route('articles.by-model', $model) }}">{{ $model->nom_modele_velo }}</a></li>
                @endforeach
            </ul>
        @endif
    @endif
</li>
