<x-staff-layout>
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Demandes de retour</h1>
    </div>

    <div class="rounded-lg bg-white shadow-md">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Commande</th>
                    <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Client</th>
                    <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">État</th>
                    <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse ($returns as $return)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm whitespace-nowrap text-gray-900">#{{ $return->id_demande_retour }}</td>
                        <td class="px-6 py-4 text-sm whitespace-nowrap text-gray-500">
                            {{ $return->date_demande->format("d/m/Y") }}
                        </td>
                        <td class="px-6 py-4 text-sm whitespace-nowrap text-gray-900">#{{ $return->order->num_commande }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ $return->order->client->prenom_client }} {{ $return->order->client->nom_client }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span
                                class="@if ($return->state->label_etat_retour === "En attente")
                                    bg-yellow-100
                                    text-yellow-800
                                @elseif ($return->state->label_etat_retour === "Validé")
                                    bg-green-100
                                    text-green-800
                                @elseif ($return->state->label_etat_retour === "Refusé")
                                    bg-red-100
                                    text-red-800
                                @elseif ($return->state->label_etat_retour === "Réceptionné")
                                    bg-blue-100
                                    text-blue-800
                                @elseif ($return->state->label_etat_retour === "Remboursé")
                                    bg-purple-100
                                    text-purple-800
                                @else
                                    bg-gray-100
                                    text-gray-800
                                @endif inline-flex rounded-full px-2 py-1 text-xs font-semibold"
                            >
                                {{ $return->state->label_etat_retour }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm whitespace-nowrap">
                            <a href="{{ route("sav.show", $return) }}" class="text-blue-600 hover:text-blue-900">Voir détails</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">Aucune demande de retour</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="border-t border-gray-200 px-6 py-4">
            {{ $returns->links() }}
        </div>
    </div>
</x-staff-layout>
