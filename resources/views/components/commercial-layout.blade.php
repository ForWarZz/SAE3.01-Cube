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
                        <svg class="mr-3 h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"
                            ></path>
                        </svg>
                        Modèles
                    </a>

                    <a
                        href="{{ route("commercial.bikes.index") }}"
                        class="flex items-center rounded-md px-4 py-2 text-gray-300 hover:bg-slate-700 hover:text-white"
                    >
                        <svg class="mr-3 h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                            ></path>
                        </svg>
                        Vélos
                    </a>

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
