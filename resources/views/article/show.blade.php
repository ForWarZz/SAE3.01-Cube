@vite("resources/js/overlay360/main.js")

<div id="overlay360" class="fixed inset-0 z-[9999] flex hidden h-screen w-screen items-center justify-center bg-black/80">
    <div class="relative flex h-[90%] w-[90%] flex-col rounded-lg bg-white p-6 shadow-2xl">
        <button
            id="closeBtn"
            class="absolute top-4 right-4 z-10 cursor-pointer text-3xl leading-none font-bold text-gray-500 hover:text-black"
        >
            &times;
        </button>
        <h1 class="mb-4 pr-8 text-2xl font-bold">Vue 360 : {{ $article->nom_article }}</h1>
        <div class="relative h-full w-full flex-1 overflow-hidden rounded">
            <x-article-360 :images="$currentReference->getImagesUrls(true)" />
        </div>
    </div>
</div>

<x-cart-added-modal />

@if (session("cart_added"))
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            window.dispatchEvent(
                new CustomEvent('cart-added', {
                    detail: @json(session("cart_added")),
                }),
            );
        });
    </script>
@endif

<div
    x-data="{ show: false }"
    @scroll.window="show = (window.pageYOffset > 600)"
    x-show="show"
    x-transition.slide.up
    class="fixed bottom-0 left-0 z-50 w-full border-t border-gray-800 bg-gray-50 p-4 lg:hidden"
>
    <div class="mx-auto flex max-w-7xl items-center justify-between">
        <div class="text-xl font-bold">{{ number_format($discountedPrice, 2) }} €</div>
        <x-button @click="document.querySelector('#article-purchase-box').scrollIntoView({behavior: 'smooth'})" size="lg">
            Commander
        </x-button>
    </div>
</div>

<x-app-layout :reference="$currentReference">
    <div class="px-36 py-12">
        <x-breadcrumb :breadcrumbs="$breadcrumbs" />

        <div class="flex gap-16">
            <x-article-image-slider :article="$article" :current-reference="$currentReference" />
            <x-article-purchase-box
                :article="$article"
                :current-reference="$currentReference"
                :size-options="$sizeOptions"
                :current-size="$currentSize"
                :frame-options="$frameOptions"
                :battery-options="$batteryOptions"
                :color-options="$colorOptions"
                :weight="$weight"
                :real-price="$realPrice"
                :discounted-price="$discountedPrice"
                :discount-percent="$discountPercent"
                :has-discount="$hasDiscount"
            />
        </div>

        <x-article-characteritics :characteristics="$characteristics" />

        <x-article-description :article="$article" />
        <x-article-resume :article="$article" />

        @if ($isBike)
            <x-bike-geometries :sizes="$geometrySizes" :geometries="$geometries" :bike="$article->bike" />
            <x-bike-size :sizes="$availableSizes" />

            @if ($currentReference->ebike && $currentReference->ebike->battery)
                <x-battery-autonomy-calculator
                    :battery-capacity="$currentReference->ebike->battery->capacite_batterie"
                    :battery-label="$currentReference->ebike->battery->label_batterie"
                    :battery-options="$batteryOptions"
                    :bike-category="$article->category?->nom_categorie"
                    :bike-model="$article->bike->bikeModel?->nom_modele_velo"
                />
            @endif

            <x-bike-compatible-accessories :compatible-accessories="$compatibleAccessories" />
        @endif

        <x-similar-articles :similar-articles="$similarArticles" />
    </div>
</x-app-layout>
