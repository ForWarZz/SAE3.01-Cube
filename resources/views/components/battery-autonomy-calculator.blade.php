@props([
    "batteryCapacity" => 500,
    "batteryOptions" => collect(),
    "article" => null,
])

@php
    $batteryOptionsJson = $batteryOptions
        ->map(
            fn ($opt) => [
                "capacity" => (int) preg_replace("/[^0-9]/", "", $opt->label),
                "label" => $opt->label,
                "url" => $opt->url,
                "active" => $opt->active,
            ],
        )
        ->values()
        ->toJson();

    $bikeCoefficient = 1.0;

    if ($article && $article->bike && $article->category) {
        $categoryName = strtolower($article->category->getFullPath());

        if (str_contains($categoryName, "vtt") || str_contains($categoryName, "mountain")) {
            $bikeCoefficient = 1.15;
        } elseif (str_contains($categoryName, "route") || str_contains($categoryName, "road")) {
            $bikeCoefficient = 0.9;
        } elseif (str_contains($categoryName, "gravel")) {
            $bikeCoefficient = 1.05;
        } elseif (str_contains($categoryName, "ville") || str_contains($categoryName, "urbain") || str_contains($categoryName, "city")) {
            $bikeCoefficient = 0.95;
        }
    }
@endphp

<div
    id="battery-autonomy-container"
    x-data="batteryCalculator(
                {{ $batteryCapacity }},
                {{ $batteryOptionsJson }},
                {{ $bikeCoefficient }},
            )"
    class="my-12 rounded-lg border border-gray-200 bg-gradient-to-br from-blue-50 via-white to-gray-50 p-8 shadow-sm"
>
    <div class="flex flex-row gap-10">
        <div class="w-1/3">
            <div class="mb-6">
                <h3 class="flex items-center gap-2 text-lg font-bold text-gray-900">
                    <x-heroicon-o-bolt class="h-5 w-5 text-blue-600" />
                    Calculateur d'autonomie
                </h3>
                <p class="mt-2 text-sm text-gray-500">Estimez l'autonomie en fonction de votre poids, vitesse et terrain.</p>
            </div>

            <div class="space-y-4 border-t border-gray-200 pt-6">
                <div>
                    <h4 class="mb-3 text-sm font-semibold text-gray-900">
                        <x-heroicon-o-battery-100 class="inline-block h-4 w-4" />
                        Batterie
                    </h4>

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
                            </div>
                        </div>
                    @endif
                </div>

                <div class="mt-4 space-y-3 text-sm text-gray-600">
                    <h4 class="flex items-center gap-2 font-semibold text-gray-900">
                        <x-heroicon-o-information-circle class="h-4 w-4 text-blue-600" />
                        Facteurs influençant l'autonomie
                    </h4>
                    <ul class="space-y-2">
                        <li class="flex items-start gap-2">
                            <x-heroicon-o-user class="mt-0.5 h-4 w-4 shrink-0 text-gray-400" />
                            <span>Poids du cycliste et du chargement</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <x-heroicon-o-arrow-trending-up class="mt-0.5 h-4 w-4 shrink-0 text-gray-400" />
                            <span>Dénivelé et type de terrain</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <x-heroicon-o-bolt class="mt-0.5 h-4 w-4 shrink-0 text-gray-400" />
                            <span>Niveau d'assistance utilisé</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <x-heroicon-o-sun class="mt-0.5 h-4 w-4 shrink-0 text-gray-400" />
                            <span>Conditions météo (vent, température)</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="flex w-2/3 flex-col gap-6 border-l border-gray-200 pl-10">
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">
                        <x-heroicon-o-scale class="inline-block h-4 w-4 text-gray-500" />
                        Poids total (kg)
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

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">
                        <x-heroicon-o-arrow-right class="inline-block h-4 w-4 text-gray-500" />
                        Vitesse moyenne (km/h)
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

            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">
                    <x-heroicon-o-map class="inline-block h-4 w-4 text-gray-500" />
                    Type de terrain
                </label>
                <div class="grid grid-cols-4 gap-3">
                    <template x-for="terrain in terrainOptions" :key="terrain.value">
                        <button
                            type="button"
                            @click="terrainType = terrain.value"
                            :class="terrainType === terrain.value
                                ? 'border-blue-600 bg-blue-50 text-blue-700 ring-2 ring-blue-600 ring-offset-1'
                                : 'border-gray-200 bg-white text-gray-600 hover:border-gray-300 hover:bg-gray-50'"
                            class="flex flex-col items-center gap-1 rounded-lg border px-3 py-3 text-sm font-medium transition-all"
                        >
                            <span class="text-xl" x-text="terrain.icon"></span>
                            <span x-text="terrain.label"></span>
                        </button>
                    </template>
                </div>
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">
                    <x-heroicon-o-bolt class="inline-block h-4 w-4 text-gray-500" />
                    Niveau d'assistance
                </label>
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

            <div class="mt-2 grid grid-cols-3 gap-4">
                <div class="rounded-xl bg-gradient-to-br from-blue-600 to-blue-700 p-5 text-white shadow-lg">
                    <div class="mb-1 text-xs font-medium tracking-wide uppercase opacity-80">Autonomie estimée</div>
                    <div class="flex items-baseline gap-1">
                        <span class="text-4xl font-bold" x-text="estimatedRange"></span>
                        <span class="text-lg opacity-80">km</span>
                    </div>
                    <div class="mt-2 flex items-center gap-1 text-xs opacity-80">
                        <x-heroicon-o-clock class="h-3 w-3" />
                        <span>
                            ~
                            <span x-text="estimatedTime"></span>
                        </span>
                    </div>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="mb-1 text-xs font-medium tracking-wide text-gray-500 uppercase">Minimum</div>
                    <div class="flex items-baseline gap-1">
                        <span class="text-3xl font-bold text-orange-600" x-text="minRange"></span>
                        <span class="text-sm text-gray-500">km</span>
                    </div>
                    <div class="mt-2 text-xs text-gray-400">Conditions difficiles</div>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="mb-1 text-xs font-medium tracking-wide text-gray-500 uppercase">Maximum</div>
                    <div class="flex items-baseline gap-1">
                        <span class="text-3xl font-bold text-green-600" x-text="maxRange"></span>
                        <span class="text-sm text-gray-500">km</span>
                    </div>
                    <div class="mt-2 text-xs text-gray-400">Conditions optimales</div>
                </div>
            </div>

            <div class="rounded-lg border border-blue-100 bg-blue-50/50 p-4">
                <h4 class="mb-2 flex items-center gap-2 text-sm font-medium text-gray-900">
                    <x-heroicon-o-light-bulb class="h-4 w-4 text-yellow-500" />
                    Conseils pour maximiser l'autonomie
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
        Alpine.data('batteryCalculator', (initialCapacity, batteryOptions, bikeCoefficient) => ({
            batteryOptions: batteryOptions,
            selectedBattery: batteryOptions.find((b) => b.active) || { capacity: initialCapacity, label: '', url: '', active: true },
            bikeCoefficient: bikeCoefficient,
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

            selectBattery(battery) {
                this.selectedBattery = battery;
            },

            get batteryCapacity() {
                return this.selectedBattery.capacity;
            },

            get consumptionPerKm() {
                const base = 10;
                const weightFactor = 1 + (this.weight - 70) * 0.008;
                const terrainFactors = { flat: 0.7, urban: 0.9, mixed: 1.2, hilly: 1.7 };
                const assistFactors = { eco: 0.55, medium: 1.0, high: 1.6 };
                const speedFactor = 1 + (this.avgSpeed - 22) * 0.02;

                return (
                    base *
                    this.bikeCoefficient *
                    weightFactor *
                    terrainFactors[this.terrainType] *
                    assistFactors[this.assistLevel] *
                    speedFactor
                );
            },

            get usableCapacity() {
                return this.batteryCapacity * 0.85;
            },

            get estimatedRange() {
                return Math.round(this.usableCapacity / this.consumptionPerKm);
            },

            get minRange() {
                const worstConsumption = 10 * 1.5 * 1.7 * 1.6 * 1.1;
                return Math.round(this.usableCapacity / worstConsumption);
            },

            get maxRange() {
                const bestConsumption = 10 * 0.85 * 0.7 * 0.55 * 0.9;
                return Math.round(this.usableCapacity / bestConsumption);
            },

            get estimatedTime() {
                const hours = this.estimatedRange / this.avgSpeed;
                const h = Math.floor(hours);
                const m = Math.round((hours - h) * 60);
                if (h === 0) return `${m} min`;
                if (m === 0) return `${h}h`;
                return `${h}h${m.toString().padStart(2, '0')}`;
            },
        }));
    });
</script>
