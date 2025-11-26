<nav id="nav" class="bg-white border-b border-gray-200 shadow-sm">
    <div class="max-w-7xl mx-auto px-6 py-4">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-bold text-gray-900">Cube France</h1>

            <div class="relative w-64">
                <input
                    type="text"
                    id="search-input"
                    placeholder="Rechercher un article..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
            </div>
        </div>

        <ul class="flex items-center space-x-0">
            @foreach ($categories as $category)
                <x-category-item :category="$category" :n="0"/>
            @endforeach
        </ul>
    </div>
</nav>
