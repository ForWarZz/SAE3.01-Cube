<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id_taille
 * @property string $nom_taille
 */
class BikeSize extends Model
{
    protected $table = 'taille_velo';
    protected $primaryKey = 'id_taille';
    public $timestamps = false;

    protected $fillable = [
        "id_taille",
        "nom_taille",
    ];
}
