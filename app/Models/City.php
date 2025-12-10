<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id_ville
 * @property string $nom_ville
 * @property string $cp_ville
 * @property string $pays_ville
 */
class City extends Model
{
    public $timestamps = false;

    protected $table = 'ville';

    protected $primaryKey = 'id_ville';

    protected $fillable = [
        'nom_ville',
        'cp_ville',
        'pays_ville',
    ];

    public function adresses(): HasMany
    {
        return $this->hasMany(Adresse::class, 'id_ville', 'id_ville');
    }
}
