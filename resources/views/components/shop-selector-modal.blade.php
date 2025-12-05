@props([
    "referenceId" => null,
    "showAvailability" => false,
])

<div x-data="shopSelector()" x-on:open-shop-modal.window="openModal($event.detail)" x-cloak>
    <div x-show="isOpen" class="fixed inset-0 z-50 overflow-hidden">
        <div
            x-show="isOpen"
            x-transition:enter="duration-300 ease-out"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="duration-200 ease-in"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black/50 transition-opacity"
            x-on:click="closeModal()"
        ></div>

        <div class="pointer-events-none fixed inset-y-0 right-0 flex h-full max-w-full pl-10">
            <div
                x-show="isOpen"
                x-transition:enter="transform transition duration-300 ease-out"
                x-transition:enter-start="translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transform transition duration-200 ease-in"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="translate-x-full"
                class="pointer-events-auto h-full w-screen max-w-xl"
            >
                <div class="flex h-full flex-col bg-white shadow-xl">
                    <div class="flex items-center justify-between border-b border-gray-200 px-6 py-5">
                        <h2 class="text-xl font-bold tracking-wide text-gray-900 uppercase">Choisir un magasin</h2>
                        <button x-on:click="closeModal()" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="px-6 py-4">
                        <div class="relative text-gray-400 focus-within:text-gray-600">
                            <input
                                type="text"
                                x-model="searchQuery"
                                x-on:input.debounce.300ms="filterShops()"
                                placeholder="Saisir une adresse, un code postal ou une ville..."
                                class="w-full rounded-sm border border-gray-300 py-3 pr-10 pl-4 text-sm text-gray-900 placeholder-gray-500 focus:border-gray-400 focus:ring-1 focus:ring-gray-400 focus:outline-none"
                            />
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                                    />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div x-show="showAvailability" class="flex items-center space-x-3 px-6 py-2">
                        <button
                            type="button"
                            x-on:click="showOnlyInStock = !showOnlyInStock"
                            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                            :class="showOnlyInStock ? 'bg-green-500' : 'bg-gray-300'"
                            role="switch"
                            :aria-checked="showOnlyInStock"
                        >
                            <span
                                class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                :class="showOnlyInStock ? 'translate-x-5' : 'translate-x-0'"
                            ></span>
                        </button>
                        <span class="text-xs font-medium tracking-wide text-gray-700 uppercase">
                            Voir uniquement les magasins avec stock
                        </span>
                    </div>

                    <div class="mt-2 flex border-b border-gray-200">
                        <template x-for="tab in ['list', 'map']">
                            <button
                                x-on:click="activeTab = tab"
                                class="flex-1 border-b-2 py-3 text-sm font-bold tracking-wide transition-colors"
                                :class="activeTab === tab ? 'text-gray-900 border-green-500' : 'text-gray-400 border-transparent hover:text-gray-600'"
                                x-text="tab === 'list' ? 'VUE LISTE' : 'VUE CARTE'"
                            ></button>
                        </template>
                    </div>

                    <div class="relative flex-1 overflow-y-auto bg-gray-50">
                        <div x-show="loading" class="bg-opacity-75 absolute inset-0 z-10 flex items-center justify-center bg-white">
                            <svg class="h-8 w-8 animate-spin text-gray-400" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path
                                    class="opacity-75"
                                    fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                ></path>
                            </svg>
                        </div>

                        <div x-show="!loading && filteredShops.length === 0" class="py-12 text-center text-gray-500">
                            Aucun magasin trouvé.
                        </div>

                        <div x-show="activeTab === 'list' && !loading" class="divide-y divide-gray-200 bg-white">
                            <template x-for="item in filteredShops" :key="item.shop.id">
                                <div class="flex flex-col space-y-3 px-6 py-5 transition-colors hover:bg-gray-50">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <h3 class="font-bold tracking-wide text-gray-900 uppercase" x-text="item.shop.name"></h3>
                                            <p class="mt-1 text-sm text-gray-600" x-text="item.shop.address"></p>
                                            <p
                                                x-show="item.shop.hours"
                                                class="mt-0.5 text-sm text-gray-500"
                                                x-text="'Horaires : ' + item.shop.hours"
                                            ></p>
                                        </div>
                                        <button
                                            x-on:click="selectShop(item.shop)"
                                            class="ml-4 flex items-center bg-gray-900 px-3 py-2 text-xs font-bold whitespace-nowrap text-white transition hover:bg-gray-800"
                                        >
                                            <span class="mr-1">▸</span>
                                            CHOISIR
                                        </button>
                                    </div>

                                    <template x-if="showAvailability">
                                        <div class="space-y-2">
                                            <div
                                                class="flex items-center text-sm font-medium"
                                                :class="{
                                                     'text-green-600': item.status === 'in_stock',
                                                     'text-orange-500': item.status === 'orderable',
                                                     'text-gray-400': item.status === 'unavailable'
                                                 }"
                                            >
                                                <svg class="mr-1.5 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path
                                                        fill-rule="evenodd"
                                                        clip-rule="evenodd"
                                                        x-show="item.status === 'in_stock'"
                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                    />
                                                    <path
                                                        fill-rule="evenodd"
                                                        clip-rule="evenodd"
                                                        x-show="item.status === 'orderable'"
                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                                    />
                                                    <path
                                                        fill-rule="evenodd"
                                                        clip-rule="evenodd"
                                                        x-show="item.status === 'unavailable'"
                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                    />
                                                </svg>
                                                <span
                                                    x-text="
                                                        item.status === 'in_stock'
                                                            ? 'Disponible en magasin'
                                                            : item.status === 'orderable'
                                                              ? 'Commandable en magasin'
                                                              : 'Indisponible en magasin'
                                                    "
                                                ></span>
                                            </div>

                                            <div x-show="item.sizes && item.sizes.length > 0" class="flex flex-wrap gap-2">
                                                <template x-for="size in item.sizes" :key="size.size_id">
                                                    <span
                                                        class="rounded border px-2 py-1 text-xs font-medium"
                                                        :class="{
                                                              'bg-green-50 text-green-700 border-green-100': size.status === 'En Stock',
                                                              'bg-orange-50 text-orange-700 border-orange-100': size.status === 'Commandable',
                                                              'bg-gray-50 text-gray-500 border-gray-100': size.status === 'Indisponible'
                                                          }"
                                                        x-text="size.size_name + ': ' + size.status"
                                                    ></span>
                                                </template>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>

                        <div x-show="activeTab === 'map'" class="flex h-full items-center justify-center text-gray-400">
                            <span class="text-sm">Carte indisponible pour le moment</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function shopSelector() {
        return {
            isOpen: false,
            loading: false,
            shops: [],
            searchQuery: '',
            showOnlyInStock: false,
            showAvailability: false,
            referenceId: null,
            sizeId: null,
            activeTab: 'list',

            init() {},

            async openModal({ referenceId = null, showAvailability = false, sizeId = null } = {}) {
                this.isOpen = true;
                this.referenceId = referenceId;
                this.showAvailability = showAvailability;
                this.sizeId = sizeId;
                this.searchQuery = '';
                document.body.style.overflow = 'hidden';
                await this.loadShops();
            },

            closeModal() {
                this.isOpen = false;
                document.body.style.overflow = '';
            },

            async loadShops() {
                this.loading = true;
                try {
                    let url = '/shops';
                    if (this.showAvailability && this.referenceId) {
                        const params = new URLSearchParams(this.sizeId ? { size: this.sizeId } : {});
                        url = `/availability/${this.referenceId}?${params}`;
                    }

                    const res = await fetch(url);
                    const data = await res.json();

                    this.shops = data.availabilities ?? data.shops ?? [];
                } catch (e) {
                    console.error('Erreur chargement:', e);
                    this.shops = [];
                }
                this.loading = false;
            },

            get filteredShops() {
                const q = this.searchQuery.toLowerCase().trim();
                if (!q && !this.showOnlyInStock) return this.shops;

                return this.shops.filter((item) => {
                    const matchStock = !this.showOnlyInStock || item.status === 'in_stock';
                    if (!matchStock) return false;

                    if (!q) return true;

                    const s = item.shop;
                    const fullText = `${s.name} ${s.address} ${s.city || ''} ${s.postalCode || ''}`.toLowerCase();
                    return fullText.includes(q);
                });
            },

            async selectShop(shop) {
                try {
                    const token = document.querySelector('meta[name="csrf-token"]')?.content;

                    const res = await fetch('/shop/select', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
                        body: JSON.stringify({ shop_id: shop.id }),
                    });

                    if (res.ok) {
                        localStorage.setItem('selectedShop', JSON.stringify(shop));
                        window.dispatchEvent(new CustomEvent('shop-selected', { detail: shop }));

                        const btn = document.getElementById('store-button-text');
                        if (btn) btn.textContent = shop.name;

                        this.closeModal();
                    }
                } catch (e) {
                    console.error('Erreur sélection:', e);
                }
            },
        };
    }
</script>

<style>
    [x-cloak] {
        display: none !important;
    }
</style>
