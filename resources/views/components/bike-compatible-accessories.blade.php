@if ($compatibleAccessories->isNotEmpty())
    <div class="bg-gray-50 py-12">
        <div class="mx-auto max-w-7xl px-6">
            <div class="mb-8">
                <h2 class="mb-2 text-2xl font-bold text-gray-900">Accessoires compatibles</h2>
            </div>
            <div class="grid grid-cols-2 gap-6 md:grid-cols-3 lg:grid-cols-4">
                @foreach ($compatibleAccessories as $accessory)
                    <x-article-card :article="$accessory" />
                @endforeach
            </div>
        </div>
    </div>
@endif
