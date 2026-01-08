import './bootstrap';
import './navbar/main.js';
import './search/main.js';
import './cart/main.js';

import Alpine from 'alpinejs';
import L from 'leaflet';
import { startTour } from './tour/main.js';

window.Alpine = Alpine;

Alpine.start();

function isCookiePopupVisible() {
    const banner = document.getElementById('tarteaucitronAlertBig');
    if (!banner) {
        return true;
    }

    return banner.style.display !== 'none';
}

function waitForCookieConsent() {
    return new Promise((resolve) => {
        if (!isCookiePopupVisible()) {
            resolve();
            return;
        }

        const interval = setInterval(() => {
            if (!isCookiePopupVisible()) {
                console.log('Cookie popup closed');
                clearInterval(interval);
                resolve();
            }
        }, 300);

        setTimeout(() => {
            clearInterval(interval);
            resolve();
        }, 60000);
    });
}

document.addEventListener('DOMContentLoaded', async () => {
    await waitForCookieConsent();

    startTour();
});

window.L = L;

import markerIcon2x from 'leaflet/dist/images/marker-icon-2x.png';
import markerIcon from 'leaflet/dist/images/marker-icon.png';
import markerShadow from 'leaflet/dist/images/marker-shadow.png';

delete L.Icon.Default.prototype._getIconUrl;

L.Icon.Default.mergeOptions({
    iconRetinaUrl: markerIcon2x,
    iconUrl: markerIcon,
    shadowUrl: markerShadow,
});
