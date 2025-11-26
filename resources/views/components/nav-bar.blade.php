<nav id="nav" class="bg-white border-b border-gray-200 shadow-sm">
    <div class="max-w-7xl mx-auto px-6">
        <ul class="flex items-center space-x-0">
            @foreach ($categories as $category)
                <x-category-item :category="$category" :n="0"/>
            @endforeach
        </ul>
    </div>
</nav>
