<?php

namespace App\Http\Controllers\Staff\Technical;

use App\Http\Controllers\Controller;
use App\Http\Requests\Technical\AddGeometryCharacteristicRequest;
use App\Http\Requests\Technical\UpdateGeometryRequest;
use App\Models\BikeModel;
use App\Services\Technical\GeometryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use RuntimeException;
use Throwable;

class TechnicalGeometryController extends Controller
{
    public function __construct(
        private readonly GeometryService $geometryService
    ) {}

    public function index(): View
    {
        $models = BikeModel::orderBy('nom_modele_velo')->paginate(15);

        return view('staff.technical.geometry.index', [
            'models' => $models,
        ]);
    }

    public function edit(BikeModel $model): View
    {
        $data = $this->geometryService->getModelEditData($model);

        return view('staff.technical.geometry.edit', $data);
    }

    public function update(UpdateGeometryRequest $request, BikeModel $model): RedirectResponse
    {
        $this->geometryService->updateGeometries($model, $request->input('geo', []));

        return redirect()
            ->route('technical.geometry.edit', $model)
            ->with('success', 'Modifications enregistrées.');
    }

    public function addCharacteristic(AddGeometryCharacteristicRequest $request, BikeModel $model): RedirectResponse
    {
        try {
            $this->geometryService->addCharacteristic(
                $model,
                $request->input('action_type'),
                $request->input('existing_id'),
                $request->input('new_name')
            );

            return redirect()
                ->route('technical.geometry.edit', $model)
                ->with('success', 'Caractéristique ajoutée à la matrice.');
        } catch (RuntimeException|Throwable $e) {
            return redirect()
                ->route('technical.geometry.edit', $model)
                ->with(['error' => $e->getMessage()]);
        }
    }
}
