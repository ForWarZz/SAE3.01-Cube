<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id_reference
 * @property int $id_cadre_velo
 * @property int $id_couleur
 * @property int $id_article
 */
class BikeReference extends Model
{
    public $timestamps = false;

    protected $table = 'reference_velo';

    protected $primaryKey = 'id_reference';

    protected $fillable = [
        'id_reference',
        'id_cadre_velo',
        'id_couleur',
        'id_article',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('active_reference', function ($builder) {
            $builder->whereHas('baseReference');
        });
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class, 'id_article', 'id_article');
    }

    public function bike(): BelongsTo
    {
        return $this->belongsTo(Bike::class, 'id_article', 'id_article');
    }

    public function ebike(): BelongsTo
    {
        return $this->belongsTo(EBikeReference::class, 'id_reference', 'id_reference');
    }

    public function color(): BelongsTo
    {
        return $this->belongsTo(Color::class, 'id_couleur', 'id_couleur');
    }

    public function frame(): BelongsTo
    {
        return $this->belongsTo(BikeFrame::class, 'id_cadre_velo', 'id_cadre_velo');
    }

    public function baseReference(): BelongsTo
    {
        return $this->belongsTo(ArticleReference::class, 'id_reference', 'id_reference');
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
