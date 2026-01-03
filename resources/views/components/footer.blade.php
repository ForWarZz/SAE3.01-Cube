<footer class="border-t border-gray-200 bg-gray-50 text-gray-700">
    <div class="flex flex-row justify-center gap-64 px-6 py-8">
        <p class="text-sm text-gray-500">© {{ date("Y") }} Cube. Tous droits réservés.</p>

        <nav class="flex flex-wrap items-center gap-4 text-sm font-medium text-gray-600">
            <a href="{{ route("legal-notices") }}" class="transition hover:text-gray-900">Mentions légales</a>
            <a href="{{ route("privacy-policy") }}" class="transition hover:text-gray-900">Politique de confidentialité</a>
            <a href="{{ route("terms-of-sale") }}" class="transition hover:text-gray-900">Condition générales de ventes</a>

            <span class="block h-4 w-px bg-gray-300"></span>

            <button
                onclick="window.rebootTour()"
                type="button"
                class="group flex cursor-pointer items-center gap-1.5 text-blue-600 transition-colors hover:text-blue-700"
            >
                <x-heroicon-o-arrow-path-rounded-square class="size-4 transition-transform group-hover:rotate-180" />
                <span class="group-hover:underline">Relancer le guide</span>
            </button>
        </nav>
    </div>
</footer>
