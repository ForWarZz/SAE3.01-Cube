@props([
    "type" => "text",
    "name",
    "id" => null,
    "label" => null,
    "value" => null,
    "placeholder" => null,
    "required" => false,
    "help" => null,
    "wrapperClass" => "mb-4",
    "inputClass" => "mt-1 block w-full rounded-md border-gray-300 p-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm",
])

@php
    $id = $id ?? $name;
@endphp

<div class="{{ $wrapperClass }}">
    @if ($label)
        <x-input-label for="{{ $id }}">
            {!! $label !!}
            @if ($required)
                <span class="text-red-600">*</span>
            @endif
        </x-input-label>
    @endif

    @if ($type === "textarea")
        <textarea
            name="{{ $name }}"
            id="{{ $id }}"
            placeholder="{{ $placeholder }}"
            {{ $attributes->except(["class"])->merge([]) }}
            class="{{ $inputClass }}"
        >
{{ old($name, $value) }}</textarea
        >
    @else
        <input
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $id }}"
            value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder }}"
            @if($required) required @endif
            {{ $attributes->except(["class"])->merge([]) }}
            class="{{ $inputClass }}"
        />
    @endif

    @if ($help)
        <p class="mt-1 text-xs text-gray-500">{{ $help }}</p>
    @endif

    <x-input-error :messages="$errors->get($name)" class="mt-2" />
</div>
