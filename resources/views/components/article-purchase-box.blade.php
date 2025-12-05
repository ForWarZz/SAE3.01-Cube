<div class="flex flex-col" x-data="{ selectedSize: @js($sizeOptions?->first(fn ($s) => ! $s['disabled'])) }">
    <div class="mb-8">
        <h1 class="mb-3 text-3xl font-bold text-gray-900">{{ $article->nom_article }}</h1>

        <div class="mb-4 flex items-center gap-3 text-gray-600">
            @if ($article->bike)
                <span>{{ $article->bike->bikeModel->nom_modele_velo }}</span>

                @if ($article->bike->ebike)
                    <span class="font-medium text-blue-600">Électrique</span>
                @endif
            @elseif ($article->accessory)
                <span class="font-medium text-gray-600">{{ $article->category->nom_categorie ?? "Accessoire" }}</span>
            @endif
        </div>

        <div class="mb-2 flex gap-2 text-sm text-black">
            <span class="bg-gray-100 p-1">Référence : {{ $currentReference->id_reference }}</span>
            <span class="bg-gray-100 p-1">Poids : {{ $weight }}</span>

            @if ($article->bike)
                <span class="bg-gray-100 p-1">Millésime : {{ $article->bike->vintage->millesime_velo }}</span>
                <span class="bg-gray-100 p-1">Matériau du cadre : {{ $article->bike->frameMaterial->label_materiau_cadre }}</span>
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

        <!-- Pour voir les disponibilitées -->
        <div class="mt-4">
            <button type="button" x-on:click="$dispatch('open-shop-modal', { showAvailability: true, referenceId: {{ $currentReference->id_reference }}, sizeId: selectedSize ? selectedSize.id : null })" class="w-full border-2 border-black bg-white text-black py-3 px-6 font-bold hover:bg-gray-100 transition flex items-center justify-center">
                <span class="mr-2">▸</span>
                VOIR LES DISPONIBILITÉS
            </button>
        </div>

        
    </div>

    @if ($article->bike)
        <div class="flex flex-col gap-8">
            <div>
                <label class="mb-3 block text-sm font-medium text-gray-900">Type de cadre</label>

                <div class="flex gap-2">
                    @foreach ($frameOptions as $opt)
                        <a href="{{ $opt['url'] }}" class="{{ $opt['active'] ? 'border-gray-900 bg-gray-900 text-white' : 'border-gray-300 bg-white text-gray-700 hover:border-gray-400' }} rounded-lg border px-5 py-2.5 text-sm font-medium">{{ $opt['label'] }}</a>
                    @endforeach
                </div>
            </div>

            @if ($article->bike->ebike && $batteryOptions->count() > 0)
                <div>
                    <label class="mb-3 block text-sm font-medium text-gray-900">Batterie</label>

                    <div class="flex gap-2">
                        @foreach ($batteryOptions as $opt)
                            <a href="{{ $opt['url'] }}" class="{{ $opt['active'] ? 'border-gray-900 bg-gray-900 text-white' : 'border-gray-300 bg-white text-gray-700 hover:border-gray-400' }} rounded-lg border px-5 py-2.5 text-sm font-medium">{{ $opt['label'] }}</a>
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
                        <a href="{{ $opt['url'] }}" title="{{ $opt['label'] }}" style="background-color: {{ $opt['hex'] }}" class="{{ $opt['active'] ? 'ring-2 ring-gray-900 ring-offset-2' : 'opacity-70 hover:opacity-100' }} size-10 rounded-full"></a>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="mb-3 block text-sm font-medium text-gray-900">Tailles</label>

                <div class="flex max-w-md min-w-md flex-wrap gap-3">
                    @foreach ($sizeOptions as $opt)
                        <div class="relative">
                            <input type="radio" name="size" id="size_{{ $opt['id'] }}" value="{{ $opt['id'] }}" class="peer sr-only" {{ $opt['disabled'] ? 'disabled' : '' }} x-on:click="selectedSize = @js($opt)" x-bind:checked="selectedSize && selectedSize.id === {{ $opt['id'] }}" />
                            <label for="size_{{ $opt['id'] }}" class="flex cursor-pointer items-center justify-center rounded-md border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition-all peer-checked:border-black peer-checked:bg-black peer-checked:text-white peer-disabled:cursor-not-allowed peer-disabled:bg-gray-100 peer-disabled:text-gray-400 peer-disabled:opacity-50 hover:border-gray-300 hover:bg-gray-50 peer-checked:hover:border-black peer-checked:hover:bg-black">
                                {{ $opt['label'] }}
                                @if ($opt['disabled'])
                                    <svg class="absolute h-full w-full text-gray-400 opacity-50" viewBox="0 0 100 100" preserveAspectRatio="none">
                                        <line x1="0" y1="100" x2="100" y2="0" stroke="currentColor" stroke-width="1" />
                                    </svg>
                                @endif
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>