<div id="overlay360" class="fixed inset-0 z-[9999] h-screen w-screen bg-black/80 flex justify-center items-center hidden">
    <div class="relative w-[90%] h-[90%] bg-white rounded-lg p-6 shadow-2xl flex flex-col">
        <button id="closeBtn" class="absolute top-4 right-4 text-gray-500 hover:text-black text-3xl font-bold leading-none z-10 cursor-pointer">
            &times; </button>
        <h1 class="text-2xl font-bold mb-4 pr-8">
            Vue 360 : {{ $article->nom_article }}
        </h1>
        <div class="flex-1 w-full h-full relative overflow-hidden rounded bg-gray-200">
            <x-article-360 :images="$article->getAllImagesUrls($currentReference->color?->id_couleur, true)" />
        </div>
    </div>
</div>
<x-app-layout>
    <div class="mx-auto max-w-7xl px-6 py-12">
        <div class="flex gap-16">
            <x-article-image-slider :article="$article" :current-reference="$currentReference" />
            <x-article-purchase-box
                :article="$article"
                :current-reference="$currentReference"
                :size-options="$sizeOptions"
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

        <x-bike-geometries :sizes="$geometrySizes" :geometries="$geometries" :bike="$article->bike" />
    </div>

    <x-bike-compatible-accessories :compatible-accessories="$compatibleAccessories" />
    <x-similar-articles :similar-articles="$similarArticles" />
</x-app-layout>
