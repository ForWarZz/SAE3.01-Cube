<div class="mt-12">
    <h2 class="mb-6 text-2xl font-bold text-gray-900">Accessoires compatibles</h2>
    <div class="grid grid-cols-4 gap-6">
        @forelse ($compatibleAccessories as $accessory)
            <x-article-card :article="$accessory->article" :is-bike="false" />
        @empty
            <p class="text-gray-600">Aucun accessoire compatible trouvé.</p>
        @endforelse
    </div>
</div>
