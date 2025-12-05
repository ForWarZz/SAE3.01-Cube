@props([
    'referenceId' => null,
    'showAvailability' => false
])

<div 
    x-data="shopSelector()"
    x-on:open-shop-modal.window="openModal($event.detail)"
    x-cloak
>
   
    <div x-show="isOpen" class="fixed inset-0 z-50 overflow-hidden">
        
        <div x-show="isOpen" class="fixed inset-0" x-on:click="closeModal()"></div>
        
        
        <div class="fixed inset-y-0 right-0 flex max-w-full pl-10 h-full">
            <div x-show="isOpen" x-transition:enter="transform transition ease-out duration-300" x-transition:enter-start="translate-x-full" 
            x-transition:enter-end="translate-x-0" x-transition:leave="transform transition ease-in duration-200" x-transition:leave-start="translate-x-0" 
            x-transition:leave-end="translate-x-full" class="w-screen max-w-xl h-full">

                <div class="flex h-full flex-col bg-white shadow-xl overflow-hidden">
                    
                    
                    <div class="flex items-center justify-between border-b border-gray-200 px-6 py-5">
                        <h2 class="text-xl font-bold text-gray-900 tracking-wide">CHOISIR UN MAGASIN</h2>

                        <button x-on:click="closeModal()" class="text-gray-400 hover:text-gray-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    
                    <div class="px-6 py-4">
                        <div class="relative">
                            <input type="text" x-model="searchQuery" x-on:input.debounce.300ms="filterShops()" placeholder="Saisir une adresse, un code postal ou une ville pour trouver votre magasin" class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-sm focus:outline-none focus:ring-1 focus:ring-gray-400 focus:border-gray-400 text-sm">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    
                    <div x-show="showAvailability" class="px-6 py-4 flex items-center">
                        <button type="button" x-on:click="showOnlyInStock = !showOnlyInStock" class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none" :class="showOnlyInStock ? 'bg-green-500' : 'bg-gray-300'" role="switch" :aria-checked="showOnlyInStock">
                            <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out" :class="showOnlyInStock ? 'translate-x-5' : 'translate-x-0'"></span>
                        </button>
                        <span class="ml-3 text-xs font-medium text-gray-700 uppercase tracking-wide">
                            Voir uniquement les magasins ayant le produit en stock
                        </span>
                    </div>

                    <div class="flex border-b border-gray-200">
                        <button x-on:click="activeTab = 'list'" class="flex-1 py-3 text-sm font-bold tracking-wide transition-colors" :class="activeTab === 'list' ? 'text-gray-900 border-b-2 border-green-500' : 'text-gray-400 hover:text-gray-600'">
                            VUE LISTE
                        </button>
                        <button x-on:click="activeTab = 'map'" class="flex-1 py-3 text-sm font-bold tracking-wide transition-colors" :class="activeTab === 'map' ? 'text-gray-900 border-b-2 border-green-500' : 'text-gray-400 hover:text-gray-600'">
                            VUE CARTE
                        </button>
                    </div>

                    

                    
                    <div x-show="activeTab === 'list'" class="flex-1 overflow-y-auto min-h-0">
                        
                        <div x-show="loading" class="flex items-center justify-center py-12">
                            <svg class="animate-spin h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>

                        
                        <div x-show="!loading && filteredShops.length === 0" class="py-12 text-center text-gray-500">
                            Aucun magasin trouvé.
                        </div>

                        
                        <div class="divide-y divide-gray-200">
                            <template x-for="item in filteredShops" :key="item.shop.id">
                                <div class="px-6 py-5 hover:bg-gray-50 transition-colors">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            
                                            <h3 class="font-bold text-gray-900 uppercase tracking-wide" x-text="item.shop.name"></h3>
                                            
                                            
                                            <p class="mt-1 text-sm text-gray-600" x-text="item.shop.address"></p>
                                            
                                            
                                            
                                            
                                            <p x-show="item.shop.hours" class="text-sm text-gray-600" x-text="'Horaires aujourd\'hui : ' + item.shop.hours"></p>

                                            
                                            <template x-if="showAvailability">
                                                <div class="mt-2">
                                                    <template x-if="item.status === 'in_stock'">

                                                        <span class="flex items-center text-green-600 text-sm font-medium">
                                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                            </svg>
                                                            Disponible dans mon magasin
                                                        </span>
                                                    </template>
                                                    <template x-if="item.status === 'orderable'">

                                                        <span class="flex items-center text-orange-500 text-sm font-medium">
                                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                                            </svg>
                                                            Commandable en magasin
                                                        </span>
                                                    </template>
                                                    <template x-if="item.status === 'unavailable'">
                                                        <span class="flex items-center text-gray-400 text-sm">
                                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                                            </svg>
                                                            Indisponible en magasins
                                                        </span>
                                                    </template>
                                                </div>
                                            </template>

                                            
                                            <template x-if="showAvailability && item.sizes && item.sizes.length > 0">
                                                <div class="mt-3 flex flex-wrap gap-2">
                                                    <template x-for="size in item.sizes" :key="size.size_id">
                                                        <span class="text-xs px-2 py-1 rounded" :class="{
                                                            'bg-green-100 text-green-700': size.status === 'En Stock',
                                                            'bg-orange-100 text-orange-700': size.status === 'Commandable',
                                                            'bg-gray-100 text-gray-500': size.status === 'Indisponible'
                                                        }" x-text="size.size_name + '  ' + size.status"></span>
                                                    </template>
                                                </div>
                                            </template>
                                        </div>

                                        
                                        <button x-on:click="selectShop(item.shop)" class="ml-4 bg-gray-900 text-white px-4 py-2 text-xs font-bold hover:bg-gray-800 transition flex items-center whitespace-nowrap flex-shrink-0">
                                            <span class="mr-1">▸</span>
                                            CHOISIR CE MAGASIN
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    
                    <!-- Ici c'est la vu carte a mettre -->

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
        selectedShopId: null,
        activeTab: 'list',

        init() {
            const savedShop = localStorage.getItem('selectedShop');
            if (savedShop) {
                const shop = JSON.parse(savedShop);
                this.selectedShopId = shop.id;
            }
        },

        async openModal(detail = {}) {
            this.isOpen = true;
            this.showAvailability = detail.showAvailability || false;
            this.referenceId = detail.referenceId || null;
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
                let url = '/shops';
                if (this.showAvailability && this.referenceId) {
                    url = `/availability/${this.referenceId}`;
                    if (this.sizeId) {
                        url += `?size=${this.sizeId}`;
                    }
                }
                const response = await fetch(url);
                const data = await response.json();
                this.shops = data.availabilities || data.shops || [];
            } catch (error) {
                console.error('Error loading shops:', error);
                this.shops = [];
            }
            this.loading = false;
        },

        

        get filteredShops() {
            let result = this.shops;
            
            
            if (this.searchQuery.trim()) {
                const query = this.searchQuery.toLowerCase();
                result = result.filter(item => 
                    item.shop.name.toLowerCase().includes(query) ||
                    item.shop.address.toLowerCase().includes(query) ||
                    (item.shop.city && item.shop.city.toLowerCase().includes(query)) ||
                    (item.shop.postalCode && item.shop.postalCode.includes(query))
                );
            }
            
            
            if (this.showOnlyInStock && this.showAvailability) {
                result = result.filter(item => item.status === 'in_stock');
            }
            
            return result;
        },

        async selectShop(shop) {
            try {
                const response = await fetch('/shop/select', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ shop_id: shop.id })
                });
                
                if (response.ok) {
                    this.selectedShopId = shop.id;
                    localStorage.setItem('selectedShop', JSON.stringify(shop));
                    
                    window.dispatchEvent(new CustomEvent('shop-selected', { detail: shop }));
                    
                    const headerBtn = document.getElementById('store-button-text');
                    if (headerBtn) {
                        headerBtn.textContent = shop.name;
                    }
                    
                    this.closeModal();
                }
            } catch (error) {
                console.error('Error selecting shop:', error);
            }
        }
    };
}
</script>

<style>
[x-cloak] { display: none !important; }
</style>