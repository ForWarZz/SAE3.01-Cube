<x-app-layout>
    <div id="cart" class="flex-1 flex-col bg-gray-100 px-24 py-12">
        <div class="flex gap-10">
            <section class="flex-2">
                <h2 class="mb-6 text-2xl font-semibold text-gray-900">Panier ({{ $count }})</h2>

                @if ($count <= 0)
                    <p class="mb-6 text-gray-700">Votre panier ne contient actuellement aucun article.</p>
                @endif

                <div class="flex flex-col gap-4">
                    @foreach ($cartData as $item)
                        <x-cart-item :cartItem="$item" />
                    @endforeach
                </div>

                <div class="mt-8 flex items-center justify-between rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                    <a href="{{ route("home") }}" class="flex cursor-pointer items-center gap-2 text-gray-700 transition hover:text-black">
                        <x-bi-arrow-left class="h-5 w-5" />
                        Continuer mes achats
                    </a>

                    @if ($count > 0)
                        <button
                            class="text-md cursor-pointer rounded-md bg-black px-6 py-3 font-medium text-white shadow-sm transition hover:bg-gray-900"
                        >
                            Valider mon panier
                        </button>
                    @else
                        <button disabled class="cursor-not-allowed rounded-md bg-gray-300 px-6 py-3 font-medium text-white shadow-sm">
                            Valider mon panier
                        </button>
                    @endif
                </div>
            </section>

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

                            @if ($count > 0)
                                <button
                                    type="submit"
                                    class="cursor-pointer rounded-md bg-black px-5 py-2 text-lg font-medium text-white shadow-sm transition hover:bg-gray-900 hover:shadow-md"
                                >
                                    Appliquer
                                </button>
                            @else
                                <button
                                    type="button"
                                    disabled
                                    class="cursor-not-allowed rounded-md bg-gray-300 px-5 py-2 text-lg font-medium text-white shadow-sm"
                                >
                                    Appliquer
                                </button>
                            @endif
                        </form>
                    @endif
                </section>
            </aside>
        </div>
    </div>
</x-app-layout>
