<x-commercial-layout>
    
    <div x-data="{ showModal: {{ $errors->any() ? 'true' : 'false' }} }">

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Gestion des Modèles</h1>
            <button @click="showModal = true" class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 transition flex items-center">
                <span class="text-xl mr-2">+</span> Nouveau Modèle
            </button>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <table class="min-w-full text-left text-sm font-light">
                    <thead class="border-b bg-gray-50 font-medium">
                        <tr>
                            <th class="px-6 py-4">ID</th>
                            <th class="px-6 py-4">Nom du Modèle</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($models as $velo)
                        <tr class="border-b hover:bg-gray-100 transition">
                            <td class="whitespace-nowrap px-6 py-4 font-medium">{{ $velo->id_modele_velo }}</td>
                            <td class="whitespace-nowrap px-6 py-4 font-bold text-gray-700">{{ $velo->nom_modele_velo }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4">{{ $models->links() }}</div>
            </div>
        </div>

        <div x-show="showModal" style="display: none;" 
             class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm overflow-y-auto h-full w-full z-50 flex items-center justify-center">
            
            <div class="relative bg-white rounded-lg shadow-xl p-8 w-full max-w-md mx-4" @click.away="showModal = false">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Ajouter un Modèle</h2>
                    <button @click="showModal = false" class="text-gray-500 hover:text-gray-700">✕</button>
                </div>

                <form action="{{ route('commercial.models.store') }}" method="POST">
                    @csrf
                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Nom du Modèle <span class="text-red-500">*</span></label>
                        <input type="text" name="nom_modele_velo" value="{{ old('nom_modele_velo') }}" required placeholder="Ex: Kathmandu Hybrid"
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('nom_modele_velo') border-red-500 @enderror">
                        
                        @error('nom_modele_velo') 
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> 
                        @enderror
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" @click="showModal = false" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">Annuler</button>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-commercial-layout>