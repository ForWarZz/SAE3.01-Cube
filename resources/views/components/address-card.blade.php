@props([
    "address",
    "name" => null,
    "model" => null,
    "value" => null,
])

@php
    $isInput = ! empty($name);
    $tag = $isInput ? "label" : "div";
@endphp

<{{ $tag }}
    {{
        $attributes->merge([
            "class" => "relative flex flex-col justify-between rounded-lg border p-4 transition " . ($isInput ? "cursor-pointer" : "bg-white border-gray-200"),
        ])
    }}
    @if ($isInput)
        :class="{{ $model }} == {{ $value }} ? 'border-blue-600 bg-blue-50' : 'border-gray-200 hover:border-gray-300 bg-white'"
    @endif
>
    @if ($isInput)
        <input type="radio" name="{{ $name }}" value="{{ $value }}" x-model="{{ $model }}" class="sr-only" />
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

            <p class="text-gray-600">{{ $address->ville->cp_ville }} {{ $address->ville->nom_ville }}</p>

            @if ($address->tva_adresse)
                <p class="mt-1 text-gray-500">TVA: {{ $address->tva_adresse }}</p>
            @endif
        </div>

        <div class="ml-4">
            @if ($isInput)
                <div
                    x-show="{{ $model }} == {{ $value }}"
                    class="flex h-5 w-5 items-center justify-center rounded-full bg-blue-600 text-white"
                    style="display: none"
                >
                    <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 12 12">
                        <path
                            d="M3.707 5.293a1 1 0 00-1.414 1.414l1.414-1.414zM5 8l-.707.707a1 1 0 001.414 0L5 8zm4.707-3.293a1 1 0 00-1.414-1.414l1.414 1.414zm-7.414 2l2 2 1.414-1.414-2-2-1.414 1.414zm3.414 2l4-4-1.414-1.414-4 4 1.414 1.414z"
                        />
                    </svg>
                </div>
            @elseif (isset($actions))
                {{ $actions }}
            @endif
        </div>
    </div>
</{{ $tag }}>
