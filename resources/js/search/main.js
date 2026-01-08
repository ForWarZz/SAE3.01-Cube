const searchInput = document.getElementById('search-input');

if (searchInput) {
    searchInput.addEventListener('keypress', function (event) {
        if (event.key === 'Enter') {
            const query = this.value.trim();
            if (!query) return;

            const currentUrl = new URL(window.location.href);

            const basePath = window.location.pathname.split('/articles')[0] || '';
            const searchPath = basePath + '/articles/search';
            const newUrl = new URL(searchPath, window.location.origin);

            currentUrl.searchParams.forEach((value, key) => {
                newUrl.searchParams.set(key, value);
            });

            newUrl.searchParams.set('search', query);
            newUrl.searchParams.delete('page');

            window.location.href = newUrl.toString();
        }
    });
}
