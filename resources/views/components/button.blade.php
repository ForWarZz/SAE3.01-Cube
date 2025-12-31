@props(["type" => "button", "color" => "blue", "size" => "md", "icon" => null])

@php
    $colors = [
        "blue" => "border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:bg-blue-700",
        "red" => "border-transparent bg-red-600 text-white hover:bg-red-700 focus:bg-red-700",
        "green" => "border-transparent bg-green-600 text-white hover:bg-green-700 focus:bg-green-700",
        "gray" => "border-transparent bg-gray-600 text-white hover:bg-gray-700 focus:bg-gray-700",
    ];

    $sizes = [
        "xs" => "px-2 py-1 text-xs",
        "sm" => "px-3 py-2 text-xs",
        "md" => "px-4 py-2 text-xs",
        "lg" => "px-5 py-3 text-sm",
        "xl" => "px-6 py-4 text-lg",
    ];

    $iconSizeClasses = [
        "xs" => "size-3",
        "sm" => "size-4",
        "md" => "size-4",
        "lg" => "size-5",
        "xl" => "size-6",
    ];

    $classes =
        "inline-flex items-center justify-center rounded-md border font-semibold tracking-widest uppercase transition cursor-pointer " .
        ($colors[$color] ?? $colors["blue"]) .
        " " .
        ($sizes[$size] ?? $sizes["md"]);

    if ($attributes->get("disabled")) {
        $classes .= " opacity-50 border-gray-300";
    }
@endphp

@if (isset($attributes["href"]))
    <a {{ $attributes->merge(["class" => $classes]) }}>
        @if ($icon)
            <x-dynamic-component :component="$icon" class="mr-2 size-4" />
        @endif

        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(["class" => $classes]) }}>
        @if ($icon)
            <x-dynamic-component :component="$icon" class="mr-2 {{ $iconSizeClasses[$size] }}" />
        @endif

        {{ $slot }}
    </button>
@endif
