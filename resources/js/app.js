import './bootstrap';
import './navbar/main.js';
import './search/main.js';
import './cart/main.js';

import Alpine from 'alpinejs';
import L from 'leaflet';
import { startTour } from './tour/main.js';

window.Alpine = Alpine;

Alpine.start();

function hasUserAcceptedOrRefusedCookies() {
    const cookies = document.cookie.split(';');
    return cookies.some((cookie) => cookie.trim().startsWith('tarteaucitron='));
}

function waitForCookieConsent() {
    return new Promise((resolve) => {
        if (hasUserAcceptedOrRefusedCookies()) {
            resolve();
            return;
        }

        const checkInterval = setInterval(() => {
            if (hasUserAcceptedOrRefusedCookies()) {
                clearInterval(checkInterval);
                resolve();
            }
        }, 500);

        setTimeout(() => {
            clearInterval(checkInterval);
            resolve();
        }, 60000);
    });
}

document.addEventListener('DOMContentLoaded', async () => {
    await waitForCookieConsent();

    setTimeout(() => {
        startTour();
    }, 500);
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
