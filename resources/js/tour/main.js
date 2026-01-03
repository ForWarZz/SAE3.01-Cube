import { driver } from 'driver.js';
import 'driver.js/dist/driver.css';

export function startTour(force = false) {
    const currentRoute = document.querySelector('meta[name="current-route"]').getAttribute('content');
    let steps = [];
    let tourKey = '';

    if (currentRoute === 'home') {
        tourKey = 'tour_seen_home';
        steps = [
            {
                element: '#nav',
                popover: {
                    title: 'La barre de navigation',
                    description:
                        "Parcourez les différentes catégories d'articles (vélos, vélos électriques, accessoires), faites des recherches et accédez à votre panier ou à votre compte depuis cette barre en haut de l'écran.",
                    side: 'bottom',
                },
            },
            {
                element: '#article-categories',
                popover: {
                    title: "Les catégories d'articles",
                    description:
                        'Explorez les différentes catégories de produits disponibles sur notre site, y compris les vélos, les vélos électriques et les accessoires. Cliquez sur une catégorie pour découvrir notre sélection, passer votre souris dessus pour voir les sous-catégories et ainsi de suite.',
                    side: 'bottom',
                },
            },
            // {
            //     element: '#selected-shop-btn',
            //     popover: {
            //         title: 'Choisir un magasin',
            //         description:
            //             'Sélectionnez votre magasin préféré afin de pouvoir ',
            //         side: 'bottom',
            //     },
            // },
            {
                element: '#search-bar',
                popover: {
                    title: 'La barre de recherche',
                    description:
                        'Recherchez rapidement des articles spécifiques en utilisant des mots-clés, des noms de produits ou des références. Le moteur de recherche vous aidera à trouver ce que vous cherchez en un instant.',
                    side: 'bottom',
                },
            },
            {
                element: '#user-actions',
                popover: {
                    title: 'Votre compte',
                    description: 'Accédez à votre tableau de bord personnel pour gérer vos commandes, vos informations et vos préférences.',
                    side: 'bottom',
                },
            },
            {
                element: '#view-cart-btn',
                popover: {
                    title: 'Le panier',
                    description:
                        "Accédez à votre panier pour voir les articles que vous avez ajoutés, modifier les quantités ou procéder au paiement lorsque vous êtes prêt. Un petit compteur vous indique le nombre d'articles dans votre panier (s'il y en a).",
                    side: 'bottom',
                },
            },
        ];
    } else if (currentRoute === 'articles.by-category' || currentRoute === 'articles.by-model') {
        tourKey = 'tour_seen_catalog';
        steps = [
            {
                popover: {
                    title: 'Le catalogue',
                    description: 'Les vélos à la vente sont ici ! Parcourez et trouvez votre bonheur.',
                    side: 'center',
                    align: 'center',
                },
            },
            {
                element: '#header-infos',
                popover: {
                    title: 'Infos',
                    description:
                        "Voyez rapidement combien d'articles sont affiché, ainsi que la catégorie d'articles dans laquelle vous vous trouvez.",
                    side: 'bottom',
                },
            },
            {
                element: '#filters-bar',
                popover: {
                    title: 'Filtres',
                    description: 'La taille, la couleur, le type... Filtrez pour trouver la perle rare !',
                    side: 'right',
                },
            },
            {
                element: '#sort-select',
                popover: {
                    title: 'Trier',
                    description: 'Triez par prix croissant pour les bonnes affaires.',
                    side: 'left',
                },
            },
            {
                element: '.article-card:first-child',
                popover: {
                    title: 'Les produits',
                    description: 'Cliquez sur une carte pour voir les détails.',
                    side: 'top',
                },
            },
        ];
    } else if (currentRoute === 'articles.show-reference') {
        tourKey = 'tour-seen-article';
        steps = [
            {
                popover: {
                    title: 'La page produit',
                    description: "Voici la page dédiée à l'article que vous avez sélectionné.",
                    side: 'center',
                    align: 'center',
                },
            },
            {
                element: '#breadcrumb',
                popover: {
                    title: "Fil d'Ariane",
                    description: "Retrouvez votre chemin facilement grâce au fil d'Ariane en haut de la page.",
                    side: 'bottom',
                },
            },
            {
                element: '#article-image-slider',
                popover: {
                    title: "Galerie d'images",
                    description:
                        "Découvrez l'article sous tous les angles grâce à notre galerie d'images interactive. Vous pouvez zoomer et naviguer entre les différentes photos pour une vue détaillée.",
                    side: 'right',
                },
            },
            {
                element: '#article-badge-box',
                popover: {
                    title: 'Les badges',
                    description: 'Repérez rapidement le millésime ainsi que le badge de nouveauté.',
                    side: 'left',
                },
            },
            {
                element: '#article-price-box',
                popover: {
                    title: 'Le prix',
                    description: 'Le prix actuel, ainsi que les promotions éventuelles, sont affichés ici.',
                    side: 'left',
                },
            },
            {
                element: '#article-configurator',
                popover: {
                    title: 'Configuration',
                    description: 'Cadre, batterie, couleur… Configurez le vélo selon vos préférences avant de choisir votre taille.',
                    side: 'left',
                },
            },
            {
                element: '#size-guide-link',
                popover: {
                    title: 'Un doute sur la taille ?',
                    description: 'Cliquez ici pour accéder à notre calculateur automatique de taille de cadre.',
                    side: 'bottom',
                },
            },
            {
                element: '#size-selection-area',
                popover: {
                    title: 'Choix de la taille',
                    description: 'Sélectionnez votre taille ici. Les tailles grisées ne sont plus disponibles.',
                    side: 'top',
                },
            },
            {
                element: '#availability-summary',
                popover: {
                    title: 'État du stock',
                    description: "Vérifiez en un coup d'œil si l'article est disponible en ligne ou en stock magasin.",
                    side: 'bottom',
                },
            },
            {
                element: '#add-to-cart-btn',
                popover: {
                    title: 'Commander',
                    description: "Une fois configuré, cliquez ici pour ajouter l'article à votre panier.",
                    side: 'top',
                },
            },
            {
                element: '#check-shop-stock-btn',
                popover: {
                    title: 'Stock par magasin',
                    description:
                        'Vous voulez le voir en vrai ? Cliquez ici pour vérifier la disponibilité dans les boutiques proches de chez vous.',
                    side: 'top',
                },
            },
            {
                element: '#specs',
                popover: {
                    title: 'Fiche Technique',
                    description: 'Dérailleur, freins, poids... Tous les détails techniques sont ici.',
                    side: 'top',
                },
            },
            {
                element: '#geometry',
                popover: {
                    title: 'Géométrie',
                    description: 'Pour les experts : les dimensions exactes du cadre selon la taille.',
                    side: 'top',
                },
            },
            {
                element: '#bike-calculator-container',
                popover: {
                    title: 'Calculateur de taille',
                    description: 'Notre outil intelligent pour trouver votre taille idéale en 3 clics.',
                    side: 'top',
                },
            },
            {
                element: '#compatible-accessories',
                popover: {
                    title: 'Accessoires compatibles',
                    description: 'Porte-bidon, béquille... Des accessoires certifiés compatibles avec CE vélo.',
                    side: 'top',
                },
            },
            {
                element: '#similar-articles',
                popover: {
                    title: 'Article similaires',
                    description: 'D’autres vélos qui pourraient vous plaire.',
                    side: 'top',
                },
            },
        ];
    } else if (currentRoute === 'dashboard.index') {
        tourKey = 'tour_seen_dashboard';
        steps = [
            {
                popover: {
                    title: 'Votre tableau de bord',
                    description:
                        'Bienvenue dans votre espace personnel. Ici, vous pouvez gérer vos informations, consulter vos commandes et bien plus encore.',
                    side: 'center',
                    align: 'center',
                },
            },
            {
                element: '#addresses-nav-link',
                popover: {
                    title: 'Vos adresses',
                    description: 'Accédez à la liste de vos adresses enregistrées et gérez-les facilement.',
                    side: 'top',
                },
            },
            {
                element: '#orders-nav-link',
                popover: {
                    title: 'Vos commandes',
                    description: 'Consultez l’historique de vos commandes, suivez leur statut et retrouvez les détails de chaque achat.',
                    side: 'top',
                },
            },
            {
                element: '#personal-info-nav-link',
                popover: {
                    title: 'Vos informations personnelles',
                    description:
                        'Mettez à jour vos informations personnelles telles que votre nom, votre adresse e-mail et votre mot de passe. Gérez vos données ainsi que la sécurité de votre compte en toute simplicité.',
                    side: 'top',
                },
            },
            {
                element: '#logout-btn',
                popover: {
                    title: 'Se déconnecter',
                    description: 'Vous pouvez facilement vous déconnecter de votre compte en cliquant ici.',
                    side: 'top',
                },
            },
        ];
    } else if (currentRoute === 'cart.index') {
        tourKey = 'tour_seen_cart';
        steps = [
            {
                popover: {
                    title: 'Votre panier',
                    description: 'Dernière étape de vérification avant de valider votre commande.',
                    side: 'center',
                    align: 'center',
                },
            },
            {
                element: '#cart-items-list',
                popover: {
                    title: 'Vos articles',
                    description: "Vérifiez bien les quantités et les tailles. Vous pouvez supprimer un article ici si vous changez d'avis.",
                    side: 'top',
                },
            },
            {
                element: '#discount-code-section',
                popover: {
                    title: 'Code promo',
                    description: 'Vous avez un code de réduction ? Saisissez-le ici et cliquez sur "Appliquer" pour voir le prix baisser.',
                    side: 'left',
                },
            },
            {
                element: '#order-summary',
                popover: {
                    title: 'Le récapitulatif',
                    description: 'Retrouvez ici le montant total TTC de votre commande, incluant la TVA et les frais de livraison estimés.',
                    side: 'left',
                },
            },
            {
                element: '#checkout-actions',
                popover: {
                    title: 'Tout est bon ?',
                    description:
                        'Si le total vous convient, cliquez ici pour passer à la séléction du mode de livraison pour finaliser votre commande.',
                    side: 'left',
                },
            },
        ];
    } else if (currentRoute === 'cart.checkout') {
        tourKey = 'tour_seen_checkout';
        steps = [
            {
                popover: {
                    title: 'Finalisation de la commande',
                    description: 'On y est presque ! Plus que quelques clics pour valider votre commande.',
                    side: 'center',
                    align: 'center',
                },
            },
            {
                element: '#billing-section',
                popover: {
                    title: '1. Facturation',
                    description: "Sélectionnez l'adresse qui apparaîtra sur la facture (votre domicile généralement).",
                    side: 'top',
                },
            },
            {
                element: '#delivery-section',
                popover: {
                    title: '2. Livraison',
                    description:
                        'Cochez la case "Utiliser l\'adresse de facturation" pour gagner du temps, ou choisissez une adresse différente, si vous faites une livraison à domicile.',
                    side: 'top',
                },
            },
            {
                element: '#shipping-methods',
                popover: {
                    title: '3. Transporteur',
                    description: 'Choisissez le mode de livraison qui vous convient. Le prix total se mettra à jour automatiquement.',
                    side: 'top',
                },
            },
            {
                element: '#submit-order-btn',
                popover: {
                    title: '4. Paiement',
                    description:
                        'Une fois les adresses validées, cliquez ici pour procéder au paiement sécurisé. Le bouton passera au vert quand tout sera rempli ! Vous serez redirigé vers notre partenaire de paiement.',
                    side: 'left',
                },
            },
        ];
    }

    const filterSteps = (stepsToFilter) => {
        return stepsToFilter.filter((step) => {
            if (!step.element) return true;
            return document.querySelector(step.element);
        });
    };

    setTimeout(
        () => {
            const availableSteps = filterSteps(steps);

            if (availableSteps.length > 0 && (!localStorage.getItem(tourKey) || force)) {
                const driverObj = driver({
                    showProgress: true,
                    animate: true,
                    nextBtnText: 'Suivant →',
                    prevBtnText: '← Retour',
                    doneBtnText: "C'est compris !",
                    steps: availableSteps,
                    onDestroyed: () => {
                        localStorage.setItem(tourKey, 'true');
                    },
                });

                driverObj.drive();
            }
        },
        force ? 0 : 1000,
    );
}

window.rebootTour = () => {
    window.scrollTo(0, 0);

    startTour(true);
};
