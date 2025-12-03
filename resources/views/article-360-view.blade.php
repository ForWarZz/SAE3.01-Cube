<x-app-layout>
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Vue 360 : {{ $article->nom_article }}</h1>
        
        <x-article-360 :images="$images" />

        <a href="{{ url()->previous() }}" class="text-gray-500 underline">Retour à l'article</a>
    </div>
</x-app-layout>