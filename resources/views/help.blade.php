<x-app-layout>
    <div class="bg-gray-50 py-12">
        <div class="mx-auto max-w-4xl px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">Centre d'aide</h2>
                <p class="mt-4 text-lg leading-8 text-gray-600">Toutes les réponses à vos questions sur l'utilisation du site Cube Bikes</p>
            </div>

            <div class="mt-12 space-y-10" x-data="{ active: null }">
                <div>
                    <h3 class="mb-4 text-sm font-semibold tracking-wide text-gray-500 uppercase">Navigation</h3>
                    <div class="space-y-2">
                        <x-faq-item task="1" title="Comment chercher un vélo ?" icon="heroicon-o-magnifying-glass">
                            <p class="mb-2">Deux méthodes pour trouver votre vélo :</p>
                            <p class="mb-2">
                                <strong>Barre de recherche :</strong>
                                Cliquez dans le champ de recherche situé en haut de page, saisissez le nom du modèle ou type de vélo
                                recherché.
                            </p>
                            <p>
                                <strong>Menu de navigation :</strong>
                                Survolez le menu principal en haut de page pour afficher les catégories (VTT, Route, Ville, Électrique...),
                                puis cliquez sur la catégorie souhaitée.
                            </p>

                            <div class="mt-4">
                                <video autoplay loop muted playsinline class="w-full max-w-md rounded-lg border shadow-sm">
                                    <source src="{{ asset("resources/help/videos/navigation.mp4") }}" type="video/mp4" />
                                    Votre navigateur ne supporte pas la vidéo.
                                </video>
                                <p class="mt-1 text-xs text-gray-500">Exemple de navigation et de recherche</p>
                            </div>
                        </x-faq-item>

                        <x-faq-item task="2" title="Comment filtrer les résultats ?" icon="heroicon-o-funnel">
                            <p class="mb-2">
                                Dans la page catalogue (vélos ou accessoires, peu importe), localisez le panneau latéral gauche contenant
                                tous les filtres disponibles :
                            </p>
                            <ul class="ml-4 space-y-1 text-sm">
                                <li>
                                    <x-heroicon-o-check-circle class="mr-1 inline h-4 w-4 text-green-600" />
                                    Cochez les critères souhaités (catégorie, prix, taille, couleur...)
                                </li>
                                <li>
                                    <x-heroicon-o-check-circle class="mr-1 inline h-4 w-4 text-green-600" />
                                    Les résultats se mettent à jour automatiquement
                                </li>
                                <li>
                                    <x-heroicon-o-check-circle class="mr-1 inline h-4 w-4 text-green-600" />
                                    Utilisez le bouton "Réinitialiser" pour effacer tous les filtres
                                </li>
                            </ul>

                            <div class="mt-4">
                                <video autoplay loop muted playsinline class="w-full max-w-md rounded-lg border shadow-sm">
                                    <source src="{{ asset("resources/help/videos/filtres.mp4") }}" type="video/mp4" />
                                    Votre navigateur ne supporte pas la vidéo.
                                </video>
                                <p class="mt-1 text-xs text-gray-500">Exemple de filtres d'articles</p>
                            </div>
                        </x-faq-item>

                        <x-faq-item task="3" title="Où trouver mon panier ?" icon="heroicon-o-shopping-cart">
                            <p>
                                Cliquez sur l'icône panier
                                <x-heroicon-o-shopping-cart class="mx-1 inline h-4 w-4" />
                                située en haut à droite de la barre de navigation. Un badge numérique indique le nombre d'articles ajoutés.
                            </p>
                        </x-faq-item>

                        <x-faq-item task="4" title="Comment accéder à mon compte ?" icon="heroicon-o-user-circle">
                            <p class="mb-2">
                                Cliquez sur l'icône cycliste
                                <img src="{{ asset("resources/cyclist.svg") }}" alt="Login" class="inline size-7" />
                                en haut à droite de la barre de navigation pour accéder à votre tableau de bord :
                            </p>
                            <ul class="ml-4 space-y-1 text-sm">
                                <li>
                                    <x-heroicon-o-map-pin class="mr-1 inline h-4 w-4 text-blue-600" />
                                    Mes adresses
                                </li>
                                <li>
                                    <x-heroicon-o-shopping-bag class="mr-1 inline h-4 w-4 text-blue-600" />
                                    Mes commandes
                                </li>
                                <li>
                                    <x-heroicon-o-user class="mr-1 inline h-4 w-4 text-blue-600" />
                                    Mon profil
                                </li>
                            </ul>
                        </x-faq-item>

                        <x-faq-item task="5" title="Comment changer le magasin sélectionné ?" icon="heroicon-o-building-storefront">
                            <p class="mb-2">
                                Cliquez sur le bouton violet du magasin actuellement sélectionné, visible dans la barre de navigation en
                                haut de page.
                            </p>
                            <p>
                                Une fenêtre s'ouvre avec la liste complète des magasins. Utilisez la recherche par ville ou code postal,
                                puis cliquez sur le magasin de votre choix pour le sélectionner.
                            </p>

                            <div class="mt-4">
                                <video autoplay loop muted playsinline class="w-full max-w-md rounded-lg border shadow-sm">
                                    <source src="{{ asset("resources/help/videos/changer-magasin.mp4") }}" type="video/mp4" />
                                    Votre navigateur ne supporte pas la vidéo.
                                </video>
                                <p class="mt-1 text-xs text-gray-500">Exemple de selection d'un magasin via la vue carte</p>
                            </div>
                        </x-faq-item>
                    </div>
                </div>

                <div>
                    <h3 class="mb-4 text-sm font-semibold tracking-wide text-gray-500 uppercase">Produits</h3>
                    <div class="space-y-2">
                        <x-faq-item task="6" title="Comment choisir ma taille de vélo ?" icon="heroicon-o-adjustments-horizontal">
                            <p class="mb-2">
                                Sur la fiche produit d'un vélo, localisez la section "Calculer ma taille idéale" située sous les options de
                                taille.
                            </p>

                            <ol class="ml-4 space-y-1 text-sm">
                                <li>1. Mesurez votre taille : debout, dos contre un mur, pieds nus</li>
                                <li>
                                    2. Mesurez votre entrejambe : du sol jusqu'au haut de l'entrejambe (utilisez un livre placé entre vos
                                    jambes pour plus de précision)
                                </li>
                                <li>3. Saisissez ces deux valeurs dans les champs du formulaire</li>
                                <li>4. Cliquez sur le bouton "Calculer"</li>
                                <li>5. Le système vous recommande la ou les tailles adaptées</li>
                            </ol>

                            <div class="mt-4">
                                <video autoplay loop muted playsinline class="w-full max-w-md rounded-lg border shadow-sm">
                                    <source src="{{ asset("resources/help/videos/choisir-taille.mp4") }}" type="video/mp4" />
                                    Votre navigateur ne supporte pas la vidéo.
                                </video>
                                <p class="mt-1 text-xs text-gray-500">Exemple d'utilisation du calculateur de taille</p>
                            </div>
                        </x-faq-item>

                        <x-faq-item task="7" title="Comment utiliser la vue 360° ?" icon="heroicon-o-arrow-path">
                            <p class="mb-2">Sur la fiche produit d'un vélo équipé de cette fonctionnalité :</p>
                            <ol class="ml-4 space-y-1 text-sm">
                                <li>1. Regardez la galerie de miniatures située sous l'image principale du produit</li>
                                <li>
                                    2. Repérez la miniature affichant une icône 360°
                                    <x-heroicon-o-arrow-path class="mx-1 inline h-4 w-4" />
                                </li>
                                <li>3. Cliquez sur cette miniature pour ouvrir la vue interactive en plein écran</li>
                                <li>
                                    4. Glissez horizontalement avec votre souris ou votre doigt pour faire tourner le vélo et visualiser
                                    tous les angles
                                </li>
                            </ol>
                            <p class="mt-2 text-sm text-amber-700">
                                Cette fonctionnalité n'est disponible que sur certains modèles de vélos.
                            </p>

                            <div class="mt-4">
                                <video autoplay loop muted playsinline class="w-full max-w-md rounded-lg border shadow-sm">
                                    <source src="{{ asset("resources/help/videos/vue-360.mp4") }}" type="video/mp4" />
                                    Votre navigateur ne supporte pas la vidéo.
                                </video>
                                <p class="mt-1 text-xs text-gray-500">Exemple d'utilisation de la vue 360°</p>
                            </div>
                        </x-faq-item>

                        <x-faq-item task="8" title="Que sont les variantes d'un vélo ?" icon="heroicon-o-swatch">
                            <p class="mb-2">Les variantes permettent de personnaliser votre vélo selon vos préférences :</p>
                            <ul class="ml-4 space-y-1 text-sm">
                                <li>
                                    <strong>Couleur :</strong>
                                    Cliquez sur les pastilles de couleur disponibles
                                </li>
                                <li>
                                    <strong>Taille :</strong>
                                    Sélectionnez dans le menu déroulant (XS, S, M, L, XL, XXL)
                                </li>
                                <li>
                                    <strong>Type de cadre :</strong>
                                    Classique, Trapèze (enjambement facilité), Col de cygne
                                </li>
                                <li>
                                    <strong>Batterie (e-bikes) :</strong>
                                    Choisissez la capacité (500Wh, 625Wh, 750Wh...)
                                </li>
                            </ul>
                            <p class="mt-2 text-sm">Les variantes indisponibles apparaissent grisées et barrées.</p>

                            <div class="mt-4">
                                <video autoplay loop muted playsinline class="w-full max-w-md rounded-lg border shadow-sm">
                                    <source src="{{ asset("resources/help/videos/changer-variante.mp4") }}" type="video/mp4" />
                                    Votre navigateur ne supporte pas la vidéo.
                                </video>
                                <p class="mt-1 text-xs text-gray-500">Exemple de choix de variante sur un vélo électrique</p>
                            </div>
                        </x-faq-item>

                        <x-faq-item task="9" title="Comment voir les disponibilités en magasin ?" icon="heroicon-o-map-pin">
                            <p class="mb-2">Sur la fiche produit, cliquez sur le bouton "Voir les disponibilités".</p>
                            <p>
                                Un panneau latéral s'ouvre affichant la liste de tous les magasins avec leur statut de disponibilité
                                (Disponible, Commandable, Indisponible). Utilisez le champ de recherche pour filtrer par ville ou code
                                postal. La distance depuis votre position est affichée pour chaque magasin.
                            </p>

                            <div class="mt-4">
                                <video autoplay loop muted playsinline class="w-full max-w-md rounded-lg border shadow-sm">
                                    <source src="{{ asset("resources/help/videos/voir-disponibilites.mp4") }}" type="video/mp4" />
                                    Votre navigateur ne supporte pas la vidéo.
                                </video>
                                <p class="mt-1 text-xs text-gray-500">Exemple de visualisation des disponibilités pour un vélo</p>
                            </div>
                        </x-faq-item>
                    </div>
                </div>

                <div>
                    <h3 class="mb-4 text-sm font-semibold tracking-wide text-gray-500 uppercase">Commande</h3>
                    <div class="space-y-2">
                        <x-faq-item task="11" title="Comment passer une commande ?" icon="heroicon-o-shopping-bag">
                            <p class="mb-2">Processus de commande en 5 étapes :</p>
                            <ol class="ml-4 space-y-1 text-sm">
                                <li>1. Ajoutez vos articles au panier</li>
                                <li>
                                    2. Cliquez sur l'icône panier
                                    <x-heroicon-o-shopping-cart class="mx-1 inline h-4 w-4" />
                                    puis sur "Valider le panier"
                                </li>
                                <li>3. Connectez-vous à votre compte ou créez-en un nouveau</li>
                                <li>4. Renseignez vos adresses de livraison et facturation, puis choisissez le mode de livraison</li>
                                <li>5. Procédez au paiement via Stripe (CB, PayPal, Apple Pay, Google Pay)</li>
                            </ol>
                        </x-faq-item>

                        <x-faq-item task="12" title="Quels sont les modes de livraison ?" icon="heroicon-o-truck">
                            <div class="space-y-3">
                                <div class="rounded-lg border border-green-200 bg-green-50 p-3">
                                    <p class="font-semibold text-green-900">
                                        <x-heroicon-o-building-storefront class="mr-1 inline h-4 w-4" />
                                        Click & Collect (OBLIGATOIRE pour les vélos)
                                    </p>
                                    <p class="mt-1 text-sm text-green-700">
                                        Récupérez votre vélo monté et réglé par un professionnel dans un magasin Cube. Gratuit. Délai :
                                        24-48h. Notification SMS/Email à réception.
                                    </p>
                                </div>
                                <div class="rounded-lg border border-blue-200 bg-blue-50 p-3">
                                    <p class="font-semibold text-blue-900">
                                        <x-heroicon-o-home class="mr-1 inline h-4 w-4" />
                                        Livraison express à domicile (Accessoires)
                                    </p>
                                    <p class="mt-1 text-sm text-blue-700">
                                        Livraison le lendemain pour accessoires. Frais appliqués automatiquement.
                                    </p>
                                </div>
                                <div class="rounded-lg border border-purple-200 bg-purple-50 p-3">
                                    <p class="font-semibold text-purple-900">
                                        <x-heroicon-o-map-pin class="mr-1 inline h-4 w-4" />
                                        Point relais (Accessoires uniquement)
                                    </p>
                                    <p class="mt-1 text-sm text-purple-700">Délai : 3-5 jours ouvrés. Frais appliqués automatiquement.</p>
                                </div>
                            </div>
                            <p class="mt-3 text-sm font-semibold">Livraison offerte en magasin si commande supérieure à 50 euros</p>
                        </x-faq-item>

                        <x-faq-item task="13" title="Comment utiliser un code promo ?" icon="heroicon-o-receipt-percent">
                            <ol class="ml-4 space-y-1 text-sm">
                                <li>
                                    1. Accédez à votre panier via l'icône
                                    <x-heroicon-o-shopping-cart class="mx-1 inline h-4 w-4" />
                                    en haut à droite
                                </li>
                                <li>2. Localisez le champ "Code promo" dans le récapitulatif du panier</li>
                                <li>3. Saisissez votre code (attention : sensible aux majuscules et minuscules)</li>
                                <li>4. Cliquez sur le bouton "Appliquer"</li>
                                <li>5. La réduction s'applique automatiquement au montant total</li>
                            </ol>
                            <p class="mt-2 text-sm text-amber-700">
                                Un seul code promo par commande. Impossible de cumuler plusieurs codes.
                            </p>

                            <div class="mt-4">
                                <video autoplay loop muted playsinline class="w-full max-w-md rounded-lg border shadow-sm">
                                    <source src="{{ asset("resources/help/videos/code-promo.mp4") }}" type="video/mp4" />
                                    Votre navigateur ne supporte pas la vidéo.
                                </video>
                                <p class="mt-1 text-xs text-gray-500">Exemple d'application d'un code promo dans son panier</p>
                            </div>
                        </x-faq-item>

                        <x-faq-item task="14" title="Comment suivre ma commande ?" icon="heroicon-o-map">
                            <ol class="ml-4 space-y-1 text-sm">
                                <li>
                                    1. Cliquez sur l'icône cycliste
                                    <img src="{{ asset("resources/cyclist.svg") }}" alt="Login" class="inline size-7" />
                                    en haut à droite
                                </li>
                                <li>2. Sélectionnez "Mes commandes" dans le menu déroulant</li>
                                <li>3. Cliquez sur la commande que vous souhaitez consulter</li>
                                <li>4. Consultez le statut actuel et l'historique complet</li>
                            </ol>
                            <p class="mt-2 text-sm">
                                Statuts possibles : En attente de paiement, Paiement accepté, Expédié, Livrée, Annulée et Retournée
                            </p>

                            <div class="mt-4">
                                <video autoplay loop muted playsinline class="w-full max-w-md rounded-lg border shadow-sm">
                                    <source src="{{ asset("resources/help/videos/historique-commande.mp4") }}" type="video/mp4" />
                                    Votre navigateur ne supporte pas la vidéo.
                                </video>
                                <p class="mt-1 text-xs text-gray-500">Exemple de visualisation de l'historique de commande</p>
                            </div>
                        </x-faq-item>
                    </div>
                </div>

                <div>
                    <h3 class="mb-4 text-sm font-semibold tracking-wide text-gray-500 uppercase">Outils</h3>
                    <div class="space-y-2">
                        <x-faq-item task="16" title="Comment accéder à l'assistant IA ?" icon="heroicon-o-chat-bubble-left-right">
                            <p class="mb-2">
                                Cliquez sur le bouton bleu avec une icône enveloppe
                                <x-heroicon-o-envelope class="mx-1 inline h-4 w-4" />
                                situé en bas à droite de votre écran, visible sur toutes les pages du site.
                            </p>
                            <p class="mb-2">L'assistant IA peut vous aider à :</p>
                            <ul class="ml-4 space-y-1 text-sm">
                                <li>
                                    <x-heroicon-o-check class="mr-1 inline h-4 w-4 text-green-600" />
                                    Répondre à des questions sur un vélo précis (en étant sur la page produit)
                                </li>
                                <li>
                                    <x-heroicon-o-check class="mr-1 inline h-4 w-4 text-green-600" />
                                    Expliquer certains mots de vocabulaire technique
                                </li>
                                <li>
                                    <x-heroicon-o-check class="mr-1 inline h-4 w-4 text-green-600" />
                                    Expliquer les caractéristiques produits
                                </li>
                                <li>
                                    <x-heroicon-o-check class="mr-1 inline h-4 w-4 text-green-600" />
                                    Vous guider dans la navigation du site
                                </li>
                                <li>
                                    <x-heroicon-o-check class="mr-1 inline h-4 w-4 text-green-600" />
                                    Fournir des conseils d'entretien de base
                                </li>
                                <li>
                                    <x-heroicon-o-check class="mr-1 inline h-4 w-4 text-green-600" />
                                    Et bien plus encore ! N'hésitez pas à lui poser vos questions.
                                </li>
                            </ul>
                        </x-faq-item>

                        <x-faq-item task="17" title="Comment gérer les cookies ?" icon="heroicon-o-shield-check">
                            <p class="mb-2">Cliquez sur l'icône citron (Tarteaucitron) située en bas à gauche de votre écran.</p>
                            <p class="mb-2">Dans le panneau qui s'ouvre, vous pouvez :</p>
                            <ul class="ml-4 space-y-1 text-sm">
                                <li>
                                    <x-heroicon-o-check class="mr-1 inline h-4 w-4 text-green-600" />
                                    Activer ou désactiver les cookies par catégorie
                                </li>
                                <li>
                                    <x-heroicon-o-check class="mr-1 inline h-4 w-4 text-green-600" />
                                    Consulter la liste complète des services utilisés
                                </li>
                                <li>
                                    <x-heroicon-o-check class="mr-1 inline h-4 w-4 text-green-600" />
                                    Tout accepter ou tout refuser en un clic
                                </li>
                                <li>
                                    <x-heroicon-o-check class="mr-1 inline h-4 w-4 text-green-600" />
                                    Accéder à notre politique de confidentialité
                                </li>
                            </ul>

                            <div class="mt-4">
                                <video autoplay loop muted playsinline class="w-full max-w-md rounded-lg border shadow-sm">
                                    <source src="{{ asset("resources/help/videos/gestion-cookies.mp4") }}" type="video/mp4" />
                                    Votre navigateur ne supporte pas la vidéo.
                                </video>
                                <p class="mt-1 text-xs text-gray-500">Exemple d'application d'un code promo dans son panier</p>
                            </div>
                        </x-faq-item>
                    </div>
                </div>

                <div>
                    <h3 class="mb-4 text-sm font-semibold tracking-wide text-gray-500 uppercase">Compte</h3>
                    <div class="space-y-2">
                        <x-faq-item task="18" title="Comment créer un compte ?" icon="heroicon-o-user-plus">
                            <p class="mb-2">Deux méthodes de création de compte disponibles :</p>
                            <div class="space-y-3">
                                <div class="rounded-lg bg-blue-50 p-3">
                                    <p class="mb-1 font-semibold text-blue-900">
                                        <x-heroicon-o-envelope class="mr-1 inline h-4 w-4" />
                                        Inscription avec email et mot de passe
                                    </p>
                                    <ol class="ml-4 space-y-1 text-sm text-blue-700">
                                        <li>
                                            1. Cliquez sur l'icône cycliste
                                            <img src="{{ asset("resources/cyclist.svg") }}" alt="Login" class="inline size-7" />
                                            en haut à droite
                                        </li>
                                        <li>2. Sélectionnez "S'inscrire" dans le menu déroulant</li>
                                        <li>3. Remplissez le formulaire d'inscription (nom, prénom, email, mot de passe)</li>
                                        <li>4. Validez votre adresse email via le lien reçu par email</li>
                                    </ol>
                                </div>
                                <div class="rounded-lg bg-red-50 p-3">
                                    <p class="mb-1 font-semibold text-red-900">
                                        <x-heroicon-o-shield-check class="mr-1 inline h-4 w-4" />
                                        Inscription avec Google (OAuth)
                                    </p>
                                    <p class="text-sm text-red-700">
                                        Cliquez sur le bouton "Continuer avec Google" pour une authentification rapide via votre compte
                                        Google existant. Aucun mot de passe à créer.
                                    </p>
                                </div>
                            </div>

                            <div class="mt-4">
                                <video autoplay loop muted playsinline class="w-full max-w-md rounded-lg border shadow-sm">
                                    <source src="{{ asset("resources/help/videos/suppresion-compte.mp4") }}" type="video/mp4" />
                                    Votre navigateur ne supporte pas la vidéo.
                                </video>
                                <p class="mt-1 text-xs text-gray-500">Exemple de création d'un compte client</p>
                            </div>
                        </x-faq-item>

                        <x-faq-item task="19" title="Mot de passe oublié : que faire ?" icon="heroicon-o-lock-closed">
                            <p class="mb-2 font-semibold">Pour les comptes créés avec email et mot de passe :</p>
                            <ol class="ml-4 space-y-1 text-sm">
                                <li>
                                    1. Accédez à la page de connexion via l'icône cycliste
                                    <img src="{{ asset("resources/cyclist.svg") }}" alt="Login" class="inline size-7" />
                                </li>
                                <li>2. Cliquez sur le lien "Mot de passe oublié ?" sous le formulaire</li>
                                <li>3. Saisissez votre adresse email dans le champ prévu</li>
                                <li>4. Consultez votre boîte mail (vérifiez également les spams)</li>
                                <li>5. Cliquez sur le lien de réinitialisation reçu par email</li>
                                <li>6. Créez votre nouveau mot de passe (saisissez-le deux fois)</li>
                                <li>7. Cliquez sur "Valider" pour confirmer</li>
                            </ol>
                            <div class="mt-3 rounded-lg bg-red-50 p-3">
                                <p class="text-sm font-semibold text-red-800">Pour les comptes créés avec Google :</p>
                                <p class="text-sm text-red-700">
                                    Le mot de passe n'est pas géré par Cube Bikes. Rendez-vous sur
                                    <strong>accounts.google.com/signin/recovery</strong>
                                    pour récupérer l'accès à votre compte Google.
                                </p>
                            </div>

                            <div class="mt-4">
                                <video autoplay loop muted playsinline class="w-full max-w-md rounded-lg border shadow-sm">
                                    <source src="{{ asset("resources/help/videos/mdp-oublie.mp4") }}" type="video/mp4" />
                                    Votre navigateur ne supporte pas la vidéo.
                                </video>
                                <p class="mt-1 text-xs text-gray-500">Exemple d'utilisation de "Mot de passe oublié"</p>
                            </div>
                        </x-faq-item>

                        <x-faq-item task="20" title="Comment changer mon mot de passe ?" icon="heroicon-o-key">
                            <p class="mb-2">Pour les comptes créés avec email et mot de passe :</p>
                            <ol class="ml-4 space-y-1 text-sm">
                                <li>
                                    1. Cliquez sur l'icône cycliste
                                    <img src="{{ asset("resources/cyclist.svg") }}" alt="Login" class="inline size-7" />
                                    en haut à droite
                                </li>
                                <li>2. Sélectionnez "Mon profil" dans le menu déroulant</li>
                                <li>3. Faites défiler jusqu'à la section "Changement de mot de passe"</li>
                                <li>
                                    4. Remplissez les trois champs : mot de passe actuel, nouveau mot de passe, confirmation du nouveau mot
                                    de passe
                                </li>
                                <li>5. Cliquez sur "Modifier le mot de passe"</li>
                            </ol>
                            <div class="mt-3 rounded-lg bg-red-50 p-3">
                                <p class="text-sm text-red-800">
                                    Non disponible pour les comptes Google. Le mot de passe se gère sur
                                    <strong>myaccount.google.com</strong>
                                </p>
                            </div>
                        </x-faq-item>

                        <x-faq-item task="21" title="Comment modifier mes informations personnelles ?" icon="heroicon-o-pencil">
                            <ol class="ml-4 space-y-1 text-sm">
                                <li>
                                    1. Cliquez sur l'icône cycliste
                                    <img src="{{ asset("resources/cyclist.svg") }}" alt="Login" class="inline size-7" />

                                    en haut à droite
                                </li>
                                <li>2. Sélectionnez "Mon profil" dans le menu déroulant</li>
                                <li>3. Cliquez sur le bouton "Modifier" situé en haut à droite de la page</li>
                                <li>4. Modifiez les champs souhaités (prénom, nom, email, numéro de téléphone)</li>
                                <li>5. Cliquez sur "Enregistrer les modifications"</li>
                            </ol>
                        </x-faq-item>

                        <x-faq-item task="22" title="Comment gérer mes adresses de livraison ?" icon="heroicon-o-home">
                            <p class="mb-2">
                                Accédez à vos adresses via l'icône cycliste
                                <img src="{{ asset("resources/cyclist.svg") }}" alt="Login" class="inline size-7" />
                                → Tableau de bord → Tuile "Mes adresses"
                            </p>
                            <p class="mb-2 font-semibold">Actions disponibles :</p>
                            <ul class="ml-4 space-y-1 text-sm">
                                <li>
                                    <strong>Créer une adresse :</strong>
                                    Cliquez sur le bouton violet "Nouvelle adresse" en haut à droite. Le formulaire utilise l'autocomplétion
                                    Google Places.
                                </li>
                                <li>
                                    <x-heroicon-o-pencil-square class="mr-1 inline h-4 w-4 text-blue-600" />
                                    <strong>Modifier :</strong>
                                    Cliquez sur l'icône crayon sur une carte d'adresse existante pour mettre à jour les informations.
                                </li>
                                <li>
                                    <x-heroicon-o-trash class="mr-1 inline h-4 w-4 text-red-600" />
                                    <strong>Supprimer :</strong>
                                    Cliquez sur l'icône corbeille pour retirer une adresse (action irréversible).
                                </li>
                            </ul>
                        </x-faq-item>
                    </div>
                </div>

                <div>
                    <h3 class="mb-4 text-sm font-semibold tracking-wide text-gray-500 uppercase">Sécurité</h3>
                    <div class="space-y-2">
                        <x-faq-item
                            task="23"
                            title="Comment activer la double authentification (A2F) ?"
                            icon="heroicon-o-shield-exclamation"
                        >
                            <p class="mb-2">Pour renforcer la sécurité de votre compte :</p>
                            <ol class="ml-4 space-y-1 text-sm">
                                <li>
                                    1. Allez dans Mon profil via l'icône cycliste
                                    <img src="{{ asset("resources/cyclist.svg") }}" alt="Login" class="inline size-7" />
                                </li>
                                <li>2. Descendez à la section "Authentification à deux facteurs"</li>
                                <li>3. Cliquez sur le bouton "Activer"</li>
                                <li>
                                    4. Scannez le QR Code affiché avec votre application d'authentification (Google Authenticator, Authy...)
                                </li>
                                <li>5. Saisissez le code à 6 chiffres généré par l'application pour confirmer</li>
                                <li>6. Important : Copiez et conservez vos codes de secours en lieu sûr</li>
                            </ol>

                            <div class="mt-4">
                                <video autoplay loop muted playsinline class="w-full max-w-md rounded-lg border shadow-sm">
                                    <source src="{{ asset("resources/help/videos/a2f.mp4") }}" type="video/mp4" />
                                    Votre navigateur ne supporte pas la vidéo.
                                </video>
                                <p class="mt-1 text-xs text-gray-500">Exemple d'activation de la double authentification</p>
                            </div>
                        </x-faq-item>

                        <x-faq-item task="24" title="Comment désactiver l'A2F ?" icon="heroicon-o-shield-check">
                            <p class="mb-2">Si vous souhaitez retirer cette protection :</p>
                            <ol class="ml-4 space-y-1 text-sm">
                                <li>1. Retournez dans la section "Authentification à deux facteurs" de votre profil</li>
                                <li>2. Cliquez sur le bouton "Désactiver"</li>
                                <li>3. Confirmez l'action en saisissant votre mot de passe actuel</li>
                            </ol>
                            <p class="mt-2 text-sm text-gray-500">
                                Pour les comptes Google, la gestion se fait directement sur votre compte Google.
                            </p>
                        </x-faq-item>

                        <x-faq-item task="25" title="J'ai perdu mes codes A2F, que faire ?" icon="heroicon-o-exclamation-triangle">
                            <p class="mb-2">Deux solutions s'offrent à vous :</p>
                            <ul class="ml-4 space-y-1 text-sm">
                                <li>
                                    <strong>Utiliser un code de secours :</strong>
                                    Lors de la connexion, utilisez l'un des codes de récupération que vous avez sauvegardés lors de
                                    l'activation.
                                </li>
                                <li>
                                    <strong>Contacter le support :</strong>
                                    Si vous n'avez plus accès à votre application ni à vos codes de secours, contactez notre support client.
                                    Une vérification stricte de votre identité sera exigée.
                                </li>
                            </ul>
                        </x-faq-item>

                        <x-faq-item task="26" title="Comment exporter mes données personnelles ?" icon="heroicon-o-arrow-down-tray">
                            <p class="mb-2">Conformément au RGPD, vous pouvez récupérer vos données :</p>
                            <ol class="ml-4 space-y-1 text-sm">
                                <li>1. Allez dans Mon profil</li>
                                <li>2. Descendez tout en bas de la page</li>
                                <li>3. Cliquez sur le bouton "Exporter mes données"</li>
                            </ol>
                            <p class="mt-2 text-sm">
                                Un fichier au format JSON contenant toutes vos informations personnelles sera téléchargé automatiquement.
                            </p>
                        </x-faq-item>

                        <x-faq-item task="27" title="Comment supprimer mon compte ?" icon="heroicon-o-trash">
                            <div class="rounded-lg border border-red-200 bg-red-50 p-3">
                                <p class="mb-1 flex items-center font-bold text-red-700">
                                    <x-heroicon-o-exclamation-triangle class="mr-2 h-5 w-5" />
                                    Attention : Action irréversible
                                </p>
                                <p class="text-sm text-red-600">
                                    La suppression effacera définitivement votre historique de commandes, vos adresses ainsi que toutes vos
                                    informations personnelles.
                                </p>
                            </div>
                            <ol class="mt-3 ml-4 space-y-1 text-sm">
                                <li>1. Allez dans Mon profil</li>
                                <li>2. Descendez tout en bas de la page</li>
                                <li>3. Cliquez sur le bouton rouge "Supprimer mon compte"</li>
                                <li>4. Saisissez votre mot de passe pour confirmer la suppression définitive</li>
                            </ol>

                            <div class="mt-4">
                                <video autoplay loop muted playsinline class="w-full max-w-md rounded-lg border shadow-sm">
                                    <source src="{{ asset("resources/help/videos/suppresion-compte.mp4") }}" type="video/mp4" />
                                    Votre navigateur ne supporte pas la vidéo.
                                </video>
                                <p class="mt-1 text-xs text-gray-500">Exemple de suppression d'un compte client</p>
                            </div>
                        </x-faq-item>
                    </div>
                </div>

                <div>
                    <h3 class="mb-4 text-sm font-semibold tracking-wide text-gray-500 uppercase">Technique</h3>
                    <div class="space-y-2">
                        <x-faq-item task="28" title="Quelle différence entre HPC et HPA ?" icon="heroicon-o-cube">
                            <div class="space-y-2 text-sm">
                                <p>
                                    <strong>HPC (High Performance Composite) :</strong>
                                    Cadre en carbone. Offre le meilleur rapport rigidité/poids. Idéal pour la performance et le confort
                                    (absorption des vibrations).
                                </p>
                                <p>
                                    <strong>HPA (High Performance Aluminium) :</strong>
                                    Cadre en aluminium haut de gamme. Très robuste, léger grâce à l'hydroformage, et plus économique que le
                                    carbone.
                                </p>
                            </div>
                        </x-faq-item>

                        <x-faq-item task="29" title="Que signifie le couple (Nm) ?" icon="heroicon-o-bolt">
                            <p class="mb-2">
                                Le couple, exprimé en Newton-mètre (Nm), représente la force d'accélération du moteur électrique.
                            </p>
                            <ul class="ml-4 space-y-1 text-sm">
                                <li>
                                    <strong>85 Nm :</strong>
                                    Moteur très puissant (ex: Bosch Performance CX). Idéal pour le VTT et les côtes raides.
                                </li>
                                <li>
                                    <strong>50-65 Nm :</strong>
                                    Moteur polyvalent (ex: Bosch Performance Line). Parfait pour la ville et le trekking.
                                </li>
                            </ul>
                        </x-faq-item>

                        <x-faq-item task="30" title="Qu'est-ce qu'un cadre Trapèze ?" icon="heroicon-o-squares-2x2">
                            <p>
                                C'est une géométrie de cadre où la barre supérieure est fortement abaissée. Cela facilite grandement
                                l'enjambement du vélo (monter et descendre) sans perdre en rigidité ni en sportivité. C'est une excellente
                                alternative mixte au cadre classique.
                            </p>
                        </x-faq-item>

                        <x-faq-item task="31" title="Quelle autonomie (Wh) choisir ?" icon="heroicon-o-battery-100">
                            <p class="mb-2">Les Watt-heures (Wh) indiquent la capacité du réservoir d'énergie :</p>
                            <ul class="ml-4 space-y-1 text-sm">
                                <li>
                                    <strong>500 Wh :</strong>
                                    Environ 60 à 100 km. Suffisant pour les trajets quotidiens.
                                </li>
                                <li>
                                    <strong>625 Wh :</strong>
                                    Environ 80 à 120 km. Pour les sorties longues.
                                </li>
                                <li>
                                    <strong>750 Wh :</strong>
                                    Environ 100 à 140 km. Pour l'aventure sans limite.
                                </li>
                            </ul>
                            <p class="mt-2 text-sm italic">
                                L'autonomie réelle dépend du poids, du dénivelé et du mode d'assistance utilisé.
                            </p>
                        </x-faq-item>

                        <x-faq-item task="32" title="C'est quoi le Tubeless ?" icon="heroicon-o-wrench-screwdriver">
                            <p>
                                Une technologie de pneus sans chambre à air (comme sur les voitures). Cela nécessite un liquide préventif à
                                l'intérieur.
                            </p>
                            <p class="mt-1 text-sm font-semibold">Avantages :</p>
                            <ul class="ml-4 list-disc text-sm">
                                <li>Meilleure résistance aux crevaisons (le liquide bouche les petits trous)</li>
                                <li>Possibilité de rouler à basse pression pour plus de confort et d'adhérence</li>
                                <li>Gain de poids</li>
                            </ul>
                        </x-faq-item>

                        <x-faq-item task="33" title="Différence Shimano XT vs Deore ?" icon="heroicon-o-cog">
                            <p class="text-sm">
                                <strong>Shimano XT :</strong>
                                Gamme compétition. Matériaux plus légers, changements de vitesse plus précis et rapides sous charge.
                            </p>
                            <p class="mt-2 text-sm">
                                <strong>Shimano Deore :</strong>
                                Gamme sport/loisir. Excellent rapport qualité/prix, très fiable et durable, mais légèrement plus lourd.
                            </p>
                        </x-faq-item>
                    </div>
                </div>

                <div>
                    <h3 class="mb-4 text-sm font-semibold tracking-wide text-gray-500 uppercase">Support</h3>
                    <div class="space-y-2">
                        <x-faq-item task="34" title="Le site semble lent, que faire ?" icon="heroicon-o-arrow-path">
                            <ul class="ml-4 list-disc space-y-1 text-sm">
                                <li>Essayez de vider le cache de votre navigateur</li>
                                <li>Testez la navigation en mode "Navigation privée"</li>
                                <li>Vérifiez votre connexion internet</li>
                                <li>Essayez depuis un autre navigateur (Chrome, Firefox, Safari)</li>
                            </ul>
                        </x-faq-item>

                        <x-faq-item task="35" title="Je ne reçois pas les emails" icon="heroicon-o-envelope">
                            <ul class="ml-4 list-disc space-y-1 text-sm">
                                <li>Vérifiez votre dossier "Spams" ou "Courriers indésirables"</li>
                                <li>Patientez quelques minutes, l'envoi peut parfois prendre un peu de temps</li>
                                <li>Vérifiez que l'adresse email saisie dans votre profil ne contient pas de faute de frappe</li>
                            </ul>
                        </x-faq-item>

                        <x-faq-item task="36" title="Mon paiement est refusé" icon="heroicon-o-x-circle">
                            <p class="mb-2">Les causes les plus fréquentes :</p>
                            <ul class="ml-4 list-disc space-y-1 text-sm">
                                <li>Erreur de saisie des numéros ou du cryptogramme visuel</li>
                                <li>Plafond de paiement de votre carte bancaire atteint</li>
                                <li>Échec de l'authentification 3D Secure (validation sur l'app bancaire)</li>
                                <li>Adresse de facturation incohérente avec la carte</li>
                            </ul>
                            <p class="mt-2 text-sm">Essayez un autre moyen de paiement (PayPal) si le problème persiste.</p>
                        </x-faq-item>

                        <x-faq-item task="37" title="Quelle est la garantie des vélos ?" icon="heroicon-o-shield-check">
                            <p class="mb-2">Tous nos vélos bénéficient de :</p>
                            <ul class="ml-4 space-y-1 text-sm">
                                <li>
                                    <strong>Garantie légale de 2 ans</strong>
                                    sur les pièces et composants (hors pièces d'usure).
                                </li>
                                <li>
                                    <strong>Extension constructeur Cube :</strong>
                                    Jusqu'à 6 ans sur les cadres aluminium et carbone (sous conditions d'utilisation normale).
                                </li>
                            </ul>
                            <p class="mt-2 text-sm font-semibold">
                                Conservez précieusement votre facture disponible dans votre espace client, elle fait office de garantie.
                            </p>
                        </x-faq-item>
                    </div>
                </div>
            </div>

            <div class="mt-12 rounded-2xl bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-8 text-center shadow-xl">
                <h3 class="text-2xl font-bold text-white">Une autre question ?</h3>
                <p class="mx-auto mt-2 max-w-xl text-blue-100">
                    Notre assistant IA connait le site par cœur et peut vous répondre instantanément.
                </p>
                <button
                    onclick="botmanChatWidget.open()"
                    class="mt-6 inline-flex items-center gap-2 rounded-lg bg-white px-8 py-3 font-semibold text-blue-600 shadow-lg transition hover:bg-blue-50 focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-blue-600 focus:outline-none"
                >
                    <x-heroicon-o-chat-bubble-left-right class="h-5 w-5" />
                    Ouvrir le chat
                </button>
            </div>
        </div>
    </div>
</x-app-layout>
