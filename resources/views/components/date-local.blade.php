@props([
    "date",
    "type" => "datetime",
])

@php
    if ($type === "date") {
        $phpFormat = "d/m/Y";
        $jsOptions = [
            "year" => "numeric",
            "month" => "2-digit",
            "day" => "2-digit",
        ];
    } else {
        $phpFormat = "d/m/Y H:i";
        $jsOptions = [
            "year" => "numeric",
            "month" => "2-digit",
            "day" => "2-digit",
            "hour" => "2-digit",
            "minute" => "2-digit",
        ];
    }
@endphp

<span
    x-data
    x-text="
        new Date({{ $date->timestamp * 1000 }}).toLocaleString(
            undefined,
            {{ json_encode($jsOptions) }},
        )
    "
    {{ $attributes }}
>
    {{ $date->format($phpFormat) }}
</span>
