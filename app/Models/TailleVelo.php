<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TailleVelo extends Model
{
    protected $table = 'taille_velo';
    protected $primaryKey = 'id_taille';
    public $timestamps = false;

    protected $fillable = [
        "id_taille",
        "label_taille",
    ];
}
