<nav id="nav" class="bg-white border-b border-gray-200 shadow-sm">
    <div class="max-w-7xl mx-auto px-6 py-4">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-bold text-gray-900">Cube France</h1>

            <div class="flex items-center space-x-4">
                <div class="relative w-64">
                    <input
                        type="text"
                        id="search-input"
                        placeholder="Rechercher un article..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                </div>

                @if(session('client'))
                    <!-- Logged in: Show dashboard icon and logout -->
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('dashboard.index') }}" class="hover:opacity-80 transition-opacity" title="Tableau de bord">
                            <img src="{{ asset('storage/cyclist.svg') }}" alt="Dashboard" class="w-5 h-5">
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-sm text-gray-600 hover:text-gray-900 transition-colors">
                                Déconnexion
                            </button>
                        </form>
                    </div>
                @else
                    <!-- Not logged in: Show login icon -->
                    <a href="{{ route('login') }}" class="hover:opacity-80 transition-opacity" title="Se connecter">
                        <img src="{{ asset('storage/cyclist.svg') }}" alt="Login" class="w-5 h-5">
                    </a>
                @endif
            </div>
        </div>

        <ul class="flex items-center space-x-0">
            @foreach ($categories as $category)
                <x-category-item :category="$category" :n="0"/>
            @endforeach
        </ul>
    </div>
</nav>
