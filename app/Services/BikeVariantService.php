<?php

namespace App\Services;

use App\DTOs\Article\VariantOptionDTO;
use App\Models\BikeReference;
use Illuminate\Support\Collection;

class BikeVariantService
{
    public function getVariants(BikeReference $currentReference): Collection
    {
        return BikeReference::where('id_article', $currentReference->id_article)
            ->with([
                'color',
                'frame',
                'ebike.battery',
            ])
            ->get();
    }

    /**
     * Build frame options for current reference
     *
     * @return Collection<int, VariantOptionDTO>
     */
    public function buildFrameOptions(Collection $variants, BikeReference $currentReference): Collection
    {
        return $variants
            ->pluck('frame')
            ->unique('id_cadre_velo')
            ->sortBy('label_cadre_velo')
            ->map(function ($frame) use ($variants, $currentReference) {
                $criteria = [
                    'id_couleur' => $currentReference->id_couleur,
                    'id_cadre_velo' => $frame->id_cadre_velo,
                    'id_batterie' => $currentReference->ebike?->id_batterie ?? null,
                ];

                $target = $this->findVariant($variants, $criteria)
                    ?? $variants->firstWhere('id_cadre_velo', $frame->id_cadre_velo);

                return new VariantOptionDTO(
                    label: $frame->label_cadre_velo,
                    url: route('articles.show-reference', [
                        'article' => $currentReference->id_article,
                        'reference' => $target->id_reference,
                    ]),
                    active: $currentReference->id_cadre_velo == $frame->id_cadre_velo,
                );
            });
    }

    private function findVariant(Collection $variants, array $criteria): ?BikeReference
    {
        /** @var ?BikeReference $variant */
        $variant = $variants->first(function (BikeReference $ref) use ($criteria): bool {
            foreach ($criteria as $field => $expected) {
                if ($expected !== null && $ref->{$field} != $expected) {
                    return false;
                }
            }

            return true;
        });

        return $variant;
    }

    /**
     * Build color options for current reference
     *
     * @return Collection<int, VariantOptionDTO>
     */
    public function buildColorOptions(Collection $variants, BikeReference $currentReference): Collection
    {
        return $variants
            ->pluck('color')
            ->unique('id_couleur')
            ->sortBy('label_couleur')
            ->map(function ($color) use ($variants, $currentReference) {
                $criteria = [
                    'id_couleur' => $color->id_couleur,
                    'id_cadre_velo' => $currentReference->id_cadre_velo,
                    'id_batterie' => $currentReference->ebike?->id_batterie ?? null,
                ];

                $target = $this->findVariant($variants, $criteria)
                    ?? $variants->firstWhere('id_couleur', $color->id_couleur);

                return new VariantOptionDTO(
                    label: $color->label_couleur,
                    url: route('articles.show-reference', [
                        'article' => $currentReference->id_article,
                        'reference' => $target->id_reference,
                    ]),
                    active: $currentReference->id_couleur == $color->id_couleur,
                    hex: $color->hex,
                );
            });
    }

    /**
     * Build battery options for ebikes
     *
     * @return Collection<int, VariantOptionDTO>
     */
    public function buildBatteryOptions(Collection $variants, BikeReference $currentReference): Collection
    {
        return $variants
            ->map(fn ($r) => $r->ebike?->battery)
            ->filter()
            ->unique('id_batterie')
            ->values()
            ->map(function ($battery) use ($variants, $currentReference) {

                $criteria = [
                    'id_batterie' => $battery->id_batterie,
                    'id_couleur' => $currentReference->id_couleur,
                    'id_cadre_velo' => $currentReference->id_cadre_velo,
                ];

                $target = $this->findVariant($variants, $criteria)
                    ?? $variants->first(fn ($r) => $r->ebike?->id_batterie === $battery->id_batterie);

                return new VariantOptionDTO(
                    label: $battery->label_batterie,
                    url: route('articles.show-reference', [
                        'article' => $currentReference->id_article,
                        'reference' => $target->id_reference,
                    ]),
                    active: $currentReference->ebike->id_batterie === $battery->id_batterie,
                );
            });
    }
}
