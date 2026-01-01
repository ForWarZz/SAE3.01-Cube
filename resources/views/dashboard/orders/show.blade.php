@php
    use Carbon\Carbon;
@endphp

<x-app-layout>
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="mx-auto max-w-7xl px-8">
            <nav class="mb-6">
                <a
                    href="{{ route("dashboard.orders.index") }}"
                    class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 transition-colors hover:text-gray-900"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Retour à mes commandes
                </a>
            </nav>

            <div class="mb-6 rounded-xl border border-gray-200 bg-white p-8 shadow-sm">
                <div class="flex items-start justify-between">
                    <div>
                        <div class="mb-2 flex items-center gap-3">
                            <h1 class="text-2xl font-bold text-gray-900">Commande #{{ $order->num_commande }}</h1>
                            <span
                                class="{{ $statusColors->bg }} {{ $statusColors->text }} inline-flex items-center rounded-full px-3 py-1 text-sm font-medium"
                            >
                                {{ $statusName }}
                            </span>
                        </div>

                        <p class="text-gray-500">
                            Passée le
                            <x-date-local :date="$order->date_commande" />
                        </p>

                        @if ($order->num_suivi_commande)
                            <p class="mt-2 text-sm">
                                <span class="text-gray-500">N° de suivi :</span>
                                <span class="font-mono font-medium text-gray-900">{{ $order->num_suivi_commande }}</span>
                            </p>
                        @endif
                    </div>

                    <div class="text-right">
                        <p class="text-sm text-gray-500">Total de la commande</p>
                        <p class="text-3xl font-bold text-gray-900">{{ number_format($financials->total, 2, ",", " ") }} €</p>
                        <p class="mt-1 text-sm text-gray-500">{{ $financials->count }} article(s)</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-6">
                <div class="col-span-2 space-y-6">
                    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                        <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                            <h2 class="text-lg font-semibold text-gray-900">Articles commandés</h2>
                        </div>

                        <div class="divide-y divide-gray-100">
                            @foreach ($items as $item)
                                <div class="p-5">
                                    <div class="flex gap-5">
                                        <div class="size-32 flex-shrink-0 overflow-hidden rounded-lg border border-gray-200">
                                            <img
                                                src="{{ $item->image }}"
                                                alt="{{ $item->name }}"
                                                class="h-full w-full object-contain p-2"
                                                loading="lazy"
                                            />
                                        </div>

                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-start justify-between">
                                                <div>
                                                    <h3 class="font-semibold text-gray-900">{{ $item->name }}</h3>
                                                    @if ($item->subtitle)
                                                        <p class="text-sm text-gray-500">{{ $item->subtitle }}</p>
                                                    @endif
                                                </div>
                                                <div class="text-right">
                                                    <p class="text-lg font-bold text-gray-900">
                                                        {{ number_format($item->totalPrice, 2, ",", " ") }} €
                                                    </p>
                                                    @if ($item->quantity > 1)
                                                        <p class="text-xs text-gray-500">
                                                            {{ number_format($item->unitPrice, 2, ",", " ") }} € / unité
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="mt-2 flex flex-col gap-1 text-sm text-gray-600">
                                                @if ($item->colorName)
                                                    <div class="flex items-center gap-1.5">
                                                        <span
                                                            class="h-3 w-3 rounded-full border border-gray-300"
                                                            style="background-color: {{ $item->colorHex }}"
                                                        ></span>
                                                        {{ $item->colorName }}
                                                    </div>
                                                @endif

                                                @if ($item->size)
                                                    <div>Taille : {{ $item->size }}</div>
                                                @endif

                                                <div>Qté : {{ $item->quantity }}</div>
                                            </div>

                                            @if ($item->articleId)
                                                <div class="mt-3">
                                                    <a
                                                        href="{{ route("articles.show", $item->articleId) }}"
                                                        class="inline-flex items-center gap-1 text-sm font-medium text-blue-600 hover:text-blue-800 hover:underline"
                                                    >
                                                        Voir ce produit
                                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path
                                                                stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M9 5l7 7-7 7"
                                                            />
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

                    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                        <div class="border-b border-gray-200 px-6 py-4">
                            <h2 class="text-lg font-semibold text-gray-900">Suivi de commande</h2>
                        </div>
                        <div class="space-y-4 p-6">
                            @foreach ($order->states as $state)
                                <div class="flex gap-4">
                                    <div class="flex size-8 flex-shrink-0 items-center justify-center rounded-full bg-green-500 text-white">
                                        <x-heroicon-o-check class="size-4" />
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $state->label_etat }}</p>
                                        <p class="text-sm text-gray-500">
                                            <x-date-local :date="Carbon::parse($state->pivot->date_changement)" />
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                        <div class="border-b border-gray-200 px-5 py-4">
                            <h2 class="font-semibold text-gray-900">Récapitulatif</h2>
                        </div>
                        <div class="p-5">
                            <dl class="space-y-3 text-sm">
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Sous-total</dt>
                                    <dd class="font-medium text-gray-900">{{ number_format($financials->subtotal, 2, ",", " ") }} €</dd>
                                </div>
                                @if ($financials->discount > 0)
                                    <div class="flex justify-between text-green-600">
                                        <dt>Remise (-{{ $order->pourcentage_remise }}%)</dt>
                                        <dd class="font-medium">-{{ number_format($financials->discount, 2, ",", " ") }} €</dd>
                                    </div>
                                @endif

                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Livraison</dt>
                                    <dd class="font-medium text-gray-900">
                                        @if ($financials->shipping > 0)
                                            {{ number_format($financials->shipping, 2, ",", " ") }} €
                                        @else
                                            <span class="text-green-600">Offerts</span>
                                        @endif
                                    </dd>
                                </div>
                                <div class="mt-3 flex items-center justify-between border-t border-gray-200 pt-3">
                                    <dt class="font-semibold text-gray-900">Total TTC</dt>
                                    <dd class="text-xl font-bold text-gray-900">{{ number_format($financials->total, 2, ",", " ") }} €</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                        <div class="border-b border-gray-200 px-5 py-4">
                            <h2 class="font-semibold text-gray-900">Livraison & Facturation</h2>
                        </div>
                        <div class="space-y-6 p-5 text-sm">
                            @if ($shop)
                                <div>
                                    <p class="mb-1 text-xs font-medium tracking-wide text-gray-500 uppercase">Point de retrait</p>
                                    <address class="text-gray-900 not-italic">
                                        <p class="flex items-center gap-1 font-bold text-blue-600">
                                            <x-heroicon-o-building-storefront class="size-4" />
                                            {{ $shop->nom_magasin }}
                                        </p>
                                        <p>{{ $shop->full_address }}</p>
                                        @if ($shop->complement_magasin)
                                            <p>{{ $shop->complement_magasin }}</p>
                                        @endif

                                        <p>{{ $shop->city->cp_ville }} {{ $shop->city->nom_ville }}</p>
                                    </address>
                                    <p class="mt-2 text-gray-500">Mode : {{ $order->shippingMode->label_moyen_livraison }}</p>
                                </div>
                            @elseif ($order->deliveryAddress)
                                <div>
                                    <p class="mb-1 text-xs font-medium tracking-wide text-gray-500 uppercase">Adresse de livraison</p>
                                    <address class="text-gray-900 not-italic">
                                        <p class="font-medium">
                                            {{ $order->deliveryAddress->prenom_adresse }} {{ $order->deliveryAddress->nom_adresse }}
                                        </p>
                                        <p>{{ $order->deliveryAddress->num_voie_adresse }} {{ $order->deliveryAddress->rue_adresse }}</p>
                                        @if ($order->deliveryAddress->complement_adresse)
                                            <p>{{ $order->deliveryAddress->complement_adresse }}</p>
                                        @endif

                                        <p>
                                            {{ $order->deliveryAddress->city->cp_ville }} {{ $order->deliveryAddress->city->nom_ville }}
                                        </p>
                                    </address>
                                    <p class="mt-2 text-gray-500">Mode : {{ $order->shippingMode->label_moyen_livraison }}</p>
                                </div>
                            @endif

                            <div class="border-t border-gray-100"></div>

                            @if ($order->billingAddress)
                                <div>
                                    <p class="mb-1 text-xs font-medium tracking-wide text-gray-500 uppercase">Adresse de facturation</p>
                                    <address class="mb-3 text-gray-900 not-italic">
                                        <p class="font-medium">
                                            {{ $order->billingAddress->prenom_adresse }} {{ $order->billingAddress->nom_adresse }}
                                        </p>
                                        <p>{{ $order->billingAddress->num_voie_adresse }} {{ $order->billingAddress->rue_adresse }}</p>
                                        <p>{{ $order->billingAddress->city->cp_ville }} {{ $order->billingAddress->city->nom_ville }}</p>
                                    </address>

                                    <div class="flex items-center gap-3">
                                        @if ($order->date_paiement)
                                            <span
                                                class="inline-flex items-center gap-1.5 rounded bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700"
                                            >
                                                <x-heroicon-o-credit-card class="size-4" />
                                                {{ $paymentType }}
                                            </span>
                                        @endif

                                        @if ($order->cb_last4)
                                            <span class="text-xs text-gray-500">•••• {{ $order->cb_last4 }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
