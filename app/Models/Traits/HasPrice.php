<?php

namespace App\Models\Traits;

trait HasPrice
{
    public function hasDiscount(): bool
    {
        return ($this->pourcentage_remise ?? 0) > 0;
    }

    public function getDiscountedPrice(): float
    {
        $prix = $this->prix_article ?? 0;
        $pourc = $this->pourcentage_remise ?? 0;

        if ($pourc > 0) {
            return (float) round($prix * (1 - $pourc / 100), 2);
        }

        return (float) $prix;
    }
}
