<?php

namespace App\Models\Concerns;

use App\Models\Accessory;
use App\Models\Article;
use App\Models\ArticleReference;
use App\Models\Bike;
use App\Models\Category;
use App\Models\Characteristic;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @mixin Model
 */
trait HasArticle
{
    public function getDiscountedPrice(): float
    {
        if ($this->hasDiscount() > 0) {
            return round($this->prix_article * (1 - $this->pourcentage_remise / 100), 2);
        }

        return $this->prix_article;
    }

    public function hasDiscount(): bool
    {
        return $this->pourcentage_remise > 0;
    }

    public function accessory(): BelongsTo
    {
        return $this->belongsTo(Accessory::class, 'id_article', 'id_article');
    }

    public function bike(): HasOne
    {
        return $this->hasOne(Bike::class, 'id_article', 'id_article');
    }

    public function similar(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'similaire', 'id_article_simil', 'id_article');
    }

    public function references(): HasMany
    {
        return $this->hasMany(ArticleReference::class, 'id_article', 'id_article');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'id_categorie', 'id_categorie');
    }

    public function characteristics(): BelongsToMany
    {
        return $this->belongsToMany(
            Characteristic::class,
            'caracterise',
            'id_article',
            'id_caracteristique'
        )->withPivot('valeur_caracteristique');
    }
}
