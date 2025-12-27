@props([
    "reference",
    "currentCategory",
])

@php
    $pageType = "general";
    $contextId = "";

    if (request()->routeIs("articles.show-reference") && isset($reference)) {
        $pageType = "article-reference";
        $contextId = $reference->id_reference;
    } elseif (request()->routeIs("articles.by-category") && isset($currentCategory)) {
        $pageType = "category";
        $contextId = $currentCategory->id_categorie;
    } elseif (request()->routeIs("cart.index")) {
        $pageType = "cart";
    } elseif (request()->routeIs("checkout.index")) {
        $pageType = "checkout";
    } elseif (request()->routeIs("dashboard.*")) {
        $pageType = "profile";
    }
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace("_", "-", app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />

        <!-- Leaflet CSS & JS -->
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>

        <title>{{ config("app.name", "Laravel") }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net" />
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(["resources/css/app.css", "resources/js/app.js"])

        <style>
            [x-cloak] {
                display: none !important;
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        <div class="flex min-h-screen flex-col">
            <x-nav-bar />

            <main class="flex flex-1 flex-col">
                {{ $slot }}
            </main>
        </div>

        <x-shop-selector-modal />

        <script>
            var botmanWidget = {
                chatServer: '/botman?page_type={{ $pageType }}&context_id={{ $contextId }}',
                frameEndpoint: '/botman/chat',

                title: 'Assistant Cube',

                mainColor: '#4f46e5',
                bubbleBackground: '#4f46e5',

                headerTextColor: '#ffffff',

                aboutText: 'Powered by Cube AI',
                introMessage:
                    "👋 <b>Bonjour !</b><br>Je suis l'IA de Cube Bikes.<br>Une question sur un vélo ou besoin d'aide sur le site ?",
                placeholderText: 'Posez votre question...',

                displayMessageTime: true,
                desktopHeight: 500,
                desktopWidth: 370,
                mobileHeight: '100%',
                mobileWidth: '100%',
            };
        </script>

        <script src="https://cdn.jsdelivr.net/npm/botman-web-widget@0/build/js/widget.js"></script>

        <script src="{{ asset("tarteaucitron/tarteaucitron.min.js") }}"></script>
        <script type="text/javascript">
            tarteaucitron.services.googleplaces = {
                key: 'googleplaces',
                type: 'api',
                name: 'Google Places (Autocomplétion)',
                uri: 'https://policies.google.com/privacy',
                needConsent: true,
                cookies: [],
                js: function () {
                    document.getElementById('google-cookie-alert').classList.add('hidden');

                    const acInput = document.getElementById('address_autocomplete');
                    acInput.disabled = false;
                    acInput.classList.remove('bg-gray-100', 'cursor-not-allowed');
                    acInput.placeholder = 'Commencez à taper votre adresse...';

                    ['code_postal', 'nom_ville'].forEach((id) => {
                        const el = document.getElementById(id);
                        el.setAttribute('readonly', 'true');
                        el.classList.add('cursor-not-allowed', 'border-gray-200', 'bg-gray-100');
                    });

                    tarteaucitron.addScript(
                        'https://maps.googleapis.com/maps/api/js?key={{ config("services.google.places_api_key") }}&libraries=places&callback=initAutocomplete',
                    );
                },
                fallback: function () {
                    document.getElementById('google-cookie-alert').classList.remove('hidden');

                    const acInput = document.getElementById('address_autocomplete');
                    acInput.disabled = true;
                    acInput.classList.add('bg-gray-100', 'cursor-not-allowed');
                    acInput.placeholder = 'Service désactivé (cookies refusés)';
                    acInput.value = '';

                    ['code_postal', 'nom_ville'].forEach((id) => {
                        const el = document.getElementById(id);
                        el.removeAttribute('readonly');
                        el.classList.remove('cursor-not-allowed', 'border-gray-200', 'bg-gray-100');
                    });
                },
            };
        </script>
        <script type="text/javascript">
            tarteaucitron.init({
                privacyUrl: '',
                bodyPosition: 'bottom',
                hashtag: '#tarteaucitron',
                cookieName: 'tarteaucitron',
                orientation: 'middle',
                groupServices: false,
                showIcon: true,
                iconPosition: 'BottomLeft',
                adblocker: false,
                DenyAllCta: true,
                AcceptAllCta: true,
                highPrivacy: true,
                handleBrowserDNTRequest: false,
                removeCredit: false,
                moreInfoLink: true,
                useExternalCss: false,
                useExternalJs: false,
                readmoreLink: '',
            });

            tarteaucitron.user.matomoId = 1;
            tarteaucitron.user.matomoHost = '//ton-analytics.com/';

            (tarteaucitron.job = tarteaucitron.job || []).push('matomo');
            (tarteaucitron.job = tarteaucitron.job || []).push('googleplaces');
        </script>
    </body>
</html>
