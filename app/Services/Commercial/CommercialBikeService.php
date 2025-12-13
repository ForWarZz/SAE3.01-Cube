<?php

namespace App\Services\Commercial;

use App\Models\ArticleReference;
use App\Models\Bike;
use App\Models\BikeModel;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CommercialBikeService
{
    public function __construct(
        protected BikeReferenceService $referenceService,
    ) {}

    public function getPaginatedBikes(int $perPage = 10): LengthAwarePaginator
    {
        return Bike::with(['bikeModel', 'category', 'frameMaterial', 'vintage', 'usage', 'article', 'references'])
            ->orderBy('id_article', 'desc')
            ->paginate($perPage);
    }

    /**
     * @throws \Exception
     */
    public function createBike(array $validated, array $referenceImages = []): int
    {
        DB::beginTransaction();

        try {
            $modelId = $this->resolveModelId($validated);

            $articleId = $this->createBikeArticle($validated, $modelId);

            foreach ($validated['references'] as $idx => $refData) {
                $images = $referenceImages[$idx] ?? [];
                $this->referenceService->createReference(
                    $articleId,
                    $refData,
                    $validated['is_vae'] ?? false,
                    $images
                );
            }

            DB::commit();

            return $articleId;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @throws \Exception
     */
    public function deleteBike(Bike $bike): void
    {
        DB::beginTransaction();

        try {
            $bike->load(['references', 'article']);

            $ids = $bike->references->pluck('id_reference')->toArray();
            ArticleReference::whereIn('id_reference', $ids)->delete();

            $bike->article->delete();

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function loadBikeDetails(Bike $bike): Bike
    {
        return $bike->load([
            'bikeModel',
            'category',
            'frameMaterial',
            'vintage',
            'usage',
            'article',
            'references.frame',
            'references.color',
            'references.availableSizes',
            'ebikeReferences.battery',
        ]);
    }

    public function getReferenceImages(Bike $bike): array
    {
        $referenceImages = [];

        foreach ($bike->references as $reference) {
            $files = $reference->getImageFiles();
            $referenceImages[$reference->id_reference] = array_map(function ($file) {
                return [
                    'path' => $file,
                    'url' => Storage::url($file),
                    'name' => basename($file),
                ];
            }, $files);
        }

        return $referenceImages;
    }

    public function isVae(Bike $bike): bool
    {
        return $bike->ebikeReferences->isNotEmpty();
    }

    private function resolveModelId(array $validated): int
    {
        if ($validated['model_choice'] === 'new') {
            $bikeModel = BikeModel::firstOrCreate([
                'nom_modele_velo' => $validated['new_model_name'],
            ]);

            return $bikeModel->id_modele_velo;
        }

        return $validated['id_modele_velo'];
    }

    private function createBikeArticle(array $validated, int $modelId): int
    {
        $result = DB::selectOne('SELECT fn_create_bike(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) as id_article', [
            $validated['is_vae'] ?? false,
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
            $validated['id_type_vae'],
        ]);

        return $result->id_article;
    }
}
