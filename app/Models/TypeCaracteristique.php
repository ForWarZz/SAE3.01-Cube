<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id_type_carac
 * @property string $nom_type_carac
 */
class TypeCaracteristique extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'id_type_carac';
    protected $table = 'type_caracteristique';

    protected $fillable = [
        'id_type_carac',
        'nom_type_carac'
    ];

//    public function caracteristiques(): HasMany
//    {
//        return $this->hasMany();
//    }
}
