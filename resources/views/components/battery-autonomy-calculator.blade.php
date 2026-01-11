@props([
    "batteryCapacity" => 500,
    "batteryLabel" => null,
    "batteryOptions" => collect(),
    "bikeCategory" => null,
    "bikeModel" => null,
])

@php
    // Prepare battery options for JavaScript
    $batteryOptionsJson = $batteryOptions->map(fn($opt) => [
        'capacity' => (int) preg_replace('/[^0-9]/', '', $opt->label),
        'label' => $opt->label,
        'url' => $opt->url,
        'active' => $opt->active,
    ])->values()->toJson();

    // Determine bike type for terrain recommendations based on category
    $bikeType = 'trekking'; // default - most versatile
    $categoryLabel = strtolower($bikeCategory ?? '');
    $modelLabel = strtolower($bikeModel ?? '');

    // Check category first (most reliable)
    if (str_contains($categoryLabel, 'vtt')) {
        $bikeType = 'mtb';
    } elseif (str_contains($categoryLabel, 'route')) {
        $bikeType = 'road';
    } elseif (str_contains($categoryLabel, 'gravel')) {
        $bikeType = 'gravel';
    } elseif (str_contains($categoryLabel, 'ville') || str_contains($categoryLabel, 'campagne')) {
        $bikeType = 'urban';
    }
    // Fallback to model name detection
    elseif (str_contains($modelLabel, 'stereo') || str_contains($modelLabel, 'reaction') || str_contains($modelLabel, 'ams')) {
        $bikeType = 'mtb'; // These are MTB models
    } elseif (str_contains($modelLabel, 'nuroad') || str_contains($modelLabel, 'cross race')) {
        $bikeType = 'gravel';
    } elseif (str_contains($modelLabel, 'attain') || str_contains($modelLabel, 'aerium') || str_contains($modelLabel, 'axial')) {
        $bikeType = 'road';
    } elseif (str_contains($modelLabel, 'touring') || str_contains($modelLabel, 'kathmandu') || str_contains($modelLabel, 'nature')) {
        $bikeType = 'trekking';
    } elseif (str_contains($modelLabel, 'hyde') || str_contains($modelLabel, 'ella') || str_contains($modelLabel, 'compact') || str_contains($modelLabel, 'supreme')) {
        $bikeType = 'urban';
    }
@endphp

<div
    id="battery-autonomy-container"
    x-data="batteryAutonomyCalculator({{ $batteryCapacity }}, {{ $batteryOptionsJson }}, '{{ $bikeType }}')"
    class="my-12 rounded-lg border border-gray-200 bg-gradient-to-br from-blue-50 via-white to-gray-50 p-8 shadow-sm"
>
    <div class="flex flex-row gap-10">
        {{-- Left column: Info --}}
        <div class="w-1/3">
            <div class="mb-6">
                <h3 class="flex items-center gap-2 text-lg font-bold text-gray-900">
                    <x-heroicon-o-bolt class="h-5 w-5 text-blue-600" />
                    Calculateur d'autonomie
                </h3>
                <p class="mt-2 text-sm text-gray-500">
                    Estimez l'autonomie de ce VAE en fonction de votre pratique pour déterminer si cette batterie vous convient.
                </p>
            </div>

            <div class="space-y-4 border-t border-gray-200 pt-6">
                {{-- Battery Selector --}}
                <div>
                    <h4 class="mb-3 text-sm font-semibold text-gray-900">Batterie</h4>

                    @if ($batteryOptions->count() > 1)
                        <div class="space-y-2">
                            <template x-for="battery in batteryOptions" :key="battery.capacity">
                                <button
                                    type="button"
                                    @click="selectBattery(battery)"
                                    :class="selectedBattery.capacity === battery.capacity
                                        ? 'border-blue-600 bg-blue-100 ring-2 ring-blue-600'
                                        : 'border-gray-200 bg-white hover:border-blue-300 hover:bg-blue-50'"
                                    class="flex w-full items-center gap-3 rounded-lg border p-3 transition-all"
                                >
                                    <div
                                        :class="selectedBattery.capacity === battery.capacity ? 'bg-blue-600' : 'bg-gray-400'"
                                        class="flex h-10 w-10 items-center justify-center rounded-full transition-colors"
                                    >
                                        <x-heroicon-o-battery-100 class="h-5 w-5 text-white" />
                                    </div>
                                    <div class="text-left">
                                        <p
                                            :class="selectedBattery.capacity === battery.capacity ? 'text-blue-700' : 'text-gray-900'"
                                            class="text-lg font-bold"
                                            x-text="battery.capacity + ' Wh'"
                                        ></p>
                                        <p class="text-xs text-gray-500" x-text="battery.label"></p>
                                    </div>
                                    <div class="ml-auto" x-show="selectedBattery.capacity === battery.capacity">
                                        <x-heroicon-s-check-circle class="h-5 w-5 text-blue-600" />
                                    </div>
                                </button>
                            </template>
                        </div>
                    @else
                        <div class="flex items-center gap-3 rounded-lg bg-blue-100 p-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-600">
                                <x-heroicon-o-battery-100 class="h-6 w-6 text-white" />
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-blue-700">{{ $batteryCapacity }} Wh</p>
                                @if ($batteryLabel)
                                    <p class="text-sm text-blue-600">{{ $batteryLabel }}</p>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <div class="mt-4 space-y-3 text-sm text-gray-600">
                    <h4 class="font-semibold text-gray-900">Facteurs influençant l'autonomie</h4>
                    <ul class="space-y-2">
                        <li class="flex items-start gap-2">
                            <x-heroicon-m-user class="mt-0.5 h-4 w-4 shrink-0 text-gray-400" />
                            <span>Poids du cycliste et du chargement</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <x-heroicon-m-arrow-trending-up class="mt-0.5 h-4 w-4 shrink-0 text-gray-400" />
                            <span>Dénivelé et type de terrain</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <x-heroicon-m-bolt class="mt-0.5 h-4 w-4 shrink-0 text-gray-400" />
                            <span>Niveau d'assistance utilisé</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <x-heroicon-m-sun class="mt-0.5 h-4 w-4 shrink-0 text-gray-400" />
                            <span>Conditions météo (vent, température)</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Right column: Calculator --}}
        <div class="flex w-2/3 flex-col gap-6 border-l border-gray-200 pl-10">
            {{-- Terrain Mismatch Warning --}}
            <div
                x-show="terrainWarning"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                class="rounded-lg border border-amber-300 bg-amber-50 p-4"
            >
                <div class="flex items-start gap-3">
                    <x-heroicon-o-exclamation-triangle class="h-5 w-5 flex-shrink-0 text-amber-600" />
                    <div class="flex-1">
                        <h4 class="font-medium text-amber-800" x-text="terrainWarning?.title"></h4>
                        <p class="mt-1 text-sm text-amber-700" x-text="terrainWarning?.message"></p>
                        <a
                            x-show="terrainWarning?.link"
                            :href="terrainWarning?.link"
                            class="mt-2 inline-flex items-center gap-1 text-sm font-medium text-amber-800 underline hover:text-amber-900"
                        >
                            <span x-text="terrainWarning?.linkText"></span>
                            <x-heroicon-o-arrow-right class="h-4 w-4" />
                        </a>
                    </div>
                </div>
            </div>

            {{-- Inputs --}}
            <div class="grid grid-cols-2 gap-6">
                {{-- Weight --}}
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">
                        Poids total (cycliste + équipement)
                    </label>
                    <div class="flex items-center gap-4">
                        <input
                            type="range"
                            x-model="weight"
                            min="50"
                            max="130"
                            step="5"
                            class="h-2 flex-1 cursor-pointer appearance-none rounded-lg bg-gray-200 accent-blue-600"
                        />
                        <div class="w-20 rounded-lg border border-gray-200 bg-white px-3 py-2 text-center">
                            <span class="font-semibold text-gray-900" x-text="weight"></span>
                            <span class="text-sm text-gray-500">kg</span>
                        </div>
                    </div>
                </div>

                {{-- Average Speed --}}
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">
                        Vitesse moyenne souhaitée
                    </label>
                    <div class="flex items-center gap-4">
                        <input
                            type="range"
                            x-model="avgSpeed"
                            min="15"
                            max="35"
                            step="1"
                            class="h-2 flex-1 cursor-pointer appearance-none rounded-lg bg-gray-200 accent-blue-600"
                        />
                        <div class="w-20 rounded-lg border border-gray-200 bg-white px-3 py-2 text-center">
                            <span class="font-semibold text-gray-900" x-text="avgSpeed"></span>
                            <span class="text-sm text-gray-500">km/h</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Terrain Type --}}
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">Type de terrain principal</label>
                <div class="grid grid-cols-4 gap-3">
                    <template x-for="terrain in terrainOptions" :key="terrain.value">
                        <button
                            type="button"
                            @click="terrainType = terrain.value"
                            :class="[
                                terrainType === terrain.value
                                    ? 'border-blue-600 bg-blue-50 text-blue-700 ring-2 ring-blue-600 ring-offset-1'
                                    : 'border-gray-200 bg-white text-gray-600 hover:border-gray-300 hover:bg-gray-50',
                                getTerrainWarningClass(terrain.value)
                            ]"
                            class="flex flex-col items-center gap-1 rounded-lg border px-3 py-3 text-sm font-medium transition-all"
                        >
                            <span class="text-xl" x-text="terrain.icon"></span>
                            <span x-text="terrain.label"></span>
                            <span
                                x-show="!isTerrainSuitable(terrain.value)"
                                class="text-[10px] text-amber-600"
                            >⚠️ Non adapté</span>
                        </button>
                    </template>
                </div>
            </div>

            {{-- Assist Level --}}
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">Niveau d'assistance moyen utilisé</label>
                <div class="grid grid-cols-3 gap-3">
                    <template x-for="level in assistLevels" :key="level.value">
                        <button
                            type="button"
                            @click="assistLevel = level.value"
                            :class="assistLevel === level.value
                                ? 'border-blue-600 bg-blue-50 text-blue-700 ring-2 ring-blue-600 ring-offset-1'
                                : 'border-gray-200 bg-white text-gray-600 hover:border-gray-300 hover:bg-gray-50'"
                            class="flex flex-col items-center gap-1 rounded-lg border px-4 py-3 text-sm font-medium transition-all"
                        >
                            <span class="text-xl" x-text="level.icon"></span>
                            <span x-text="level.label"></span>
                            <span class="text-xs text-gray-400" x-text="level.desc"></span>
                        </button>
                    </template>
                </div>
            </div>

            {{-- Results --}}
            <div class="mt-2 grid grid-cols-3 gap-4">
                {{-- Estimated Range --}}
                <div class="rounded-xl bg-gradient-to-br from-blue-600 to-blue-700 p-5 text-white shadow-lg">
                    <div class="mb-1 text-xs font-medium uppercase tracking-wide opacity-80">Autonomie estimée</div>
                    <div class="flex items-baseline gap-1">
                        <span class="text-4xl font-bold" x-text="estimatedRange"></span>
                        <span class="text-lg opacity-80">km</span>
                    </div>
                    <div class="mt-2 flex items-center gap-1 text-xs opacity-80">
                        <x-heroicon-o-clock class="h-3 w-3" />
                        <span>~<span x-text="estimatedTime"></span> de trajet</span>
                    </div>
                </div>

                {{-- Min Range --}}
                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="mb-1 text-xs font-medium uppercase tracking-wide text-gray-500">Minimum (turbo)</div>
                    <div class="flex items-baseline gap-1">
                        <span class="text-3xl font-bold text-orange-600" x-text="minRange"></span>
                        <span class="text-sm text-gray-500">km</span>
                    </div>
                    <div class="mt-2 text-xs text-gray-400">Conditions difficiles</div>
                </div>

                {{-- Max Range --}}
                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="mb-1 text-xs font-medium uppercase tracking-wide text-gray-500">Maximum (éco)</div>
                    <div class="flex items-baseline gap-1">
                        <span class="text-3xl font-bold text-green-600" x-text="maxRange"></span>
                        <span class="text-sm text-gray-500">km</span>
                    </div>
                    <div class="mt-2 text-xs text-gray-400">Conditions optimales</div>
                </div>
            </div>

            {{-- Range visualization --}}
            <div class="rounded-lg border border-gray-200 bg-white p-4">
                <div class="mb-2 flex items-center justify-between text-sm">
                    <span class="font-medium text-gray-700">Plage d'autonomie possible</span>
                    <span class="font-medium" :class="gaugeColor" x-text="gaugeLabel"></span>
                </div>
                <div class="relative h-6 overflow-hidden rounded-full bg-gray-100">
                    {{-- Min to Max range bar --}}
                    <div
                        class="absolute h-full bg-gradient-to-r from-orange-200 via-blue-200 to-green-200"
                        :style="`left: ${minRangePercent}%; width: ${maxRangePercent - minRangePercent}%`"
                    ></div>
                    {{-- Current estimate marker --}}
                    <div
                        class="absolute top-0 h-full w-1 bg-blue-600 shadow-lg transition-all duration-300"
                        :style="`left: ${currentRangePercent}%`"
                    ></div>
                </div>
                <div class="mt-1 flex justify-between text-xs text-gray-500">
                    <span>0 km</span>
                    <span x-text="maxPossibleRange + ' km'"></span>
                </div>
            </div>

            {{-- Tips --}}
            <div class="rounded-lg border border-blue-100 bg-blue-50/50 p-4">
                <h4 class="mb-2 flex items-center gap-2 text-sm font-medium text-gray-900">
                    <x-heroicon-o-light-bulb class="h-4 w-4 text-yellow-500" />
                    Conseils pour maximiser votre autonomie
                </h4>
                <ul class="grid grid-cols-2 gap-x-4 gap-y-1 text-xs text-gray-600">
                    <li class="flex items-center gap-1">
                        <span class="h-1 w-1 rounded-full bg-blue-600"></span>
                        Utilisez le mode Éco sur le plat
                    </li>
                    <li class="flex items-center gap-1">
                        <span class="h-1 w-1 rounded-full bg-blue-600"></span>
                        Maintenez vos pneus bien gonflés
                    </li>
                    <li class="flex items-center gap-1">
                        <span class="h-1 w-1 rounded-full bg-blue-600"></span>
                        Pédalez de manière fluide
                    </li>
                    <li class="flex items-center gap-1">
                        <span class="h-1 w-1 rounded-full bg-blue-600"></span>
                        Évitez les accélérations brusques
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('batteryAutonomyCalculator', (initialCapacity, batteryOptions, bikeType) => ({
            batteryOptions: batteryOptions,
            selectedBattery: batteryOptions.find(b => b.active) || { capacity: initialCapacity, label: '', url: '', active: true },
            bikeType: bikeType, // 'mtb', 'road', 'gravel', 'urban', 'trekking'
            weight: 75,
            terrainType: 'mixed',
            assistLevel: 'medium',
            avgSpeed: 22,

            terrainOptions: [
                { value: 'flat', label: 'Plat', icon: '🛤️' },
                { value: 'urban', label: 'Urbain', icon: '🏙️' },
                { value: 'mixed', label: 'Vallonné', icon: '⛰️' },
                { value: 'hilly', label: 'Montagne', icon: '🏔️' },
            ],

            assistLevels: [
                { value: 'eco', label: 'Éco', icon: '🍃', desc: 'Économique' },
                { value: 'medium', label: 'Tour', icon: '⚡', desc: 'Équilibré' },
                { value: 'high', label: 'Turbo', icon: '🚀', desc: 'Puissant' },
            ],

            // Terrain suitability by bike type
            terrainSuitability: {
                mtb: ['flat', 'urban', 'mixed', 'hilly'], // VTT can do everything
                gravel: ['flat', 'urban', 'mixed', 'hilly'], // Gravel is versatile
                trekking: ['flat', 'urban', 'mixed'], // Trekking not for mountains
                road: ['flat', 'urban'], // Road bikes for flat/urban only
                urban: ['flat', 'urban'], // City bikes for flat/urban only
            },

            selectBattery(battery) {
                this.selectedBattery = battery;
            },

            get batteryCapacity() {
                return this.selectedBattery.capacity;
            },

            isTerrainSuitable(terrain) {
                const suitable = this.terrainSuitability[this.bikeType] || ['flat', 'urban', 'mixed', 'hilly'];
                return suitable.includes(terrain);
            },

            getTerrainWarningClass(terrain) {
                if (!this.isTerrainSuitable(terrain)) {
                    return 'opacity-70';
                }
                return '';
            },

            get terrainWarning() {
                if (this.isTerrainSuitable(this.terrainType)) {
                    return null;
                }

                const warnings = {
                    hilly: {
                        urban: {
                            title: 'Ce vélo n\'est pas conçu pour la montagne',
                            message: 'Les vélos urbains ne sont pas adaptés aux terrains montagneux. Pour une pratique en montagne, nous vous recommandons un VTT électrique.',
                            link: '/articles/categories/36?bike_model[]=4', // VTT category
                            linkText: 'Voir nos VTT électriques',
                        },
                        road: {
                            title: 'Ce vélo n\'est pas optimal pour la montagne',
                            message: 'Les vélos de route électriques sont conçus pour le bitume. Pour les sentiers de montagne, un VTT ou Gravel serait plus adapté.',
                            link: '/articles/categories/36?bike_model[]=4',
                            linkText: 'Voir nos VTT électriques',
                        },
                        trekking: {
                            title: 'Terrain difficile pour ce vélo',
                            message: 'Les vélos trekking peuvent gérer des chemins vallonnés mais ne sont pas conçus pour la montagne. Un VTT serait plus approprié.',
                            link: '/articles/categories/36?bike_model[]=4',
                            linkText: 'Voir nos VTT électriques',
                        },
                    },
                    mixed: {
                        urban: {
                            title: 'Terrain vallonné peu adapté',
                            message: 'Les vélos urbains sont conçus pour la ville et les routes plates. Pour des parcours vallonnés, un Gravel ou VTT serait plus adapté et confortable.',
                            link: '/articles/categories/36?bike_model[]=7',
                            linkText: 'Voir nos Gravel électriques',
                        },
                        road: {
                            title: 'Terrain vallonné peu adapté',
                            message: 'Les vélos de route sont optimisés pour le bitume lisse. Pour des chemins vallonnés, un Gravel ou VTT serait préférable.',
                            link: '/articles/categories/36?bike_model[]=7',
                            linkText: 'Voir nos Gravel électriques',
                        },
                    },
                    flat: {
                        mtb: {
                            title: 'Ce VTT est surqualifié pour ce terrain',
                            message: 'Un VTT électrique est conçu pour les terrains accidentés. Pour une utilisation principalement sur route plate, un vélo urbain ou de route serait plus efficace et confortable.',
                            link: '/articles/categories/36?bike_model[]=1',
                            linkText: 'Voir nos vélos urbains électriques',
                        },
                    },
                    urban: {
                        mtb: {
                            title: 'Ce VTT est surqualifié pour la ville',
                            message: 'Un VTT électrique est conçu pour les sentiers. Pour un usage urbain quotidien, un vélo de ville serait plus pratique et confortable.',
                            link: '/articles/categories/36?bike_model[]=1',
                            linkText: 'Voir nos vélos urbains électriques',
                        },
                    },
                };

                return warnings[this.terrainType]?.[this.bikeType] || null;
            },

            // Base consumption in Wh/km
            get consumptionPerKm() {
                let base = 10;

                // Weight factor (heavier = more consumption)
                const weightFactor = 1 + (this.weight - 70) * 0.008;

                // Terrain factor
                const terrainFactors = {
                    flat: 0.7,
                    urban: 0.9,
                    mixed: 1.2,
                    hilly: 1.7,
                };

                // Assist level factor
                const assistFactors = {
                    eco: 0.55,
                    medium: 1.0,
                    high: 1.6,
                };

                // Speed factor (faster = more consumption)
                const speedFactor = 1 + (this.avgSpeed - 22) * 0.02;

                return base * weightFactor * terrainFactors[this.terrainType] * assistFactors[this.assistLevel] * speedFactor;
            },

            // Usable capacity (85% to preserve battery life)
            get usableCapacity() {
                return this.batteryCapacity * 0.85;
            },

            get estimatedRange() {
                return Math.round(this.usableCapacity / this.consumptionPerKm);
            },

            get minRange() {
                // Worst case: turbo mode, hilly terrain, heavy rider
                const worstConsumption = 10 * 1.5 * 1.7 * 1.6 * 1.1;
                return Math.round(this.usableCapacity / worstConsumption);
            },

            get maxRange() {
                // Best case: eco mode, flat terrain, light rider
                const bestConsumption = 10 * 0.85 * 0.7 * 0.55 * 0.9;
                return Math.round(this.usableCapacity / bestConsumption);
            },

            get maxPossibleRange() {
                // Absolute max for scale
                return Math.round(this.batteryCapacity * 0.85 / (10 * 0.7 * 0.55 * 0.85 * 0.9));
            },

            get estimatedTime() {
                const hours = this.estimatedRange / this.avgSpeed;
                const h = Math.floor(hours);
                const m = Math.round((hours - h) * 60);
                if (h === 0) return `${m} min`;
                if (m === 0) return `${h}h`;
                return `${h}h${m.toString().padStart(2, '0')}`;
            },

            get minRangePercent() {
                return (this.minRange / this.maxPossibleRange) * 100;
            },

            get maxRangePercent() {
                return Math.min((this.maxRange / this.maxPossibleRange) * 100, 100);
            },

            get currentRangePercent() {
                return (this.estimatedRange / this.maxPossibleRange) * 100;
            },

            get gaugeColor() {
                const percent = this.currentRangePercent;
                if (percent >= 60) return 'text-green-600';
                if (percent >= 35) return 'text-blue-600';
                return 'text-orange-600';
            },

            get gaugeLabel() {
                const percent = this.currentRangePercent;
                if (percent >= 60) return 'Excellente autonomie';
                if (percent >= 35) return 'Bonne autonomie';
                return 'Autonomie modérée';
            },
        }));
    });
</script>
