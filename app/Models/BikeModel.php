<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id_modele_velo
 * @property string $nom_modele_velo
 */
class BikeModel extends Model
{
    protected $table = 'modele_velo';
    protected $primaryKey = 'id_modele_velo';
    protected $fillable = ['nom_modele_velo'];

    public function bikes(): HasMany
    {
        return $this->hasMany(Bike::class, 'id_modele_velo', 'id_modele_velo');
    }

    public function geometries(): HasMany
    {
        return $this->hasMany(Geometry::class, 'id_modele_velo', 'id_modele_velo');
    }
}
