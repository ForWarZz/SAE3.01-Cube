@props([
    "task",
    "title",
    "icon" => null,
])

<div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
    <button
        @click="active = (active === {{ $task }} ? null : {{ $task }})"
        class="flex w-full items-center justify-between p-5 text-left transition hover:bg-gray-50"
    >
        <span class="flex items-center gap-3 font-semibold text-gray-900">
            @if ($icon)
                <x-dynamic-component :component="$icon" class="h-5 w-5 text-blue-600" />
            @endif

            {{ $title }}
        </span>

        <x-heroicon-o-chevron-down
            class="size-5 text-gray-500 transition-transform"
            x-bind:class="active === {{ $task }} ? 'rotate-180' : ''"
        />
    </button>

    <div x-show="active === {{ $task }}" class="border-t border-gray-100 bg-gray-50 p-5">
        <div class="prose prose-sm max-w-none text-gray-600">
            {{ $slot }}
        </div>
    </div>
</div>
