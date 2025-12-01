<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id_reference
 * @property int $id_cadre_velo
 * @property int $id_couleur
 * @property int $id_article
 */
class BikeReference extends ArticleReference
{
    public $timestamps = false;

    protected $table = 'reference_velo';

    protected $fillable = [
        'id_reference',
        'id_cadre_velo',
        'id_couleur',
        'id_article',
    ];

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
}
