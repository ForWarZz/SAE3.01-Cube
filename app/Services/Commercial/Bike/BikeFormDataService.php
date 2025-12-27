<?php

namespace App\Services\Commercial\Bike;

use App\Models\Battery;
use App\Models\Bike;
use App\Models\BikeFrame;
use App\Models\BikeFrameMaterial;
use App\Models\BikeModel;
use App\Models\Category;
use App\Models\Color;
use App\Models\EBikeType;
use App\Models\Size;
use App\Models\Usage;
use App\Models\Vintage;
use Illuminate\Support\Collection;

class BikeFormDataService
{
    public function getCreateFormData(): array
    {
        $models = $this->getModels();
        $bikesByModel = $this->getBikesByModel();

        return [
            'models' => $models,
            'modelsCategory' => $this->buildModelsCategory($models, $bikesByModel),
            'categories' => $this->getCategories(),
            'bikeCategories' => $this->getBikeCategories(),
            'eBikeCategories' => $this->getEBikeCategories(),
            'materials' => $this->getMaterials(),
            'vintages' => $this->getVintages(),
            'usages' => $this->getUsages(),
            'frames' => $this->getFrames(),
            'colors' => $this->getColors(),
            'sizes' => $this->getSizes(),
            'batteries' => $this->getBatteries(),
            'eBikeTypes' => $this->getEBikeTypes(),
        ];
    }

    public function getModels(): Collection
    {
        return BikeModel::orderBy('nom_modele_velo')->get();
    }

    /**
     * @return Collection<int, Bike>
     */
    private function getBikesByModel(): Collection
    {
        return Bike::with('category')
            ->get()
            ->keyBy('id_modele_velo');
    }

    /**
     * @param  Collection<int, BikeModel>  $models
     * @param  Collection<int, Bike>  $bikesByModel
     */
    private function buildModelsCategory(Collection $models, Collection $bikesByModel): Collection
    {
        return $models->mapWithKeys(function (BikeModel $bikeModel) use ($bikesByModel) {
            /** @var Bike|null $bike */
            $bike = $bikesByModel->get($bikeModel->id_modele_velo);

            return [
                $bikeModel->id_modele_velo => $bike?->category?->id_categorie,
            ];
        });
    }

    public function getCategories(): Collection
    {
        return Category::with(['parentRecursive', 'children'])
            ->whereDoesntHave('children')
            ->get()
            ->sortBy(fn (Category $cat) => $cat->getFullPath(), SORT_NATURAL);
    }

    public function getBikeCategories(): Collection
    {
        $bikeCategory = Category::find(Category::BIKE_CATEGORY_ID);
        $bikeChildrenIds = $bikeCategory?->getAllChildrenIds() ?? [];

        return Category::with(['parentRecursive', 'children'])
            ->whereDoesntHave('children')
            ->whereIn('id_categorie', $bikeChildrenIds)
            ->get()
            ->sortBy(fn (Category $cat) => $cat->getFullPath(), SORT_NATURAL);
    }

    public function getEBikeCategories(): Collection
    {
        $eBikeCategory = Category::find(Category::EBIKE_CATEGORY_ID);
        $eBikeChildrenIds = $eBikeCategory?->getAllChildrenIds() ?? [];

        return Category::with(['parentRecursive', 'children'])
            ->whereDoesntHave('children')
            ->whereIn('id_categorie', $eBikeChildrenIds)
            ->get()
            ->sortBy(fn (Category $cat) => $cat->getFullPath(), SORT_NATURAL);
    }

    public function getMaterials(): Collection
    {
        return BikeFrameMaterial::all();
    }

    public function getVintages(): Collection
    {
        return Vintage::orderBy('millesime_velo', 'desc')->get();
    }

    public function getUsages(): Collection
    {
        return Usage::all();
    }

    public function getFrames(): Collection
    {
        return BikeFrame::all();
    }

    public function getColors(): Collection
    {
        return Color::all();
    }

    public function getSizes(): Collection
    {
        return Size::all();
    }

    public function getBatteries(): Collection
    {
        return Battery::all();
    }

    public function getEBikeTypes(): Collection
    {
        return EBikeType::all();
    }

    public function getReferenceFormData(): array
    {
        return [
            'frames' => $this->getFrames(),
            'colors' => $this->getColors(),
            'sizes' => $this->getSizes(),
            'batteries' => $this->getBatteries(),
        ];
    }
}
