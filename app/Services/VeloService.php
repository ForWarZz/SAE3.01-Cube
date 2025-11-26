<?php

namespace App\Services;

use App\Models\Caracteristique;
use App\Models\ModeleVelo;
use App\Models\ReferenceVelo;
use App\Models\Velo;
use Illuminate\Support\Collection;

class VeloService
{
    /**
     * @param ReferenceVelo $currentRef
     * @return array{
     *     currentRef: ReferenceVelo,
     *     article: Velo,
     *     isVae: bool,
     *     optionsCadres: Collection,
     *     optionsCouleurs: Collection,
     *     optionsBatteries: Collection,
     *     optionsTailles: Collection,
     *     geometries: Collection,
     *     taillesGeo: Collection,
     *     caracteristiques: Collection
     * }
     */
    public function prepareViewData(ReferenceVelo $currentRef): array
    {
        $currentRef->load([
            'velo.modeleVelo.geometries.caracteristique',
            'velo.modeleVelo.geometries.taille',
            'article.caracteristiques.type',
            'referenceVae.batterie',
            'couleur',
            'cadre',
            'taillesDispo'
        ]);

        $velo = $currentRef->velo;
        $isVae = $currentRef->referenceVae !== null;

        $variantes = ReferenceVelo::where('id_article', $velo->id_article)
            ->with(['couleur', 'cadre', 'referenceVae.batterie'])
            ->get();

        $optionsCadres = $this
            ->buildOptions($variantes, $currentRef, 'cadre', 'id_cadre_velo', 'label_cadre_velo');
        $optionsCouleurs = $this
            ->buildOptions($variantes, $currentRef, 'couleur', 'id_couleur', 'label_couleur');

        $optionsBatteries = collect();
        if ($isVae) {
            $optionsBatteries = $this->buildBatteryOptions($variantes, $currentRef);
        }

        $geoData = $this->buildGeometryData($velo->modeleVelo);
        $optionsTailles = $this->buildSizeOptions($currentRef, $geoData['headers']);

        $caracteristiquesGroupees = $velo->article->caracteristiques->groupBy('type.nom_type_carac');
        $poids = $velo->article->caracteristiques
            ->firstWhere('type.nom_type_carac', '=', 'Poids')
            ->pivot->valeur_caracteristique;

        return [
            'currentRef' => $currentRef,
            'article' => $velo,
            'isVae' => $isVae,
            'optionsCadres' => $optionsCadres,
            'optionsCouleurs' => $optionsCouleurs,
            'optionsBatteries' => $optionsBatteries,
            'optionsTailles' => $optionsTailles,

            'geometries' => $geoData['rows'],
            'taillesGeo' => $geoData['headers'],

            'caracteristiques' => $caracteristiquesGroupees,
            'poids' => $poids
        ];
    }

    /**
     * @param ModeleVelo $modele
     * @return array{
     *     headers: Collection,
     *     rows: Collection
     * }
     */
    private function buildGeometryData(ModeleVelo $modele): array
    {
        $geometries = $modele->geometries;
        $headers = $geometries->pluck('taille')
            ->unique('id_taille')
            ->sortBy('id_taille')
            ->values();


        $rows = $geometries->groupBy('caracteristique.label_carac_geo')
            ->map(function ($group, $label) use ($headers) {
                $values = $headers->map(function ($taille) use ($group) {
                    $geo = $group->firstWhere('id_taille', $taille->id_taille);
                    return $geo ? $geo->valeur_carac : '-';
                });

                return [
                    'label'  => $label,
                    'values' => $values
                ];
            });

        return ['headers' => $headers, 'rows' => $rows];
    }

    /**
     * @param Collection $siblings
     * @param ReferenceVelo $currentRef
     * @param string $relation
     * @param string $fk
     * @param string $labelField
     * @return Collection{
     *     label: string,
     *     url: string,
     *     active: bool
     * }
     */
    private function buildOptions(Collection $siblings, ReferenceVelo $currentRef, string $relation, string $fk, string $labelField): Collection
    {
        return $siblings
            ->pluck($relation)
            ->unique($fk)
            ->sortBy($labelField)
            ->map(function ($item) use ($siblings, $currentRef, $fk, $labelField) {
                $target = $siblings->first(function ($ref) use ($item, $currentRef, $fk) {
                    if ($ref->$fk != $item->$fk) return false;
                    if ($fk === 'id_cadre_velo' && $ref->id_couleur != $currentRef->id_couleur) return false;
                    if ($fk === 'id_couleur' && $ref->id_cadre_velo != $currentRef->id_cadre_velo) return false;

                    return true;
                });

            if (!$target) {
                $target = $siblings->firstWhere($fk, $item->$fk);
            }

            return [
                'label' => $item->$labelField,
                'url' => route('velo.show', $target->id_reference),
                'active' => $currentRef->$fk == $item->$fk,
            ];
        });
    }

    /**
     * @param Collection $siblings
     * @param ReferenceVelo $currentRef
     * @return Collection{
     *     label: string,
     *     url: string,
     *     active: bool
     * }
     */
    private function buildBatteryOptions(Collection $siblings, ReferenceVelo $currentRef): Collection
    {
        $batteries = $siblings->map(fn($ref) => $ref->referenceVae?->batterie)
            ->filter()
            ->unique('id_batterie')
            ->sortBy('capacite_batterie');

        return $batteries->map(function ($batterie) use ($siblings, $currentRef) {
            $target = $siblings->first(function ($ref) use ($batterie, $currentRef) {
                return $ref->referenceVae?->id_batterie == $batterie->id_batterie
                    && $ref->id_cadre_velo == $currentRef->id_cadre_velo
                    && $ref->id_couleur == $currentRef->id_couleur;
            });

            if (!$target) {
                $target = $siblings->first(fn($r) => $r->referenceVae?->id_batterie == $batterie->id_batterie);
            }

            return [
                'label' => $batterie->capacite_batterie . ' Wh',
                'url' => route('velo.show', $target->id_reference),
                'active' => $currentRef->referenceVae->id_batterie == $batterie->id_batterie
            ];
        });
    }

    /**
     * @param ReferenceVelo $currentRef
     * @param Collection|null $taillesGeo
     * @return Collection{
     *     id: int,
     *     label: string,
     *     disabled: bool
     * }
     */
    private function buildSizeOptions(ReferenceVelo $currentRef, ?Collection $taillesGeo): Collection
    {
        $listeBase = ($taillesGeo && $taillesGeo->isNotEmpty())
            ? $taillesGeo
            : $currentRef->taillesDispo;

        if (!$listeBase) return collect();

        return $listeBase->map(function ($taille) use ($currentRef) {
            $dispoRef = $currentRef->taillesDispo->firstWhere('id_taille', $taille->id_taille);
            $enStock = $dispoRef && $dispoRef->pivot->dispo_en_ligne;

            return [
                'id' => $taille->id_taille,
                'label' => $taille->nom_taille,
                'disabled' => !$enStock
            ];
        });
    }
}
