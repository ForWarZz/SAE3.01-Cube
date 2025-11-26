<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id_materiau_cadre
 * @property string label_materiau_cadre
 */
class MateriauCadreVelo extends Model
{
    public $timestamps = false;

    protected $table = 'materiau_cadre_velo';
    protected $primaryKey = 'id_materiau_cadre';
    protected $fillable = [
        'id_materiau_cadre',
        'label_materiau_cadre'
    ];
}
