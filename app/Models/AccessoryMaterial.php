<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id_matiere_accessoire
 * @property string $nom_matiere_accessoire
 */
class AccessoryMaterial extends Model
{
    public $timestamps = false;

    protected $primaryKey = 'id_matiere_accessoire';

    protected $table = 'matiere_accessoire';

    protected $fillable = [
        'id_matiere_accessoire',
        'nom_matiere_accessoire',
    ];
}
