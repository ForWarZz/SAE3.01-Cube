<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id_modele_velo
 * @property int $id_taille
 * @property int $id_carac_geo
 * @property string|null $valeur_carac
 */
class Geometry extends Model
{
    public $incrementing = false;

    public $timestamps = false;

    protected $table = 'de_geometrie';

    protected $fillable = [
        'id_modele_velo',
        'id_taille',
        'id_carac_geo',
        'valeur_carac',
    ];

    public function bikeModel(): BelongsTo
    {
        return $this->belongsTo(BikeModel::class, 'id_modele_velo', 'id_modele_velo');
    }

    public function size(): BelongsTo
    {
        return $this->belongsTo(Size::class, 'id_taille', 'id_taille');
    }

    public function characteristic(): BelongsTo
    {
        return $this->belongsTo(GeometryCharacteristic::class, 'id_carac_geo', 'id_carac_geo');
    }
}
