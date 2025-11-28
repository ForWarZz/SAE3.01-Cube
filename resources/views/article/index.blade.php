<x-app-layout>
    <div class="max-w-7xl mx-auto px-6 py-12">
        @if($breadcrumbs ?? false)
            <nav class="mb-8 flex items-center space-x-2 text-sm text-gray-600">
                @foreach($breadcrumbs as $crumb)
                    @if($crumb['url'])
                        <a href="{{ $crumb['url'] }}" class="hover:text-gray-900">{{ $crumb['label'] }}</a>
                    @else
                        <span class="text-gray-900">{{ $crumb['label'] }}</span>
                    @endif
                    @if(!$loop->last)
                        <span>/</span>
                    @endif
                @endforeach
            </nav>
        @endif

        <div class="mb-10 flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    {{ $pageTitle ?? 'Nos produits' }}
                </h1>
                <p class="text-gray-600 mt-2">{{ $articles->total() }} produit{{ $articles->total() > 1 ? 's' : '' }} disponible{{ $articles->total() > 1 ? 's' : '' }}</p>
            </div>
            
            <!-- Sort Dropdown -->
            <div class="flex items-center gap-2">
                <label for="sort" class="text-sm font-medium text-gray-700">Trier par:</label>
                <select 
                    id="sort" 
                    name="sort_by"
                    onchange="window.location.href = updateQueryString('sort_by', this.value)"
                    class="rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                >
                    <option value="name_asc" {{ ($sortBy ?? 'name_asc') == 'name_asc' ? 'selected' : '' }}>Nom (A-Z)</option>
                    <option value="name_desc" {{ ($sortBy ?? '') == 'name_desc' ? 'selected' : '' }}>Nom (Z-A)</option>
                    <option value="price_asc" {{ ($sortBy ?? '') == 'price_asc' ? 'selected' : '' }}>Prix (croissant)</option>
                    <option value="price_desc" {{ ($sortBy ?? '') == 'price_desc' ? 'selected' : '' }}>Prix (décroissant)</option>
                    <option value="reference_asc" {{ ($sortBy ?? '') == 'reference_asc' ? 'selected' : '' }}>Référence (croissant)</option>
                    <option value="reference_desc" {{ ($sortBy ?? '') == 'reference_desc' ? 'selected' : '' }}>Référence (décroissant)</option>
                </select>
            </div>
        </div>
        
        <script>
            function updateQueryString(key, value) {
                const url = new URL(window.location.href);
                url.searchParams.set(key, value);
                return url.toString();
            }
        </script>

        <div class="flex gap-8">
            <!-- Filters Sidebar -->
            @if(isset($vintages) || isset($frames) || isset($materials) || isset($colors) || isset($priceRange) || isset($usages))
            <aside class="w-64 flex-shrink-0">
                <form method="GET" action="" id="filterForm" class="space-y-6 bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                    <input type="hidden" name="sort_by" value="{{ $sortBy ?? 'name_asc' }}">
                    
                    <div class="flex justify-between items-center border-b pb-4">
                        <h2 class="text-lg font-semibold text-gray-900">Filtres</h2>
                        <a href="{{ url()->current() }}" class="text-sm text-blue-600 hover:text-blue-800">Réinitialiser</a>
                    </div>

                    <!-- Promotion Filter -->
                    @if(isset($hasPromotions) && $hasPromotions)
                    <div>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input 
                                type="checkbox" 
                                name="promotion" 
                                value="1"
                                {{ ($filters['promotion'] ?? false) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                            >
                            <span class="text-sm font-medium text-gray-700">🏷️ En promotion</span>
                        </label>
                    </div>
                    @endif

                    <!-- Usage Filter -->
                    @if(isset($usages) && $usages->count() > 0)
                    <div x-data="{ expanded: false }">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Usage</label>
                        <div class="space-y-2">
                            @foreach($usages as $index => $usage)
                                <label class="flex items-center gap-2 cursor-pointer" x-show="{{ $index < 3 }} || expanded">
                                    <input 
                                        type="checkbox" 
                                        name="usage[]" 
                                        value="{{ $usage->id_usage }}"
                                        {{ in_array($usage->id_usage, (array)($filters['usage'] ?? [])) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                    >
                                    <span class="text-sm text-gray-600">{{ $usage->label_usage }}</span>
                                </label>
                            @endforeach
                        </div>
                        @if($usages->count() > 3)
                        <button type="button" @click="expanded = !expanded" class="mt-2 text-sm text-blue-600 hover:text-blue-800">
                            <span x-text="expanded ? 'Voir moins' : 'Voir plus ({{ $usages->count() - 3 }})'"></span>
                        </button>
                        @endif
                    </div>
                    @endif

                    <!-- Millesime Filter -->
                    @if(isset($vintages) && $vintages->count() > 0)
                    <div x-data="{ expanded: false }">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Millésime</label>
                        <div class="space-y-2">
                            @foreach($vintages as $index => $vintage)
                                <label class="flex items-center gap-2 cursor-pointer" x-show="{{ $index < 3 }} || expanded">
                                    <input 
                                        type="checkbox" 
                                        name="millesime[]" 
                                        value="{{ $vintage->id_millesime }}"
                                        {{ in_array($vintage->id_millesime, (array)($filters['millesime'] ?? [])) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                    >
                                    <span class="text-sm text-gray-600">{{ $vintage->millesime_velo }}</span>
                                </label>
                            @endforeach
                        </div>
                        @if($vintages->count() > 3)
                        <button type="button" @click="expanded = !expanded" class="mt-2 text-sm text-blue-600 hover:text-blue-800">
                            <span x-text="expanded ? 'Voir moins' : 'Voir plus ({{ $vintages->count() - 3 }})'"></span>
                        </button>
                        @endif
                    </div>
                    @endif

                    <!-- Type de Cadre Filter -->
                    @if(isset($frames) && $frames->count() > 0)
                    <div x-data="{ expanded: false }">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Type de cadre</label>
                        <div class="space-y-2">
                            @foreach($frames as $index => $frame)
                                <label class="flex items-center gap-2 cursor-pointer" x-show="{{ $index < 3 }} || expanded">
                                    <input 
                                        type="checkbox" 
                                        name="cadre[]" 
                                        value="{{ $frame->id_cadre_velo }}"
                                        {{ in_array($frame->id_cadre_velo, (array)($filters['cadre'] ?? [])) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                    >
                                    <span class="text-sm text-gray-600">{{ $frame->label_cadre_velo }}</span>
                                </label>
                            @endforeach
                        </div>
                        @if($frames->count() > 3)
                        <button type="button" @click="expanded = !expanded" class="mt-2 text-sm text-blue-600 hover:text-blue-800">
                            <span x-text="expanded ? 'Voir moins' : 'Voir plus ({{ $frames->count() - 3 }})'"></span>
                        </button>
                        @endif
                    </div>
                    @endif

                    <!-- Prix Slider -->
                    @if(isset($priceRange) && $priceRange['max'] > $priceRange['min'])
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Prix max</label>
                        <div class="space-y-3">
                            <input 
                                type="range" 
                                name="prix_max"
                                id="prix_slider"
                                min="{{ $priceRange['min'] }}" 
                                max="{{ $priceRange['max'] }}" 
                                value="{{ $filters['prix_max'] ?? $priceRange['max'] }}"
                                data-max="{{ $priceRange['max'] }}"
                                class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-blue-600"
                                oninput="document.getElementById('prix_display').textContent = this.value + ' €'"
                            >
                            <div class="flex justify-between text-xs text-gray-500">
                                <span>{{ number_format($priceRange['min'], 0, ',', ' ') }} €</span>
                                <span id="prix_display" class="font-medium text-blue-600">{{ number_format($filters['prix_max'] ?? $priceRange['max'], 0, ',', ' ') }} €</span>
                                <span>{{ number_format($priceRange['max'], 0, ',', ' ') }} €</span>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Poids Slider -->
                    @if(isset($weightRange) && $weightRange['max'] > $weightRange['min'])
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Poids max (kg)</label>
                        <div class="space-y-3">
                            <input 
                                type="range" 
                                name="poids_max"
                                id="poids_slider"
                                min="{{ $weightRange['min'] }}" 
                                max="{{ $weightRange['max'] }}" 
                                value="{{ $filters['poids_max'] ?? $weightRange['max'] }}"
                                data-max="{{ $weightRange['max'] }}"
                                step="0.1"
                                class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-blue-600"
                                oninput="document.getElementById('poids_display').textContent = this.value + ' kg'"
                            >
                            <div class="flex justify-between text-xs text-gray-500">
                                <span>{{ $weightRange['min'] }} kg</span>
                                <span id="poids_display" class="font-medium text-blue-600">{{ $filters['poids_max'] ?? $weightRange['max'] }} kg</span>
                                <span>{{ $weightRange['max'] }} kg</span>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Matériau Filter -->
                    @if(isset($materials) && $materials->count() > 0)
                    <div x-data="{ expanded: false }">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Matériau</label>
                        <div class="space-y-2">
                            @foreach($materials as $index => $material)
                                <label class="flex items-center gap-2 cursor-pointer" x-show="{{ $index < 3 }} || expanded">
                                    <input 
                                        type="checkbox" 
                                        name="materiau[]" 
                                        value="{{ $material->id_materiau_cadre }}"
                                        {{ in_array($material->id_materiau_cadre, (array)($filters['materiau'] ?? [])) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                    >
                                    <span class="text-sm text-gray-600">{{ $material->label_materiau_cadre }}</span>
                                </label>
                            @endforeach
                        </div>
                        @if($materials->count() > 3)
                        <button type="button" @click="expanded = !expanded" class="mt-2 text-sm text-blue-600 hover:text-blue-800">
                            <span x-text="expanded ? 'Voir moins' : 'Voir plus ({{ $materials->count() - 3 }})'"></span>
                        </button>
                        @endif
                    </div>
                    @endif

                    <!-- Couleur Filter -->
                    @if(isset($colors) && $colors->count() > 0)
                    <div x-data="{ expanded: false }">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Couleur</label>
                        <div class="space-y-2">
                            @foreach($colors as $index => $color)
                                <label class="flex items-center gap-2 cursor-pointer" x-show="{{ $index < 3 }} || expanded">
                                    <input 
                                        type="checkbox" 
                                        name="couleur[]" 
                                        value="{{ $color->id_couleur }}"
                                        {{ in_array($color->id_couleur, (array)($filters['couleur'] ?? [])) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                    >
                                    <span class="text-sm text-gray-600">{{ $color->label_couleur }}</span>
                                </label>
                            @endforeach
                        </div>
                        @if($colors->count() > 3)
                        <button type="button" @click="expanded = !expanded" class="mt-2 text-sm text-blue-600 hover:text-blue-800">
                            <span x-text="expanded ? 'Voir moins' : 'Voir plus ({{ $colors->count() - 3 }})'"></span>
                        </button>
                        @endif
                    </div>
                    @endif

                    <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                        Appliquer les filtres
                    </button>
                </form>

                <script>
                    // Disable sliders at max value before form submit to avoid unnecessary filtering
                    document.getElementById('filterForm').addEventListener('submit', function(e) {
                        const prixSlider = document.getElementById('prix_slider');
                        const poidsSlider = document.getElementById('poids_slider');
                        
                        if (prixSlider && prixSlider.value == prixSlider.dataset.max) {
                            prixSlider.disabled = true;
                        }
                        if (poidsSlider && poidsSlider.value == poidsSlider.dataset.max) {
                            poidsSlider.disabled = true;
                        }
                    });
                </script>
            </aside>
            @endif

            <!-- Products Grid -->
            <div class="flex-1">
                <div class="grid grid-cols-4 gap-6">
                    @foreach ($articles as $article)
                        <x-article-card :article="$article" />
                    @endforeach
                </div>

                <div class="mt-12">
                    {{ $articles->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
