<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id_taille
 * @property string $nom_taille
 * @property string $pivot
 */
class Size extends Model
{
    protected $table = 'taille';

    protected $primaryKey = 'id_taille';

    public $timestamps = false;

    protected $fillable = [
        'id_taille',
        'nom_taille',
    ];
}
