<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id_magasin
 * @property int $id_ville
 * @property string $num_voie_magasin
 * @property string $rue_magasin
 * @property string $complement_magasin
 * @property string $nom_magasin
 */
class Shop extends Model
{
    public $timestamps = false;
    protected $table = 'magasin';
    protected $primaryKey = 'id_magasin';
    protected $fillable = [
        'id_magasin',
        'id_ville',
        'num_voie_magasin',
        'rue_magasin',
        'complement_magasin',
        'nom_magasin',
    ];
}
