@props([
    "options",
    "active",
])

@php
    $limitMin = $options["price"]["min"];
    $limitMax = $options["price"]["max"];
    $currentMin = isset($active["price"]["min"]) ? (float) $active["price"]["min"] : $limitMin;
    $currentMax = isset($active["price"]["max"]) ? (float) $active["price"]["max"] : $limitMax;
@endphp

<div class="mb-4">
    <label class="mb-2 block text-sm font-medium text-gray-700">Prix</label>

    <div class="mb-2 flex justify-between text-sm text-gray-600">
        <span id="price-display-min">{{ number_format($currentMin, 0, ",", " ") }} €</span>
        <span id="price-display-max">{{ number_format($currentMax, 0, ",", " ") }} €</span>
    </div>

    <div class="space-y-3">
        <div>
            <label class="text-xs text-gray-500">Minimum</label>
            <input
                type="range"
                id="price-min"
                value="{{ $currentMin }}"
                min="{{ $limitMin }}"
                max="{{ $limitMax }}"
                step="1"
                class="w-full accent-blue-600"
            />
        </div>

        <div>
            <label class="text-xs text-gray-500">Maximum</label>
            <input
                type="range"
                id="price-max"
                value="{{ $currentMax }}"
                min="{{ $limitMin }}"
                max="{{ $limitMax }}"
                step="1"
                class="w-full accent-blue-600"
            />
        </div>
    </div>
</div>

<script>
    (function () {
        const minSlider = document.querySelector('#price-min');
        const maxSlider = document.querySelector('#price-max');
        const minDisplay = document.querySelector('#price-display-min');
        const maxDisplay = document.querySelector('#price-display-max');

        let timeout;

        function formatPrice(value) {
            return new Intl.NumberFormat('fr-FR').format(value) + ' €';
        }

        function updateUrl() {
            const url = new URL(window.location);
            const params = url.searchParams;

            const min = minSlider.value;
            const max = maxSlider.value;

            params.delete('price');
            params.delete('price[]');

            if (min !== {{ $limitMin }}) {
                params.set('price[min]', min);
            } else {
                params.delete('price[min]');
            }

            if (max !== {{ $limitMax }}) {
                params.set('price[max]', max);
            } else {
                params.delete('price[max]');
            }

            params.delete('page');

            window.location = `${url.pathname}?${params}`;
        }

        function handleInput() {
            let min = parseInt(minSlider.value);
            let max = parseInt(maxSlider.value);

            if (min > max) {
                minSlider.value = max;
                min = max;
            }
            if (max < min) {
                maxSlider.value = min;
                max = min;
            }

            minDisplay.textContent = formatPrice(min);
            maxDisplay.textContent = formatPrice(max);

            clearTimeout(timeout);
            timeout = setTimeout(updateUrl, 1000);
        }

        minSlider.addEventListener('input', handleInput);
        maxSlider.addEventListener('input', handleInput);
    })();
</script>
