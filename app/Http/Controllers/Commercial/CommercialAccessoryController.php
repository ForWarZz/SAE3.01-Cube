<?php

namespace App\Http\Controllers\Commercial;

use App\Http\Controllers\Controller;
use App\Http\Requests\Accessory\AccessoryUpdateRequest;
use App\Models\Accessory;
use App\Services\Commercial\Accessory\CommercialAccessoryService;

class CommercialAccessoryController extends Controller
{
    public function __construct(
        private readonly CommercialAccessoryService $accessoryService,
    ) {}

    public function index()
    {
        $accessories = $this->accessoryService->getPaginatedAccessories();

        return view('commercial.accessories.index', [
            'accessories' => $accessories,
        ]);
    }

    public function edit(Accessory $accessory)
    {
        $accessory->load([
            'article',
            'category',
        ]);

        $availableCategories = $this->accessoryService->getAccessoryCategories();

        return view('commercial.accessories.edit', [
            'accessory' => $accessory,
            'categories' => $availableCategories,
        ]);
    }

    public function update(AccessoryUpdateRequest $request, Accessory $accessory)
    {
        $validated = $request->validated();

        $this->accessoryService->updateAccessory($accessory, $validated);

        return redirect()
            ->route('commercial.accessories.index')
            ->with('success', 'Accessoire mis à jour avec succès.');
    }
}
