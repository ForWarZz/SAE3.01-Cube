<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id_type_vae
 * @property string $label_type_vae
 */
class EBikeType extends Model
{
    public $timestamps = false;

    protected $primaryKey = 'id_type_vae';

    protected $table = 'type_vae';

    protected $fillable = [
        'id_type_vae',
        'label_type_vae',
    ];
}
