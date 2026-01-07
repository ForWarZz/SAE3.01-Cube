function filterFAQ() {
    console.log('Filtering FAQ...');
    const search = document.getElementById('faqSearch').value.toLowerCase();
    const items = document.querySelectorAll('.faq-item');

    items.forEach((item) => {
        console.log('Checking item:', item);
        const title = item.querySelector('button').textContent.toLowerCase();
        const content = item.querySelector('[x-show]')?.textContent.toLowerCase() || '';

        if (title.includes(search) || content.includes(search)) {
            item.style.display = '';
        } else {
            item.style.display = search ? 'none' : '';
        }
    });

    hideEmptyCategories();
}

function hideEmptyCategories() {
    const categories = document.querySelectorAll('.faq-category');

    categories.forEach((category) => {
        const visibleItems = category.querySelectorAll('.faq-item:not([style*="display: none"])');

        category.style.display = visibleItems.length === 0 ? 'none' : '';
    });
}

window.filterFAQ = filterFAQ;
