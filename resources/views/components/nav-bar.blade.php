<nav id="nav" class="bg-white border-b border-gray-200 shadow-sm">
    <div class="max-w-7xl mx-auto px-6 py-4">
        <div class="flex items-center justify-between mb-4">
            <a href="{{ route('home') }}" class="text-2xl font-bold text-gray-900 hover:opacity-80 transition-opacity">
                Cube France
            </a>

            <div class="flex items-center space-x-4">
                
                <!-- Shop Button and selected -->

                <button type="button" id="store-button" x-data x-on:click="$dispatch('open-shop-modal', { showAvailability: false })" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-medium text-sm text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span id="store-button-text">@if(session('selected_shop')){{ session('selected_shop')->nom_magasin }}@else Choisir un magasin @endif</span>
                </button>

                <!-- Search bar -->

                <div class="relative w-64">
                    <input type="text" id="search-input" placeholder="Rechercher un article..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                @if(session('client'))
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('dashboard.index') }}" class="hover:opacity-80 transition-opacity" title="Tableau de bord">
                            <img src="{{ asset('storage/cyclist.svg') }}" alt="Dashboard" class="w-5 h-5">
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-sm text-gray-600 hover:text-gray-900 transition-colors">Déconnexion</button>
                        </form>
                    </div>
                @else
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