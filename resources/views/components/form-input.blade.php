@props([
    "type" => "text",
    "name",
    "id" => null,
    "label" => null,
    "value" => null,
    "placeholder" => null,
    "required" => false,
    "readonly" => false,
    "help" => null,
    "wrapperClass" => "mb-4",
    "inputClass" => "mt-1 block w-full rounded-md border-gray-300 p-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm",
])

@php
    $id = $id ?? $name;
    $readonlyClass = $readonly ? "cursor-not-allowed border-gray-200 bg-gray-100" : "";
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
            @if($required) required @endif
            @if($readonly) readonly @endif
            {{ $attributes->except(["class"])->merge([]) }}
            class="{{ $inputClass }} {{ $readonlyClass }}"
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
            @if($readonly) readonly @endif
            {{ $attributes->except(["class"])->merge([]) }}
            class="{{ $inputClass }} {{ $readonlyClass }}"
        />
    @endif

    @if ($help)
        <p class="mt-1 text-xs text-gray-500">{{ $help }}</p>
    @endif

    <x-input-error :messages="$errors->get($name)" class="mt-2" />
</div>
