<x-staff-layout>
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800">Demande de retour #{{ $returnRequest->id_demande_retour }}</h1>
        <a href="{{ route("sav.index") }}" class="text-blue-600 hover:text-blue-800">← Retour à la liste</a>
    </div>

    <x-flash-message key="success" type="success" />
    <x-flash-message key="error" type="error" />

    <div class="mb-6 grid grid-cols-2 gap-6">
        <div class="rounded-lg bg-white p-6 shadow-md">
            <h2 class="mb-4 text-xl font-semibold text-gray-800">Informations générales</h2>

            <div class="space-y-3">
                <div>
                    <span class="text-sm font-medium text-gray-500">Date de demande:</span>
                    <p class="text-gray-900"><x-date-local :date="$returnRequest->date_demande" /></p>
                </div>

                <div>
                    <span class="text-sm font-medium text-gray-500">Commande:</span>
                    <p class="text-gray-900">#{{ $returnRequest->order->num_commande }}</p>
                </div>

                <div>
                    <span class="text-sm font-medium text-gray-500">Client:</span>
                    <p class="text-gray-900">
                        {{ $returnRequest->order->client->prenom_client }} {{ $returnRequest->order->client->nom_client }}
                    </p>
                    <p class="text-sm text-gray-600">{{ $returnRequest->order->client->email_client }}</p>
                </div>

                <div>
                    <span class="text-sm font-medium text-gray-500">Adresse de facturation:</span>
                    <p class="text-gray-900">
                        {{ $returnRequest->order->billingAddress->num_voie_adresse }}
                        {{ $returnRequest->order->billingAddress->rue_adresse }}
                        <br />
                        {{ $returnRequest->order->billingAddress->city->cp_ville }}
                        {{ $returnRequest->order->billingAddress->city->nom_ville }}
                    </p>
                </div>

                @if ($returnRequest->order->id_adresse_livraison)
                    <div>
                        <span class="text-sm font-medium text-gray-500">Adresse de livraison:</span>
                        <p class="text-gray-900">
                            {{ $returnRequest->order->deliveryAddress->num_voie_adresse }}
                            {{ $returnRequest->order->deliveryAddress->rue_adresse }}
                            <br />
                            {{ $returnRequest->order->deliveryAddress->city->cp_ville }}
                            {{ $returnRequest->order->deliveryAddress->city->nom_ville }}
                        </p>
                    </div>
                @elseif ($returnRequest->order->id_magasin)
                    <div>
                        <span class="text-sm font-medium text-gray-500">Retrait en magasin:</span>
                        <p class="text-gray-900">
                            {{ $returnRequest->order->shop->nom_magasin }}
                            <br />
                            {{ $returnRequest->order->shop->full_address }}
                            <br />
                            {{ $returnRequest->order->shop->city->cp_ville }} {{ $returnRequest->order->shop->city->nom_ville }}
                        </p>
                    </div>
                @endif

                @if ($returnRequest->description_demande)
                    <div>
                        <span class="text-sm font-medium text-gray-500">Description:</span>
                        <p class="text-gray-900">{{ $returnRequest->description_demande }}</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="rounded-lg bg-white p-6 shadow-md">
            <h2 class="mb-4 text-xl font-semibold text-gray-800">État de la demande</h2>

            <div class="mb-4">
                <span class="text-sm font-medium text-gray-500">État actuel:</span>
                <p class="mt-1">
                    <span
                        class="@if ($returnRequest->state->label_etat_retour === "En attente")
                            bg-yellow-100
                            text-yellow-800
                        @elseif ($returnRequest->state->label_etat_retour === "Validé")
                            bg-green-100
                            text-green-800
                        @elseif ($returnRequest->state->label_etat_retour === "Refusé")
                            bg-red-100
                            text-red-800
                        @elseif ($returnRequest->state->label_etat_retour === "Réceptionné")
                            bg-blue-100
                            text-blue-800
                        @elseif ($returnRequest->state->label_etat_retour === "Remboursé")
                            bg-purple-100
                            text-purple-800
                        @else
                            bg-gray-100
                            text-gray-800
                        @endif inline-flex rounded-full px-3 py-1 text-sm font-semibold"
                    >
                        {{ $returnRequest->state->label_etat_retour }}
                    </span>
                </p>
            </div>

            <form method="POST" action="{{ route("sav.update-state", $returnRequest) }}" class="mt-6">
                @csrf
                <div>
                    <label for="id_etat_retour" class="mb-2 block text-sm font-medium text-gray-700">Modifier l'état</label>
                    <select
                        id="id_etat_retour"
                        name="id_etat_retour"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        required
                    >
                        @foreach ($availableStates as $state)
                            <option
                                value="{{ $state->id_etat_retour }}"
                                @selected($state->id_etat_retour === $returnRequest->id_etat_retour)
                            >
                                {{ $state->label_etat_retour }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <x-button type="submit" class="mt-8 w-full" size="lg">Mettre à jour l'état</x-button>
            </form>
        </div>
    </div>

    <div class="rounded-lg bg-white p-6 shadow-md">
        <h2 class="mb-4 text-xl font-semibold text-gray-800">Articles à retourner</h2>

        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Article</th>
                    <th class="px-4 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Référence</th>
                    <th class="px-4 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Détails</th>
                    <th class="px-4 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Quantité</th>
                    <th class="px-4 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Prix unitaire</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @foreach ($returnRequest->lines as $line)
                    <tr>
                        <td class="px-4 py-4 text-sm text-gray-900">
                            {{ $line->orderLine->reference->article->nom_article }}
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-500">
                            {{ $line->orderLine->reference->numero_reference }}
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-600">
                            @if ($line->orderLine->reference->bikeReference)
                                <div class="space-y-1">
                                    @if ($line->orderLine->size)
                                        <div class="text-xs">Taille: {{ $line->orderLine->size->nom_taille }}</div>
                                    @endif

                                    @if ($line->orderLine->reference->bikeReference->color)
                                        <div class="text-xs">
                                            Couleur: {{ $line->orderLine->reference->bikeReference->color->label_couleur }}
                                        </div>
                                    @endif

                                    @if ($line->orderLine->reference->bikeReference->ebike && $line->orderLine->reference->bikeReference->ebike->battery)
                                        <div class="text-xs">
                                            Batterie: {{ $line->orderLine->reference->bikeReference->ebike->battery->label_batterie }}
                                        </div>
                                    @endif
                                </div>
                            @else
                                <span class="text-xs text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-900">
                            {{ $line->quantite_retournee }}
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-900">
                            {{ number_format($line->orderLine->prix_unit_ligne, 2, ",", " ") }} €
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6 rounded-lg bg-white p-6 shadow-md">
        <h2 class="mb-4 text-xl font-semibold text-gray-800">Pièces jointes</h2>

        @if ($returnRequest->attachments->count() > 0)
            <div class="space-y-2">
                @foreach ($returnRequest->attachments as $attachment)
                    <div class="flex items-center justify-between rounded border border-gray-200 p-3">
                        <div class="flex items-center space-x-3">
                            <x-heroicon-o-paper-clip class="h-5 w-5 text-gray-400" />
                            <span class="text-sm font-medium text-gray-900">{{ $attachment->getFileName() }}</span>
                        </div>
                        <a
                            href="{{ route("sav.download-attachment", [$returnRequest, $attachment]) }}"
                            class="text-blue-600 hover:text-blue-800"
                            title="Télécharger"
                        >
                            <x-heroicon-o-arrow-down-tray class="h-5 w-5" />
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-sm text-gray-500">Aucune pièce jointe</p>
        @endif
    </div>
</x-staff-layout>
