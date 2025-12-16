@php
    use Carbon\Carbon;
@endphp

<x-app-layout>
    <div class="py-12">
        <div class="mx-auto max-w-4xl space-y-6 px-8">
            <!-- Success Message -->
            <x-flash-message key="success" type="success" />

            <!-- Profile Information -->
            <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                <div class="p-6">
                    <div class="mb-6 flex items-center justify-between">
                        <h2 class="text-2xl font-bold text-gray-900">Mon profil</h2>
                        <a
                            href="{{ route("dashboard.profile.edit") }}"
                            class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition hover:bg-indigo-500 focus:bg-indigo-700"
                        >
                            Modifier
                        </a>
                    </div>

                    <div class="space-y-4">
                        <div class="grid grid-cols-3 gap-4">
                            <div class="text-sm font-medium text-gray-500">Civilité</div>
                            <div class="col-span-2 text-sm text-gray-900">{{ $client->civilite }}</div>
                        </div>

                        <div class="grid grid-cols-3 gap-4">
                            <div class="text-sm font-medium text-gray-500">Prénom</div>
                            <div class="col-span-2 text-sm text-gray-900">{{ $client->prenom_client }}</div>
                        </div>

                        <div class="grid grid-cols-3 gap-4">
                            <div class="text-sm font-medium text-gray-500">Nom</div>
                            <div class="col-span-2 text-sm text-gray-900">{{ $client->nom_client }}</div>
                        </div>

                        <div class="grid grid-cols-3 gap-4">
                            <div class="text-sm font-medium text-gray-500">Email</div>
                            <div class="col-span-2 text-sm text-gray-900">
                                {{ $client->email_client }}
                                @if ($client->google_id)
                                    <span
                                        class="ml-2 inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800"
                                    >
                                        <x-bi-google class="mr-1 h-3 w-3" />
                                        Compte Google
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-4">
                            <div class="text-sm font-medium text-gray-500">Date de naissance</div>
                            <div class="col-span-2 text-sm text-gray-900">
                                {{ $client->naissance_client ? $client->naissance_client->format("d/m/Y") : "Non renseignée" }}
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-4">
                            <div class="text-sm font-medium text-gray-500">Dernière connexion</div>
                            <div class="col-span-2 text-sm text-gray-900">
                                @if ($client->date_der_connexion)
                                    <x-date-local :date="$client->date_der_connexion" />
                                @else
                                    Jamais
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if (! $client->google_id)
                <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="p-6">
                        <h3 class="mb-4 text-lg font-semibold text-gray-900">Changer le mot de passe</h3>

                        <form method="POST" action="{{ route("dashboard.profile.password") }}">
                            @csrf
                            @method("PUT")

                            <div class="space-y-4">
                                <x-form-input
                                    type="password"
                                    name="current_password"
                                    id="current_password"
                                    label="Mot de passe actuel"
                                    required
                                />

                                <x-form-input type="password" name="password" id="password" label="Nouveau mot de passe" required />

                                <x-form-input
                                    type="password"
                                    name="password_confirmation"
                                    id="password_confirmation"
                                    label="Confirmer le mot de passe"
                                    required
                                />

                                <div class="flex justify-end">
                                    <button
                                        type="submit"
                                        class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition hover:bg-indigo-500 focus:bg-indigo-700"
                                    >
                                        Modifier le mot de passe
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @else
                <div class="overflow-hidden border border-blue-200 bg-blue-50 shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="mb-2 text-lg font-semibold text-blue-900">Mot de passe</h3>
                        <p class="text-sm text-blue-700">
                            Vous êtes connecté avec votre compte Google. Pour modifier votre mot de passe, veuillez gérer votre sécurité via
                            votre
                            <a href="https://myaccount.google.com/security" target="_blank" class="underline hover:text-blue-900">
                                compte Google
                            </a>
                            .
                        </p>
                    </div>
                </div>
            @endif

            <div class="overflow-hidden border-2 border-red-200 bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <x-flash-message key="delete_error" />

                    <h3 class="mb-4 text-lg font-semibold text-red-900">Supprimer mon compte</h3>

                    <div class="mb-4 rounded-md border border-red-200 bg-red-50 p-4">
                        <p class="mb-2 text-sm text-red-800">
                            <strong>Attention :</strong>
                            Cette action est irréversible. Toutes vos données seront définitivement supprimées :
                        </p>
                        <ul class="list-inside list-disc space-y-1 text-sm text-red-700">
                            <li>Informations personnelles</li>
                            <li>Adresses de livraison</li>
                            <li>Historique des commandes</li>
                            <li>Vélos enregistrés</li>
                            <li>Demandes de service</li>
                        </ul>
                        <p class="mt-2 text-sm text-red-800">
                            Conformément au RGPD, vous avez le droit de demander la suppression de toutes vos données.
                        </p>
                    </div>

                    <div class="flex gap-2">
                        <button
                            type="button"
                            onclick="document.getElementById('deleteModal').classList.remove('hidden')"
                            class="inline-flex items-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition hover:bg-red-500 focus:bg-red-700"
                        >
                            Supprimer mon compte
                        </button>

                        <a
                            href="{{ route("dashboard.profile.export") }}"
                            class="inline-flex items-center rounded-md border border-transparent bg-gray-600 px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition hover:bg-gray-500 focus:bg-gray-700"
                        >
                            Demander mes données (RGPD)
                        </a>
                    </div>
                </div>
            </div>

            <div class="flex justify-center">
                <a href="{{ route("dashboard.index") }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">
                    &larr; Retour au tableau de bord
                </a>
            </div>
        </div>
    </div>

    <div id="deleteModal" class="fixed inset-0 z-50 flex hidden h-full w-full items-center justify-center overflow-y-auto bg-black/75">
        <div class="w-md rounded-md border bg-white p-5 shadow-lg">
            <h3 class="mb-4 text-lg font-medium text-gray-900">Confirmer la suppression</h3>

            <form method="POST" action="{{ route("dashboard.profile.destroy") }}">
                @csrf
                @method("DELETE")

                <div class="space-y-4">
                    @if (! $client->google_id)
                        <x-form-input type="password" name="password" id="delete_password" label="Mot de passe" required />
                    @else
                        <div class="rounded-md border border-blue-200 bg-blue-50 p-3">
                            <p class="text-sm text-blue-700">Compte connecté via Google. Aucun mot de passe requis pour la suppression.</p>
                        </div>
                    @endif

                    <div class="flex items-center">
                        <input
                            type="checkbox"
                            name="confirmation"
                            id="confirmation"
                            required
                            value="1"
                            class="h-4 w-4 rounded border-gray-300 text-red-600 focus:ring-red-500"
                        />
                        <label for="confirmation" class="ml-2 block text-sm text-gray-900">
                            Je comprends que cette action est irréversible
                        </label>
                    </div>

                    <div class="flex justify-between gap-2">
                        <button
                            type="button"
                            onclick="document.getElementById('deleteModal').classList.add('hidden')"
                            class="flex-1 rounded-md bg-gray-300 px-2 py-3 text-gray-700 transition hover:bg-gray-400"
                        >
                            Annuler
                        </button>
                        <button type="submit" class="flex-1 rounded-md bg-red-600 px-2 py-3 text-white transition hover:bg-red-700">
                            Supprimer définitivement
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
