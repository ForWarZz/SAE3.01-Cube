@if (! empty($breadcrumbs))
    <nav class="mb-8 flex items-center space-x-2 text-sm text-gray-600">
        @foreach ($breadcrumbs as $crumb)
            @if ($crumb["url"])
                <a href="{{ $crumb["url"] }}" class="hover:text-gray-900">{{ $crumb["label"] }}</a>
            @else
                <span class="text-gray-900">{{ $crumb["label"] }}</span>
            @endif
            @if (! $loop->last)
                <span>/</span>
            @endif
        @endforeach
    </nav>
@endif
