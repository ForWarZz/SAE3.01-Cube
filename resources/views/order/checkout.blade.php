<x-app-layout>
    <div class="min-h-screen bg-gray-100 px-24 py-12">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Finaliser ma commande</h1>
            <p class="mt-2 text-sm text-gray-600">Veuillez sélectionner vos adresses</p>
        </div>

        @php
            $defaultId = $addresses->isEmpty() ? "null" : $addresses->first()->id_adresse;
            $newAddressRoute = route("dashboard.addresses.create", ["intended" => route("cart.checkout")]);

            $savedBillingId = $orderData->billing_address_id ?? $defaultId;
            $savedDeliveryId = $orderData->delivery_address_id ?? $defaultId;
            $savedShippingId = $selectedShippingId ?? "null";
            $sameAddressDefault = $savedBillingId === $savedDeliveryId ? "true" : "false";
        @endphp

        <div
            x-data="{
                billingId: {{ $savedBillingId }},
                deliveryId: {{ $savedDeliveryId }},
                shippingId: {{ $savedShippingId }},
                sameAddress: {{ $sameAddressDefault }},
            }"
            x-effect="if (sameAddress) deliveryId = billingId"
        >
            <div class="flex gap-10">
                <div class="flex flex-2 flex-col gap-8">
                    <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                        <div class="mb-6 flex items-center justify-between">
                            <h2 class="text-xl font-bold text-gray-900">Adresse de facturation</h2>
                            <a
                                href="{{ $newAddressRoute }}"
                                class="rounded-lg bg-indigo-600 px-3 py-2 text-sm font-medium text-white hover:bg-indigo-800"
                            >
                                + Nouvelle adresse
                            </a>
                        </div>

                        @if ($addresses->isEmpty())
                            <div class="rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 p-8 text-center text-gray-500">
                                Aucune adresse enregistrée.
                            </div>
                        @else
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                @foreach ($addresses as $address)
                                    <x-address-card
                                        :address="$address"
                                        name="billing_address_id"
                                        model="billingId"
                                        :value="$address->id_adresse"
                                    />
                                @endforeach
                            </div>
                        @endif

                        <x-input-error :messages="$errors->get('billing_address_id')" class="mt-2" />
                    </section>

                    <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                        <div class="mb-4 flex items-center justify-between">
                            <h2 class="text-xl font-bold text-gray-900">Adresse de livraison</h2>
                            <a
                                href="{{ $newAddressRoute }}"
                                class="rounded-lg bg-indigo-600 px-3 py-2 text-sm font-medium text-white hover:bg-indigo-800"
                            >
                                + Nouvelle adresse
                            </a>
                        </div>

                        @if ($addresses->isNotEmpty())
                            <label
                                class="mb-6 flex cursor-pointer items-center gap-3 rounded-lg border border-gray-200 bg-gray-50 p-4 transition hover:bg-gray-100"
                            >
                                <input
                                    type="checkbox"
                                    x-model="sameAddress"
                                    class="h-5 w-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                />
                                <span class="font-medium text-gray-900">Utiliser l'adresse de facturation pour la livraison</span>
                            </label>

                            <div x-show="!sameAddress" class="grid grid-cols-1 gap-4 md:grid-cols-2" style="display: none">
                                @foreach ($addresses as $address)
                                    <x-address-card
                                        :address="$address"
                                        name="delivery_address_id"
                                        model="deliveryId"
                                        :value="$address->id_adresse"
                                    />
                                @endforeach
                            </div>

                            <input type="hidden" name="delivery_address_id" :value="deliveryId" />
                        @endif

                        <x-input-error :messages="$errors->get('delivery_address_id')" class="mt-2" />
                    </section>

                    <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                        <h2 class="mb-4 text-xl font-bold text-gray-900">Mode de livraison</h2>

                        <form method="POST" action="{{ route("checkout.update-shipping") }}">
                            @csrf

                            <input type="hidden" name="billing_id" :value="billingId" />
                            <input type="hidden" name="delivery_id" :value="deliveryId" />

                            <div class="grid grid-cols-3 gap-4">
                                @foreach ($deliveryModes as $mode)
                                    <label
                                        class="{{ $selectedShippingId == $mode->id ? "border-blue-600 bg-blue-50" : "border-gray-200 bg-white hover:border-gray-300" }} relative flex cursor-pointer flex-col justify-between rounded-lg border p-4 transition"
                                    >
                                        <input
                                            type="radio"
                                            name="shipping_id"
                                            value="{{ $mode->id }}"
                                            {{ $selectedShippingId == $mode->id ? "checked" : "" }}
                                            @change="shippingId = {{ $mode->id }}; $event.target.form.submit()"
                                            class="sr-only"
                                        />

                                        <div class="flex items-start justify-between">
                                            <div class="text-sm">
                                                <h3 class="font-bold text-gray-900">{{ $mode->name }}</h3>
                                                <p class="mt-2 font-medium text-gray-900">
                                                    {{ number_format($mode->price, 2, ",", " ") }} €
                                                </p>
                                            </div>

                                            <div class="ml-4">
                                                @if ($selectedShippingId == $mode->id)
                                                    <div
                                                        class="flex h-5 w-5 items-center justify-center rounded-full bg-blue-600 text-white"
                                                    >
                                                        <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 12 12">
                                                            <path
                                                                d="M10.28 2.28L3.989 8.575 1.695 6.28A1 1 0 00.28 7.695l3 3a1 1 0 001.414 0l7-7A1 1 0 0010.28 2.28z"
                                                            />
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>

                            <x-input-error :messages="$errors->get('shipping_id')" class="mt-2" />
                        </form>
                    </section>
                </div>

                <aside class="flex flex-1 flex-col gap-6">
                    <x-cart-summary :summary-data="$summaryData" :count="$count" :discount-data="$discountData" :is-checkout="true" />

                    <form action="{{ route("payment.process") }}" method="post">
                        @csrf

                        <button
                            type="submit"
                            :disabled="!deliveryId || !billingId || !shippingId"
                            :class="(!deliveryId || !billingId || !shippingId) ? 'cursor-not-allowed bg-gray-300' : 'cursor-pointer bg-green-600 hover:bg-green-700'"
                            class="w-full rounded-md px-5 py-4 text-lg font-bold text-white uppercase shadow-md transition hover:shadow-lg"
                        >
                            Passer la commande
                        </button>
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
    </div>
</x-app-layout>
