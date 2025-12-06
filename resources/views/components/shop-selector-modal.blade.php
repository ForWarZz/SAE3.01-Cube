@props(["referenceId" => null, "showAvailability" => false])

<div
    x-data="shopSelector()"
    x-on:open-shop-modal.window="openModal($event.detail)"
    x-on:close-shop-modal.window="closeModal()"
    x-cloak
>
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
                        <h2 class="text-xl font-bold tracking-wide text-gray-900 uppercase">CHOISIR UN MAGASIN</h2>
                        <button x-on:click="closeModal()" class="text-gray-400 hover:text-gray-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="px-6 py-4">
                        <div class="relative">
                            <input
                                type="text"
                                x-model="searchQuery"
                                x-on:input.debounce.300ms="filterShops()"
                                placeholder="Saisir une adresse, un code postal ou une ville..."
                                class="w-full rounded-sm border border-gray-300 px-4 py-3 pr-12 text-sm focus:border-gray-400 focus:ring-1 focus:ring-gray-400 focus:outline-none"
                            />
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400">
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

                    <div x-show="showAvailability" class="flex items-center px-6 pb-4">
                        <button
                            type="button"
                            x-on:click="showOnlyInStock = !showOnlyInStock"
                            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                            :class="showOnlyInStock ? 'bg-green-500' : 'bg-gray-300'"
                            role="switch"
                        >
                            <span
                                class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                :class="showOnlyInStock ? 'translate-x-5' : 'translate-x-0'"
                            ></span>
                        </button>
                        <span class="ml-3 text-xs font-medium tracking-wide text-gray-700 uppercase">
                            Voir uniquement les magasins avec stock
                        </span>
                    </div>

                    <div class="flex border-b border-gray-200">
                        <template x-for="tab in ['list', 'map']">
                            <button
                                x-on:click="tab === 'map' ? switchToMap() : (activeTab = 'list')"
                                class="flex-1 border-b-2 py-3 text-sm font-bold tracking-wide transition-colors"
                                :class="activeTab === tab ? 'text-gray-900 border-green-500' : 'text-gray-400 border-transparent hover:text-gray-600'"
                                x-text="tab === 'list' ? 'VUE LISTE' : 'VUE CARTE'"
                            ></button>
                        </template>
                    </div>

                    <div x-show="showAvailability" class="border-b border-gray-100 bg-gray-50 px-6 py-3 text-sm text-gray-600">
                        Le stock est approximatif. Pour plus d'informations, veuillez contacter le magasin.
                    </div>

                    <div x-show="activeTab === 'list'" class="relative flex-1 overflow-y-auto bg-gray-50">
                        <div x-show="loading" class="bg-opacity-80 absolute inset-0 z-10 flex items-center justify-center bg-white">
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

                        <div x-show="!loading" class="divide-y divide-gray-200 bg-white">
                            <template x-for="item in filteredShops" :key="item.shop.id">
                                <div class="group px-6 py-5 transition-colors hover:bg-gray-50">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="flex-1 space-y-2">
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <h3
                                                        class="font-bold tracking-wide text-gray-900 uppercase"
                                                        x-text="item.shop.name"
                                                    ></h3>
                                                    <span
                                                        x-show="item.distance"
                                                        class="text-xs font-medium whitespace-nowrap text-gray-500"
                                                        x-text="'(' + item.distance.toFixed(1) + ' km)'"
                                                    ></span>
                                                </div>
                                                <p class="mt-1 text-sm text-gray-600" x-text="item.shop.address"></p>
                                            </div>

                                            <div
                                                class="flex items-center text-sm font-medium"
                                                :class="item.shop.isOpen !== false ? 'text-green-500' : 'text-orange-500'"
                                            >
                                                <span class="mr-2 h-2 w-2 rounded-full bg-current"></span>
                                                <span x-text="item.shop.isOpen !== false ? 'OUVERT' : 'FERMÉ'"></span>
                                            </div>

                                            <p
                                                x-show="item.shop.hours"
                                                class="text-sm text-gray-600"
                                                x-text="'Horaires : ' + item.shop.hours"
                                            ></p>

                                            <template x-if="showAvailability">
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
                                                            x-show="item.status === 'in_stock'"
                                                            fill-rule="evenodd"
                                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                            clip-rule="evenodd"
                                                        />
                                                        <path
                                                            x-show="item.status === 'orderable'"
                                                            fill-rule="evenodd"
                                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                                            clip-rule="evenodd"
                                                        />
                                                        <path
                                                            x-show="item.status === 'unavailable'"
                                                            fill-rule="evenodd"
                                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                            clip-rule="evenodd"
                                                        />
                                                    </svg>
                                                    <span
                                                        x-text="
                                                            item.status === 'in_stock'
                                                                ? 'Disponible dans mon magasin'
                                                                : item.status === 'orderable'
                                                                  ? 'Commandable en magasin'
                                                                  : 'Indisponible en magasin'
                                                        "
                                                    ></span>
                                                </div>
                                            </template>

                                            <div x-show="showAvailability && item.sizes?.length" class="flex flex-wrap gap-2 pt-1">
                                                <template x-for="size in item.sizes" :key="size.size_id">
                                                    <span
                                                        class="rounded border px-2 py-1 text-xs"
                                                        :class="{
                                                              'bg-green-50 text-green-700 border-green-200': size.status === 'En Stock',
                                                              'bg-orange-50 text-orange-700 border-orange-200': size.status === 'Commandable',
                                                              'bg-gray-50 text-gray-500 border-gray-200': size.status === 'Indisponible'
                                                          }"
                                                        x-text="size.size_name + ' ' + size.status"
                                                    ></span>
                                                </template>
                                            </div>
                                        </div>

                                        <button
                                            x-on:click="selectShop(item.shop)"
                                            class="ml-4 flex flex-shrink-0 items-center bg-gray-900 px-4 py-2 text-xs font-bold whitespace-nowrap text-white uppercase transition hover:bg-gray-800"
                                        >
                                            <span class="mr-1">▸</span>
                                            CHOISIR CE MAGASIN
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div x-show="activeTab === 'map'" class="relative min-h-0 flex-1 bg-gray-100">
                        <div id="shop-map" class="h-full w-full"></div>
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
            map: null,
            markers: [],
            userLat: null,
            userLng: null,

            init() {
                this.getUserLocation();
            },

            getUserLocation() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        (pos) => {
                            this.userLat = pos.coords.latitude;
                            this.userLng = pos.coords.longitude;
                        },
                        (err) => console.log('Géolocalisation désactivée:', err.message),
                    );
                }
            },

            calculateDistance(lat1, lng1, lat2, lng2) {
                const R = 6371; // Rayon de la Terre en km
                const dLat = ((lat2 - lat1) * Math.PI) / 180;
                const dLng = ((lng2 - lng1) * Math.PI) / 180;
                const a =
                    Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                    Math.cos((lat1 * Math.PI) / 180) * Math.cos((lat2 * Math.PI) / 180) * Math.sin(dLng / 2) * Math.sin(dLng / 2);
                const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                return R * c;
            },

            async openModal(detail = {}) {
                this.isOpen = true;
                this.referenceId = detail.referenceId || null;
                this.showAvailability = detail.showAvailability || false;
                this.sizeId = detail.sizeId || null;
                this.searchQuery = '';
                this.activeTab = 'list';
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
                    const url = this.buildShopsUrl();
                    const res = await fetch(url);
                    const data = await res.json();
                    this.shops = data.availabilities || data.shops || [];
                } catch (error) {
                    console.error('Erreur chargement:', error);
                    this.shops = [];
                } finally {
                    this.loading = false;
                }
            },

            buildShopsUrl() {
                if (this.showAvailability && this.referenceId) {
                    const params = this.sizeId ? `?size=${this.sizeId}` : '';
                    return `/availability/${this.referenceId}${params}`;
                }
                return '/shops';
            },

            matchesSearchQuery(item, query) {
                return (
                    item.shop.name.toLowerCase().includes(query) ||
                    item.shop.address.toLowerCase().includes(query) ||
                    (item.shop.postalCode || '').includes(query) ||
                    (item.shop.city || '').toLowerCase().includes(query)
                );
            },

            get filteredShops() {
                const query = this.searchQuery.toLowerCase().trim();
                let result = this.shops;

                // Filtre par recherche textuelle
                if (query) {
                    result = result.filter((item) => this.matchesSearchQuery(item, query));
                }

                // Filtre par stock
                if (this.showOnlyInStock && this.showAvailability) {
                    result = result.filter((item) => item.status === 'in_stock');
                }

                // Calcul des distances et tri par proximité
                if (this.userLat && this.userLng) {
                    result = result
                        .map((item) => {
                            const distance = this.calculateDistance(this.userLat, this.userLng, item.shop.lat, item.shop.lng);
                            return { ...item, distance };
                        })
                        .sort((a, b) => a.distance - b.distance);
                }

                return result;
            },

            switchToMap() {
                this.activeTab = 'map';
                this.$nextTick(() => setTimeout(() => this.initMap(), 100));
            },

            initMap() {
                if (!document.getElementById('shop-map')) return;

                if (this.map) {
                    this.map.invalidateSize();
                    this.updateMarkers();
                    return;
                }

                this.map = L.map('shop-map').setView([46.603354, 1.888334], 6);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap',
                }).addTo(this.map);

                this.updateMarkers();
            },

            updateMarkers() {
                this.markers.forEach((m) => m.remove());
                this.markers = [];

                this.filteredShops
                    .filter((item) => item.shop.lat && item.shop.lng)
                    .forEach((item) => {
                        const marker = L.marker([item.shop.lat, item.shop.lng]).addTo(this.map).bindPopup(this.buildMarkerPopup(item));
                        this.markers.push(marker);
                    });
            },

            buildMarkerPopup(item) {
                const statusColors = { in_stock: '#16a34a', orderable: '#f97316', unavailable: '#6b7280' };
                const statusLabels = { in_stock: 'Disponible', orderable: 'Commandable', unavailable: 'Indisponible' };
                const color = statusColors[item.status] || '#6b7280';
                const statusText = statusLabels[item.status] || 'Indisponible';
                const distanceText = `<p class="text-xs text-gray-500 mb-2">(${item.distance.toFixed(1)} km)</p>`;

                return `
                    <div class="p-2 min-w-[200px] font-sans">
                        <h3 class="font-bold uppercase text-sm mb-1">${item.shop.name}</h3>
                        <p class="text-xs text-gray-600 mb-2">${item.shop.address}</p>
                        ${distanceText}
                        ${this.showAvailability ? `<p class="text-xs font-bold mb-2" style="color:${color}">${statusText}</p>` : ''}
                        <button onclick="window.dispatchEvent(new CustomEvent('select-shop-from-map', { detail: { id: ${item.shop.id}, name: '${item.shop.name.replace(/'/g, "\\'")}' } }))"
                                class="w-full bg-gray-900 text-white px-3 py-2 text-xs font-bold uppercase hover:bg-gray-800">
                            ▸ Choisir ce magasin
                        </button>
                    </div>
                `;
            },

            async selectShop(shop) {
                try {
                    const token = document.querySelector('meta[name="csrf-token"]')?.content;
                    const res = await fetch('/shop/select', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                        },
                        body: JSON.stringify({ shop_id: shop.id }),
                    });

                    if (res.ok) {
                        this.updateSelectedShop(shop);
                        this.closeModal();
                    } else {
                        console.error('Erreur serveur lors de la sélection');
                    }
                } catch (e) {
                    console.error('Erreur sélection:', e);
                }
            },

            updateSelectedShop(shop) {
                localStorage.setItem('selectedShop', JSON.stringify(shop));
                window.dispatchEvent(new CustomEvent('shop-selected', { detail: shop }));

                const btn = document.getElementById('store-button-text');
                if (btn) btn.textContent = shop.name;
            },
        };
    }

    // Sélection depuis la carte
    window.addEventListener('select-shop-from-map', async (e) => {
        const { id, name } = e.detail;

        try {
            const token = document.querySelector('meta[name="csrf-token"]')?.content;
            const res = await fetch('/shop/select', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                },
                body: JSON.stringify({ shop_id: id }),
            });

            if (res.ok) {
                localStorage.setItem('selectedShop', JSON.stringify({ id, name }));
                const btn = document.getElementById('store-button-text');

                if (btn) btn.textContent = name;

                window.dispatchEvent(new CustomEvent('close-shop-modal'));
                window.location.reload();
            }
        } catch (e) {
            console.error('Erreur sélection depuis carte:', e);
        }
    });
</script>

<style>
    [x-cloak] {
        display: none !important;
    }
</style>
