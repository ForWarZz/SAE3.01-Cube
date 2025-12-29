@props(['article'])

@php
    use Illuminate\Support\Facades\DB;

    $sizes = DB::table('taille')
        ->join('taille_dispo', 'taille.id_taille', '=', 'taille_dispo.id_taille')
        ->join('reference_article', 'taille_dispo.id_reference', '=', 'reference_article.id_reference')
        ->where('reference_article.id_article', $article->id_article)
        ->select('taille.nom_taille', 'taille.taille_min', 'taille.taille_max')
        ->distinct()
        ->get();
@endphp

<div 
    id="bike-calculator-container"
    data-chart='@json($sizes)' 
    class="my-12 rounded-lg border border-gray-200 bg-gray-50 p-8 shadow-sm"
>
    <div class="flex flex-col gap-8 md:flex-row">
        
        <div class="md:w-1/3">
            <h2 class="mb-2 text-2xl font-bold text-gray-900">Calculateur de taille</h2>
            <p class="text-sm text-gray-600">
                Renseignez vos mensurations pour obtenir une recommandation précise.
            </p>
            
            <div class="mt-4 rounded bg-blue-100 p-3 text-xs text-blue-800">
                <strong>💡 Comment mesurer ?</strong>
                <ul class="list-disc list-inside mt-1 space-y-1">
                    <li>Tenez-vous droit, pieds nus, légèrement écartés.</li>
                    <li>Mesurez du sol jusqu'au périnée (haut de l'intérieur des cuisses).</li>
                </ul>
            </div>
        </div>

        <div class="flex flex-col justify-center gap-6 md:w-2/3">
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="client-height" class="mb-1 block text-sm font-medium text-gray-700">Votre taille (cm)</label>
                    <input 
                        type="number" 
                        id="client-height" 
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors" 
                        placeholder="Taille..."
                        min="100" max="250"
                    >
                </div>

                <div>
                    <label for="client-inseam" class="mb-1 block text-sm font-medium text-gray-700">Hauteur d'entrejambe (cm)</label>
                    <input 
                        type="number" 
                        id="client-inseam" 
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors" 
                        placeholder="Hauteur d'entrejambe..."
                        min="50" max="120"
                    >
                </div>
            </div>

            <button 
                id="calc-btn"
                class="w-full rounded bg-gray-900 px-6 py-2 font-bold text-white hover:bg-gray-800 transition mt-2"
            >
                Calculer ma taille
            </button>

            <div id="result-box" class="hidden mt-2 rounded-md border-l-4 bg-white p-4 shadow-sm transition-all duration-300">
                <p class="text-sm font-semibold text-gray-500">Résultat :</p>
                <p id="result-text" class="text-xl font-bold text-gray-900">--</p>
                <p id="result-details" class="text-sm text-gray-600 mt-1 hidden"></p>
            </div>

        </div>
    </div>
</div>

@vite(['resources/js/bikesize/main.js'])