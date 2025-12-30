@php
    use Carbon\Carbon;
@endphp

<x-staff-layout>
    <div x-data="{
        currentDate: '{{ $selectedDate }}',
        usersCount: {{ $usersCount }},
    }">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Conformité RGPD & DPO</h1>
                <p class="mt-1 text-sm text-gray-500">Gestion de la politique de rétention et registre des traitements.</p>
            </div>
        </div>
    </div>

    <x-flash-message key="success" type="success" />

    <div class="mb-8 rounded-lg border border-gray-200 bg-white shadow-sm">
        <div class="rounded-lg border-b border-gray-200 bg-gray-50 px-6 py-4">
            <h3 class="flex items-center text-lg leading-6 font-medium text-gray-900">
                <x-heroicon-o-user-minus class="mr-2 h-5 w-5 text-blue-500" />
                1. Droit à l'oubli : Clients inactifs (> 3 ans)
            </h3>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-2 gap-8">
                <div>
                    <form method="GET" id="dateFilterForm">
                        <label for="date" class="block text-sm font-medium text-gray-700">Seuil d'inactivité</label>
                        <p class="mb-2 text-xs text-gray-500">Comptes sans connexion avant le :</p>
                        <input
                            type="date"
                            name="date"
                            id="date"
                            value="{{ $selectedDate }}"
                            onchange="document.querySelector('#dateFilterForm').submit()"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        />
                    </form>
                </div>

                <div class="flex items-center justify-between rounded-lg border border-blue-100 bg-blue-50 p-4">
                    <div>
                        <p class="text-sm font-medium text-blue-800">Comptes à traiter</p>
                        <p class="text-2xl font-bold text-blue-600">{{ $usersCount }}</p>
                    </div>
                    <div class="rounded-full bg-blue-100 p-2 text-blue-600">
                        <x-heroicon-o-users class="size-6" />
                    </div>
                </div>
            </div>

            <div class="mt-6 border-t border-gray-100 pt-6">
                <form
                    action="{{ route("dpo.anonymize-client") }}"
                    method="POST"
                    onsubmit="return confirm('Confirmer l\'anonymisation de {{ $usersCount }} comptes ?');"
                >
                    @csrf
                    <input type="hidden" name="date_threshold" value="{{ $selectedDate }}" />

                    <x-button type="submit" size="md" :disabled="$usersCount == 0" icon="heroicon-o-user-minus">
                        Traiter les utilisateurs
                    </x-button>
                </form>
            </div>
        </div>
    </div>

    <div class="mb-8 rounded-lg border border-gray-200 bg-white shadow-sm">
        <div class="rounded-lg border-b border-gray-200 bg-gray-50 px-6 py-4">
            <h3 class="flex items-center text-lg leading-6 font-medium text-gray-900">
                <x-heroicon-o-archive-box-x-mark class="mr-2 h-5 w-5 text-red-500" />
                2. Purge Légale : Commandes expirées (> 10 ans)
            </h3>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-2 gap-8">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Date limite légale</label>
                    <p class="mb-2 text-xs text-gray-500">Fixée par l'article L123-22 (Code commerce) :</p>
                    <div class="mt-1 block w-full rounded-md border border-gray-200 bg-gray-100 px-3 py-2 text-gray-500 shadow-sm">
                        {{ Carbon::parse($legalDate)->format("d/m/Y") }}
                    </div>
                </div>

                <div class="flex items-center justify-between rounded-lg border border-red-100 bg-red-50 p-4">
                    <div>
                        <p class="text-sm font-medium text-red-800">Archives à détruire</p>
                        <p class="text-2xl font-bold text-red-600">{{ $expiredOrdersCount }}</p>
                    </div>
                    <div class="rounded-full bg-red-100 p-2 text-red-600">
                        <x-heroicon-o-trash class="size-6" />
                    </div>
                </div>
            </div>

            <div class="mt-6 border-t border-gray-100 pt-6">
                <form
                    action="{{ route("dpo.delete-expired-orders") }}"
                    method="POST"
                    onsubmit="return confirm('ATTENTION : Suppression DÉFINITIVE des {{ $expiredOrdersCount }} archives. Continuer ?');"
                >
                    @csrf
                    <x-button type="submit" color="red" size="md" :disabled="$expiredOrdersCount == 0" icon="heroicon-o-trash">
                        Détruire les archives
                    </x-button>
                </form>
            </div>
        </div>
    </div>
</x-staff-layout>
