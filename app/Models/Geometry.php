<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id_geo
 * @property int $id_taille
 * @property int $id_carac_geo
 */
class Geometry extends Model
{
    protected $table = 'de_geometrie';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'id_geo',
        'id_taille',
        'id_carac_geo',
    ];

    public function size(): BelongsTo
    {
        return $this->belongsTo(Size::class, 'id_taille');
    }

    public function characteristic(): BelongsTo
    {
        return $this->belongsTo(GeometryCharacteristic::class, 'id_carac_geo');
    }
}
