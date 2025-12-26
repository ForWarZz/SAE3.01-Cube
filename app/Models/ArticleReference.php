<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id_reference
 * @property int $numero_reference
 * @property int $id_article
 */
class ArticleReference extends Model
{
    public $timestamps = false;

    protected $table = 'reference_article';

    protected $primaryKey = 'id_reference';

    protected $fillable = ['id_article'];

    public function bikeReference(): BelongsTo
    {
        return $this->belongsTo(BikeReference::class, 'id_reference', 'id_reference');
    }

    public function accessory(): BelongsTo
    {
        return $this->belongsTo(Accessory::class, 'id_reference', 'id_reference');
    }

    public function variant()
    {
        return $this->bikeReference ?? $this->accessory;
    }

    public function isBike(): bool
    {
        return (bool) $this->bikeReference;
    }

    public function isAccessory(): bool
    {
        return (bool) $this->accessory;
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class, 'id_article', 'id_article');
    }

    public function availableSizes(): BelongsToMany
    {
        return $this->belongsToMany(
            Size::class,
            'taille_dispo',
            'id_reference',
            'id_taille'
        )->withPivot('dispo_en_ligne');
    }

    public function shopAvailabilities(): BelongsToMany
    {
        return $this->belongsToMany(
            Shop::class,
            'dispo_magasin',
            'id_reference',
            'id_magasin'
        )->withPivot(['id_taille', 'statut']);
    }

    //    public function scopeWithFullRelations(Builder $query): Builder
    //    {
    //        return $query->with([
    //            'article',
    //            'article.category',
    //            'article.characteristics.characteristicType',
    //            'article.accessory',
    //
    //            'article.similar',
    //            'article.similar.category',
    //            'article.similar.bike.bikeModel',
    //            'article.similar.bike.references',
    //            'article.similar.accessory',
    //
    //            // disponibilité / tailles
    //            'availableSizes',
    //            'shopAvailabilities',
    //
    //            // vélo et modèle + géométrie (pour tableau géométrie) — charger le bike principal
    //            'article.bike',
    //            'article.bike.bikeModel:id_modele_velo,nom_modele_velo',
    //            'article.bike.bikeModel.geometries',
    //            'article.bike.bikeModel.geometries.characteristic',
    //            'article.bike.bikeModel.geometries.size',
    //
    //            // références/variants du bike (uniquement colonnes nécessaires) et leurs petites sous-relations
    //            'article.bike.references',
    //            'article.bike.references.color:id_couleur,label_couleur,hex',
    //            'article.bike.references.frame:id_cadre_velo,label_cadre_velo',
    //            'article.bike.references.ebike:id_reference,id_batterie',
    //            'article.bike.references.ebike.battery:id_batterie,label_batterie',
    //
    //            // accessoires compatibles — charger léger
    //            'article.bike.compatibleAccessories.article.accessory',
    //            'article.bike.compatibleAccessories.article.category',
    //        ]);
    //    }
}
