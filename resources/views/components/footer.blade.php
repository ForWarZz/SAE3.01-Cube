<footer class="border-t border-gray-200 bg-gray-50 text-gray-700">
    <div class="flex flex-row justify-center gap-64 px-6 py-8">
        <p class="text-sm text-gray-500">© {{ date("Y") }} Cube. Tous droits réservés.</p>

        <nav class="flex flex-wrap items-center gap-4 text-sm font-medium text-gray-600">
            <a href="{{ route("legal-notices") }}" class="transition hover:text-gray-900">Mentions légales</a>
            <a href="{{ route("privacy-policy") }}" class="transition hover:text-gray-900">Politique de confidentialité</a>
            <a href="{{ route("terms-of-sale") }}" class="transition hover:text-gray-900">Condition générales de ventes</a>

            <span class="block h-4 w-px bg-gray-300"></span>

            <div class="flex items-center gap-4">
                <a
                    href="{{ route("user-guide") }}"
                    class="group flex items-center gap-1.5 rounded-full bg-white px-4 py-2 text-sm font-bold text-blue-600 shadow-sm ring-1 ring-gray-200 transition hover:bg-blue-50 hover:ring-blue-300"
                >
                    <x-heroicon-o-question-mark-circle class="size-5" />
                    <span>Centre d'aide</span>
                </a>

                <button
                    onclick="window.rebootTour()"
                    class="group flex cursor-pointer items-center gap-1.5 text-sm font-medium text-gray-500 transition hover:text-blue-600"
                    title="Relancer la visite guidée"
                >
                    <x-heroicon-o-play class="h-4 w-4" />
                    <span class="hidden md:inline">Guide</span>
                </button>
            </div>
        </nav>
    </div>
</footer>
