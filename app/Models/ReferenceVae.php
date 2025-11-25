<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id_reference
 * @property int $id_batterie
 * @property int $id_article
 * @property int $id_cadre_velo
 * @property int $id_couleur
 */
class ReferenceVae extends Model
{
    public $timestamps = false;

    protected $table = 'reference_vae';
    protected $primaryKey = 'id_reference';
    protected $fillable = [
        'id_reference',
        'id_batterie',
        'id_article',
        'id_cadre_velo',
        'id_couleur',
    ];

    public function batterie(): BelongsTo
    {
        return $this->belongsTo(Batterie::class, 'id_batterie', 'id_batterie');
    }
}
