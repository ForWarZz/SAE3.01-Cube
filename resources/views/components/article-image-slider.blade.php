<div
    class="flex-1/2 flex-shrink-0"
    x-data="{
        currentImageIndex: 0,
        zoomed: false,
        zoomX: 0,
        zoomY: 0,
        images: @js($currentReference->getImagesUrls()),
        prev() {
            if (! this.images.length) return

            this.currentImageIndex =
                (this.currentImageIndex - 1 + this.images.length) %
                this.images.length
        },
        next() {
            if (! this.images.length) return

            this.currentImageIndex =
                (this.currentImageIndex + 1) % this.images.length
        },
        updateZoom(event) {
            const rect = event.target.getBoundingClientRect()
            this.zoomX = ((event.clientX - rect.left) / rect.width) * 100
            this.zoomY = ((event.clientY - rect.top) / rect.height) * 100
        },
    }"
>
    <div class="relative h-[550px] w-full overflow-hidden rounded-lg">
        <img
            :src="images.length > 0 ? images[currentImageIndex] : ''"
            alt="{){ $article->nom_article }}"
            class="h-full w-full object-contain transition-transform duration-200"
            @mousemove="images.length && (zoomed = true); updateZoom($event)"
            @mouseleave="zoomed = false"
        />

        <div
            class="pointer-events-none absolute inset-0 overflow-hidden"
            style="background-repeat: no-repeat"
            :style="`background-image: url(${images[currentImageIndex]}); background-size: 200%; background-position: ${zoomX}% ${zoomY}%; display: ${zoomed ? 'block' : 'none'}`"
        ></div>

        <button
            @click="prev()"
            class="absolute top-1/2 left-3 flex size-10 -translate-y-1/2 cursor-pointer items-center justify-center rounded-full bg-white/70 shadow transition hover:bg-white"
        >
            <svg class="h-5 w-5 text-gray-800" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
        </button>

        <button
            @click="next()"
            class="absolute top-1/2 right-3 flex size-10 -translate-y-1/2 cursor-pointer items-center justify-center rounded-full bg-white/70 shadow transition hover:bg-white"
        >
            <svg class="h-5 w-5 text-gray-800" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
        </button>
    </div>

    <div class="mt-4 flex justify-center gap-4">
        @if (! empty($currentReference->getImagesUrls(true)))
            <div>
                <button type="button" id="butOverlay" class="cursor-pointer">
                    <img src="{{ asset("resources/360.svg") }}" alt="vue 360°" class="h-20 w-16" />
                </button>
            </div>
        @endif

        <template x-for="(img, index) in images" :key="index">
            <img
                :src="img"
                @click="currentImageIndex = index"
                :class="{'ring-2 ring-blue-600': currentImageIndex === index, 'opacity-70': currentImageIndex !== index}"
                class="h-20 w-20 cursor-pointer rounded-lg object-cover shadow transition"
            />
        </template>
    </div>
</div>
