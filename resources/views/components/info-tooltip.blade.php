@props([
    "text",
    "title" => null,
    "color" => "text-gray-500",
    "width" => "w-32",
    "size" => "size-5",
])

<div class="group relative z-50 mx-2 inline-flex items-center justify-center align-middle">
    <x-heroicon-o-information-circle class="{{ $size }} {{ $color }} cursor-help transition-colors" />

    <div
        class="{{ $width }} pointer-events-none invisible absolute bottom-full left-1/2 z-50 mb-2 -translate-x-1/2 translate-y-1 transform rounded-lg bg-gray-900 px-3 py-2 text-center text-xs text-white opacity-0 shadow-[0_4px_10px_rgba(0,0,0,0.3)] transition-all duration-200 group-hover:visible group-hover:translate-y-0 group-hover:opacity-100"
    >
        @if ($title)
            <p class="mb-1 text-[10px] font-bold tracking-wider text-blue-300 uppercase">{{ $title }}</p>
        @endif

        <p class="leading-relaxed">{{ $text }}</p>

        <div class="absolute top-full left-1/2 -mt-1 h-2 w-2 -translate-x-1/2 rotate-45 bg-gray-900"></div>
    </div>
</div>
