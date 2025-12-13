<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id_batterie
 * @property string $capacite_batterie
 * @property string $label_batterie
 */
class Battery extends Model
{
    public $timestamps = false;

    protected $table = 'batterie';

    protected $primaryKey = 'id_batterie';

    protected $fillable = [
        'id_batterie',
        'capacite_batterie',
        'label_batterie',
    ];

    public function ebikeReferences(): HasMany
    {
        return $this->hasMany(EBikeReference::class, 'id_reference', 'id_reference');
    }
}
