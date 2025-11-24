<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Geometrie extends Model
{
    protected $table = 'de_geometrie';
    public $incrementing = false;
    public $timestamps = false;

    public function taille(): BelongsTo
    {
        return $this->belongsTo(TailleVelo::class, 'id_taille');
    }

    public function caracteristique(): BelongsTo
    {
        return $this->belongsTo(CaracteristiqueGeometrie::class, 'id_carac_geo');
    }
}
