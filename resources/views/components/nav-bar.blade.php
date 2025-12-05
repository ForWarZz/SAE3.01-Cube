<nav id="nav" class="sticky top-0 z-50 w-full border-b border-gray-200 bg-white shadow-sm">
    <div class="mx-auto max-w-screen-2xl px-8">
        <div class="flex h-16 items-center justify-between">
            <div class="flex flex-shrink-0 items-center">
                <a href="{{ route("home") }}" class="text-2xl font-bold tracking-tight text-gray-900 uppercase transition">Cube France</a>
            </div>

            <ul class="hidden items-center space-x-8 text-sm font-medium text-gray-700 lg:flex">
                @foreach ($categories as $category)
                    <x-category-item :category="$category" :n="0" />
                @endforeach
            </ul>

            <div class="flex items-center gap-4">
                <div class="relative">
                    <input
                        type="text"
                        id="search-input"
                        placeholder="Rechercher..."
                        value="{{ request("search") }}"
                        class="w-48 rounded-full border border-gray-300 bg-gray-50 py-1.5 pr-10 pl-4 text-sm transition-all focus:w-64 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-200 focus:outline-none"
                    />
                    <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center">
                        <x-bi-search class="size-4 text-gray-400" />
                    </span>
                </div>

                <div class="h-6 w-px bg-gray-200"></div>

                @if (! auth()->guest())
                    <!-- Logged in: Show dashboard icon and logout -->
                    <div class="flex items-center space-x-3">
                        <a href="{{ route("dashboard.index") }}" class="transition-opacity hover:opacity-80" title="Tableau de bord">
                            <img src="{{ asset("storage/cyclist.svg") }}" alt="Dashboard" class="size-8" />
                        </a>
                        <form method="POST" action="{{ route("logout") }}" class="inline">
                            @csrf
                            <button type="submit" class="text-sm text-gray-600 transition-colors hover:text-gray-900">Déconnexion</button>
                        </form>
                    </div>
                @else
                    <!-- Not logged in: Show login icon -->
                    <a href="{{ route("login") }}" class="transition-opacity hover:opacity-80" title="Se connecter">
                        <img src="{{ asset("storage/cyclist.svg") }}" alt="Login" class="size-8" />
                    </a>
                @endif

                <a href="{{ route("cart.index") }}" class="group relative flex items-center p-2" title="Voir le panier">
                    <x-bi-cart class="size-6 text-gray-700 transition group-hover:text-blue-600" />

                    @if ($cartItemCount > 0)
                        <span
                            class="absolute top-0 right-0 flex size-4 items-center justify-center rounded-full bg-blue-600 text-[10px] font-bold text-white shadow-sm"
                        >
                            {{ $cartItemCount }}
                        </span>
                    @endif
                </a>
            </div>
        </div>
    </div>
</nav>
