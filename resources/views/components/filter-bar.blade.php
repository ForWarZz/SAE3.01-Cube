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

        <x-filter-checkbox-group label="Usage" name="usage" :items="$filterOptions['usages']" :selected="$activeFilters['usage'] ?? []" />
        <x-filter-checkbox-group label="Millésime" name="millesime" :items="$filterOptions['vintages']" :selected="$activeFilters['millesime'] ?? []" />
        <x-filter-checkbox-group label="Type de cadre" name="cadre" :items="$filterOptions['frames']" :selected="$activeFilters['cadre'] ?? []" />
        <x-filter-checkbox-group label="Matériau" name="materiau" :items="$filterOptions['materials']" :selected="$activeFilters['materiau'] ?? []" />
        <x-filter-checkbox-group label="Couleur" name="couleur" :items="$filterOptions['colors']" :selected="$activeFilters['couleur'] ?? []" />
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('aside input[type="checkbox"]').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const url = new URL(window.location.href);
                    const params = new URLSearchParams(url.search);
                    const filterName = this.name.replace('[]', '');

                    const checkedValues = Array.from(
                        document.querySelectorAll(`input[name="${this.name}"]:checked`)
                    ).map(cb => cb.value);

                    params.delete(filterName + '[]');
                    params.delete(filterName);

                    if (checkedValues.length > 0) {
                        checkedValues.forEach(value => {
                            params.append(filterName + '[]', value);
                        });
                    }

                    window.location.href = url.pathname + '?' + params.toString();
                });
            });
        });
    </script>
</aside>
