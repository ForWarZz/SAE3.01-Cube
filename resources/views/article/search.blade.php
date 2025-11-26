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
                {{ $pageTitle ?? 'Résultats de recherche' }}
            </h1>
            <p class="text-gray-600 mt-2">
                @if($articles->count() > 0)
                    {{ $articles->count() }} résultat{{ $articles->count() > 1 ? 's' : '' }} trouvé{{ $articles->count() > 1 ? 's' : '' }}
                @else
                    Aucun résultat pour votre recherche
                @endif
            </p>
        </div>

        @if($articles->count() > 0)
            <div class="grid grid-cols-4 gap-6">
                @foreach ($articles as $article)
                    <x-article-card :article="$article" />
                @endforeach
            </div>
        @else
            <div class="text-center py-16">
                <p class="text-gray-600 text-lg mb-4">Aucun article ne correspond à votre recherche</p>
                <a href="{{ route('home') }}" class="inline-block px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Retour à l'accueil
                </a>
            </div>
        @endif
    </div>
</x-app-layout>

