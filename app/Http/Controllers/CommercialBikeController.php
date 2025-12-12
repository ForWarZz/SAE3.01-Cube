<?php

namespace App\Http\Controllers;

use App\Http\Requests\BikeCreateRequest;
use App\Models\Bike;
use App\Models\BikeFrame;
use App\Models\BikeFrameMaterial;
use App\Models\BikeModel;
use App\Models\BikeReference;
use App\Models\Category;
use App\Models\Color;
use App\Models\Size;
use App\Models\Usage;
use App\Models\Vintage;
use Illuminate\Support\Facades\DB;

class CommercialBikeController extends Controller
{
    public function index()
    {
        $bikes = Bike::with(['bikeModel', 'category', 'frameMaterial', 'vintage', 'usage', 'article', 'references'])
            ->orderBy('id_article', 'desc')
            ->paginate(10);

        return view('commercial.bikes.index', compact('bikes'));
    }

    public function create()
    {
        $models = BikeModel::orderBy('nom_modele_velo')->get();
        $categories = Category::with(['parent', 'children'])
            ->whereDoesntHave('children')
            ->get()
            ->sortBy(fn (Category $cat) => $cat->getFullPath(), SORT_NATURAL);

        $materials = BikeFrameMaterial::all();
        $vintages = Vintage::orderBy('millesime_velo', 'desc')->get();
        $usages = Usage::all();
        $frames = BikeFrame::all();
        $colors = Color::all();
        $sizes = Size::all();

        return view('commercial.bikes.create', compact(
            'models', 'categories', 'materials', 'vintages',
            'usages', 'frames', 'colors', 'sizes'
        ));
    }

    public function store(BikeCreateRequest $request)
    {
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            if ($validated['model_choice'] === 'new') {
                $bikeModel = BikeModel::firstOrCreate([
                    'nom_modele_velo' => $validated['new_model_name'],
                ]);

                $modelId = $bikeModel->id_modele_velo;

            } else {
                $modelId = $validated['id_modele_velo'];
            }

            $result = DB::selectOne('SELECT fn_create_muscular_bike(?, ?, ?, ?, ?, ?, ?, ?, ?, ?) as id_article',
                [
                    $validated['nom_article'],
                    $validated['description_article'],
                    $validated['resumer_article'],
                    $validated['prix_article'],
                    $validated['pourcentage_remise'] ?? 0,
                    $validated['id_categorie'],
                    $modelId,
                    $validated['id_materiau_cadre'],
                    $validated['id_millesime'],
                    $validated['id_usage'],
                ]);

            $articleId = $result->id_article;

            foreach ($validated['references'] as $refData) {
                $forcedId = $refData['id_reference'] ?? null;

                $refResult = DB::selectOne('SELECT fn_add_bike_reference(?, ?, ?, ?) as id_ref',
                    [
                        $forcedId,
                        $articleId,
                        $refData['id_cadre_velo'],
                        $refData['id_couleur'],
                    ]);

                $referenceId = $refResult->id_ref;

                if (! empty($refData['sizes'])) {
                    $bikeRef = BikeReference::find($referenceId);
                    $bikeRef->availableSizes()->attach(
                        $refData['sizes'],
                        ['dispo_en_ligne' => true]
                    );
                }
            }

            DB::commit();

            return redirect()
                ->route('commercial.bikes.index')
                ->with('success', 'Vélo créé avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Supprime une référence
     */
    public function deleteReference(BikeReference $reference)
    {
        try {
            DB::beginTransaction();

            $reference->availableSizes()->detach();
            $reference->delete();

            DB::commit();

            return back()->with('success', 'La référence a été supprimée.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors(['error' => 'Erreur lors de la suppression : '.$e->getMessage()]);
        }
    }
}
