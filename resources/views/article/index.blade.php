<x-app-layout>
    <div class="max-w-7xl mx-auto px-6 py-12">
        @if($breadcrumbs ?? false)
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

        <div class="mb-10">
            <h1 class="text-3xl font-bold text-gray-900">
                {{ $pageTitle ?? 'Nos produits' }}
            </h1>
            <p class="text-gray-600 mt-2">{{ $articles->total() }} produit{{ $articles->total() > 1 ? 's' : '' }} disponible{{ $articles->total() > 1 ? 's' : '' }}</p>
        </div>

        <div class="grid grid-cols-4 gap-6">
            @foreach ($articles as $article)
                <article class="group bg-white rounded-lg border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow">
                    <a href="{{ route('articles.show', $article->id_article) }}" class="block">

                        <div class="relative bg-gray-50 h-56 overflow-hidden">
                            <img
                                alt="{{ $article->nom_article }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                loading="lazy"
                            >

{{--                            @if($article->is_new ?? false)--}}
{{--                                <span class="absolute top-3 right-3 bg-blue-600 text-white text-xs font-medium px-3 py-1 rounded-full">--}}
{{--                                    Nouveau--}}
{{--                                </span>--}}
{{--                            @endif--}}
                        </div>

                        <div class="p-5">
                            <h3 class="text-base font-semibold text-gray-900 mb-1 line-clamp-2">
                                {{ $article->nom_article }}
                            </h3>

                            @if($article->modeleVelo)
                                <p class="text-sm text-gray-500 mb-3">
                                    {{ $article->modeleVelo->nom_modele_velo }}
                                </p>
                            @endif

                            <div class="flex items-center justify-between mt-4">
                                <span class="text-xl font-bold text-gray-900">
                                    {{ number_format($article->prix_article, 0, ',', ' ') }} €
                                </span>

                                <span class="text-sm text-blue-600 font-medium group-hover:underline">
                                    Voir →
                                </span>
                            </div>
                        </div>
                    </a>
                </article>
            @endforeach
        </div>

        <div class="mt-12">
            {{ $articles->links() }}
        </div>

    </div>
</x-app-layout>
