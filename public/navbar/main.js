/**
 * Gestion du menu de navigation avec hover
 */

const menuItems = document.querySelectorAll('.menu-item');

menuItems.forEach(item => {
    const submenu = item.closest('li').querySelector('.submenu');

    if (submenu) {
        item.addEventListener('mouseenter', function() {
            submenu.classList.remove('hidden');
        });

        item.closest('li').addEventListener('mouseleave', function() {
            submenu.classList.add('hidden');
        });
    }
});

