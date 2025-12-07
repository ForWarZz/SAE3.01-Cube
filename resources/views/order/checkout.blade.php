<x-app-layout>
    <div class="min-h-screen bg-gray-100 px-24 py-12">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Finaliser ma commande</h1>
            <p class="mt-2 text-sm text-gray-600">Veuillez sélectionner vos adresses</p>
        </div>

        @php
            $defaultId = $addresses->isEmpty() ? "null" : $addresses->first()->id_adresse;
            $newAddressRoute = route("dashboard.adresses.create", ["intended" => route("cart.checkout")]);
        @endphp

        <form
            method="POST"
            action=""
            x-data="{
                billingId: {{ $defaultId }},
                deliveryId: {{ $defaultId }},
                sameAddress: true,
            }"
            x-effect="if (sameAddress) deliveryId = billingId"
        >
            @csrf

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
                </div>

                <aside class="flex flex-1 flex-col gap-6">
                    <x-cart-summary :summary-data="$summaryData" :count="$count" :discount-data="$discountData" :is-checkout="true" />

                    <button
                        type="submit"
                        :disabled="!deliveryId || !billingId"
                        :class="(!deliveryId || !billingId) ? 'cursor-not-allowed bg-gray-300' : 'cursor-pointer bg-green-600 hover:bg-green-700'"
                        class="w-full rounded-md px-5 py-4 text-lg font-bold text-white uppercase shadow-md transition hover:shadow-lg"
                    >
                        Passer la commande
                    </button>

                    <a
                        href="{{ route("cart.index") }}"
                        class="flex items-center justify-center gap-2 text-sm text-gray-600 hover:text-gray-900"
                    >
                        &larr; Retour au panier
                    </a>
                </aside>
            </div>
        </form>
    </div>
</x-app-layout>
