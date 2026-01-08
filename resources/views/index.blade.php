<x-app-layout>
    <div class="relative flex-1 overflow-hidden bg-gray-900">
        <div class="absolute inset-0">
            <img
                src="https://images.unsplash.com/photo-1571068316344-75bc76f77890?q=80&w=2070&auto=format&fit=crop"
                alt="Cycliste en action"
                class="h-full w-full object-cover object-center opacity-90"
            />
        </div>

        <div class="absolute inset-0 bg-gradient-to-r from-gray-900 via-gray-900/80 to-transparent"></div>

        <div class="relative mx-auto flex h-full max-w-7xl flex-col justify-center px-8 py-56">
            <div class="max-w-2xl">
                <div
                    class="mb-4 inline-flex items-center gap-2 rounded-full border border-green-400/30 bg-green-500/20 px-4 py-2 text-sm font-medium text-green-300 backdrop-blur-sm"
                >
                    <x-heroicon-o-bolt class="h-4 w-4" />
                    Mobilité douce & écologique
                </div>

                <h1 class="text-7xl font-bold tracking-tight text-white uppercase drop-shadow-lg">
                    Vos déplacements quotidiens,
                    <span class="bg-gradient-to-r from-blue-400 to-emerald-400 bg-clip-text text-transparent">en mode éco.</span>
                </h1>

                <p class="mt-6 text-lg leading-8 text-gray-300 drop-shadow-md">
                    Vélo musculaire, électrique, accessoires... Pour vos trajets en ville, vos balades ou vos aventures sportives :
                    <strong class="text-white">trouvez le vélo qui vous ressemble</strong>
                    et roulez
                    <strong class="text-white">sans pollution</strong>
                    .
                </p>

                <div class="mt-8 grid grid-cols-3 gap-4 text-white">
                    <div class="rounded-lg border border-white/20 bg-white/10 p-3 backdrop-blur-sm">
                        <div class="text-2xl font-bold text-green-400">0 CO₂</div>
                        <div class="text-xs text-gray-300">à l'usage</div>
                    </div>
                    <div class="rounded-lg border border-white/20 bg-white/10 p-3 backdrop-blur-sm">
                        <div class="text-2xl font-bold text-blue-400">Jusqu'à 400€</div>
                        <div class="text-xs text-gray-300">d'aide (VAE)</div>
                    </div>
                    <div class="rounded-lg border border-white/20 bg-white/10 p-3 backdrop-blur-sm">
                        <div class="text-2xl font-bold text-purple-400">Économies</div>
                        <div class="text-xs text-gray-300">sur vos trajets</div>
                    </div>
                </div>

                <div class="mt-10 flex items-center gap-x-6">
                    <a
                        href="{{ route("articles.by-category", $bikeCategoryId) }}"
                        class="rounded-md bg-blue-600 px-6 py-3 text-sm font-semibold text-white shadow-lg transition-all duration-300 hover:bg-blue-700 hover:shadow-xl focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-500"
                    >
                        Trouver mon vélo
                    </a>
                    <a
                        href="{{ route("articles.by-category", $accessoryCategoryId) }}"
                        class="text-sm leading-6 font-semibold text-white transition-colors duration-300 hover:text-blue-300"
                    >
                        Nos accessoires
                        <span>→</span>
                    </a>
                    <a
                        href="{{ route("user-guide") }}"
                        class="inline-flex items-center gap-2 text-sm leading-6 font-semibold text-gray-300 transition-colors duration-300 hover:text-white"
                    >
                        <x-heroicon-o-question-mark-circle class="h-5 w-5" />
                        Comment ça marche ?
                        <span>→</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
