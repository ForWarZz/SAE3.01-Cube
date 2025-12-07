<div
    x-data="{
        isOpen: false,
        item: null,
        openModal(detail) {
            this.item = detail
            this.isOpen = true
        },
        closeModal() {
            this.isOpen = false
        },
    }"
    x-on:cart-added.window="openModal($event.detail)"
    x-show="isOpen"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
    style="display: none"
    x-on:click="closeModal()"
>
    <div x-show="isOpen" class="w-full max-w-md rounded-lg bg-white p-6 shadow-lg" x-on:click.stop>
        <h3 class="mb-4 text-lg font-semibold text-gray-900">Article ajouté au panier</h3>

        <template x-if="item">
            <div class="mb-4 flex gap-4">
                <div class="h-20 w-20 overflow-hidden rounded-md border">
                    <img :src="item.image" :alt="item.name" class="h-full w-full object-cover" />
                </div>

                <div class="flex flex-col justify-center">
                    <h4 class="font-medium text-gray-900" x-text="item.name"></h4>

                    <p class="mt-1 text-sm text-gray-600">
                        <span x-show="item.color">
                            Couleur :
                            <span class="font-medium" x-text="item.color"></span>
                        </span>

                        <span x-show="item.color && item.size">•</span>

                        <span x-show="item.size">
                            Taille :
                            <span class="font-medium" x-text="item.size"></span>
                        </span>
                    </p>

                    <p class="mt-2 font-bold text-blue-600" x-text="item.price"></p>
                </div>
            </div>
        </template>

        <div class="flex gap-3">
            <button
                x-on:click="closeModal()"
                class="flex-1 rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-700 hover:bg-gray-100"
            >
                Continuer
            </button>

            <a href="{{ route("cart.index") }}" class="flex-1 rounded-lg bg-black px-4 py-2 text-center text-white hover:bg-gray-900">
                Voir le panier
            </a>
        </div>
    </div>
</div>
