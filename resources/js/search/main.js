const searchInput = document.getElementById('search-input');

searchInput.addEventListener('keypress', function (event) {
    if (event.key === 'Enter') {
        const query = this.value.trim();
        if (!query) return;

        const currentUrl = new URL(window.location.href);
        const newUrl = new URL('/articles/search', window.location.origin);

        currentUrl.searchParams.forEach((value, key) => {
            newUrl.searchParams.set(key, value);
        });

        newUrl.searchParams.set('search', query);
        newUrl.searchParams.delete('page');

        window.location.href = newUrl.toString();
    }
});
