import './bootstrap';
import './navbar/main.js';
import './search/main.js';
import './cart/main.js';

import { driver } from 'driver.js';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
window.driver = driver;

Alpine.start();
