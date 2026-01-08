<x-app-layout>
    <div id="cart" class="flex-1 flex-col bg-gray-100 px-24 py-12">
        <x-flash-message key="error" />

        @if (session("success"))
            <div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4 text-green-800">
                <div class="flex items-start gap-3">
                    <x-heroicon-o-check-circle class="mt-0.5 h-5 w-5 shrink-0 text-green-600" />

                    <div class="flex flex-col gap-1">
                        <p class="font-medium">
                            {{ session("success") }}
                        </p>

                        @if (session("order_id"))
                            <a
                                href="{{ route("dashboard.orders.show", session("order_id")) }}"
                                class="group flex items-center gap-1 text-sm font-semibold text-green-700 hover:text-green-900"
                            >
                                Voir ma commande
                                <span class="transition-transform group-hover:translate-x-1" aria-hidden="true">&rarr;</span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        @if ($count > 0)
            <div class="mb-6 rounded-lg border border-blue-200 bg-blue-50 p-3" x-data="{ show: true }" x-show="show" x-transition>
                <div class="flex items-start gap-3">
                    <x-heroicon-o-information-circle class="mt-0.5 h-5 w-5 flex-shrink-0 text-blue-600" />
                    <div class="flex-1 text-sm text-blue-900">
                        <p class="mb-1 font-semibold">Finalisez votre commande</p>
                        <p class="text-xs text-blue-800">
                            <strong>Vélos :</strong>
                            Click & Collect gratuit en magasin (vélo monté sous 24-48h).
                            <strong>Accessoires :</strong>
                            Livraison à domicile ou en magasin (gratuite dès 50€).
                        </p>
                    </div>
                    <button @click="show = false" class="flex-shrink-0 text-blue-400 hover:text-blue-600">
                        <x-heroicon-o-x-mark class="h-4 w-4" />
                    </button>
                </div>
            </div>
        @endif

        <div class="flex gap-10">
            <x-cart-list :count="$count" :cart-data="$cartData" />

            <aside class="flex flex-1 flex-col gap-6">
                <x-cart-summary :summary-data="$summaryData" :count="$count" :discountData="$discountData" />

                <section id="discount-code-section" class="flex flex-col gap-6">
                    <div class="flex items-center gap-2">
                        <h2 class="text-2xl font-semibold text-gray-900">Code promo</h2>
                        <x-info-tooltip
                            text="Les codes promo peuvent être obtenus via nos newsletters ou promotions spéciales. Appliquez-en un avant de finaliser votre commande pour bénéficier d'une réduction."
                            width="w-64"
                            color="text-gray-400"
                        />
                    </div>

                    @if ($errors->has("discount_code"))
                        <div class="rounded-md border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                            {{ $errors->first("discount_code") }}
                        </div>
                    @endif

                    @if ($discountData)
                        <div class="flex flex-col gap-2 rounded-lg border border-green-200 bg-green-50 p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="text-xs font-semibold tracking-wide text-green-800 uppercase">Code appliqué</span>
                                    <p class="text-xl font-bold text-green-900">{{ $discountData->label_code_promo }}</p>
                                    <p class="text-sm text-green-700">-{{ $discountData->pourcentage_remise }}% sur votre commande</p>
                                </div>

                                <form action="{{ route("cart.discount.remove") }}" method="POST">
                                    @csrf
                                    @method("DELETE")
                                    <x-button type="submit" color="green">Retirer le code</x-button>
                                </form>
                            </div>
                        </div>
                    @else
                        <form
                            action="{{ route("cart.discount.apply") }}"
                            method="POST"
                            class="flex gap-3 rounded-lg bg-white p-6 shadow-sm"
                        >
                            @csrf

                            <input
                                type="text"
                                name="discount_code"
                                placeholder="Entrez votre code promo"
                                required
                                class="flex-1 rounded-md border border-gray-300 px-4 py-2"
                            />

                            <x-button type="submit" :disabled="$count === 0">Appliquer</x-button>
                        </form>
                    @endif
                </section>
            </aside>
        </div>
    </div>
</x-app-layout>
