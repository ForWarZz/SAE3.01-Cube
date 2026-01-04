@props([
    "address",
    "name" => null,
    "value" => null,
    "selected" => false,
])

@php
    $isInput = ! empty($name);
    $tag = $isInput ? "label" : "div";

    $baseClasses = "relative flex flex-col justify-between rounded-lg border p-4 transition";

    if ($isInput) {
        $classes = $baseClasses . " cursor-pointer " . ($selected ? "border-blue-600 bg-blue-50 ring-1 ring-blue-600" : "border-gray-200 bg-white hover:border-gray-300");
    } else {
        $classes = $baseClasses . " bg-white border-gray-200";
    }
@endphp

<{{ $tag }} {{ $attributes->merge(["class" => $classes]) }}>
    @if ($isInput)
        <input
            type="radio"
            name="{{ $name }}"
            value="{{ $value }}"
            class="sr-only"
            {{ $selected ? "checked" : "" }}
            onchange="this.form.submit()"
        />
    @endif

    <div class="flex items-start justify-between">
        <div class="text-sm">
            <h3 class="font-bold text-gray-900">{{ $address->alias_adresse }}</h3>

            @if ($address->societe_adresse)
                <p class="font-medium text-gray-700">{{ $address->societe_adresse }}</p>
            @endif

            <p class="text-gray-600">{{ $address->prenom_adresse }} {{ $address->nom_adresse }}</p>

            @if ($address->telephone_adresse)
                <p class="text-gray-600">Tél: {{ $address->telephone_adresse }}</p>
            @endif

            <p class="mt-1 text-gray-600">{{ $address->num_voie_adresse }} {{ $address->rue_adresse }}</p>

            @if ($address->complement_adresse)
                <p class="text-gray-600">{{ $address->complement_adresse }}</p>
            @endif

            <p class="text-gray-600">{{ $address->code_postal }} {{ $address->nom_ville }}</p>

            @if ($address->tva_adresse)
                <p class="mt-1 text-gray-500">TVA: {{ $address->tva_adresse }}</p>
            @endif
        </div>

        <div class="ml-4">
            @if ($isInput && $selected)
                <div class="text-blue-600">
                    <x-bi-check-circle-fill class="h-6 w-6" />
                </div>
            @elseif (isset($actions))
                {{ $actions }}
            @endif
        </div>
    </div>
</{{ $tag }}>
