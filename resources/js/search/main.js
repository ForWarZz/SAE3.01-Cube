const searchInput = document.getElementById('search-input');

searchInput.addEventListener('keypress', function(event) {
    if (event.key === 'Enter') {
        const query = this.value.trim();

        if (query) {
            window.location.href = `/articles/search?search=${encodeURIComponent(query)}`;
        }
    }
});
