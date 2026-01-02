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

    // setTimeout(() => {
    //     const botmanRoot = document.getElementById('botmanWidgetRoot');
    //
    //     if (botmanRoot) {
    //         // 1. CSS INITIAL : On force la taille à celle du bouton uniquement (60px)
    //         // Comme ça, le reste de l'écran est libéré
    //         botmanRoot.style.width = '60px';
    //         botmanRoot.style.height = '60px';
    //         botmanRoot.style.overflow = 'hidden'; // Important pour couper ce qui dépasse
    //         botmanRoot.style.transition = 'width 0.3s ease, height 0.3s ease'; // Animation fluide
    //
    //         // 2. OUVERTURE : Quand on clique sur le widget (le bouton), on l'agrandit
    //         botmanRoot.addEventListener('click', () => {
    //             botmanRoot.style.width = '400px'; // Largeur configurée dans ton script
    //             botmanRoot.style.height = '600px'; // Hauteur configurée
    //         });
    //
    //         // 3. FERMETURE : On écoute le signal de fermeture venant de l'iframe Botman
    //         window.addEventListener('message', (event) => {
    //             // Botman envoie souvent ce message quand on clique sur la croix
    //             if (event.data === 'botmanChatWidgetClose') {
    //                 botmanRoot.style.width = '60px';
    //                 botmanRoot.style.height = '60px';
    //             }
    //         });
    //     }
    // }, 1500);
});
