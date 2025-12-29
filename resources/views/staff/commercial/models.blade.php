<x-staff-layout>
    <div x-data="{ showModal: {{ $errors->any() ? "true" : "false" }} }">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-3xl font-bold text-gray-800">Gestion des Modèles</h1>
            <x-button @click="showModal = true" icon="heroicon-o-plus">Nouveau Modèle</x-button>
        </div>

        @if (session("success"))
            <div class="mb-4 border-l-4 border-green-500 bg-green-100 p-4 text-green-700">
                {{ session("success") }}
            </div>
        @endif

        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
            <div class="border-b border-gray-200 bg-white p-6">
                <table class="min-w-full text-left text-sm font-light">
                    <thead class="border-b bg-gray-50 font-medium">
                        <tr>
                            <th class="px-6 py-4">ID</th>
                            <th class="px-6 py-4">Nom du Modèle</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($models as $velo)
                            <tr class="border-b transition hover:bg-gray-100">
                                <td class="px-6 py-4 font-medium whitespace-nowrap">{{ $velo->id_modele_velo }}</td>
                                <td class="px-6 py-4 font-bold whitespace-nowrap text-gray-700">{{ $velo->nom_modele_velo }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4">{{ $models->links() }}</div>
            </div>
        </div>

        <div
            x-show="showModal"
            style="display: none"
            class="fixed inset-0 z-50 flex h-full w-full items-center justify-center overflow-y-auto bg-gray-900/40 backdrop-blur-sm"
        >
            <div class="relative mx-4 w-full max-w-md rounded-lg bg-white p-8 shadow-xl" @click.away="showModal = false">
                <div class="mb-6 flex items-center justify-between">
                    <h2 class="text-2xl font-bold text-gray-800">Ajouter un Modèle</h2>
                    <button @click="showModal = false" class="text-gray-500 hover:text-gray-700">✕</button>
                </div>

                <form action="{{ route("commercial.models.store") }}" method="POST">
                    @csrf
                    <div class="mb-6">
                        <label class="mb-2 block text-sm font-bold text-gray-700">
                            Nom du Modèle
                            <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="nom_modele_velo"
                            value="{{ old("nom_modele_velo") }}"
                            required
                            placeholder="Ex: Kathmandu Hybrid"
                            class="@error("nom_modele_velo") @enderror w-full appearance-none rounded border border-red-500 px-3 py-2 text-gray-700 shadow focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        />

                        @error("nom_modele_velo")
                            <p class="mt-1 text-xs text-red-500 italic">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end space-x-3">
                        <x-button @click="showModal = false" color="gray" size="sm" class="!px-4 !py-2">Annuler</x-button>
                        <x-button type="submit" size="sm">Enregistrer</x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-staff-layout>
