<x-app-layout>
    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            
            {{-- Navigation --}}
            <nav class="mb-6">
                <a href="{{ route('dashboard.orders.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-gray-900 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Retour à mes commandes
                </a>
            </nav>

            @php
                $currentState = $order->currentState();
                $stateName = $currentState?->nom_etat ?? 'En attente';
                $statusConfig = match($stateName) {
                    'Livrée' => ['bg' => 'bg-green-100', 'text' => 'text-green-800'],
                    'En cours', 'Expédiée' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800'],
                    'Annulée' => ['bg' => 'bg-red-100', 'text' => 'text-red-800'],
                    'En préparation' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-800'],
                    default => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800'],
                };
                
                $subtotal = $order->items->sum(fn($item) => $item->quantite_ligne * ($item->prix_unit_ligne ?? 0));
                $discount = $order->pourcentage_remise ? ($subtotal * $order->pourcentage_remise / 100) : 0;
                $shipping = ($order->frais_livraison ?? 0) + ($order->frais_expedition ?? 0);
                $total = $subtotal - $discount + $shipping;
                $nbArticles = $order->items->sum('quantite_ligne');
            @endphp

            {{-- En-tête de la commande --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sm:p-8 mb-6">
                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
                    {{-- Infos commande --}}
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <h1 class="text-2xl font-bold text-gray-900">Commande #{{ trim($order->num_commande) }}</h1>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }}">
                                {{ $stateName }}
                            </span>
                        </div>
                        <p class="text-gray-500">
                            Passée le {{ \Carbon\Carbon::parse($order->date_commande)->format('d/m/Y à H:i') }}
                        </p>
                        @if($order->num_suivi_commande)
                            <p class="mt-2 text-sm">
                                <span class="text-gray-500">N° de suivi :</span>
                                <span class="font-mono font-medium text-gray-900">{{ trim($order->num_suivi_commande) }}</span>
                            </p>
                        @endif
                    </div>
                    
                    {{-- Total --}}
                    <div class="lg:text-right">
                        <p class="text-sm text-gray-500">Total de la commande</p>
                        <p class="text-3xl font-bold text-gray-900">{{ number_format($total, 2, ',', ' ') }} €</p>
                        <p class="text-sm text-gray-500 mt-1">{{ $nbArticles }} article(s)</p>
                    </div>
                </div>
            </div>

            {{-- Contenu principal --}}
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                
                {{-- Colonne principale (2/3) - Articles --}}
                <div class="xl:col-span-2 space-y-6">
                    
                    {{-- Articles commandés --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                            <h2 class="text-lg font-semibold text-gray-900">Articles commandés</h2>
                            <span class="text-sm text-gray-500">{{ $order->items->count() }} référence(s)</span>
                        </div>
                        
                        <div class="divide-y divide-gray-100">
                            @foreach($order->items as $item)
                                @php
                                    $bikeRef = $item->reference?->bikeReference;
                                    $accessory = $item->reference?->accessory;
                                    
                                    // Article vient soit d'un vélo soit d'un accessoire
                                    $article = $bikeRef?->article ?? $accessory?->article ?? null;
                                    $hasArticle = $article !== null;
                                    
                                    // Couleur uniquement pour les vélos
                                    $colorName = $bikeRef?->color?->nom_couleur ?? null;
                                    $colorHex = $bikeRef?->color?->code_hex ?? '#888888';
                                    $colorId = $bikeRef?->id_couleur ?? null;
                                    
                                    // Détecter si c'est un vélo ou un accessoire
                                    $isAccessory = $accessory !== null && $bikeRef === null;
                                @endphp
                                
                                <div class="p-5">
                                    <div class="flex gap-5">
                                        {{-- Image --}}
                                        <div class="flex-shrink-0">
                                            @if ($hasArticle)
                                                <div class="w-28 h-28 sm:w-32 sm:h-32 rounded-lg bg-gray-50 border border-gray-200 overflow-hidden">
                                                    <img
                                                        src="{{ $isAccessory ? $article->getCoverThumbnailUrl() : $article->getCoverThumbnailUrl($colorId) }}"
                                                        alt="{{ $article->nom_article }}"
                                                        class="w-full h-full object-contain p-2"
                                                        loading="lazy"
                                                    />
                                                </div>
                                            @else
                                                <div class="w-28 h-28 sm:w-32 sm:h-32 rounded-lg bg-gray-100 border border-gray-200 flex items-center justify-center">
                                                    <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Détails --}}
                                        <div class="flex-1 min-w-0">
                                            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2">
                                                <div>
                                                    <h3 class="font-semibold text-gray-900">
                                                        @if ($hasArticle)
                                                            {{ $article->nom_article }}
                                                        @else
                                                            Article #{{ $item->id_reference }}
                                                        @endif
                                                    </h3>
                                                    
                                                    {{-- Sous-titre selon le type --}}
                                                    @if ($hasArticle)
                                                        @if ($isAccessory)
                                                            <p class="text-sm text-gray-500">
                                                                {{ $article->category->nom_categorie ?? 'Accessoire' }}
                                                            </p>
                                                        @elseif ($article->bike?->bikeModel)
                                                            <p class="text-sm text-gray-500">
                                                                {{ $article->bike->bikeModel->nom_modele_velo }}
                                                            </p>
                                                        @endif
                                                    @endif
                                                </div>
                                                
                                                {{-- Prix --}}
                                                <div class="text-right">
                                                    <p class="text-lg font-bold text-gray-900">
                                                        {{ number_format($item->prix_unit_ligne * $item->quantite_ligne, 2, ',', ' ') }} €
                                                    </p>
                                                    @if($item->quantite_ligne > 1)
                                                        <p class="text-xs text-gray-500">
                                                            {{ number_format($item->prix_unit_ligne, 2, ',', ' ') }} € / unité
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                            {{-- Attributs --}}
                                            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-2 text-sm text-gray-600">
                                                @if ($colorName && !$isAccessory)
                                                    <div class="flex items-center gap-1.5">
                                                        <span class="w-3 h-3 rounded-full border border-gray-300" style="background-color: {{ $colorHex }}"></span>
                                                        {{ $colorName }}
                                                    </div>
                                                @endif
                                                @if ($item->size)
                                                    <div>Taille : {{ $item->size->nom_taille ?? $item->size->libelle_taille ?? '' }}</div>
                                                @endif
                                                <div>Qté : {{ $item->quantite_ligne }}</div>
                                            </div>
                                            
                                            {{-- Lien vers le produit --}}
                                            @if ($hasArticle)
                                                <div class="mt-3">
                                                    <a href="{{ route('articles.show', $article->id_article) }}" class="inline-flex items-center gap-1 text-sm font-medium text-blue-600 hover:text-blue-800 hover:underline">
                                                        Voir ce produit
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                        </svg>
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Suivi de commande --}}
                    @if($order->states->isNotEmpty())
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h2 class="text-lg font-semibold text-gray-900">Suivi de commande</h2>
                            </div>
                            
                            <div class="p-6">
                                <div class="space-y-4">
                                    @foreach($order->states->sortByDesc('pivot.date_changement') as $state)
                                        <div class="flex items-start gap-4">
                                            <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center
                                                @if($loop->first) bg-green-500 text-white @else bg-gray-200 text-gray-500 @endif">
                                                @if($loop->first)
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                @else
                                                    <span class="text-xs font-bold">{{ $loop->iteration }}</span>
                                                @endif
                                            </div>
                                            <div class="flex-1">
                                                <p class="font-medium text-gray-900">{{ $state->nom_etat }}</p>
                                                <p class="text-sm text-gray-500">
                                                    {{ \Carbon\Carbon::parse($state->pivot->date_changement)->format('d/m/Y à H:i') }}
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Colonne latérale (1/3) --}}
                <div class="space-y-6">
                    
                    {{-- Récapitulatif --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-200">
                            <h2 class="font-semibold text-gray-900">Récapitulatif</h2>
                        </div>
                        
                        <div class="p-5">
                            <dl class="space-y-3 text-sm">
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Sous-total ({{ $nbArticles }} articles)</dt>
                                    <dd class="font-medium text-gray-900">{{ number_format($subtotal, 2, ',', ' ') }} €</dd>
                                </div>

                                @if($discount > 0)
                                    <div class="flex justify-between text-green-600">
                                        <dt>Remise (-{{ $order->pourcentage_remise }}%)</dt>
                                        <dd class="font-medium">-{{ number_format($discount, 2, ',', ' ') }} €</dd>
                                    </div>
                                @endif

                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Livraison</dt>
                                    <dd class="font-medium text-gray-900">
                                        @if($shipping > 0)
                                            {{ number_format($shipping, 2, ',', ' ') }} €
                                        @else
                                            <span class="text-green-600">Offerts</span>
                                        @endif
                                    </dd>
                                </div>

                                <div class="pt-3 mt-3 border-t border-gray-200">
                                    <div class="flex justify-between items-center">
                                        <dt class="font-semibold text-gray-900">Total TTC</dt>
                                        <dd class="text-xl font-bold text-gray-900">{{ number_format($total, 2, ',', ' ') }} €</dd>
                                    </div>
                                </div>
                            </dl>
                        </div>
                    </div>

                    {{-- Livraison --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-200">
                            <h2 class="font-semibold text-gray-900">Livraison</h2>
                        </div>
                        
                        <div class="p-5 space-y-4 text-sm">
                            @if($order->deliveryMode)
                                <div>
                                    <p class="text-gray-500 mb-1">Mode de livraison</p>
                                    <p class="font-medium text-gray-900">{{ $order->deliveryMode->nom_moyen_livraison ?? 'Standard' }}</p>
                                </div>
                            @endif

                            @if($order->deliveryAddress)
                                <div>
                                    <p class="text-gray-500 mb-1">Adresse</p>
                                    <address class="not-italic text-gray-900">
                                        <p class="font-medium">{{ $order->deliveryAddress->prenom_adresse }} {{ $order->deliveryAddress->nom_adresse }}</p>
                                        <p>{{ $order->deliveryAddress->num_voie_adresse }} {{ $order->deliveryAddress->rue_adresse }}</p>
                                        @if($order->deliveryAddress->complement_adresse)
                                            <p>{{ $order->deliveryAddress->complement_adresse }}</p>
                                        @endif
                                        @if($order->deliveryAddress->ville)
                                            <p>{{ $order->deliveryAddress->ville->cp_ville }} {{ $order->deliveryAddress->ville->nom_ville }}</p>
                                        @endif
                                    </address>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Facturation --}}
                    @if($order->billingAddress)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="px-5 py-4 border-b border-gray-200">
                                <h2 class="font-semibold text-gray-900">Facturation</h2>
                            </div>
                            
                            <div class="p-5 space-y-4 text-sm">
                                <div>
                                    <p class="text-gray-500 mb-1">Adresse</p>
                                    <address class="not-italic text-gray-900">
                                        <p class="font-medium">{{ $order->billingAddress->prenom_adresse }} {{ $order->billingAddress->nom_adresse }}</p>
                                        <p>{{ $order->billingAddress->num_voie_adresse }} {{ $order->billingAddress->rue_adresse }}</p>
                                        @if($order->billingAddress->complement_adresse)
                                            <p>{{ $order->billingAddress->complement_adresse }}</p>
                                        @endif
                                        @if($order->billingAddress->ville)
                                            <p>{{ $order->billingAddress->ville->cp_ville }} {{ $order->billingAddress->ville->nom_ville }}</p>
                                        @endif
                                    </address>
                                </div>

                                @if($order->cb_last4)
                                    <div>
                                        <p class="text-gray-500 mb-1">Paiement</p>
                                        <p class="font-medium text-gray-900">Carte •••• {{ $order->cb_last4 }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Aide --}}
                    <div class="bg-gray-100 rounded-xl p-5 text-sm">
                        <p class="font-semibold text-gray-900">Besoin d'aide ?</p>
                        <p class="text-gray-600 mt-1">Une question sur votre commande ?</p>
                        <a href="mailto:contact@cube-france.fr" class="inline-flex items-center gap-1 mt-2 font-medium text-gray-700 hover:text-gray-900">
                            Nous contacter →
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>