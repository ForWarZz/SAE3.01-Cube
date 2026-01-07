@if (! empty($breadcrumbs))
    <nav id="breadcrumb" class="mb-8 flex items-center space-x-2 text-sm text-gray-600" aria-label="Fil d'Ariane">
        <x-heroicon-o-home class="mr-1 size-4 text-gray-400" />
        @foreach ($breadcrumbs as $crumb)
            @if ($crumb->url)
                <a
                    href="{{ $crumb->url }}"
                    class="transition-colors hover:text-blue-600 hover:underline"
                    @if ($loop->first) title="Retour à l'accueil" @endif
                >
                    {{ $crumb->label }}
                </a>
            @else
                <span class="font-medium text-gray-900">{{ $crumb->label }}</span>
            @endif
            @if (! $loop->last)
                <x-heroicon-o-chevron-right class="size-3 text-gray-400" />
            @endif
        @endforeach
    </nav>
@endif
