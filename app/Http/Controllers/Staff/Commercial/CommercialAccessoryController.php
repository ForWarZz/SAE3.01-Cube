<?php

namespace App\Http\Controllers\Staff\Commercial;

use App\Http\Controllers\Controller;
use App\Http\Requests\Accessory\AccessoryUpdateRequest;
use App\Models\Accessory;
use App\Models\AccessoryMaterial;
use App\Models\Size;
use App\Services\Commercial\Accessory\CommercialAccessoryService;

class CommercialAccessoryController extends Controller
{
    public function __construct(
        private readonly CommercialAccessoryService $accessoryService,
    ) {}

    public function index()
    {
        $accessories = $this->accessoryService->getPaginatedAccessories();

        return view('staff.commercial.accessories.index', [
            'accessories' => $accessories,
        ]);
    }

    public function edit(Accessory $accessory)
    {
        $availableCategories = $this->accessoryService->getAccessoryCategories();
        $accessoryMaterials = AccessoryMaterial::all();
        $sizes = Size::all();

        return view('staff.commercial.accessories.edit', [
            'accessory' => $accessory,
            'categories' => $availableCategories,
            'accessoryMaterials' => $accessoryMaterials,
            'sizes' => $sizes,
            'accessorySizes' => $accessory->availableSizes->pluck('id_taille')->toArray(),
        ]);
    }

    public function update(AccessoryUpdateRequest $request, Accessory $accessory)
    {
        try {
            $validated = $request->validated();

            $this->accessoryService->updateAccessory($accessory, $validated);

            return redirect()
                ->route('commercial.accessories.index')
                ->with('success', 'Accessoire mis à jour avec succès.');
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }
}
