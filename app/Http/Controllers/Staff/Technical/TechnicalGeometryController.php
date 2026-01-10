<?php

namespace App\Http\Controllers\Staff\Technical;

use App\Http\Controllers\Controller;
use App\Models\BikeModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TechnicalGeometryController extends Controller
{
    public function index()
    {
        $models = BikeModel::orderBy('nom_modele_velo')->paginate(12);
        return view('staff.technical.geometry.index', compact('models'));
    }

    public function edit($modelId)
    {
        $model = BikeModel::findOrFail($modelId);

        $sizes = DB::table('taille')
            ->join('taille_dispo', 'taille.id_taille', '=', 'taille_dispo.id_taille')
            ->join('reference_article', 'taille_dispo.id_reference', '=', 'reference_article.id_reference')
            ->join('velo', 'reference_article.id_article', '=', 'velo.id_article')
            ->where('velo.id_modele_velo', $modelId)
            ->distinct()
            ->orderBy('taille.id_taille')
            ->select('taille.*')
            ->get();

        if ($sizes->isEmpty()) {
            $sizes = DB::table('taille')->orderBy('id_taille')->get();
        }

        $existingValues = DB::table('de_geometrie')
            ->where('id_modele_velo', $modelId)
            ->get();

        $usedCaracIds = $existingValues->pluck('id_carac_geo')->unique();

        $matrixCharacteristics = DB::table('caracteristique_geometrie')
            ->whereIn('id_carac_geo', $usedCaracIds)
            ->orderBy('id_carac_geo')
            ->get();

        $availableCharacteristics = DB::table('caracteristique_geometrie')
            ->whereNotIn('id_carac_geo', $usedCaracIds)
            ->orderBy('label_carac_geo')
            ->get();

        $matrix = [];
        foreach ($existingValues as $row) {
            $matrix[$row->id_carac_geo][$row->id_taille] = $row->valeur_carac;
        }

        return view('staff.technical.geometry.edit', compact(
            'model', 
            'matrixCharacteristics', 
            'availableCharacteristics', 
            'sizes', 
            'matrix'
        ));
    }

    public function update(Request $request, $modelId)
    {
        $data = $request->input('geo', []);

        DB::transaction(function () use ($modelId, $data) {
            DB::table('de_geometrie')->where('id_modele_velo', $modelId)->delete();

            $insertData = [];
            foreach ($data as $caracId => $sizes) {
                foreach ($sizes as $sizeId => $value) {
                    if ($value !== null && $value !== '') {
                        $insertData[] = [
                            'id_modele_velo' => $modelId,
                            'id_carac_geo'   => $caracId,
                            'id_taille'      => $sizeId,
                            'valeur_carac'   => $value
                        ];
                    }
                }
            }

            if (!empty($insertData)) {
                DB::table('de_geometrie')->insert($insertData);
            }
        });

        return redirect()->route('technical.geometry.edit', $modelId)
            ->with('success', 'Modifications enregistrées.');
    }

    public function addCharacteristic(Request $request, $modelId)
    {
        $request->validate([
            'action_type' => 'required|in:existing,new',
            'existing_id' => 'required_if:action_type,existing',
            'new_name' => 'required_if:action_type,new|nullable|string|max:255',
        ]);

        $caracId = null;

        if ($request->action_type === 'existing') {
            $caracId = $request->existing_id;
        } else {
            $existingCarac = DB::table('caracteristique_geometrie')
                ->where('label_carac_geo', $request->new_name)
                ->first();

            if ($existingCarac) {
                $caracId = $existingCarac->id_carac_geo;
            } else {
                $caracId = DB::table('caracteristique_geometrie')->insertGetId(
                    ['label_carac_geo' => $request->new_name],
                    'id_carac_geo'
                );
            }
        }

        $firstSize = DB::table('taille')->first(); 
        
        DB::table('de_geometrie')->insertOrIgnore([
            'id_modele_velo' => $modelId,
            'id_carac_geo' => $caracId,
            'id_taille' => $firstSize->id_taille,
            'valeur_carac' => null 
        ]);

        return redirect()->route('technical.geometry.edit', $modelId)
            ->with('success', 'Caractéristique ajoutée à la matrice.');
    }
}