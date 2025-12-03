<x-app-layout>
    <div class="relative h-screen overflow-hidden bg-gray-900">
        <div class="absolute inset-0">
            <img
                src="https://images.unsplash.com/photo-1571068316344-75bc76f77890?q=80&w=2070&auto=format&fit=crop"
                alt="Cycliste en action"
                class="h-full w-full object-cover object-center opacity-90"
            />
        </div>

        <div class="absolute inset-0 bg-gradient-to-r from-gray-900 via-gray-900/80 to-transparent"></div>

        <div class="relative mx-auto flex h-full max-w-7xl flex-col justify-center px-6 py-24 sm:py-32 lg:px-8">
            <div class="max-w-2xl">
                <h1 class="text-5xl font-bold tracking-tight text-white uppercase drop-shadow-lg sm:text-7xl">
                    Des vélos
                    <br />
                    et accessoires,
                    <span class="bg-gradient-to-r from-blue-400 to-emerald-400 bg-clip-text text-transparent">pour tout public.</span>
                </h1>

                <p class="mt-6 text-lg leading-8 text-gray-300 drop-shadow-md">
                    Que vous soyez adepte de la route, fanatique de VTT ou citadin branché, nous avons l'équipement qu'il vous faut pour
                    repousser vos limites.
                </p>

                <div class="mt-10 flex items-center gap-x-6">
                    <a
                        href="#"
                        class="rounded-md bg-white px-3.5 py-2.5 text-sm font-semibold text-gray-900 shadow-sm transition-all duration-300 hover:bg-gray-200 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white"
                    >
                        Acheter un vélo
                    </a>
                    <a href="#" class="text-sm leading-6 font-semibold text-white transition-colors duration-300 hover:text-blue-300">
                        Nos accessoires
                        <span aria-hidden="true">→</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
