<x-commercial-layout>
    <div
        x-data="{
            showAddRefModal: false,
            showEditRefModal: false,
            showAddImagesModal: false,
            editingRef: null,
            imagesRef: null,
        }"
    >
        <div class="mb-6 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route("commercial.bikes.index") }}" class="text-gray-500 hover:text-gray-700">
                    <x-heroicon-o-arrow-left class="h-6 w-6" />
                </a>
                <h1 class="text-3xl font-bold text-gray-800">{{ $bike->nom_article }}</h1>
                @if ($isVae)
                    <span class="rounded-full bg-green-100 px-3 py-1 text-sm text-green-800">VAE</span>
                @endif
            </div>
            <button
                @click="showAddRefModal = true"
                class="flex items-center rounded bg-blue-600 px-4 py-2 text-white shadow transition hover:bg-blue-700"
            >
                <x-heroicon-o-plus class="mr-2 h-5 w-5" />
                Ajouter une référence
            </button>
        </div>

        <x-flash-message key="success" type="success" />

        @if ($errors->any())
            <div class="mb-4 rounded border-l-4 border-red-500 bg-red-100 p-4 text-red-700">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <div class="mb-6 rounded-lg bg-white p-6 shadow">
            <h2 class="mb-4 text-xl font-semibold text-gray-700">Informations du vélo</h2>
            <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                <div>
                    <span class="text-sm text-gray-500">Modèle</span>
                    <p class="font-medium">{{ $bike->bikeModel->nom_modele_velo }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-500">Catégorie</span>
                    <p class="font-medium">{{ $bike->category->nom_categorie }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-500">Millésime</span>
                    <p class="font-medium">{{ $bike->vintage->millesime_velo }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-500">Matériau cadre</span>
                    <p class="font-medium">{{ $bike->frameMaterial->label_materiau_cadre }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-500">Usage</span>
                    <p class="font-medium">{{ $bike->usage->label_usage }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-500">Prix</span>
                    <p class="font-medium">
                        @if ($bike->article->hasDiscount())
                            <span class="text-gray-400 line-through">{{ number_format($bike->prix_article, 2) }} €</span>
                            <span class="text-green-600">{{ number_format($bike->article->getDiscountedPrice(), 2) }} €</span>
                            <span class="ml-1 text-sm text-red-500">(-{{ $bike->article->pourcentage_remise }}%)</span>
                        @else
                            {{ number_format($bike->prix_article, 2) }} €
                        @endif
                    </p>
                </div>
            </div>

            <div class="mt-4">
                <span class="text-sm text-gray-500">Description</span>
                <p class="text-gray-700">{{ $bike->description_article }}</p>
            </div>
            <div class="mt-4">
                <span class="text-sm text-gray-500">En résumer</span>
                <p class="text-gray-700">{{ $bike->resumer_article }}</p>
            </div>
        </div>

        <div class="rounded-lg bg-white p-6 shadow">
            <h2 class="mb-4 text-xl font-semibold text-gray-700">
                Références
                <span class="ml-2 rounded-full bg-blue-100 px-2 py-0.5 text-sm text-blue-800">
                    {{ $bike->references->count() }}
                </span>
            </h2>

            <div class="space-y-6">
                @forelse ($bike->references as $reference)
                    <div class="rounded-lg border border-gray-200 p-4">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="mb-3 flex items-center space-x-4">
                                    <span class="font-mono text-lg font-bold text-gray-800">#{{ $reference->id_reference }}</span>
                                    <span class="rounded bg-gray-100 px-2 py-1 text-sm">
                                        {{ $reference->frame->label_cadre_velo }}
                                    </span>
                                    <span class="flex items-center space-x-1">
                                        <span
                                            class="inline-block h-4 w-4 rounded-full border border-gray-300"
                                            style="background-color: {{ $reference->color->hex }}"
                                        ></span>
                                        <span class="text-sm">{{ $reference->color->label_couleur }}</span>
                                    </span>
                                    @if ($reference->ebike && $reference->ebike->battery)
                                        <span class="rounded bg-green-100 px-2 py-1 text-sm text-green-800">
                                            {{ $reference->ebike->battery->label_batterie }}
                                        </span>
                                    @endif
                                </div>

                                <div class="mb-3">
                                    <span class="text-sm text-gray-500">Tailles :</span>
                                    <div class="mt-1 flex flex-wrap gap-1">
                                        @foreach ($reference->availableSizes as $size)
                                            <span class="rounded bg-blue-50 px-2 py-0.5 text-xs text-blue-700">
                                                {{ $size->nom_taille }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>

                                <div>
                                    <div class="mb-2 flex items-center justify-between">
                                        <span class="text-sm text-gray-500">
                                            Images ({{ count($referenceImages[$reference->id_reference] ?? []) }}/5)
                                        </span>
                                        <button
                                            @click="imagesRef = {{ $reference->id_reference }}; showAddImagesModal = true"
                                            class="text-sm text-blue-600 hover:text-blue-800"
                                        >
                                            + Ajouter des images
                                        </button>
                                    </div>
                                    <div class="flex flex-wrap gap-2">
                                        @forelse ($referenceImages[$reference->id_reference] ?? [] as $image)
                                            <div class="group relative">
                                                <img
                                                    src="{{ $image["url"] }}"
                                                    alt="Image {{ $image["name"] }}"
                                                    class="h-20 w-20 rounded border object-cover"
                                                />
                                                <form
                                                    action="{{ route("commercial.bikes.references.images.destroy", [$bike, $reference, $image["name"]]) }}"
                                                    method="POST"
                                                    class="absolute -top-2 -right-2 hidden group-hover:block"
                                                >
                                                    @csrf
                                                    @method("DELETE")
                                                    <button
                                                        type="submit"
                                                        class="rounded-full bg-red-500 p-1 text-white hover:bg-red-600"
                                                        onclick="return confirm('Supprimer cette image ?');"
                                                    >
                                                        <x-heroicon-o-x-mark class="h-3 w-3" />
                                                    </button>
                                                </form>
                                            </div>
                                        @empty
                                            <span class="text-sm text-gray-400">Aucune image</span>
                                        @endforelse
                                    </div>
                                </div>
                            </div>

                            <div class="ml-4 flex space-x-2">
{{--                                <button--}}
{{--                                    @click="editingRef = {{--}}
{{--                                        json_encode([--}}
{{--                                            "id_reference" => $reference->id_reference,--}}
{{--                                            "id_cadre_velo" => $reference->id_cadre_velo,--}}
{{--                                            "id_couleur" => $reference->id_couleur,--}}
{{--                                            "id_batterie" => $reference->ebike?->id_batterie,--}}
{{--                                            "sizes" => $reference->availableSizes->pluck("id_taille")->toArray(),--}}
{{--                                        ])--}}
{{--                                    }}; showEditRefModal = true"--}}
{{--                                    class="rounded bg-yellow-100 px-3 py-1 text-yellow-700 hover:bg-yellow-200"--}}
{{--                                    title="Modifier"--}}
{{--                                >--}}
{{--                                    <x-heroicon-o-pencil class="h-4 w-4" />--}}
                                </button>
                                @if ($bike->references->count() > 1)
                                    <form action="{{ route("commercial.bikes.references.destroy", [$bike, $reference]) }}" method="POST">
                                        @csrf
                                        @method("DELETE")
                                        <button
                                            type="submit"
                                            class="rounded bg-red-100 px-3 py-1 text-red-700 hover:bg-red-200"
                                            title="Supprimer"
                                            onclick="return confirm('Supprimer cette référence ? Cette action est irréversible.');"
                                        >
                                            <x-heroicon-o-trash class="h-4 w-4" />
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-gray-500">
                        Aucune référence pour ce vélo.
                        <button @click="showAddRefModal = true" class="text-blue-600 hover:underline">Ajouter la première référence</button>
                    </div>
                @endforelse
            </div>
        </div>

        <div
            x-show="showAddRefModal"
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
            @keydown.escape.window="showAddRefModal = false"
        >
            <div class="mx-4 w-full max-w-2xl rounded-lg bg-white p-6 shadow-xl" @click.away="showAddRefModal = false">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-xl font-semibold">Ajouter une référence</h3>
                    <button @click="showAddRefModal = false" class="text-gray-500 hover:text-gray-700">
                        <x-heroicon-o-x-mark class="h-6 w-6" />
                    </button>
                </div>

                <form action="{{ route("commercial.bikes.references.store", $bike) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Numéro de référence (optionnel)</label>
                            <input
                                type="number"
                                name="numero_reference"
                                class="w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Auto-généré si vide"
                            />
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Cadre *</label>
                            <select
                                name="id_cadre_velo"
                                required
                                class="w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                                <option value="">Sélectionner...</option>
                                @foreach ($frames as $frame)
                                    <option value="{{ $frame->id_cadre_velo }}">{{ $frame->label_cadre_velo }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Couleur *</label>
                            <select
                                name="id_couleur"
                                required
                                class="w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                                <option value="">Sélectionner...</option>
                                @foreach ($colors as $color)
                                    <option value="{{ $color->id_couleur }}">{{ $color->label_couleur }}</option>
                                @endforeach
                            </select>
                        </div>

                        @if ($isVae)
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">Batterie *</label>
                                <select
                                    name="id_batterie"
                                    required
                                    class="w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                >
                                    <option value="">Sélectionner...</option>
                                    @foreach ($batteries as $battery)
                                        <option value="{{ $battery->id_batterie }}">{{ $battery->label_batterie }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    </div>

                    <div class="mt-4">
                        <label class="mb-1 block text-sm font-medium text-gray-700">Tailles disponibles *</label>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($sizes as $size)
                                <label class="flex items-center space-x-1">
                                    <input
                                        type="checkbox"
                                        name="sizes[]"
                                        value="{{ $size->id_taille }}"
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                    />
                                    <span class="text-sm">{{ $size->nom_taille }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="mb-1 block text-sm font-medium text-gray-700">Images (max 5)</label>
                        <input
                            type="file"
                            name="images[]"
                            multiple
                            accept="image/jpeg,image/png,image/jpg,image/webp"
                            class="w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        />
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button
                            type="button"
                            @click="showAddRefModal = false"
                            class="rounded border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-50"
                        >
                            Annuler
                        </button>
                        <button type="submit" class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">Ajouter</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- <div --}}
        {{-- x-show="showEditRefModal" --}}
        {{-- x-cloak --}}
        {{-- class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" --}}
        {{-- @keydown.escape.window="showEditRefModal = false" --}}
        {{-- > --}}
        {{-- <div class="mx-4 w-full max-w-2xl rounded-lg bg-white p-6 shadow-xl" @click.away="showEditRefModal = false"> --}}
        {{-- <div class="mb-4 flex items-center justify-between"> --}}
        {{-- <h3 class="text-xl font-semibold">Modifier la référence</h3> --}}
        {{-- <button @click="showEditRefModal = false" class="text-gray-500 hover:text-gray-700"> --}}
        {{-- <x-heroicon-o-x-mark class="h-6 w-6" /> --}}
        {{-- </button> --}}
        {{-- </div> --}}

        {{-- <div class="mb-4 rounded bg-yellow-50 p-3 text-sm text-yellow-800"> --}}
        {{-- <strong>Note :</strong> --}}
        {{-- La modification d'une référence créera une nouvelle référence et archivera l'ancienne. --}}
        {{-- </div> --}}

        {{-- <form --}}
        {{-- :action="`{{ route("commercial.bikes.show", $bike) }}/references/${editingRef?.id_reference}`" --}}
        {{-- method="POST" --}}
        {{-- enctype="multipart/form-data" --}}
        {{-- > --}}
        {{-- @csrf --}}
        {{-- @method("PUT") --}}

        {{-- <div class="grid grid-cols-2 gap-4"> --}}
        {{-- <div> --}}
        {{-- <label class="mb-1 block text-sm font-medium text-gray-700">Cadre *</label> --}}
        {{-- <select --}}
        {{-- name="id_cadre_velo" --}}
        {{-- required --}}
        {{-- x-model="editingRef?.id_cadre_velo" --}}
        {{-- class="w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" --}}
        {{-- > --}}
        {{-- <option value="">Sélectionner...</option> --}}
        {{-- @foreach ($frames as $frame) --}}
        {{-- <option value="{{ $frame->id_cadre_velo }}">{{ $frame->nom_cadre_velo }}</option> --}}
        {{-- @endforeach --}}
        {{-- </select> --}}
        {{-- </div> --}}
        {{--  --}}
        {{-- <div> --}}
        {{-- <label class="mb-1 block text-sm font-medium text-gray-700">Couleur *</label> --}}
        {{-- <select --}}
        {{-- name="id_couleur" --}}
        {{-- required --}}
        {{-- x-model="editingRef?.id_couleur" --}}
        {{-- class="w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" --}}
        {{-- > --}}
        {{-- <option value="">Sélectionner...</option> --}}
        {{-- @foreach ($colors as $color) --}}
        {{-- <option value="{{ $color->id_couleur }}">{{ $color->nom_couleur }}</option> --}}
        {{-- @endforeach --}}
        {{-- </select> --}}
        {{-- </div> --}}
        {{--  --}}
        {{-- @if ($isVae) --}}
        {{-- <div> --}}
        {{-- <label class="mb-1 block text-sm font-medium text-gray-700">Batterie *</label> --}}
        {{-- <select --}}
        {{-- name="id_batterie" --}}
        {{-- required --}}
        {{-- x-model="editingRef?.id_batterie" --}}
        {{-- class="w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" --}}
        {{-- > --}}
        {{-- <option value="">Sélectionner...</option> --}}
        {{-- @foreach ($batteries as $battery) --}}
        {{-- <option value="{{ $battery->id_batterie }}">{{ $battery->capacite_batterie }} Wh</option> --}}
        {{-- @endforeach --}}
        {{-- </select> --}}
        {{-- </div> --}}
        {{-- @endif --}}
        {{-- </div> --}}
        {{--  --}}
        {{-- <div class="mt-4"> --}}
        {{-- <label class="mb-1 block text-sm font-medium text-gray-700">Tailles disponibles *</label> --}}
        {{-- <div class="flex flex-wrap gap-2"> --}}
        {{-- @foreach ($sizes as $size) --}}
        {{-- <label class="flex items-center space-x-1"> --}}
        {{-- <input --}}
        {{-- type="checkbox" --}}
        {{-- name="sizes[]" --}}
        {{-- value="{{ $size->id_taille }}" --}}
        {{-- x-bind:checked="editingRef?.sizes?.includes({{ $size->id_taille }})" --}}
        {{-- class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" --}}
        {{-- /> --}}
        {{-- <span class="text-sm">{{ $size->nom_taille }}</span> --}}
        {{-- </label> --}}
        {{-- @endforeach --}}
        {{-- </div> --}}
        {{-- </div> --}}
        {{--  --}}
        {{-- <div class="mt-4"> --}}
        {{-- <label class="mb-1 block text-sm font-medium text-gray-700">Ajouter des images supplémentaires</label> --}}
        {{-- <input --}}
        {{-- type="file" --}}
        {{-- name="images[]" --}}
        {{-- multiple --}}
        {{-- accept="image/jpeg,image/png,image/jpg,image/webp" --}}
        {{-- class="w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" --}}
        {{-- /> --}}
        {{-- </div> --}}

        {{-- <div class="mt-6 flex justify-end space-x-3"> --}}
        {{-- <button --}}
        {{-- type="button" --}}
        {{-- @click="showEditRefModal = false" --}}
        {{-- class="rounded border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-50" --}}
        {{-- > --}}
        {{-- Annuler --}}
        {{-- </button> --}}
        {{-- <button type="submit" class="rounded bg-yellow-600 px-4 py-2 text-white hover:bg-yellow-700">Modifier</button> --}}
        {{-- </div> --}}
        {{-- </form> --}}
        {{-- </div> --}}
        {{-- </div> --}}

        <div
            x-show="showAddImagesModal"
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
            @keydown.escape.window="showAddImagesModal = false"
        >
            <div class="mx-4 w-full max-w-md rounded-lg bg-white p-6 shadow-xl" @click.away="showAddImagesModal = false">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-xl font-semibold">Ajouter des images</h3>
                    <button @click="showAddImagesModal = false" class="text-gray-500 hover:text-gray-700">
                        <x-heroicon-o-x-mark class="h-6 w-6" />
                    </button>
                </div>

                <form
                    :action="`{{ route("commercial.bikes.show", $bike) }}/references/${imagesRef}/images`"
                    method="POST"
                    enctype="multipart/form-data"
                >
                    @csrf

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Images (max 5 au total par référence)</label>
                        <input
                            type="file"
                            name="images[]"
                            multiple
                            required
                            accept="image/jpeg,image/png,image/jpg,image/webp"
                            class="w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        />
                        <p class="mt-1 text-xs text-gray-500">Formats acceptés : JPEG, PNG, JPG, WebP. Max 2 Mo par image.</p>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button
                            type="button"
                            @click="showAddImagesModal = false"
                            class="rounded border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-50"
                        >
                            Annuler
                        </button>
                        <button type="submit" class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">Ajouter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-commercial-layout>
