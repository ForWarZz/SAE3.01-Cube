<x-app-layout>
    <div class="max-w-7xl mx-auto px-6 py-12">
        <div class="flex gap-6 justify-between">
            <div class="flex-shrink-0 w-1/2">
                <img src="{{ $bike->article->getCoverUrl($currentReference->color->id_couleur) }}"
                     alt="{{ $bike->nom_article }} - {{ $currentReference->color->label_couleur }}"
                     class="w-full h-auto object-cover rounded-lg shadow"
                     loading="lazy">
            </div>

            <div class="flex flex-col">
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 mb-3">{{ $bike->nom_article }}</h1>
                    <div class="flex items-center gap-3 text-gray-600 mb-4">
                        <span>{{ $bike->bikeModel->nom_modele_velo }}</span>
                        @if($isEbike)
                            <span class="text-blue-600 font-medium">Électrique</span>
                        @endif
                    </div>
                    <div class="flex flex-col">
                        <div class="flex gap-2 mb-2">
                            <span class="p-1 bg-gray-100 text-black text-sm">
                                Poids : {{ $weight }}
                            </span>
                            <span class="p-1 bg-gray-100 text-black text-sm">
                                Millesime : {{ $bike->vintage->millesime_velo }}
                            </span>
                            <span class="p-1 bg-gray-100 text-black text-sm">
                                Materiau du cadre : {{ $bike->frameMaterial->label_materiau_cadre }}
                            </span>
                        </div>

                        <div class="text-3xl font-bold text-blue-600">
                            {{ number_format($bike->prix_article, 2, ',', ' ') }} €
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-8">
                    <div>
                        <label class="block text-sm font-medium text-gray-900 mb-3">Type de cadre</label>
                        <div class="flex gap-2">
                            @foreach($frameOptions as $opt)
                                <a href="{{ $opt['url'] }}"
                                   class="px-5 py-2.5 border rounded-lg text-sm font-medium {{ $opt['active'] ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-700 border-gray-300 hover:border-gray-400' }}">
                                    {{ $opt['label'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    @if($isEbike && $batteryOptions->count() > 0)
                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-3">Batterie</label>
                            <div class="flex gap-2">
                                @foreach($batteryOptions as $opt)
                                    <a href="{{ $opt['url'] }}"
                                       class="px-5 py-2.5 border rounded-lg text-sm font-medium {{ $opt['active'] ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-700 border-gray-300 hover:border-gray-400' }}">
                                        {{ $opt['label'] }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-900 mb-3">
                            Couleur : <span class="font-normal text-gray-600">{{ $currentReference->color->label_couleur }}</span>
                        </label>
                        <div class="flex gap-3">
                            @foreach($colorOptions as $opt)
                                <a href="{{ $opt['url'] }}"
                                   title="{{ $opt['label'] }}"
                                   class="size-10 rounded-full bg-gray-200 {{ $opt['active'] ? 'ring-2 ring-offset-2 ring-gray-900' : 'opacity-70 hover:opacity-100' }}">
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-900 mb-3">Tailles</label>
                        <div class="flex flex-wrap gap-3 min-w-md max-w-md">
                            @foreach($sizeOptions as $opt)
                                <div class="relative">
                                    <input type="radio"
                                           name="size"
                                           id="size_{{ $opt['id'] }}"
                                           value="{{ $opt['id'] }}"
                                           class="peer sr-only"
                                        {{ $opt['disabled'] ? 'disabled' : '' }}>

                                    <label for="size_{{ $opt['id'] }}"
                                           class="flex items-center justify-center px-4 py-2 border rounded-md text-sm font-medium cursor-pointer transition-all
                                          bg-white border-gray-200 text-gray-700 hover:bg-gray-50 hover:border-gray-300
                                          peer-checked:bg-black peer-checked:text-white peer-checked:border-black
                                          peer-checked:hover:bg-black peer-checked:hover:border-black
                                          peer-disabled:opacity-50 peer-disabled:cursor-not-allowed peer-disabled:bg-gray-100 peer-disabled:text-gray-400">

                                        {{ $opt['label'] }}

                                        @if($opt['disabled'])
                                            <svg class="absolute w-full h-full text-gray-400 opacity-50"
                                                 viewBox="0 0 100 100" preserveAspectRatio="none">
                                                <line x1="0" y1="100" x2="100" y2="0" stroke="currentColor"
                                                      stroke-width="1"/>
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

        <div class="mt-16 pt-12 border-t border-gray-200">
            <h2 class="text-2xl font-bold text-gray-900 mb-8">Fiche technique</h2>

            <div class="flex flex-col gap-4">
                @foreach($characteristics as $type => $group)
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 uppercase tracking-wide mb-4 pb-2 border-b-2 border-gray-300 text-center">
                            {{ $type }}
                        </h3>

                        <div class="divide-y divide-gray-200">
                            @foreach($group as $char)
                                <div class="flex py-3 px-4">
                                        <span class="font-semibold text-gray-900 w-1/4">
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

        <div class="mt-16 pt-12 border-t border-gray-200">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Description</h2>
            <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed">
                {{ $bike->resumer_article }}
            </div>
        </div>

        <div class="mt-16 pt-12 border-t border-gray-200">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">En Résumé</h2>
            <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed">
                {{ $bike->resumer_article }}
            </div>
        </div>

        @if($geometries)
            <div class="mt-16 pt-12 border-t border-gray-200">
                @include('article.bike.partials.geometrie', [
                    'modelName' => $bike->bikeModel->nom_modele_velo,
                    'sizes' => $geometrySizes,
                    'geometries' => $geometries
                ])
            </div>
        @endif

    </div>
</x-app-layout>
