<?php

namespace App\Http\Controllers\Staff\Commercial;

use App\Http\Controllers\Controller;
use App\Http\Requests\Bike\BikeCreateRequest;
use App\Http\Requests\Bike\BikeReferenceImageRequest;
use App\Http\Requests\Bike\BikeReferenceRequest;
use App\Models\Bike;
use App\Models\BikeReference;
use App\Services\Commercial\Bike\BikeFormDataService;
use App\Services\Commercial\Bike\BikeReferenceService;
use App\Services\Commercial\Bike\CommercialBikeService;
use Exception;
use Throwable;

class CommercialBikeController extends Controller
{
    public function __construct(
        private readonly CommercialBikeService $bikeService,
        private readonly BikeReferenceService $referenceService,
        private readonly BikeFormDataService $formDataService,
    ) {}

    public function index()
    {
        $bikes = $this->bikeService->getPaginatedBikes();

        return view('staff.commercial.bikes.index', compact('bikes'));
    }

    public function create()
    {
        $formData = $this->formDataService->getCreateFormData();

        return view('staff.commercial.bikes.create', $formData);
    }

    public function store(BikeCreateRequest $request)
    {
        $validated = $request->validated();

        try {
            $this->bikeService->createBike($validated);

            return redirect()
                ->route('commercial.bikes.index')
                ->with('success', 'Vélo créé avec succès.');

        } catch (Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function show(Bike $bike)
    {
        $bike = $this->bikeService->loadBikeDetails($bike);
        $referenceImages = $this->bikeService->getReferenceImages($bike);
        $isVae = $this->bikeService->isVae($bike);

        $referenceFormData = $this->formDataService->getShowFormData();

        return view('staff.commercial.bikes.show', array_merge(
            compact('bike', 'referenceImages', 'isVae'),
            $referenceFormData
        ));
    }

    public function addReference(BikeReferenceRequest $request, Bike $bike)
    {
        $validated = $request->validated();
        $images = $request->hasFile('images') ? $request->file('images') : [];

        $bike->load([
            'ebike',
        ]);

        try {
            $this->referenceService->addReferenceToExistingBike($bike, $validated, $images);

            return back()->with('success', 'Référence ajoutée avec succès.');

        } catch (Throwable $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Erreur lors de l\'ajout de la référence : '.$e->getMessage()]);
        }
    }

    public function deleteReference(Bike $bike, BikeReference $reference)
    {
        if ($reference->id_article !== $bike->id_article) {
            return back()->withErrors(['error' => 'Cette référence n\'appartient pas à ce vélo.']);
        }

        $reference->load(['availableSizes', 'baseReference']);

        try {
            $this->referenceService->deleteReference($reference);

            return back()->with('success', 'La référence a été supprimée.');

        } catch (Throwable $e) {
            return back()->withErrors(['error' => 'Erreur lors de la suppression : '.$e->getMessage()]);
        }
    }

    public function addReferenceImages(BikeReferenceImageRequest $request, Bike $bike, BikeReference $reference)
    {
        if ($reference->id_article !== $bike->id_article) {
            return back()->withErrors(['error' => 'Cette référence n\'appartient pas à ce vélo.']);
        }

        $newImagesCount = count($request->file('images'));

        if (! $this->referenceService->canAddImages($reference, $newImagesCount)) {
            $existingCount = $this->referenceService->getExistingImagesCount($reference);

            return back()->withErrors([
                'error' => "Impossible d'ajouter ces images. Limite de 5 images par référence. Actuellement: {$existingCount} images.",
            ]);
        }

        $this->referenceService->addImagesToReference($reference, $request->file('images'));

        return back()->with('success', 'Images ajoutées avec succès.');
    }

    public function deleteReferenceImage(Bike $bike, BikeReference $reference, string $imageName)
    {
        if ($reference->id_article !== $bike->id_article) {
            return back()->withErrors(['error' => 'Cette référence n\'appartient pas à ce vélo.']);
        }

        try {
            $this->referenceService->deleteImage($reference, $imageName);

            return back()->with('success', 'Image supprimée avec succès.');

        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la suppression de l\'image : '.$e->getMessage()]);
        }
    }

    public function destroy(Bike $bike)
    {
        try {
            $bike->load(['references', 'article']);
            $this->bikeService->deleteBike($bike);

            return redirect()
                ->route('commercial.bikes.index')
                ->with('success', 'Vélo supprimé avec succès.');
        } catch (Throwable $e) {
            return back()
                ->withErrors(['error' => 'Erreur lors de la suppression : '.$e->getMessage()]);
        }
    }
}
