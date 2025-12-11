@props([
    "key" => "message",
    "type" => "error",
])

@php
    $types = [
        "error" => [
            "color" => "red",
            "icon" => "heroicon-s-exclamation-circle",
        ],
        "success" => [
            "color" => "green",
            "icon" => "heroicon-s-check-circle",
        ],
        "info" => [
            "color" => "blue",
            "icon" => "heroicon-s-information-circle",
        ],
        "warning" => [
            "color" => "yellow",
            "icon" => "heroicon-s-exclamation",
        ],
    ];

    $selected = $types[$type] ?? $types["info"];
@endphp

@if (session($key))
    <div
        class="border-{{ $selected["color"] }}-200 bg-{{ $selected["color"] }}-50 text-{{ $selected["color"] }}-800 mb-6 flex items-center gap-3 rounded-lg border p-4"
    >
        <x-dynamic-component :component="$selected['icon']" class="h-5 w-5 shrink-0" />

        <p class="font-medium">
            {{ session($key) }}
        </p>
    </div>
@endif
