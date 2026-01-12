<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id_reference
 * @property int $numero_reference
 * @property int $id_article
 */
class ArticleReference extends Model
{
    use SoftDeletes;

    public $timestamps = false;

    protected $table = 'reference_article';

    protected $primaryKey = 'id_reference';

    protected $fillable = ['id_article'];

    public function bikeReference(): BelongsTo
    {
        return $this->belongsTo(BikeReference::class, 'id_reference', 'id_reference');
    }

    public function ebikeReference(): BelongsTo
    {
        return $this->belongsTo(EBikeReference::class, 'id_reference', 'id_reference');
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
}
