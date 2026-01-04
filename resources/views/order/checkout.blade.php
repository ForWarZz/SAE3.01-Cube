<x-app-layout>
    <div class="min-h-screen bg-gray-100 px-24 py-12">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Finaliser ma commande</h1>
        </div>

        @php
            $defaultId = $addresses->first()->id_adresse ?? null;
            $billingId = $orderData->billing_address_id ?? $defaultId;
            $deliveryId = $orderData->delivery_address_id ?? $defaultId;
            $shippingId = $selectedShippingId;
            $ccId = \App\Models\ShippingMode::CLICK_AND_COLLECT;
            $shopId = $selectedShop ? $selectedShop->id : "null";
        @endphp

        <div
            x-data="{
                billingId: {{ $billingId ?? "null" }},
                deliveryId: {{ $deliveryId ?? "null" }},
                shippingId: {{ $shippingId ?? "null" }},
                shopId: {{ $shopId }},
                ccId: {{ $ccId }},

                get isClickAndCollect() {
                    return this.shippingId == this.ccId
                },

                get canSubmit() {
                    if (! this.billingId || ! this.shippingId) return false
                    return this.isClickAndCollect ? this.shopId : this.deliveryId
                },
            }"
            class="flex gap-10"
        >
            <div class="flex flex-2 flex-col gap-8">
                <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <h2 class="mb-4 text-xl font-bold text-gray-900">1. Mode de livraison</h2>

                    <form method="POST" action="{{ route("checkout.update-order") }}" x-ref="shippingForm">
                        @csrf
                        @method("PUT")

                        <input type="hidden" name="billing_id" value="{{ $billingId }}" />
                        <input type="hidden" name="delivery_id" value="{{ $deliveryId }}" />

                        <div class="grid grid-cols-3 gap-4">
                            @foreach ($deliveryModes as $mode)
                                <label
                                    class="relative flex cursor-pointer flex-col justify-between rounded-lg border p-4 transition"
                                    :class="shippingId == {{ $mode->id }} ? 'border-blue-600 bg-blue-50 ring-1 ring-blue-600' : 'border-gray-200 bg-white hover:border-gray-300'"
                                >
                                    <input
                                        type="radio"
                                        name="shipping_id"
                                        value="{{ $mode->id }}"
                                        @change="$refs.shippingForm.submit()"
                                        x-model="shippingId"
                                        class="sr-only"
                                    />
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <h3 class="font-bold text-gray-900">{{ $mode->name }}</h3>
                                            <p class="mt-1 font-medium text-gray-900">{{ number_format($mode->price, 2, ",", " ") }} €</p>
                                        </div>
                                        <div x-show="shippingId == {{ $mode->id }}" class="text-blue-600">
                                            <x-bi-check-circle-fill class="h-6 w-6" />
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </form>
                </section>

                <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="mb-6 flex items-center justify-between">
                        <h2 class="text-xl font-bold text-gray-900">2. Adresse de facturation</h2>
                        <a
                            href="{{ route("dashboard.addresses.create", ["intended" => route("checkout.index")]) }}"
                            class="text-sm font-medium text-blue-600 hover:underline"
                        >
                            + Nouvelle adresse
                        </a>
                    </div>

                    @if ($addresses->isEmpty())
                        <div class="rounded-lg bg-gray-50 p-6 text-center text-gray-500">Aucune adresse.</div>
                    @else
                        <form method="POST" action="{{ route("checkout.update-order") }}">
                            @csrf
                            @method("PUT")

                            <input type="hidden" name="delivery_id" value="{{ $deliveryId }}" />
                            <input type="hidden" name="shipping_id" value="{{ $shippingId }}" />

                            <div class="grid grid-cols-2 gap-4">
                                @foreach ($addresses as $address)
                                    <x-address-card
                                        :address="$address"
                                        name="billing_id"
                                        :value="$address->id_adresse"
                                        :selected="$billingId == $address->id_adresse"
                                    />
                                @endforeach
                            </div>
                        </form>
                    @endif
                </section>

                <section x-show="!isClickAndCollect" class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-xl font-bold text-gray-900">3. Adresse de livraison</h2>
                    </div>

                    <form method="POST" action="{{ route("checkout.update-order") }}">
                        @csrf
                        @method("PUT")

                        <input type="hidden" name="billing_id" value="{{ $billingId }}" />
                        <input type="hidden" name="shipping_id" value="{{ $shippingId }}" />

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            @foreach ($addresses as $address)
                                <x-address-card
                                    :address="$address"
                                    name="delivery_id"
                                    :value="$address->id_adresse"
                                    :selected="$deliveryId == $address->id_adresse"
                                />
                            @endforeach
                        </div>
                    </form>
                </section>

                <section x-show="isClickAndCollect" x-cloak class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <h2 class="mb-4 text-xl font-bold text-gray-900">3. Point de retrait</h2>

                    <div
                        @click="$dispatch('open-shop-modal', { showAvailability: false })"
                        class="flex cursor-pointer items-center justify-between rounded-lg border border-gray-200 bg-gray-50 p-4 transition hover:border-gray-300 hover:bg-gray-100"
                        :class="shopId ? 'border-green-200 bg-green-50' : ''"
                    >
                        <div class="flex items-center gap-4">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-blue-600">
                                <x-bi-geo-alt class="h-5 w-5" />
                            </div>

                            @if ($selectedShop)
                                <div>
                                    <p class="font-bold text-gray-900">{{ $selectedShop->name }}</p>
                                    <p class="text-sm text-gray-600">
                                        {{ $selectedShop->address }} - {{ $selectedShop->postalCode }} {{ $selectedShop->city }}
                                    </p>
                                </div>
                            @else
                                <div>
                                    <p class="font-medium text-gray-500">Choisir un magasin</p>
                                    <p class="text-sm text-gray-400">Cliquez pour voir la carte</p>
                                </div>
                            @endif
                        </div>

                        @if ($selectedShop)
                            <span class="text-sm font-medium text-green-600">✓ Sélectionné</span>
                        @endif
                    </div>
                </section>
            </div>

            <aside class="flex flex-1 flex-col gap-6">
                <x-cart-summary :summary-data="$summaryData" :count="$count" :discount-data="$discountData" :is-checkout="true" />

                <form action="{{ route("payment.process") }}" method="post">
                    @csrf
                    <x-button type="submit" size="lg" color="green" class="w-full" x-bind:disabled="!canSubmit">Payer la commande</x-button>
                </form>

                <a
                    href="{{ route("cart.index") }}"
                    class="flex items-center justify-center gap-2 text-sm text-gray-600 hover:text-gray-900"
                >
                    &larr; Retour au panier
                </a>
            </aside>
        </div>
    </div>
</x-app-layout>
