const searchInput = document.getElementById('search-input');

if (searchInput && window.appRoutes?.articlesSearch) {
    searchInput.addEventListener('keydown', function (event) {
        if (event.key !== 'Enter') return;

        const query = this.value.trim();
        const url = new URL(window.appRoutes.articlesSearch);

        const params = new URLSearchParams(window.location.search);
        params.forEach((value, key) => {
            url.searchParams.set(key, value);
        });

        url.searchParams.set('search', query);
        url.searchParams.delete('page');

        window.location.href = url.toString();
    });
}
