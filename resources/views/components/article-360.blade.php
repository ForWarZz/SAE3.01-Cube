@props(['images']) 
<div 
    x-data="{
        images: @js($images),
        currentIndex: 0,
        isDragging: false,
        startX: 0,
        sensitivity: 15,
        
        startDrag(e) {
            this.isDragging = true;
            this.startX = e.pageX;
        },

        stopDrag() {
            this.isDragging = false;
        },

        move(e) {
            if (!this.isDragging) return;

            const currentX = e.pageX;
            const diff = currentX - this.startX;

            if (Math.abs(diff) > this.sensitivity) {
                if (diff > 0) {
                    this.prev();
                } else {
                    this.next();
                }
                this.startX = currentX;
            }
        },

        next() {
            this.currentIndex = (this.currentIndex + 1) % this.images.length;
        },

        prev() {
            this.currentIndex = (this.currentIndex - 1 + this.images.length) % this.images.length;
        }
    }"
    class="relative w-full max-w-2xl mx-auto select-none cursor-grab active:cursor-grabbing"
    @mousedown="startDrag"
    @mousemove.window="move"
    @mouseup.window="stopDrag"
>

    <div class="relative aspect-square overflow-hidden rounded-lg shadow-lg bg-gray-100">
        <img 
            :src="images[currentIndex]" 
            class="w-full h-full object-contain pointer-events-none" 
            alt="Vue 360"
        >
        
        <div class="absolute bottom-4 left-1/2 -translate-x-1/2 bg-black/50 text-white text-xs px-2 py-1 rounded-full pointer-events-none">
            <span x-text="currentIndex + 1"></span> / <span x-text="images.length"></span>
            <span class="ml-1">Glissez pour tourner</span>
        </div>
    </div>

    <div class="hidden">
        <template x-for="img in images">
            <img :src="img">
        </template>
    </div>
</div>