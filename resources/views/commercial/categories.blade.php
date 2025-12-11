<x-commercial-layout>
    
    <div x-data="{ showModal: {{ $errors->any() ? 'true' : 'false' }} }">

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Gestion des Catégories</h1>
            
            <button @click="showModal = true" class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 transition flex items-center">
                <span class="text-xl mr-2">+</span> Nouvelle Catégorie
            </button>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <table class="min-w-full text-left text-sm font-light">
                    <thead class="border-b bg-gray-50 font-medium">
                        <tr>
                            <th class="px-6 py-4">ID</th>
                            <th class="px-6 py-4">Nom</th>
                            <th class="px-6 py-4">Parent</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $category)
                        <tr class="border-b hover:bg-gray-100 transition">
                            <td class="whitespace-nowrap px-6 py-4 font-medium">{{ $category->id_categorie }}</td>
                            <td class="whitespace-nowrap px-6 py-4 font-bold text-gray-700">{{ $category->nom_categorie }}</td>
                            <td class="whitespace-nowrap px-6 py-4">
                                @if($category->parent)
                                    <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded">{{ $category->parent->nom_categorie }}</span>
                                @else
                                    <span class="text-gray-400 italic text-xs">-- Principale --</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4">{{ $categories->links() }}</div>
            </div>
        </div>

        <div x-show="showModal" 
             style="display: none;"
             class="fixed inset-0 bg-gray-900/50 overflow-y-auto h-full w-full z-50 flex items-center justify-center"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">

            <div class="relative bg-white rounded-lg shadow-xl p-8 w-full max-w-md mx-4"
                 @click.away="showModal = false"> <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Ajouter une catégorie</h2>
                    <button @click="showModal = false" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form action="{{ route('commercial.categories.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="nom">
                            Nom de la catégorie <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nom_categorie" id="nom" required
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Nom catégorie...">

                        @error('nom_categorie')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="parent">
                            Catégorie Parente
                        </label>
                        
                        <select name="id_categorie_parent" id="parent" 
                                class="shadow border rounded w-full py-2 px-3 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            
                            <option value="">-- Aucune (Catégorie principale) --</option>
                            
                            @foreach($allCategories as $cat)
                                <option value="{{ $cat->id_categorie }}">
                                    {{ $cat->getFullPath() }}
                                </option>
                            @endforeach

                        </select>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" @click="showModal = false" 
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                            Annuler
                        </button>
                        <button type="submit" 
                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow">
                            Enregistrer
                        </button>
                    </div>
                </form>

            </div>
        </div>

    </div>
</x-commercial-layout>