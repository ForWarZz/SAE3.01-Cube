<x-app-layout>
    <div class="py-12">
        <div class="mx-auto max-w-7xl px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-6 flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Mes vélos enregistrés</h1>
                            <p class="mt-1 text-sm text-gray-500">Gérez vos différents vélo, pour faciliter leur entretien</p>
                        </div>
                        <x-button :href="route('dashboard.bike-registered.create')">Enregistrer un nouveau vélo</x-button>
                    </div>

                    <x-flash-message key="success" type="success" />

                    @if ($bikes->isEmpty())
                        <div class="py-12 text-center">
                            <img src="{{ asset("resources/cyclist.svg") }}" alt="Icone de bicyclette" class="mx-auto size-16" />

                            <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun vélo enregistré</h3>
                            <p class="mt-1 text-sm text-gray-500">Commencez par enregistrer un nouveau vélo.</p>
                        </div>
                    @else
                        <div class="overflow-hidden rounded-lg border border-gray-200">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">
                                            N° Série
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">
                                            Millésime
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">
                                            Date d'achat
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">
                                            Magasin
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">
                                            Facture
                                        </th>
                                        <th class="px-6 py-3 text-right text-xs font-medium tracking-wider text-gray-500 uppercase">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    @foreach ($bikes as $bike)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="font-mono text-sm font-medium text-gray-900">
                                                    {{ $bike->num_serie_velo_enr ?? "N/A" }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-sm whitespace-nowrap text-gray-500">
                                                {{ $bike->millesime_velo_enr ?? "N/A" }}
                                            </td>
                                            <td class="px-6 py-4 text-sm whitespace-nowrap text-gray-500">
                                                {{ $bike->date_achat_velo_enr?->format("d/m/Y") ?? "N/A" }}
                                            </td>
                                            <td class="px-6 py-4 text-sm whitespace-nowrap text-gray-500">
                                                {{ $bike->shop->nom_magasin ?? "N/A" }}
                                            </td>
                                            <td class="px-6 py-4 text-sm whitespace-nowrap">
                                                <a
                                                    href="{{ route("dashboard.bike-registered.download-invoice", $bike) }}"
                                                    class="text-blue-600 hover:text-blue-900"
                                                    title="Télécharger la facture"
                                                >
                                                    Télécharger la facture
                                                </a>
                                            </td>
                                            <td class="px-6 py-4 text-right text-sm font-medium whitespace-nowrap">
                                                <div class="flex items-center justify-end space-x-3">
                                                    <a
                                                        href="{{ route("dashboard.bike-registered.edit", $bike) }}"
                                                        class="cursor-pointer text-yellow-600 hover:text-yellow-900"
                                                        title="Modifier"
                                                    >
                                                        <x-heroicon-o-pencil-square class="h-5 w-5" />
                                                    </a>
                                                    <form
                                                        action="{{ route("dashboard.bike-registered.destroy", $bike) }}"
                                                        method="POST"
                                                        class="inline-flex"
                                                        onsubmit="return confirm('Supprimer ce vélo ?');"
                                                    >
                                                        @csrf
                                                        @method("DELETE")
                                                        <button
                                                            type="submit"
                                                            class="cursor-pointer text-red-600 hover:text-red-900"
                                                            title="Supprimer"
                                                        >
                                                            <x-heroicon-o-trash class="size-5" />
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-6">{{ $bikes->links() }}</div>
                    @endif

                    <div class="mt-6 border-t border-gray-200 pt-6">
                        <a href="{{ route("dashboard.index") }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">
                            &larr; Retour au tableau de bord
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
