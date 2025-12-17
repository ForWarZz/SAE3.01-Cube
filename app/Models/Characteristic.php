<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id_caracteristique
 * @property int $id_type_carac
 * @property string $nom_caracteristique
 * @property string $pivot
 */
class Characteristic extends Model
{
    public $timestamps = false;

    protected $table = 'caracteristique';

    protected $primaryKey = 'id_caracteristique';

    protected $fillable = [
        'id_caracteristique',
        'id_type_carac',
        'nom_caracteristique',
    ];

    public function characteristicType(): BelongsTo
    {
        return $this->belongsTo(CharacteristicType::class, 'id_type_carac', 'id_type_carac');
    }
}
