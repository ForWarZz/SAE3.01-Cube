@props([
    'filterOptions',
    'activeFilters',
    'sortBy' => null,
])

<aside class="w-64">
    <div class="space-y-6 bg-white p-6 rounded-lg shadow-sm border border-gray-200">
        <div class="flex justify-between items-center border-b pb-4">
            <h2 class="text-lg font-semibold text-gray-900">Filtres</h2>
            <a href="{{ url()->current() }}" class="text-sm text-blue-600 hover:text-blue-800">Réinitialiser</a>
        </div>

        <x-filter-checkbox-group label="Disponibilité" name="availability" :filterOptions="$filterOptions" :activeFilters="$activeFilters"  />
        <x-filter-checkbox-group label="Usage" name="usage" :filterOptions="$filterOptions" :activeFilters="$activeFilters"  />
        <x-filter-checkbox-group label="Modèle de vélo" name="bikeModel" :filterOptions="$filterOptions" :activeFilters="$activeFilters"  />
        <x-filter-checkbox-group label="En promotion" name="promotion" :filterOptions="$filterOptions" :activeFilters="$activeFilters"  />
        <x-filter-checkbox-group label="Millésime" name="vintage" :filterOptions="$filterOptions" :activeFilters="$activeFilters"  />
        <x-filter-checkbox-group label="Type de cadre" name="frame" :filterOptions="$filterOptions" :activeFilters="$activeFilters"  />
        <x-filter-checkbox-group label="Matériau" name="material" :filterOptions="$filterOptions" :activeFilters="$activeFilters"  />
        <x-filter-checkbox-group label="Couleur" name="color" :filterOptions="$filterOptions" :activeFilters="$activeFilters"  />
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
