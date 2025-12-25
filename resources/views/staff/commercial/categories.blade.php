<x-staff-layout>
    <div x-data="{ showModal: {{ $errors->any() ? "true" : "false" }} }">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-3xl font-bold text-gray-800">Gestion des Catégories</h1>

            <button
                @click="showModal = true"
                class="flex items-center rounded bg-blue-600 px-4 py-2 text-white shadow transition hover:bg-blue-700"
            >
                <span class="mr-2 text-xl">+</span>
                Nouvelle Catégorie
            </button>
        </div>

        @if (session("success"))
            <div class="mb-4 border-l-4 border-green-500 bg-green-100 p-4 text-green-700" role="alert">
                {{ session("success") }}
            </div>
        @endif

        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
            <div class="border-b border-gray-200 bg-white p-6">
                <table class="min-w-full text-left text-sm font-light">
                    <thead class="border-b bg-gray-50 font-medium">
                        <tr>
                            <th class="px-6 py-4">ID</th>
                            <th class="px-6 py-4">Nom</th>
                            <th class="px-6 py-4">Parent</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $category)
                            <tr class="border-b transition hover:bg-gray-100">
                                <td class="px-6 py-4 font-medium whitespace-nowrap">{{ $category->id_categorie }}</td>
                                <td class="px-6 py-4 font-bold whitespace-nowrap text-gray-700">{{ $category->nom_categorie }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($category->parent)
                                        <span class="rounded bg-blue-100 px-2.5 py-0.5 text-xs font-semibold text-blue-800">
                                            {{ $category->parent->getFullPath() }}
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400 italic">-- Principale --</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4">{{ $categories->links() }}</div>
            </div>
        </div>

        <div
            x-show="showModal"
            style="display: none"
            class="fixed inset-0 z-50 flex h-full w-full items-center justify-center overflow-y-auto bg-gray-900/50"
            x-transition:enter="transition duration-300 ease-out"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition duration-200 ease-in"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
        >
            <div class="relative mx-4 w-full max-w-md rounded-lg bg-white p-8 shadow-xl" @click.away="showModal = false">
                <div class="mb-6 flex items-center justify-between">
                    <h2 class="text-2xl font-bold text-gray-800">Ajouter une catégorie</h2>
                    <button @click="showModal = false" class="text-gray-500 hover:text-gray-700">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form action="{{ route("commercial.categories.store") }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label class="mb-2 block text-sm font-bold text-gray-700" for="nom">
                            Nom de la catégorie
                            <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="nom_categorie"
                            id="nom"
                            required
                            class="w-full appearance-none rounded border px-3 py-2 leading-tight text-gray-700 shadow focus:ring-2 focus:ring-blue-500 focus:outline-none"
                            placeholder="Nom catégorie..."
                        />

                        @error("nom_categorie")
                            <p class="mt-1 text-xs text-red-500 italic">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label class="mb-2 block text-sm font-bold text-gray-700" for="parent">Catégorie Parente</label>

                        <select
                            name="id_categorie_parent"
                            id="parent"
                            class="w-full rounded border bg-white px-3 py-2 shadow focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        >
                            <option value="">-- Aucune (Catégorie principale) --</option>

                            @foreach ($allCategories as $cat)
                                <option value="{{ $cat->id_categorie }}">
                                    {{ $cat->getFullPath() }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button
                            type="button"
                            @click="showModal = false"
                            class="rounded bg-gray-300 px-4 py-2 font-bold text-gray-800 hover:bg-gray-400"
                        >
                            Annuler
                        </button>
                        <button type="submit" class="rounded bg-blue-600 px-4 py-2 font-bold text-white shadow hover:bg-blue-700">
                            Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-staff-layout>
