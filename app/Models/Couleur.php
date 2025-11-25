<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Couleur extends Model
{
    public $timestamps = false;

    protected $primaryKey = 'id_couleur';
    protected $table = 'couleur';
    protected $fillable = [
        'id_couleur',
        'label_couleur'
    ];
}
