@props([
    "images",
])
<div
    x-data="{
        images: @js($images),
        currentIndex: 0,
        isDragging: false,
        startX: 0,
        sensitivity: 15,

        startDrag(e) {
            this.isDragging = true
            this.startX = e.pageX
        },

        stopDrag() {
            this.isDragging = false
        },

        move(e) {
            if (! this.isDragging) return

            const currentX = e.pageX
            const diff = currentX - this.startX

            if (Math.abs(diff) > this.sensitivity) {
                if (diff > 0) {
                    this.prev()
                } else {
                    this.next()
                }
                this.startX = currentX
            }
        },

        next() {
            this.currentIndex = (this.currentIndex + 1) % this.images.length
        },

        prev() {
            this.currentIndex =
                (this.currentIndex - 1 + this.images.length) % this.images.length
        },
    }"
    class="relative mx-auto w-full cursor-grab select-none active:cursor-grabbing"
    @mousedown="startDrag"
    @mousemove.window="move"
    @mouseup.window="stopDrag"
>
    <img :src="images[currentIndex]" class="pointer-events-none h-full w-full object-contain" alt="Vue 360" />

    <div class="pointer-events-none absolute bottom-4 left-1/2 -translate-x-1/2 rounded-full bg-black/50 px-2 py-1 text-xs text-white">
        <span x-text="currentIndex + 1"></span>
        /
        <span x-text="images.length"></span>
        <span class="ml-1">Glissez pour tourner</span>
    </div>
</div>
