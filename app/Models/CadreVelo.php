<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CadreVelo extends Model
{
    protected $table = 'cadre_velo';
    protected $primaryKey = 'id_cadre_velo';
    protected $fillable = [
        'id_cadre_velo',
        'label_cadre_velo'
    ];
}
