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

        <div class="flex gap-10">
            <x-cart-list :count="$count" :cart-data="$cartData" />

            <aside class="flex flex-1 flex-col gap-6">
                <x-cart-summary :summary-data="$summaryData" :count="$count" :discountData="$discountData" />

                <section class="flex flex-col gap-6">
                    <h2 class="text-2xl font-semibold text-gray-900">Code promo</h2>

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

                            <x-button
                                type="submit"
                                :disabled="$count === 0"
                                class="{{ $count > 0 ? '' : 'bg-gray-300 text-white cursor-not-allowed' }} text-md rounded-md px-5 py-2 font-medium shadow-sm transition"
                            >
                                Appliquer
                            </x-button>
                        </form>
                    @endif
                </section>
            </aside>
        </div>
    </div>
</x-app-layout>
