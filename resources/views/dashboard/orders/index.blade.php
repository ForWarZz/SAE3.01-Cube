<x-app-layout>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6">
                    <h1 class="text-2xl font-bold text-gray-900">Mes commandes</h1>
                    <p class="mt-1 text-sm text-gray-500">Historique de vos commandes passées</p>

                    <div class="mt-6">
                        @if ($orders->isEmpty())
                            <div class="py-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                                <h3 class="mt-2 text-lg font-medium text-gray-900">Aucune commande</h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    Vous n'avez pas encore passé de commande.
                                </p>
                                <a href="{{ route('home') }}" 
                                   class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">
                                    Découvrir nos produits
                                </a>
                            </div>
                        @else
                            <div class="grid gap-4 sm:grid-cols-1 lg:grid-cols-2">
                                @foreach ($orders as $order)
                                    @php
                                        $currentState = $order->currentState();
                                        $stateName = $currentState?->nom_etat ?? 'En attente';
                                        
                                        $statusColors = match($stateName) {
                                            'Livrée' => 'bg-green-100 text-green-800',
                                            'En cours', 'Expédiée' => 'bg-blue-100 text-blue-800',
                                            'Annulée' => 'bg-red-100 text-red-800',
                                            'En préparation' => 'bg-yellow-100 text-yellow-800',
                                            default => 'bg-gray-100 text-gray-800',
                                        };
                                        
                                        $subtotal = $order->items->sum(fn($item) => $item->quantite_ligne * ($item->prix_unit_ligne ?? 0));
                                        $discount = $order->pourcentage_remise ? ($subtotal * $order->pourcentage_remise / 100) : 0;
                                        $total = $subtotal - $discount + ($order->frais_livraison ?? 0);
                                        $nbArticles = $order->items->sum('quantite_ligne');
                                    @endphp

                                    <div class="relative flex flex-col justify-between rounded-lg border bg-white border-gray-200 p-4 transition hover:shadow-md">
                                        <div class="flex items-start justify-between">
                                            <div class="text-sm flex-1">
                                                {{-- Numéro et statut --}}
                                                <div class="flex items-center gap-2 mb-2">
                                                    <h3 class="font-bold text-gray-900">#{{ trim($order->num_commande) }}</h3>
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $statusColors }}">
                                                        {{ $stateName }}
                                                    </span>
                                                </div>

                                                {{-- Date --}}
                                                <p class="text-gray-600">
                                                    Commandé le {{ \Carbon\Carbon::parse($order->date_commande)->format('d/m/Y') }}
                                                </p>

                                                {{-- Numéro de suivi --}}
                                                @if ($order->num_suivi_commande)
                                                    <p class="text-gray-600 mt-1">
                                                        Suivi : <span class="font-mono text-xs bg-gray-100 px-1 py-0.5 rounded">{{ trim($order->num_suivi_commande) }}</span>
                                                    </p>
                                                @endif

                                                {{-- Adresse de livraison --}}
                                                @if ($order->deliveryAddress)
                                                    <div class="mt-3 pt-3 border-t border-gray-100">
                                                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Livraison</p>
                                                        <p class="text-gray-600">{{ $order->deliveryAddress->prenom_adresse }} {{ $order->deliveryAddress->nom_adresse }}</p>
                                                        <p class="text-gray-600">{{ $order->deliveryAddress->num_voie_adresse }} {{ $order->deliveryAddress->rue_adresse }}</p>
                                                        @if ($order->deliveryAddress->ville)
                                                            <p class="text-gray-600">{{ $order->deliveryAddress->ville->cp_ville }} {{ $order->deliveryAddress->ville->nom_ville }}</p>
                                                        @endif
                                                    </div>
                                                @endif

                                                {{-- Résumé financier --}}
                                                <div class="mt-3 pt-3 border-t border-gray-100 space-y-1">
                                                    <div class="flex justify-between">
                                                        <span class="text-gray-500">{{ $nbArticles }} article(s)</span>
                                                        <span class="text-gray-900">{{ number_format($subtotal, 2, ',', ' ') }} €</span>
                                                    </div>
                                                    @if ($order->frais_livraison > 0)
                                                        <div class="flex justify-between">
                                                            <span class="text-gray-500">Livraison</span>
                                                            <span class="text-gray-900">{{ number_format($order->frais_livraison, 2, ',', ' ') }} €</span>
                                                        </div>
                                                    @endif
                                                    @if ($discount > 0)
                                                        <div class="flex justify-between text-green-600">
                                                            <span>Remise (-{{ $order->pourcentage_remise }}%)</span>
                                                            <span>-{{ number_format($discount, 2, ',', ' ') }} €</span>
                                                        </div>
                                                    @endif
                                                    <div class="flex justify-between font-bold pt-1 border-t border-gray-100">
                                                        <span class="text-gray-900">Total</span>
                                                        <span class="text-gray-900">{{ number_format($total, 2, ',', ' ') }} €</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="ml-4">
                                                <a href="{{ route('dashboard.orders.show', $order->id_commande) }}" 
                                                   class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-800">
                                                    Détails
                                                    <svg class="ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                    </svg>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Pagination (seulement si c'est un paginator) --}}
                            @if (method_exists($orders, 'hasPages') && $orders->hasPages())
                                <div class="mt-6">
                                    {{ $orders->links() }}
                                </div>
                            @endif
                        @endif
                    </div>

                    <div class="mt-6 border-t pt-4">
                        <a href="{{ route('dashboard.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                            &larr; Retour au tableau de bord
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>