<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BikeModel;

class CommercialModelController extends Controller
{
    public function index()
    {
        $models = BikeModel::orderBy('id_modele_velo', 'desc')->paginate(10);

        return view('commercial.models', compact('models'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom_modele_velo' => 'required|string|max:50|unique:modele_velo,nom_modele_velo'
        ], [
            'nom_modele_velo.unique' => 'Ce modèle de vélo existe déjà.'
        ]);

        BikeModel::create([
            'nom_modele_velo' => $request->nom_modele_velo
        ]);

        return redirect()->back();
    }
}
