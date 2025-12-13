<!DOCTYPE html>
<html lang="{{ str_replace("_", "-", app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />

        <title>{{ config("app.name", "Laravel") }} - Commercial</title>

        @vite(["resources/css/app.css", "resources/js/app.js"])
    </head>
    <body class="bg-gray-100 font-sans antialiased">
        <div class="flex h-screen overflow-hidden">
            <aside class="flex w-64 flex-col bg-slate-800 text-white">
                <div class="flex h-16 items-center justify-center border-b border-slate-700 text-xl font-bold">CUBE ADMIN</div>

                <nav class="flex-1 space-y-2 px-2 py-4">
                    <a
                        href="{{ route("commercial.dashboard") }}"
                        class="flex items-center rounded-md px-4 py-2 text-gray-300 hover:bg-slate-700 hover:text-white"
                    >
                        <x-heroicon-o-squares-2x2 class="mr-3 h-6 w-6" />
                        Tableau de bord
                    </a>

                    <a
                        href="{{ route("commercial.categories.index") }}"
                        class="flex items-center rounded-md px-4 py-2 text-gray-300 hover:bg-slate-700 hover:text-white"
                    >
                        <x-heroicon-o-rectangle-stack class="mr-3 h-6 w-6" />
                        Catégories
                    </a>

                    <a
                        href="{{ route("commercial.models.index") }}"
                        class="flex items-center rounded-md px-4 py-2 text-gray-300 hover:bg-slate-700 hover:text-white"
                    >
                        <x-heroicon-o-cube-transparent class="mr-3 h-6 w-6" />
                        Modèles
                    </a>

                    <a
                        href="{{ route("commercial.bikes.index") }}"
                        class="flex items-center rounded-md px-4 py-2 text-gray-300 hover:bg-slate-700 hover:text-white"
                    >
                        <x-heroicon-o-tag class="mr-3 h-6 w-6" />
                        Vélos
                    </a>

                    @if ($isDirector)
                        <a
                            href="{{ route("commercial.stats") }}"
                            class="flex items-center rounded-md px-4 py-2 text-gray-300 hover:bg-slate-700 hover:text-white"
                        >
                            <x-heroicon-o-chart-bar class="mr-3 size-6" />
                            Statistiques
                        </a>
                    @endif

                    <form method="POST" action="{{ route("commercial.logout") }}" class="mt-auto">
                        @csrf
                        <button
                            type="submit"
                            class="flex w-full items-center rounded-md px-4 py-2 text-red-400 hover:bg-slate-700 hover:text-red-200"
                        >
                            <x-heroicon-o-arrow-right-on-rectangle class="mr-3 h-6 w-6" />
                            Déconnexion
                        </button>
                    </form>
                </nav>
            </aside>

            <div class="flex flex-1 flex-col overflow-hidden">
                <header class="flex items-center justify-between border-b-2 border-gray-200 bg-white px-6 py-4">
                    <h2 class="text-xl font-semibold text-gray-800">
                        {{ $header ?? "Dashboard" }}
                    </h2>
                    <div class="flex items-center">
                        <span class="mr-2 text-gray-600">
                            Bonjour, {{ Auth::guard("commercial")->user()->prenom_commercial ?? "Commercial" }}
                        </span>
                    </div>
                </header>

                <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
