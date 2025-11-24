<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CaracteristiqueGeometrie extends Model
{
    protected $table = 'caracteristique_geometrie';
    protected $primaryKey = 'id_carac_geo';
    public $timestamps = false;

    protected $fillable = [
        "id_carac_geo",
        "label_carac_geo",
    ];
}
