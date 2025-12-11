<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} - Commercial</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-100">
        <div class="flex h-screen overflow-hidden">
            
            <aside class="w-64 bg-slate-800 text-white flex flex-col hidden md:flex">
                <div class="h-16 flex items-center justify-center text-xl font-bold border-b border-slate-700">
                    CUBE ADMIN
                </div>
                <nav class="flex-1 px-2 py-4 space-y-2">
                    <a href="{{ route('commercial.dashboard') }}" class="flex items-center px-4 py-2 text-gray-300 hover:bg-slate-700 hover:text-white rounded-md">
                        <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                        Tableau de bord
                    </a>

                    <a href="{{ route('commercial.categories.index') }}" class="flex items-center px-4 py-2 text-gray-300 hover:bg-slate-700 hover:text-white rounded-md">
                        <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        Catégories
                    </a>

                    <a href="{{ route('commercial.models.index') }}" class="flex items-center px-4 py-2 text-gray-300 hover:bg-slate-700 hover:text-white rounded-md">
                        <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                        Modèles
                    </a>

                    <form method="POST" action="{{ route('commercial.logout') }}" class="mt-auto">
                        @csrf
                        <button type="submit" class="flex w-full items-center px-4 py-2 text-red-400 hover:bg-slate-700 hover:text-red-200 rounded-md">
                            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                            Déconnexion
                        </button>
                    </form>
                </nav>
            </aside>

            <div class="flex-1 flex flex-col overflow-hidden">
                <header class="flex justify-between items-center py-4 px-6 bg-white border-b-2 border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">
                        {{ $header ?? 'Dashboard' }}
                    </h2>
                    <div class="flex items-center">
                        <span class="text-gray-600 mr-2">Bonjour, {{ Auth::guard('commercial')->user()->prenom_commercial ?? 'Commercial' }}</span>
                    </div>
                </header>

                <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>