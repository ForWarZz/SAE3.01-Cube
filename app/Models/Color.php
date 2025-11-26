<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id_couleur
 * @property string $label_couleur
 */
class Color extends Model
{
    public $timestamps = false;

    protected $primaryKey = 'id_couleur';
    protected $table = 'couleur';
    protected $fillable = [
        'id_couleur',
        'label_couleur'
    ];
}
