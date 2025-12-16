<?php

namespace App\Services\Commercial;

use App\Models\Bike;
use App\Models\BikeReference;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use phpseclib3\Exception\FileNotFoundException;

class BikeReferenceService
{
    /**
     * @param  array<UploadedFile>  $images
     */
    public function createReference(int $articleId, array $refData, bool $isVae, array $images = []): int
    {
        $refResult = DB::selectOne('SELECT fn_add_bike_reference(?, ?, ?, ?, ?, ?) as id_ref', [
            $isVae,
            $refData['numero_reference'],
            $articleId,
            $refData['id_cadre_velo'],
            $refData['id_couleur'],
            $refData['id_batterie'] ?? null,
        ]);

        $referenceId = $refResult->id_ref;

        $this->attachSizes($referenceId, $refData['sizes'] ?? []);
        $this->storeImages($articleId, $referenceId, $images);

        return $referenceId;
    }

    /**
     * @param  array<UploadedFile>  $images
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function addReferenceToExistingBike(Bike $bike, array $validated, array $images = []): int
    {
        DB::beginTransaction();

        try {
            $isVae = $bike->ebike->exists;

            $referenceId = $this->createReference(
                $bike->id_article,
                [
                    'numero_reference' => $validated['numero_reference'],
                    'id_cadre_velo' => $validated['id_cadre_velo'],
                    'id_couleur' => $validated['id_couleur'],
                    'id_batterie' => $isVae ? ($validated['id_batterie'] ?? null) : null,
                    'sizes' => $validated['sizes'] ?? [],
                ],
                $isVae,
                $images
            );

            DB::commit();

            return $referenceId;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function deleteReference(BikeReference $reference): void
    {
        DB::beginTransaction();

        try {
            $reference->availableSizes()->detach();
            $reference->baseReference->delete();

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @param  array<UploadedFile>  $images
     */
    public function addImagesToReference(BikeReference $reference, array $images): void
    {
        $existingFiles = $reference->getImageFiles();
        $directory = $reference->getStorageDirectory();
        $existingCount = count($existingFiles);

        foreach ($images as $index => $image) {
            $filename = ($existingCount + $index + 1).'.'.$image->getClientOriginalExtension();
            $image->storeAs($directory, $filename, 'public');
        }
    }

    public function deleteImage(BikeReference $reference, string $imageName): void
    {
        $imagePath = $reference->getImagePathFromName($imageName);

        if (! Storage::disk('public')->exists($imagePath)) {
            throw new FileNotFoundException('L\'image spécifiée est introuvable.');
        }

        Storage::disk('public')->delete($imagePath);
    }

    public function canAddImages(BikeReference $reference, int $newImagesCount): bool
    {
        $existingCount = count($reference->getImageFiles());

        return ($existingCount + $newImagesCount) <= 5;
    }

    public function getExistingImagesCount(BikeReference $reference): int
    {
        return count($reference->getImageFiles());
    }

    private function attachSizes(int $referenceId, array $sizes): void
    {
        if (empty($sizes)) {
            return;
        }

        $bikeRef = BikeReference::find($referenceId);
        $bikeRef->availableSizes()->attach($sizes, ['dispo_en_ligne' => true]);
    }

    /**
     * @param  array<UploadedFile>  $images
     */
    private function storeImages(int $articleId, int $referenceId, array $images): void
    {
        if (empty($images)) {
            return;
        }

        $storagePath = "articles/$articleId/$referenceId";

        foreach ($images as $index => $image) {
            $filename = ($index + 1).'.'.$image->getClientOriginalExtension();
            $image->storeAs($storagePath, $filename, 'public');
        }
    }
}
