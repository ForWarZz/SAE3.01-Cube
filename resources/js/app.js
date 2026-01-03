import './bootstrap';
import './navbar/main.js';
import './search/main.js';
import './cart/main.js';

import Alpine from 'alpinejs';
import { startTour } from './tour/main.js';

window.Alpine = Alpine;

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    startTour();
});
