<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />

        <title>{{ config("app.name", "Laravel") }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net" />
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(["resources/css/app.css", "resources/js/app.js"])
    </head>
    <body class="font-sans antialiased">
        <div class="flex min-h-screen flex-col">
            <x-nav-bar />

            {{-- <!-- Page Heading --> --}}
            {{-- @if (isset($header)) --}}
            {{-- <header class="bg-white dark:bg-gray-800 shadow"> --}}
            {{-- <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8"> --}}
            {{-- {{ $header }} --}}
            {{-- </div> --}}
            {{-- </header> --}}
            {{-- @endif --}}

            <main class="flex flex-1 flex-col">
                {{ $slot }}
            </main>
        </div>
        <x-shop-selector-modal />
    </body>

    {{-- <script src="{{ asset("tarteaucitron/tarteaucitron.min.js") }}"></script> --}}
    {{-- <script type="text/javascript"> --}}
    {{-- tarteaucitron.init({ --}}
    {{-- privacyUrl: '', --}}
    {{-- bodyPosition: 'bottom', --}}
    {{-- hashtag: '#tarteaucitron', --}}
    {{-- cookieName: 'tarteaucitron', --}}
    {{-- orientation: 'middle', --}}
    {{-- groupServices: false, --}}
    {{-- showIcon: true, --}}
    {{-- iconPosition: 'BottomRight', --}}
    {{-- adblocker: false, --}}
    {{-- DenyAllCta: true, --}}
    {{-- AcceptAllCta: true, --}}
    {{-- highPrivacy: true, --}}
    {{-- handleBrowserDNTRequest: false, --}}
    {{-- removeCredit: false, --}}
    {{-- moreInfoLink: true, --}}
    {{-- useExternalCss: false, --}}
    {{-- useExternalJs: false, --}}
    {{-- readmoreLink: '', --}}
    {{-- }); --}}

    {{-- tarteaucitron.user.matomoId = 1; --}}
    {{-- tarteaucitron.user.matomoHost = '//ton-analytics.com/'; --}}
    {{-- (tarteaucitron.job = tarteaucitron.job || []).push('matomo'); --}}
    {{-- (tarteaucitron.job = tarteaucitron.job || []).push('googlemaps'); --}}
    {{-- (tarteaucitron.job = tarteaucitron.job || []).push('paypal'); --}}
    {{-- </script> --}}
</html>
