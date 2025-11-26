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
                <x-article-card :article="$article" />
            @endforeach
        </div>

        <div class="mt-12">
            {{ $articles->links() }}
        </div>

    </div>
</x-app-layout>
