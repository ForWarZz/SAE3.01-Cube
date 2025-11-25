@props(['categorie'])

<li>
    {{ $categorie->nom_categorie }}
    @if ($categorie->catEnfants->isNotEmpty())
        <ul>
            @foreach ($categorie->catEnfants as $enfant)
                <x-category-item :categorie="$enfant" />
            @endforeach
        </ul>
    {{-- @elseif ($categorie->articles->isNotEmpty())
        <ul>
            @foreach ($categorie->articles as $article)
                <li>{{ $article->nom_article }}</li>
            @endforeach
        </ul> --}}
    @endif
</li>