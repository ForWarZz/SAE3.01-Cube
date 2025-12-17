<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id_cadre_velo
 * @property string $label_cadre_velo
 */
class BikeFrame extends Model
{
    protected $table = 'cadre_velo';

    protected $primaryKey = 'id_cadre_velo';

    protected $fillable = [
        'id_cadre_velo',
        'label_cadre_velo',
    ];
}
