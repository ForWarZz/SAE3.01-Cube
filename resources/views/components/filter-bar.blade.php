@props([
    'filterOptions',
    'activeFilters',
    'sortBy' => null,
])

<aside class="w-64 flex-shrink-0">
    <div class="space-y-6 bg-white p-6 rounded-lg shadow-sm border border-gray-200">
        <div class="flex justify-between items-center border-b pb-4">
            <h2 class="text-lg font-semibold text-gray-900">Filtres</h2>
            <a href="{{ url()->current() }}" class="text-sm text-blue-600 hover:text-blue-800">Réinitialiser</a>
        </div>

        <x-filter-checkbox-group label="Disponibilité(s)" name="disponibilite" :items="$filterOptions['availabilities']" :selected="$activeFilters['disponibilite'] ?? []" />
        <x-filter-checkbox-group label="Usage" name="usage" :items="$filterOptions['usages']" :selected="$activeFilters['usage'] ?? []" />
        <x-filter-checkbox-group label="Modèle de vélo" name="modele_velo" :items="$filterOptions['bikeModels']" :selected="$activeFilters['modele_velo'] ?? []" />
        <x-filter-checkbox-group label="En promotion" name="promotion" :items="$filterOptions['promotions']" :selected="$activeFilters['promotion'] ?? []" />
        <x-filter-checkbox-group label="Millésime" name="millesime" :items="$filterOptions['vintages']" :selected="$activeFilters['millesime'] ?? []" />
        <x-filter-checkbox-group label="Type de cadre" name="cadre" :items="$filterOptions['frames']" :selected="$activeFilters['cadre'] ?? []" />
        <x-filter-checkbox-group label="Matériau" name="materiau" :items="$filterOptions['materials']" :selected="$activeFilters['materiau'] ?? []" />
        <x-filter-checkbox-group label="Couleur" name="couleur" :items="$filterOptions['colors']" :selected="$activeFilters['couleur'] ?? []" />
    </div>

    <script>
        const url = new URL(window.location);
        const params = url.searchParams;

        document.querySelector('aside').addEventListener('change', e => {
            if (e.target.type !== 'checkbox') return;

            const name = e.target.name.replace('[]', '');
            const values = [...document.querySelectorAll(`input[name="${e.target.name}"]:checked`)]
                .map(x => x.value);

            params.delete(name);
            params.delete(name + '[]');

            values.forEach(v => params.append(name + '[]', v));

            window.location = `${url.pathname}?${params}`;
        });
    </script>
</aside>
