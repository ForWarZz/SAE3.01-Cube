<x-app-layout>
    <div class="mx-auto max-w-7xl px-6 py-12">
        <div class="flex justify-between gap-6">
            <div class="w-1/2 flex-shrink-0">
                <img
                    src="{{ $bike->article->getCoverUrl($currentReference->color->id_couleur) }}"
                    alt="{{ $bike->nom_article }} - {{ $currentReference->color->label_couleur }}"
                    class="h-auto w-full rounded-lg object-cover shadow"
                    loading="lazy"
                />
            </div>

            <div class="flex flex-col" x-data="{ selectedSize: @js($sizeOptions->first(fn ($s) => ! $s["disabled"])) }">
                <div class="mb-8">
                    <h1 class="mb-3 text-3xl font-bold text-gray-900">{{ $bike->nom_article }}</h1>
                    <div class="mb-4 flex items-center gap-3 text-gray-600">
                        <span>{{ $bike->bikeModel->nom_modele_velo }}</span>
                        @if ($isEbike)
                            <span class="font-medium text-blue-600">Électrique</span>
                        @endif
                    </div>
                    <div class="flex flex-col">
                        <div class="mb-2 flex gap-2">
                            <span class="bg-gray-100 p-1 text-sm text-black">Poids : {{ $weight }}</span>
                            <span class="bg-gray-100 p-1 text-sm text-black">Millésime : {{ $bike->vintage->millesime_velo }}</span>
                            <span class="bg-gray-100 p-1 text-sm text-black">
                                Matériau du cadre : {{ $bike->frameMaterial->label_materiau_cadre }}
                            </span>
                        </div>

                        <div class="text-3xl font-bold text-blue-600">{{ number_format($bike->prix_article, 2, ",", " ") }} €</div>

                        <div class="mt-2 text-sm text-gray-700" x-show="selectedSize">
                            <span :class="selectedSize.availableOnline ? 'text-green-600 font-medium' : 'text-gray-400'">
                                <span x-text="selectedSize.availableOnline ? 'Disponible en ligne' : 'Non disponible en ligne'"></span>
                            </span>
                            <br />
                            <span :class="selectedSize.availableInShop ? 'text-green-600 font-medium' : 'text-gray-400'">
                                <span x-text="selectedSize.availableInShop ? 'En stock en magasin' : 'Indisponible en magasin'"></span>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-8">
                    <div>
                        <label class="mb-3 block text-sm font-medium text-gray-900">Type de cadre</label>
                        <div class="flex gap-2">
                            @foreach ($frameOptions as $opt)
                                <a
                                    href="{{ $opt["url"] }}"
                                    class="{{ $opt["active"] ? "border-gray-900 bg-gray-900 text-white" : "border-gray-300 bg-white text-gray-700 hover:border-gray-400" }} rounded-lg border px-5 py-2.5 text-sm font-medium"
                                >
                                    {{ $opt["label"] }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    @if ($isEbike && $batteryOptions->count() > 0)
                        <div>
                            <label class="mb-3 block text-sm font-medium text-gray-900">Batterie</label>
                            <div class="flex gap-2">
                                @foreach ($batteryOptions as $opt)
                                    <a
                                        href="{{ $opt["url"] }}"
                                        class="{{ $opt["active"] ? "border-gray-900 bg-gray-900 text-white" : "border-gray-300 bg-white text-gray-700 hover:border-gray-400" }} rounded-lg border px-5 py-2.5 text-sm font-medium"
                                    >
                                        {{ $opt["label"] }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div>
                        <label class="mb-3 block text-sm font-medium text-gray-900">
                            Couleur :
                            <span class="font-normal text-gray-600">{{ $currentReference->color->label_couleur }}</span>
                        </label>
                        <div class="flex gap-3">
                            @foreach ($colorOptions as $opt)
                                <a
                                    href="{{ $opt["url"] }}"
                                    title="{{ $opt["label"] }}"
                                    class="{{ $opt["active"] ? "ring-2 ring-gray-900 ring-offset-2" : "opacity-70 hover:opacity-100" }} size-10 rounded-full bg-gray-200"
                                ></a>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label class="mb-3 block text-sm font-medium text-gray-900">Tailles</label>
                        <div class="flex max-w-md min-w-md flex-wrap gap-3">
                            @foreach ($sizeOptions as $opt)
                                <div class="relative">
                                    <input
                                        type="radio"
                                        name="size"
                                        id="size_{{ $opt["id"] }}"
                                        value="{{ $opt["id"] }}"
                                        class="peer sr-only"
                                        {{ $opt["disabled"] ? "disabled" : "" }}
                                        @click="selectedSize = @js($opt)"
                                        :checked="selectedSize && selectedSize.id === {{ $opt["id"] }}"
                                    />

                                    <label
                                        for="size_{{ $opt["id"] }}"
                                        class="flex cursor-pointer items-center justify-center rounded-md border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition-all peer-checked:border-black peer-checked:bg-black peer-checked:text-white peer-disabled:cursor-not-allowed peer-disabled:bg-gray-100 peer-disabled:text-gray-400 peer-disabled:opacity-50 hover:border-gray-300 hover:bg-gray-50 peer-checked:hover:border-black peer-checked:hover:bg-black"
                                    >
                                        {{ $opt["label"] }}

                                        @if ($opt["disabled"])
                                            <svg
                                                class="absolute h-full w-full text-gray-400 opacity-50"
                                                viewBox="0 0 100 100"
                                                preserveAspectRatio="none"
                                            >
                                                <line x1="0" y1="100" x2="100" y2="0" stroke="currentColor" stroke-width="1" />
                                            </svg>
                                        @endif
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-16 border-t border-gray-200 pt-12">
            <h2 class="mb-8 text-2xl font-bold text-gray-900">Fiche technique</h2>

            <div class="flex flex-col gap-4">
                @foreach ($characteristics as $type => $group)
                    <div>
                        <h3
                            class="mb-4 border-b-2 border-gray-300 pb-2 text-center text-lg font-bold tracking-wide text-gray-900 uppercase"
                        >
                            {{ $type }}
                        </h3>

                        <div class="divide-y divide-gray-200">
                            @foreach ($group as $char)
                                <div class="flex px-4 py-3">
                                    <span class="w-1/4 font-semibold text-gray-900">
                                        {{ $char->nom_caracteristique }}
                                    </span>
                                    <span class="text-gray-700">
                                        {{ $char->pivot->valeur_caracteristique }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="mt-16 border-t border-gray-200 pt-12">
            <h2 class="mb-6 text-2xl font-bold text-gray-900">Description</h2>
            <div class="prose prose-lg max-w-none leading-relaxed text-gray-700">
                {{ $bike->resumer_article }}
            </div>
        </div>

        <div class="mt-16 border-t border-gray-200 pt-12">
            <h2 class="mb-6 text-2xl font-bold text-gray-900">En Résumé</h2>
            <div class="prose prose-lg max-w-none leading-relaxed text-gray-700">
                {{ $bike->resumer_article }}
            </div>
        </div>

        <div class="mt-16 border-t border-gray-200 pt-12">
            @include(
                "article.bike.partials.geometrie",
                [
                    "modelName" => $bike->bikeModel->nom_modele_velo,
                    "sizes" => $geometrySizes,
                    "geometries" => $geometries,
                ]
            )
        </div>
    </div>

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

    <div class="mx-auto max-w-7xl px-6 py-12">
        <h2 class="mb-6 text-2xl font-bold text-gray-900">Produits similaires</h2>
        <div class="grid grid-cols-4 gap-6">
            @forelse ($similarBikes as $similarArticle)
                <x-article-card :article="$similarArticle" />
            @empty
                <p class="text-gray-600">Aucun produit similaire trouvé.</p>
            @endforelse
        </div>
    </div>
</x-app-layout>
