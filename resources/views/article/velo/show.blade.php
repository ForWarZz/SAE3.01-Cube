<x-app-layout>
    <div class="max-w-7xl mx-auto px-6 py-12">
        <div class="flex gap-6 justify-between">
            <div class="size-48"></div>

            <div class="flex flex-col">
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 mb-3">{{ $article->nom_article }}</h1>
                    <div class="flex items-center gap-3 text-gray-600 mb-4">
                        <span>{{ $article->modeleVelo->nom_modele_velo }}</span>
                        @if($isVae)
                            <span class="text-blue-600 font-medium">Électrique</span>
                        @endif
                    </div>
                    <div class="flex flex-col">
                        <div class="flex gap-2 mb-2">
                            <span class="p-1 bg-gray-100 text-black text-sm">
                                Poids: {{ $poids }}
                            </span>
                            <span class="p-1 bg-gray-100 text-black text-sm">
                                Millesime: {{ $article->millesime->millesime_velo }}
                            </span>
                            <span class="p-1 bg-gray-100 text-black text-sm">
                                Materiau du cadre: {{ $article->materiauCadre->label_materiau_cadre }}
                            </span>
                        </div>

                        <div class="text-3xl font-bold text-blue-600">
                            {{ number_format($article->prix_article, 2, ',', ' ') }} €
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-8">
                    <div>
                        <label class="block text-sm font-medium text-gray-900 mb-3">Type de cadre</label>
                        <div class="flex gap-2">
                            @foreach($optionsCadres as $opt)
                                <a href="{{ $opt['url'] }}"
                                   class="px-5 py-2.5 border rounded-lg text-sm font-medium {{ $opt['active'] ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-700 border-gray-300 hover:border-gray-400' }}">
                                    {{ $opt['label'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    @if($isVae && $optionsBatteries->count() > 0)
                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-3">Capacité batterie</label>
                            <div class="flex gap-2">
                                @foreach($optionsBatteries as $opt)
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
                            Couleur : <span class="font-normal text-gray-600">{{ $currentRef->couleur->label_couleur }}</span>
                        </label>
                        <div class="flex gap-3">
                            @foreach($optionsCouleurs as $opt)
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
                            @foreach($optionsTailles as $opt)
                                <div class="relative">
                                    <input type="radio"
                                           name="taille"
                                           id="taille_{{ $opt['id'] }}"
                                           value="{{ $opt['id'] }}"
                                           class="peer sr-only"
                                        {{ $opt['disabled'] ? 'disabled' : '' }}>

                                    <label for="taille_{{ $opt['id'] }}"
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

        @if($geometries)
            <div class="mt-16 pt-12 border-t border-gray-200">
                @include('article.velo.partials.geometrie', [
                    'nomModele' => $article->modeleVelo->nom_modele_velo,
                    'tailles' => $taillesGeo,
                    'geometries' => $geometries
                ])
            </div>
        @endif

    </div>
</x-app-layout>
