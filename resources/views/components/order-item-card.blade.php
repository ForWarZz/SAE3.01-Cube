{{-- @props([ --}}
{{-- 'item', --}}
{{-- ]) --}}

{{-- @php --}}
{{-- // Récupérer l'article via bikeReference->article ou accessory --}}
{{-- $bikeRef = $item->reference?->bikeReference; --}}
{{-- $accessory = $item->reference?->accessory; --}}
{{--  --}}
{{-- // L'article vient de bikeReference->article pour les vélos --}}
{{-- $article = $bikeRef?->article ?? $accessory ?? null; --}}
{{-- $hasArticle = $article !== null; --}}
{{-- @endphp --}}

{{-- <div class="flex items-start gap-4 rounded-lg border border-gray-200 bg-white p-4 transition-shadow hover:shadow-md"> --}}
{{--  --}}
{{-- Image --}}
{{-- <div class="flex-shrink-0 w-24 h-24 bg-gray-100 rounded-lg overflow-hidden"> --}}
{{-- @if ($hasArticle) --}}
{{-- <img --}}
{{-- src="{{ $article->getCoverThumbnailUrl() }}" --}}
{{-- alt="{{ $article->nom_article }}" --}}
{{-- class="w-full h-full object-contain" --}}
{{-- loading="lazy" --}}
{{-- /> --}}
{{-- @else --}}
{{-- <div class="w-full h-full flex items-center justify-center text-gray-400"> --}}
{{-- <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"> --}}
{{-- <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /> --}}
{{-- </svg> --}}
{{-- </div> --}}
{{-- @endif --}}
{{-- </div> --}}

{{--  --}}
{{-- Détails --}}
{{-- <div class="flex-1 min-w-0"> --}}
{{-- <h3 class="text-sm font-semibold text-gray-900"> --}}
{{-- @if ($hasArticle) --}}
{{-- <a href="{{ route('articles.show', $article->id_article) }}" class="hover:text-indigo-600"> --}}
{{-- {{ $article->nom_article }} --}}
{{-- </a> --}}
{{-- @else --}}
{{-- Référence #{{ $item->reference->id_reference ?? $item->id_reference }} --}}
{{-- @endif --}}
{{-- </h3> --}}

{{-- @if ($hasArticle && $article->bike) --}}
{{-- <p class="mt-0.5 text-xs text-gray-500"> --}}
{{-- {{ $article->bike->bikeModel->nom_modele_velo ?? '' }} --}}
{{-- </p> --}}
{{-- @endif --}}

{{-- <p class="mt-1 text-sm text-gray-600"> --}}
{{-- Quantité : <span class="font-medium">{{ $item->quantite_ligne }}</span> --}}
{{-- </p> --}}

{{-- @if ($item->size) --}}
{{-- <p class="text-sm text-gray-600"> --}}
{{-- Taille : <span class="font-medium">{{ $item->size->nom_taille ?? $item->size->libelle_taille ?? $item->id_taille }}</span> --}}
{{-- </p> --}}
{{-- @endif --}}
{{-- </div> --}}

{{--  --}}
{{-- Prix --}}
{{-- <div class="text-right flex-shrink-0"> --}}
{{-- <p class="text-base font-bold text-gray-900"> --}}
{{-- {{ number_format($item->prix_unit_ligne * $item->quantite_ligne, 2, ',', ' ') }} € --}}
{{-- </p> --}}
{{-- <p class="mt-1 text-xs text-gray-500"> --}}
{{-- {{ number_format($item->prix_unit_ligne, 2, ',', ' ') }} € / unité --}}
{{-- </p> --}}
{{-- </div> --}}
{{-- </div> --}}
