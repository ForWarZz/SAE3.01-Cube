<x-app-layout>
    <div id="cart" class="flex-1 flex-col bg-gray-100 px-24 py-12">
        <x-flash-message key="error" />
        <x-flash-message key="success" type="success" />

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

                                    <button
                                        type="submit"
                                        class="cursor-pointer rounded-full p-1 text-green-600 transition hover:bg-green-100 hover:text-green-800"
                                        title="Retirer le code"
                                    >
                                        <x-bi-x class="size-7" />
                                    </button>
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

                            <button
                                type="submit"
                                @disabled($count === 0)
                                class="{{ $count > 0 ? "cursor-pointer bg-black text-white hover:bg-gray-900 hover:shadow-md" : "cursor-not-allowed bg-gray-300 text-white" }} text-md rounded-md px-5 py-2 font-medium shadow-sm transition"
                            >
                                Appliquer
                            </button>
                        </form>
                    @endif
                </section>
            </aside>
        </div>
    </div>
</x-app-layout>
