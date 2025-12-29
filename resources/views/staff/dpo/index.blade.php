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

    <div class="mb-8 overflow-hidden border border-gray-200 bg-white shadow-sm sm:rounded-lg">
        <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
            <h3 class="flex items-center text-lg leading-6 font-medium text-gray-900">
                <svg class="mr-2 h-5 w-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                    ></path>
                </svg>
                Politique de Rétention des Données (Anonymisation)
            </h3>
        </div>

        <div class="p-6">
            <form method="GET" id="dateFilterForm" class="mb-6">
                <div class="grid grid-cols-1 items-end gap-6 md:grid-cols-2 lg:grid-cols-3">
                    <div>
                        <label for="date" class="block text-sm font-medium text-gray-700">Date limite d'inactivité</label>
                        <p class="mb-2 text-xs text-gray-500">Anonymiser les utilisateurs inactifs avant le :</p>
                        <input
                            type="date"
                            name="date"
                            id="date"
                            value="{{ $selectedDate }}"
                            onchange="document.getElementById('dateFilterForm').submit()"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                        />
                        <x-input-error :messages="$errors->get('date_threshold')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-between rounded-lg border border-red-100 bg-red-50 p-4">
                        <div>
                            <p class="text-sm font-medium text-red-800">Comptes à nettoyer</p>
                            <p class="text-2xl font-bold text-red-600">{{ $usersCount }}</p>
                        </div>
                        <div class="rounded-full bg-red-100 p-2 text-red-600">
                            <x-heroicon-o-trash class="size-6" />
                        </div>
                    </div>

                    <div class="flex h-full justify-end"></div>
                </div>
            </form>

            <div class="mt-4 flex items-center justify-between border-t border-gray-100 pt-4">
                <form
                    action="{{ route("dpo.anonymize-client") }}"
                    method="POST"
                    onsubmit="
                        return confirm(
                            'Êtes-vous sûr de vouloir anonymiser définitivement ces {{ $usersCount }} comptes ? Cette action est irréversible.',
                        );
                    "
                >
                    @csrf
                    <input type="hidden" name="date_threshold" value="{{ $selectedDate }}" />

                    <x-button type="submit" color="red" :disabled="$usersCount == 0" icon="heroicon-o-trash" class="!px-4 !py-2">
                        Exécuter l'anonymisation
                    </x-button>
                </form>
            </div>
        </div>
    </div>
</x-staff-layout>
