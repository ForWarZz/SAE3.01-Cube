<div class="flex flex-col" x-data="{ selectedSize: @js($sizeOptions?->first(fn ($s) => ! $s["disabled"])) }">
    <div class="mb-8">
        <div class="mb-4 flex items-center gap-2">
            @if ($article->bike?->isNew())
                <span class="bg-lime-400 px-3 py-1 text-xs font-bold text-black uppercase">Nouveau</span>
            @endif

            @if ($article->bike?->vintage)
                <span class="border border-gray-300 bg-white px-3 py-1 text-xs font-bold text-gray-700 uppercase">
                    Saison {{ $article->bike->vintage->millesime_velo }}
                </span>
            @endif
        </div>

        <div class="mb-4 flex items-center gap-3 text-sm text-gray-500 uppercase">
            @if ($article->bike)
                <span>{{ $article->bike->bikeModel->nom_modele_velo }}</span>

                @if ($article->bike->ebike)
                    <span class="font-medium text-blue-600">Électrique</span>
                @endif
            @elseif ($article->accessory)
                <span class="font-medium text-gray-600">{{ $article->category->nom_categorie ?? "Accessoire" }}</span>
            @endif
        </div>

        <h1 class="mb-4 text-3xl font-bold text-gray-900">{{ $article->nom_article }}</h1>

        <div class="mb-4 flex flex-wrap gap-2 text-sm">
            <span class="inline-flex items-center gap-1 rounded-md border border-gray-200 bg-gray-100 px-2 py-1 font-medium text-gray-700">
                <x-bi-hash class="size-4 text-gray-500" />
                {{ $currentReference->id_reference }}
            </span>

            <span class="inline-flex items-center gap-1 rounded-md border border-gray-200 bg-gray-100 px-2 py-1 font-medium text-gray-700">
                <x-bi-box-seam class="size-4 text-gray-500" />
                {{ $weight }}
            </span>

            @if ($article->bike?->frameMaterial)
                <span
                    class="inline-flex items-center gap-1 rounded-md border border-gray-200 bg-gray-100 px-2 py-1 font-medium text-gray-700"
                >
                    <x-bi-layers class="size-4 text-gray-500" />
                    {{ $article->bike->frameMaterial->label_materiau_cadre }}
                </span>
            @endif
        </div>

        <div class="flex flex-col">
            <span class="text-3xl font-bold text-blue-600">{{ number_format($discountedPrice, 2, ",", " ") }} €</span>

            @if ($hasDiscount)
                <div class="flex items-center space-x-2">
                    <span class="text-md font-semibold text-gray-400 line-through">{{ number_format($realPrice, 2, ",", " ") }} €</span>
                    <span class="rounded bg-red-600 px-2 py-0.5 text-xs font-bold text-white">-{{ $discountPercent }}%</span>
                </div>
            @endif
        </div>

        @if (isset($sizeOptions))
            <div class="mt-3 text-sm text-gray-700" x-show="selectedSize">
                <span :class="selectedSize.availableOnline ? 'text-green-600 font-medium' : 'text-gray-400'">
                    <span x-text="selectedSize.availableOnline ? 'Disponible en ligne' : 'Non disponible en ligne'"></span>
                </span>

                <br />

                <template x-if="selectedSize.shopStatus === 'in_stock'">
                    <span class="font-medium text-green-600">En stock en magasin</span>
                </template>

                <template x-if="selectedSize.shopStatus === 'orderable'">
                    <span class="font-medium text-orange-500">Commandable en magasin</span>
                </template>

                <template x-if="selectedSize.shopStatus === 'unavailable'">
                    <span class="text-gray-400">Indisponible en magasin</span>
                </template>
            </div>
        @endif
    </div>

    <form method="post" action="{{ route("cart.add") }}" class="flex flex-col gap-8">
        @csrf

        <input hidden type="text" name="reference_id" value="{{ $currentReference->id_reference }}" />

        @if ($article->bike)
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

            @if ($article->bike->ebike && $batteryOptions->count() > 0)
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
                            style="background-color: {{ $opt["hex"] }}"
                            href="{{ $opt["url"] }}"
                            title="{{ $opt["label"] }}"
                            class="{{ $opt["active"] ? "ring-2 ring-gray-900 ring-offset-2" : "opacity-70 hover:opacity-100" }} size-10 rounded-full"
                        ></a>
                    @endforeach
                </div>
            </div>
        @endif

        <div>
            <label class="mb-3 block text-sm font-medium text-gray-900">Tailles</label>

            <div class="flex max-w-md min-w-md flex-wrap gap-3">
                @foreach ($sizeOptions as $opt)
                    <div class="relative">
                        <input
                            type="radio"
                            name="size_id"
                            id="size_{{ $opt["id"] }}"
                            value="{{ $opt["id"] }}"
                            class="peer sr-only"
                            @click="selectedSize = @js($opt)"
                            :checked="selectedSize && selectedSize.id === {{ $opt["id"] }}"
                        />
                        <label
                            for="size_{{ $opt["id"] }}"
                            class="{{ $opt["disabled"] ? "cursor-not-allowed bg-gray-100 text-gray-400 opacity-50" : "cursor-pointer" }} flex items-center justify-center rounded-md border px-4 py-2 text-sm font-medium text-gray-700 transition-all peer-checked:border-black peer-checked:bg-black peer-checked:text-white"
                        >
                            {{ $opt["label"] }}
                        </label>
                    </div>
                @endforeach
            </div>
        </div>

        <button
            x-show="selectedSize && !selectedSize.disabled"
            type="submit"
            class="mt-6 flex cursor-pointer items-center justify-center gap-3 rounded-lg bg-black px-5 py-4 text-xl font-bold text-white transition-colors hover:bg-gray-900"
        >
            <x-bi-cart-plus class="size-6" />
            <span>Ajouter au panier</span>
        </button>
    </form>
</div>
