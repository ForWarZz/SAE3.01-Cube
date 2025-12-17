<?php

namespace App\Http\Controllers\Commercial;

use App\Http\Controllers\Controller;
use App\Http\Requests\Bike\BikeModelCreateRequest;
use App\Models\BikeModel;

class CommercialModelController extends Controller
{
    public function index()
    {
        $models = BikeModel::orderBy('id_modele_velo', 'desc')->paginate(10);

        return view('commercial.models', compact('models'));
    }

    public function store(BikeModelCreateRequest $request)
    {
        $validated = $request->validated();
        BikeModel::create($validated);

        return redirect()->back();
    }
}
