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
