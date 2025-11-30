<div class="mt-16 border-t border-gray-200 pt-12">
    <h2 class="mb-8 text-2xl font-bold text-gray-900">Fiche technique</h2>

    <div class="flex flex-col gap-4">
        @foreach ($characteristics as $type => $group)
            <div>
                <h3 class="mb-4 border-b-2 border-gray-300 pb-2 text-center text-lg font-bold tracking-wide text-gray-900 uppercase">
                    {{ $type }}
                </h3>

                <div class="divide-y divide-gray-200">
                    @foreach ($group as $char)
                        <div class="flex px-4 py-3">
                            <span class="w-1/4 font-semibold text-gray-900">
                                {{ $char->nom_caracteristique }}
                            </span>
                            <span class="text-gray-700">
                                {{ $char->pivot->valeur_caracteristique }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>
