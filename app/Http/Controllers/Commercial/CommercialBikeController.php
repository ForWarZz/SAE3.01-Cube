<?php

namespace App\Http\Controllers\Commercial;

use App\Http\Controllers\Controller;
use App\Http\Requests\BikeCreateRequest;
use App\Http\Requests\BikeReferenceImageRequest;
use App\Http\Requests\BikeReferenceRequest;
use App\Models\ArticleReference;
use App\Models\Battery;
use App\Models\Bike;
use App\Models\BikeFrame;
use App\Models\BikeFrameMaterial;
use App\Models\BikeModel;
use App\Models\BikeReference;
use App\Models\Category;
use App\Models\Color;
use App\Models\EBikeType;
use App\Models\Size;
use App\Models\Usage;
use App\Models\Vintage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CommercialBikeController extends Controller
{
    public function index()
    {
        $bikes = Bike::with(['bikeModel', 'category', 'frameMaterial', 'vintage', 'usage', 'article', 'references'])
            ->orderBy('id_article', 'desc')
            ->paginate(10);

        return view('commercial.bikes.index', compact('bikes'));
    }

    public function create()
    {
        $models = BikeModel::orderBy('nom_modele_velo')->get();
        $categories = Category::with(['parent', 'children'])
            ->whereDoesntHave('children')
            ->get()
            ->sortBy(fn (Category $cat) => $cat->getFullPath(), SORT_NATURAL);

        $materials = BikeFrameMaterial::all();
        $vintages = Vintage::orderBy('millesime_velo', 'desc')->get();
        $usages = Usage::all();
        $frames = BikeFrame::all();
        $colors = Color::all();
        $sizes = Size::all();
        $batteries = Battery::all();
        $eBikeTypes = EBikeType::all();

        $bikesByModel = Bike::with('category')
            ->get()
            ->keyBy('id_modele_velo');

        $modelsCategory = $models->mapWithKeys(function (BikeModel $bikeModel) use ($bikesByModel) {
            $bike = $bikesByModel->get($bikeModel->id_modele_velo);

            return [
                $bikeModel->id_modele_velo => $bike?->category?->id_categorie,
            ];
        });

        return view('commercial.bikes.create', compact(
            'modelsCategory', 'models', 'categories', 'materials', 'vintages',
            'usages', 'frames', 'colors', 'sizes', 'batteries', 'eBikeTypes'
        ));
    }

    public function store(BikeCreateRequest $request)
    {
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            if ($validated['model_choice'] === 'new') {
                $bikeModel = BikeModel::firstOrCreate([
                    'nom_modele_velo' => $validated['new_model_name'],
                ]);

                $modelId = $bikeModel->id_modele_velo;

            } else {
                $modelId = $validated['id_modele_velo'];
            }

            $result = DB::selectOne('SELECT fn_create_bike(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) as id_article',
                [
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

            $articleId = $result->id_article;

            foreach ($validated['references'] as $idx => $refData) {
                $forcedId = $refData['numero_reference'] ?? null;

                $refResult = DB::selectOne('SELECT fn_add_bike_reference(?, ?, ?, ?, ?, ?) as id_ref',
                    [
                        $validated['is_vae'] ?? false,
                        $forcedId,
                        $articleId,
                        $refData['id_cadre_velo'],
                        $refData['id_couleur'],
                        $refData['id_batterie'],
                    ]);

                $referenceId = $refResult->id_ref;

                if (! empty($refData['sizes'])) {
                    $bikeRef = BikeReference::find($referenceId);
                    $bikeRef->availableSizes()->attach(
                        $refData['sizes'],
                        ['dispo_en_ligne' => true]
                    );
                }

                if ($request->hasFile("references.{$idx}.images")) {
                    $images = $request->file("references.{$idx}.images");
                    $storagePath = "articles/references/{$referenceId}";

                    foreach ($images as $index => $image) {
                        $filename = ($index + 1).'.'.$image->getClientOriginalExtension();
                        $image->storeAs($storagePath, $filename, 'public');
                    }
                }
            }

            DB::commit();

            return redirect()
                ->route('commercial.bikes.index')
                ->with('success', 'Vélo créé avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Affiche les détails d'un vélo avec ses références
     */
    public function show(Bike $bike)
    {
        $bike->load([
            'bikeModel',
            'category',
            'frameMaterial',
            'vintage',
            'usage',
            'article',
            'references.frame',
            'references.color',
            'references.availableSizes',
            'references.ebike.battery',
        ]);

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

        $frames = BikeFrame::all();
        $colors = Color::all();
        $sizes = Size::all();
        $batteries = Battery::all();

        $isVae = $bike->references->first()?->ebike !== null;

        return view('commercial.bikes.show', compact(
            'bike', 'referenceImages', 'frames', 'colors', 'sizes', 'batteries', 'isVae'
        ));
    }

    /**
     * Ajoute une nouvelle référence à un vélo existant
     */
    public function addReference(BikeReferenceRequest $request, Bike $bike)
    {
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            // Déterminer si c'est un VAE
            $isVae = $bike->references()
                ->whereHas('ebike')
                ->exists();

            $forcedId = $validated['numero_reference'] ?? null;

            $refResult = DB::selectOne('SELECT fn_add_bike_reference(?, ?, ?, ?, ?, ?) as id_ref',
                [
                    $isVae,
                    $forcedId,
                    $bike->id_article,
                    $validated['id_cadre_velo'],
                    $validated['id_couleur'],
                    $isVae ? ($validated['id_batterie'] ?? null) : null,
                ]);

            $referenceId = $refResult->id_ref;

            // Attacher les tailles
            if (! empty($validated['sizes'])) {
                $bikeRef = BikeReference::find($referenceId);
                $bikeRef->availableSizes()->attach(
                    $validated['sizes'],
                    ['dispo_en_ligne' => true]
                );
            }

            // Sauvegarder les images
            if ($request->hasFile('images')) {
                $images = $request->file('images');
                $storagePath = "articles/references/{$referenceId}";

                foreach ($images as $index => $image) {
                    $filename = ($index + 1).'.'.$image->getClientOriginalExtension();
                    $image->storeAs($storagePath, $filename, 'public');
                }
            }

            DB::commit();

            return back()->with('success', 'Référence ajoutée avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->withErrors(['error' => 'Erreur lors de l\'ajout de la référence : '.$e->getMessage()]);
        }
    }

    //    public function updateReference(BikeReferenceRequest $request, Bike $bike, BikeReference $reference)
    //    {
    //        if ($reference->id_article !== $bike->id_article) {
    //            return back()->withErrors(['error' => 'Cette référence n\'appartient pas à ce vélo.']);
    //        }
    //
    //        $validated = $request->validated();
    //
    //        try {
    //            DB::beginTransaction();
    //
    //            $isVae = $reference->ebike !== null;
    //
    //            $oldRefId = $reference->id_reference;
    //            $reference->availableSizes()->detach();
    //            $reference->baseReference->delete();
    //
    //            $forcedId = $validated['numero_reference'] ?? null;
    //
    //            $refResult = DB::selectOne('SELECT fn_add_bike_reference(?, ?, ?, ?, ?, ?) as id_ref',
    //                [
    //                    $isVae,
    //                    $forcedId,
    //                    $bike->id_article,
    //                    $validated['id_cadre_velo'],
    //                    $validated['id_couleur'],
    //                    $isVae ? ($validated['id_batterie'] ?? null) : null,
    //                ]);
    //
    //            $newReferenceId = $refResult->id_ref;
    //
    //            if (! empty($validated['sizes'])) {
    //                $newBikeRef = BikeReference::find($newReferenceId);
    //                $newBikeRef->availableSizes()->attach(
    //                    $validated['sizes'],
    //                    ['dispo_en_ligne' => true]
    //                );
    //            }
    //
    //            $oldStoragePath = "articles/references/{$oldRefId}";
    //            $newStoragePath = "articles/references/{$newReferenceId}";
    //
    //            if (Storage::disk('public')->exists($oldStoragePath)) {
    //                $existingFiles = Storage::disk('public')->files($oldStoragePath);
    //                foreach ($existingFiles as $file) {
    //                    $filename = basename($file);
    //                    Storage::disk('public')->copy($file, "{$newStoragePath}/{$filename}");
    //                }
    //            }
    //
    //            if ($request->hasFile('images')) {
    //                $images = $request->file('images');
    //                $existingCount = count(Storage::disk('public')->files($newStoragePath));
    //
    //                foreach ($images as $index => $image) {
    //                    $filename = ($existingCount + $index + 1).'.'.$image->getClientOriginalExtension();
    //                    $image->storeAs($newStoragePath, $filename, 'public');
    //                }
    //            }
    //
    //            DB::commit();
    //
    //            return back()->with('success', 'Référence modifiée avec succès. Une nouvelle référence a été créée.');
    //
    //        } catch (\Exception $e) {
    //            DB::rollBack();
    //
    //            return back()
    //                ->withInput()
    //                ->withErrors(['error' => 'Erreur lors de la modification de la référence : '.$e->getMessage()]);
    //        }
    //    }

    public function deleteReference(Bike $bike, BikeReference $reference)
    {
        if ($reference->id_article !== $bike->id_article) {
            return back()->withErrors(['error' => 'Cette référence n\'appartient pas à ce vélo.']);
        }

        $reference->load([
            'availableSizes',
            'baseReference',
        ]);

        $remainingCount = $bike->references()->where('id_reference', '!=', $reference->id_reference)->count();
        if ($remainingCount < 1) {
            return back()->withErrors(['error' => 'Impossible de supprimer la dernière référence du vélo.']);
        }

        try {
            DB::beginTransaction();

            $reference->availableSizes()->detach();
            $reference->baseReference->delete();

            DB::commit();

            return back()->with('success', 'La référence a été supprimée.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors(['error' => 'Erreur lors de la suppression : '.$e->getMessage()]);
        }
    }

    public function addReferenceImages(BikeReferenceImageRequest $request, Bike $bike, BikeReference $reference)
    {
        if ($reference->id_article !== $bike->id_article) {
            return back()->withErrors(['error' => 'Cette référence n\'appartient pas à ce vélo.']);
        }

        $existingFiles = $reference->getImageFiles();
        $directory = $reference->getStorageDirectory();

        $existingCount = count($existingFiles);

        $newImagesCount = count($request->file('images'));
        if ($existingCount + $newImagesCount > 5) {
            return back()->withErrors([
                'error' => "Impossible d'ajouter ces images. Limite de 5 images par référence. Actuellement: {$existingCount} images.",
            ]);
        }

        foreach ($request->file('images') as $index => $image) {
            $filename = ($existingCount + $index + 1).'.'.$image->getClientOriginalExtension();
            $image->storeAs($directory, $filename, 'public');
        }

        return back()->with('success', 'Images ajoutées avec succès.');
    }

    public function deleteReferenceImage(Bike $bike, BikeReference $reference, string $imageName)
    {
        if ($reference->id_article !== $bike->id_article) {
            return back()->withErrors(['error' => 'Cette référence n\'appartient pas à ce vélo.']);
        }

        try {
            $imagePath = $reference->getImagePathFromName($imageName);

            if (! Storage::disk('public')->exists($imagePath)) {
                return back()->withErrors(['error' => 'Image introuvable.']);
            }

            Storage::disk('public')->delete($imagePath);

            return back()->with('success', 'Image supprimée avec succès.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la suppression de l\'image : '.$e->getMessage()]);
        }
    }

    public function destroy(Bike $bike)
    {
        try {
            DB::beginTransaction();

            $bike->load([
                'references',
                'article',
            ]);

            $ids = $bike->references->pluck('id_reference')->toArray();
            ArticleReference::whereIn('id_reference', $ids)->delete();

            $bike->article->delete();

            DB::commit();

            return redirect()
                ->route('commercial.bikes.index')
                ->with('success', 'Vélo supprimé avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withErrors(['error' => 'Erreur lors de la suppression : '.$e->getMessage()]);
        }
    }
}
