const menuItems = document.querySelectorAll('.ul-navbar li');

menuItems.forEach(item => {
    item.addEventListener('mouseenter', function() {
        const subMenu = this.querySelector('ul');
        if (subMenu) {
            subMenu.style.display = 'block';
        }
    });
    item.addEventListener('mouseleave', function() {
        const subMenu = this.querySelector('ul');
        if (subMenu) {
            subMenu.style.display = 'none';
        }
    });
});