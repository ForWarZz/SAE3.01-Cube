@props(['categorie', 'n'])

<li class="categorie{{ $n }}">
    <a href="/categorie/{{ $categorie->id_categorie }}">{{ $categorie->nom_categorie }}</a>
    @if ($categorie->catEnfants->isNotEmpty())
        <ul>
            @foreach ($categorie->catEnfants as $enfant)
                <x-category-item :categorie="$enfant" :n="$n+1" />
            @endforeach
        </ul>
    @elseif ($categorie->articles->isNotEmpty())
        @php
            $lmodele = collect([])
        @endphp
        @foreach ($categorie->articles as $article)
            @if (!is_null($article->velo))
                @php
                    $modele = $article->velo->modelevelo;
                    if (!$lmodele->contains($modele)){
                        $lmodele->push($modele);
                    }
                @endphp
            @endif
        @endforeach
        @if ($lmodele->isNotEmpty())
            <ul>
                @foreach ($lmodele as $modele)
                    <li class="modele"><a href="/modele/{{ $modele->id_modele_velo }}">{{ $modele->nom_modele_velo }}</a></li>
                @endforeach
            </ul>
        @endif
    @endif
</li>